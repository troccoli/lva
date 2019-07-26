<?php

namespace Tests\Browser\CRUD;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DivisionTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testListAllDivisionsForNonExistingCompetition(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            $browser->visit('/competitions/1/divisions')
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testListAllDivisionsForCompetition(): void
    {
        $this->browse(function (Browser $browser): void {
            $season = factory(Season::class)->create(['year' => 2000]);
            $competition = factory(Competition::class)->create([
                'season_id' => $season->getId(),
                'name'      => 'Youth Games',
            ]);

            $browser->loginAs(factory(User::class)->create());

            $browser->visit('/competitions/' . $competition->getId() . '/divisions')
                ->assertSee("Divisions in the Youth Games 2000/01 competition")
                ->assertSeeIn('@list', 'There are no divisions in this competition yet.');

            factory(Division::class)->times(7)->create();
            $divisions = [
                factory(Division::class)->create(['competition_id' => $competition->getId(), 'display_order' => 1]),
                factory(Division::class)->create(['competition_id' => $competition->getId(), 'display_order' => 2]),
                factory(Division::class)->create(['competition_id' => $competition->getId(), 'display_order' => 3]),
            ];

            $browser->visit('/competitions/' . $competition->getId() . '/divisions')
                ->assertSeeLink('New division')
                ->with('@list', function (Browser $table) use ($divisions): void {
                    foreach ($divisions as $index => $division) {
                        $row = $index + 1;
                        /** @var Division $division */
                        $table->with("tr:nth-child($row)", function (Browser $row) use ($division): void {
                            $row->assertSeeIn('td:nth-child(1)', $division->getName());
                        });
                    }
                });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAddDivision(): void
    {
        $this->browse(function (Browser $browser): void {
            /** @var Competition $competition */
            $competition = factory(Competition::class)->create();
            /** @var Season $season */
            $season = $competition->getSeason();

            $browser->loginAs(factory(User::class)->create());

            // Check we can add a division from the landing page
            $browser->visit('/competitions/' . $competition->getId() . '/divisions')
                ->clickLink('New division')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions/create')
                ->assertSee("Add a new division for the {$competition->getName()} {$season->getName()} competition");

            // Check the form
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/create')
                ->assertInputValue('@competition-field', $competition->getName() . ' ' . $season->getName())
                ->assertDisabled('@competition-field')
                ->assertInputValue('name', '')
                ->assertInputValue('display_order', '1')
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'ADD DIVISION');

            // All fields missing
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/create')
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->type('display_order', ' ')// This is to get around the HTML5 validation on the browser
                ->press('ADD DIVISION')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions/create')
                ->assertSeeIn('@name-error', 'The name is required.')
                ->assertSeeIn('@display_order-error', 'The order is required.');

            /** @var Division $division */
            $division = factory(Division::class)->make(['competition_id' => $competition->getId()]);

            // Use a non numeric display order
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/create')
                ->type('name', $division->getName())
                ->type('display_order', 'A')
                ->press('ADD DIVISION')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions/create')
                ->assertSeeIn('@display_order-error', 'The order must be a positive number.');

            // Use a non positive display order
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/create')
                ->type('name', $division->getName())
                ->type('display_order', 0)
                ->press('ADD DIVISION')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions/create')
                ->assertSeeIn('@display_order-error', 'The order must be a positive number.');
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/create')
                ->type('name', $division->getName())
                ->type('display_order', -1)
                ->press('ADD DIVISION')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions/create')
                ->assertSeeIn('@display_order-error', 'The order must be a positive number.');

            // Use the same display order of an existing division
            factory(Division::class)->create(['competition_id' => $competition->getId(), 'display_order' => 3]);
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/create')
                ->type('name', $division->getName())
                ->type('display_order', 3)
                ->press('ADD DIVISION')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions/create')
                ->assertSeeIn('@display_order-error', 'The order is already used for another division.');

            // Brand new division
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/create')
                ->type('name', $division->getName())
                ->type('display_order', $division->getOrder())
                ->press('ADD DIVISION')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions')
                ->assertSee('Division added!');

            // Add the same division
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/create')
                ->type('name', $division->getName())
                ->type('display_order', $division->getOrder())
                ->press('ADD DIVISION')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions/create')
                ->assertSeeIn('@name-error', 'The division already exists in this competition.');

            // Add same division in different competition
            /** @var Competition $anotherCompetition */
            $anotherCompetition = factory(Competition::class)->create();
            $browser->visit('/competitions/' . $anotherCompetition->getId() . '/divisions/create')
                ->type('name', $division->getName())
                ->type('display_order', $division->getOrder())
                ->press('ADD DIVISION')
                ->assertPathIs('/competitions/' . $anotherCompetition->getId() . '/divisions')
                ->assertSee('Division added!');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testEditDivision(): void
    {
        $this->browse(function (Browser $browser): void {
            /** @var Division $division */
            $division = factory(Division::class)->create();
            $competition = $division->getCompetition();

            $browser->loginAs(factory(User::class)->create());

            // Check we can edit a competitions from the landing page
            $browser->visit('/competitions/' . $competition->getId() . '/divisions')
                ->with('@list', function (Browser $table) {
                    $table->clickLink('Update');
                })
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->assertSee("Edit the {$division->getName()} division in the {$competition->getName()} {$competition->getSeason()->getName()} competition");

            // Check the form
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->assertInputValue('@competition-field', $competition->getName() . ' ' . $competition->getSeason()->getName())
                ->assertDisabled('@competition-field')
                ->assertInputValue('name', $division->getName())
                ->assertInputValue('display_order', $division->getOrder())
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'SAVE CHANGES');

            // All fields missing
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->type('display_order', ' ')// This is to get around the HTML5 validation on the browser
                ->press('SAVE CHANGES')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->assertSeeIn('@name-error', 'The name is required.')
                ->assertSeeIn('@display_order-error', 'The order is required.');

            // Use an already existing division
            /** @var Division $anotherDivision */
            $anotherDivision = factory(Division::class)->create(['competition_id' => $competition->getId()]);
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->type('name', $anotherDivision->getName())
                ->press('SAVE CHANGES')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->assertSeeIn('@name-error', 'The division already exists in this competition.');

            // Use an invalid display order
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->type('display_order', 'A')
                ->press('SAVE CHANGES')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->assertSeeIn('@display_order-error', 'The order must be a positive number.');
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->type('display_order', 0)
                ->press('SAVE CHANGES')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->assertSeeIn('@display_order-error', 'The order must be a positive number.');
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->type('display_order', -1)
                ->press('SAVE CHANGES')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->assertSeeIn('@display_order-error', 'The order must be a positive number.');
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->type('display_order', $anotherDivision->getOrder())
                ->press('SAVE CHANGES')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->assertSeeIn('@display_order-error', 'The order is already used for another division.');

            /** @var Competition $newCompetition */
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->type('name', 'Great Division')
                ->type('display_order', 99)
                ->press('SAVE CHANGES')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions')
                ->assertSee('Division updated!')
                ->assertSeeIn('@list', 'Great Division');

            // Add same competition in different season
            $yetAnotherDivision = factory(Division::class)->create();
            $browser->visit('/competitions/' . $competition->getId() . '/divisions/' . $division->getId() . '/edit')
                ->type('name', $yetAnotherDivision->getName())
                ->type('display_order', $yetAnotherDivision->getOrder())
                ->press('SAVE CHANGES')
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions')
                ->assertSee('Division updated!')
                ->assertSeeIn('@list', $yetAnotherDivision->getName());
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeleteDivision(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            /** @var Division $division */
            $division = factory(Division::class)->create();

            $browser->visit('/competitions/' . $division->getCompetition()->getId() . '/divisions')
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Cancel')
                        ->pause(1000);
                })
                ->assertDontSee('Division deleted!')
                ->assertSeeIn('@list', $division->getName());
            $browser->visit('/competitions/' . $division->getCompetition()->getId() . '/divisions')
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Division deleted!')
                ->assertDontSeeIn('@list', $division->getName());
        });
    }
}
