<?php

namespace Tests\Browser\CRUD;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DivisionTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testListingAllDivisionsForNonExistingCompetition(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $browser->visit('/competitions/1/divisions')
                    ->assertTitle('Not Found')
                    ->assertSee('404')
                    ->assertSee('NOT FOUND');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testListingAllDivisionsForCompetition(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            /** @var Season $season */
            $season = Season::factory()->create(['year' => 1999]);
            $competition = Competition::factory()->for($season)->create(['name' => 'Youth Games']);

            $browser->visit("/competitions/{$competition->getId()}/divisions")
                    ->assertSee("Divisions in the Youth Games {$season->getName()} competition")
                    ->assertSeeIn('@list', 'There are no divisions in this competition yet.');

            $anotherSeason = Season::factory()->create(['year' => 2001]);
            $anotherCompetition = Competition::factory()->for($anotherSeason)->create();
            Division::factory()->for($anotherCompetition)->create(['name' => 'MP']);

            Division::factory()->for($competition)->create(['name' => 'DIV2BM', 'display_order' => 3]);
            Division::factory()->for($competition)->create(['name' => 'DIV1M', 'display_order' => 1]);
            Division::factory()->for($competition)->create(['name' => 'DIV2AM', 'display_order' => 2]);

            $browser->visit("/competitions/{$competition->getId()}/divisions")
                    ->assertSeeLink('New division')
                    ->with('@list', function (Browser $table): void {
                        $table->assertSeeIn('thead tr:nth-child(1)', 'Division');

                        $table->assertSeeIn('tbody tr:nth-child(1)', 'DIV1M');
                        $table->assertSeeIn('tbody tr:nth-child(2)', 'DIV2AM');
                        $table->assertSeeIn('tbody tr:nth-child(3)', 'DIV2BM');
                        $table->assertDontSee('MP');
                    });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAddingADivision(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $browser->visit('/competitions/1/divisions/create')
                    ->assertTitle('Not Found')
                    ->assertSee('404')
                    ->assertSee('NOT FOUND');

            $season = Season::factory()->create(['year' => 2010]);
            $competition = Competition::factory()->for($season)->create(['name' => 'London League']);

            // Check we can add a division from the landing page
            $browser->visit("/competitions/{$competition->getId()}/divisions")
                    ->clickLink('New division')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions/create")
                    ->assertSee('Add a new division for the London League 2010/11 competition');

            // Check the form
            $browser->visit("/competitions/{$competition->getId()}/divisions/create")
                    ->assertInputValue('@competition-field', 'London League 2010/11')
                    ->assertDisabled('@competition-field')
                    ->assertInputValue('name', '')
                    ->assertInputValue('display_order', '1')
                    ->assertVisible('@submit-button')
                    ->assertSeeIn('@submit-button', 'ADD DIVISION');

            // All fields missing
            $browser->visit("/competitions/{$competition->getId()}/divisions/create")
                    ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                    ->type('display_order', ' ')// This is to get around the HTML5 validation on the browser
                    ->press('ADD DIVISION')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions/create")
                    ->assertSeeIn('@name-error', 'The name is required.')
                    ->assertSeeIn('@display_order-error', 'The order is required.');
            $this->assertDatabaseMissing('divisions', ['name' => 'MP', 'competition_id' => $competition->getId()]);

            // Use a non numeric display order
            $browser->visit("/competitions/{$competition->getId()}/divisions/create")
                    ->type('name', 'MP')
                    ->type('display_order', 'A')
                    ->press('ADD DIVISION')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions/create")
                    ->assertSeeIn('@display_order-error', 'The order must be a positive number.');
            $this->assertDatabaseMissing('divisions', ['name' => 'MP', 'competition_id' => $competition->getId()]);

            // Use a non positive display order
            $browser->visit("/competitions/{$competition->getId()}/divisions/create")
                    ->type('name', 'MP')
                    ->type('display_order', 0)
                    ->press('ADD DIVISION')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions/create")
                    ->assertSeeIn('@display_order-error', 'The order must be a positive number.');
            $this->assertDatabaseMissing('divisions', ['name' => 'MP', 'competition_id' => $competition->getId()]);
            $browser->visit("/competitions/{$competition->getId()}/divisions/create")
                    ->type('name', 'MP')
                    ->type('display_order', -1)
                    ->press('ADD DIVISION')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions/create")
                    ->assertSeeIn('@display_order-error', 'The order must be a positive number.');
            $this->assertDatabaseMissing('divisions', ['name' => 'MP', 'competition_id' => $competition->getId()]);

            // Use the same display order of an existing division
            Division::factory()->for($competition)->create(['display_order' => 3]);
            $browser->visit("/competitions/{$competition->getId()}/divisions/create")
                    ->type('name', 'MP')
                    ->type('display_order', 3)
                    ->press('ADD DIVISION')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions/create")
                    ->assertSeeIn('@display_order-error', 'The order is already used for another division.');
            $this->assertDatabaseMissing(
                'divisions',
                [
                    'name' => 'MP',
                    'competition_id' => $competition->getId(),
                    'display_order' => 3,
                ]
            );

            // Brand new division
            $browser->visit("/competitions/{$competition->getId()}/divisions/create")
                    ->type('name', 'MP')
                    ->type('display_order', 2)
                    ->press('ADD DIVISION')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions")
                    ->assertSee('Division added!');
            $this->assertDatabaseHas(
                'divisions',
                [
                    'name' => 'MP',
                    'competition_id' => $competition->getId(),
                    'display_order' => 2,
                ]
            );

            // Add same division in different competition
            $anotherCompetition = Competition::factory()->for($season)->create(['name' => 'Sussex League']);
            Division::factory()->for($anotherCompetition)->create(['name' => 'DIV1M']);
            $browser->visit("/competitions/{$competition->getId()}/divisions/create")
                    ->type('name', 'DIV1M')
                    ->type('display_order', 1)
                    ->press('ADD DIVISION')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions")
                    ->assertSee('Division added!');
            $this->assertDatabaseHas(
                'divisions',
                [
                    'name' => 'DIV1M',
                    'competition_id' => $competition->getId(),
                    'display_order' => 1,
                ]
            );
        });
    }

    /**
     * @throws \Throwable
     */
    public function testEditingADivision(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $browser->visit('/competitions/1/divisions/1/edit')
                    ->assertTitle('Not Found')
                    ->assertSee('404')
                    ->assertSee('NOT FOUND');

            $season = Season::factory()->create(['year' => 2019]);
            $competition = Competition::factory()->for($season)->create(['name' => 'London League']);

            $browser->visit("/competitions/{$competition->getId()}/divisions/1/edit")
                    ->assertTitle('Not Found')
                    ->assertSee('404')
                    ->assertSee('NOT FOUND');

            $division = Division::factory()->for($competition)->create(['name' => 'MP', 'display_order' => 2]);

            // Check we can edit a competitions from the landing page
            $browser->visit("/competitions/{$competition->getId()}/divisions")
                    ->with("@division-{$division->getId()}-row", function (Browser $table) {
                        $table->clickLink('Update');
                    })
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->assertSee('Edit the MP division in the London League 2019/20 competition');

            // Check the form
            $browser->visit("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->assertInputValue('@competition-field', 'London League 2019/20')
                    ->assertDisabled('@competition-field')
                    ->assertInputValue('name', 'MP')
                    ->assertInputValue('display_order', '2')
                    ->assertVisible('@submit-button')
                    ->assertSeeIn('@submit-button', 'SAVE CHANGES');

            // All fields missing
            $browser->visit("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                    ->type('display_order', ' ')// This is to get around the HTML5 validation on the browser
                    ->press('SAVE CHANGES')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->assertSeeIn('@name-error', 'The name is required.')
                    ->assertSeeIn('@display_order-error', 'The order is required.');
            $this->assertDatabaseHas(
                'divisions',
                [
                    'name' => 'MP',
                    'competition_id' => $competition->getId(),
                    'display_order' => 2,
                ]
            );

            // Use an already existing division
            Division::factory()->for($competition)->create(['name' => 'DIV1M', 'display_order' => 1]);
            $browser->visit("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->type('name', 'DIV1M')
                    ->press('SAVE CHANGES')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->assertSeeIn('@name-error', 'The division already exists in this competition.');
            $this->assertDatabaseHas(
                'divisions',
                [
                    'name' => 'MP',
                    'competition_id' => $competition->getId(),
                    'display_order' => 2,
                ]
            );

            // Use an invalid display order
            $browser->visit("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->type('display_order', 'A')
                    ->press('SAVE CHANGES')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->assertSeeIn('@display_order-error', 'The order must be a positive number.');
            $this->assertDatabaseHas(
                'divisions',
                [
                    'name' => 'MP',
                    'competition_id' => $competition->getId(),
                    'display_order' => 2,
                ]
            );
            $browser->visit("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->type('display_order', 0)
                    ->press('SAVE CHANGES')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->assertSeeIn('@display_order-error', 'The order must be a positive number.');
            $this->assertDatabaseHas(
                'divisions',
                [
                    'name' => 'MP',
                    'competition_id' => $competition->getId(),
                    'display_order' => 2,
                ]
            );
            $browser->visit("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->type('display_order', -1)
                    ->press('SAVE CHANGES')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->assertSeeIn('@display_order-error', 'The order must be a positive number.');
            $this->assertDatabaseHas(
                'divisions',
                [
                    'name' => 'MP',
                    'competition_id' => $competition->getId(),
                    'display_order' => 2,
                ]
            );
            $browser->visit("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->type('display_order', '1')
                    ->press('SAVE CHANGES')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->assertSeeIn('@display_order-error', 'The order is already used for another division.');
            $this->assertDatabaseHas(
                'divisions',
                [
                    'name' => 'MP',
                    'competition_id' => $competition->getId(),
                    'display_order' => 2,
                ]
            );

            // OK
            $browser->visit("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->type('name', 'WP')
                    ->type('display_order', 3)
                    ->press('SAVE CHANGES')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions")
                    ->assertSee('Division updated!');
            $this->assertDatabaseMissing(
                'divisions',
                [
                    'name' => 'MP',
                    'competition_id' => $competition->getId(),
                    'display_order' => 2,
                ]
            );
            $this->assertDatabaseHas(
                'divisions',
                [
                    'name' => 'WP',
                    'competition_id' => $competition->getId(),
                    'display_order' => 3,
                ]
            );

            // Add same division in different competition
            $anotherCompetition = Competition::factory()->for($season)->create(['name' => 'Sussex League']);
            Division::factory()->for($anotherCompetition)->create(['name' => 'DIV2M']);
            $browser->visit("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
                    ->type('name', 'DIV2M')
                    ->press('SAVE CHANGES')
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions")
                    ->assertSee('Division updated!');
            $this->assertDatabaseMissing(
                'divisions',
                [
                    'name' => 'WP',
                    'competition_id' => $competition->getId(),
                    'display_order' => 3,
                ]
            );
            $this->assertDatabaseHas(
                'divisions',
                [
                    'name' => 'DIV2M',
                    'competition_id' => $competition->getId(),
                    'display_order' => 3,
                ]
            );
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeletingADivision(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $season = Season::factory()->create(['year' => 2019]);
            $competition = Competition::factory()->for($season)->create();
            $division = Division::factory()->for($competition)->create();

            $browser->visit("/competitions/{$competition->getId()}/divisions")
                    ->within("@division-{$division->getId()}-row", function (Browser $row): void {
                        $row->press('Delete');
                    })
                    ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                        $modal->assertSee('Are you sure?')
                              ->press('Cancel')
                              ->pause(1000);
                    })
                    ->assertDontSee('Division deleted!');
            $this->assertDatabaseHas('divisions', ['id' => $division->getId(), 'deleted_at' => null]);

            $browser->visit("/competitions/{$competition->getId()}/divisions")
                    ->within("@division-{$division->getId()}-row", function (Browser $row): void {
                        $row->press('Delete');
                    })
                    ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                        $modal->assertSee('Are you sure?')
                              ->press('Confirm')
                              ->pause(1000);
                    })
                    ->assertSee('Division deleted!');
            $this->assertSoftDeleted('divisions', ['id' => $division->getId()]);
        });
    }
}
