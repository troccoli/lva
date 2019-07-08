<?php

namespace Tests\Browser\CRUD;

use App\Models\Competition;
use App\Models\Season;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CompetitionTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testListAllCompetitionsForNonExistingSeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            $browser->visit('/competitions')
                ->assertPathIs('/seasons')
                ->assertSee('There are no seasons yet!');

            $browser->visit('/competitions?season_id=1')
                ->assertPathIs('/seasons')
                ->assertSee('The season does not exist!');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testListAllCompetitionsForLatestSeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $oldSeason = factory(Season::class)->state('last-year')->create();
            $season = factory(Season::class)->create();
            factory(Competition::class)->times(3)->create(['season_id' => $oldSeason->getId()]);
            $competitions = factory(Competition::class)
                ->times(3)
                ->create(['season_id' => $season->getId()])
                ->sort(function ($c1, $c2) {
                    return $c1->getName() <=> $c2->getName();
                });

            $browser->loginAs(factory(User::class)->create());

            $browser->visit('/competitions')
                ->assertSee('Competitions for season ' . $season->getName())
                ->assertSeeLink('New competition')
                ->with('@list', function (Browser $table) use ($competitions): void {
                    $child = 1;
                    foreach ($competitions as $competition) {
                        $table->with("tr:nth-child($child)", function (Browser $row) use ($competition): void {
                            $row->assertSeeIn('td:nth-child(1)', $competition->getName());
                        });
                        $child++;
                    }
                });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testListAllCompetitionsForSpecificSeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $oldSeason = factory(Season::class)->state('last-year')->create();
            $season = factory(Season::class)->create();
            factory(Competition::class)->times(3)->create(['season_id' => $season->getId()]);
            $competitions = factory(Competition::class)
                ->times(3)
                ->create(['season_id' => $oldSeason->getId()])
                ->sort(function (Competition $c1, Competition $c2) {
                    return $c1->getName() <=> $c2->getName();
                });

            $browser->loginAs(factory(User::class)->create());

            $browser->visit('/competitions?season_id=' . $oldSeason->getId())
                ->assertSee('Competitions for season ' . $oldSeason->getName())
                ->assertSeeLink('New competition')
                ->with('@list', function (Browser $table) use ($competitions): void {
                    $child = 1;
                    foreach ($competitions as $competition) {
                        /** @var Competition $competition */
                        $table->with("tr:nth-child($child)", function (Browser $row) use ($competition): void {
                            $row->assertSeeIn('td:nth-child(1)', $competition->getName());
                        });
                        $child++;
                    }
                });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAddCompetition(): void
    {
        $this->browse(function (Browser $browser) {
            $oldSeason = factory(Season::class)->state('last-year')->create();
            $season = factory(Season::class)->create();

            $browser->loginAs(factory(User::class)->create());

            // Check we can add a competitions from the landing page
            $browser->visit('/competitions')
                ->clickLink('New competition')
                ->assertPathIs('/competitions/create');

            // Check we are adding a competition for the correct season
            $browser->visit('/competitions')
                ->clickLink('New competition')
                ->assertSee('Add a new competition in season ' . $season->getName());
            $browser->visit('/competitions?season_id=' . $season->getId())
                ->clickLink('New competition')
                ->assertSee('Add a new competition in season ' . $season->getName());
            $browser->visit('/competitions?season_id=' . $oldSeason->getId())
                ->clickLink('New competition')
                ->assertSee('Add a new competition in season ' . $oldSeason->getName());

            // Check the form
            $browser->visit('/competitions/create?season_id=' . $season->getId())
                ->assertInputValue('@season-field', $season->getName())
                ->assertDisabled('@season-field')
                ->assertInputValue('name', '')
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'ADD COMPETITION');

            // All fields missing
            $browser->visit('/competitions/create?season_id=' . $season->getId())
                ->type('name', ' ') // This is to get around the HTML5 validation on the browser
                ->press('ADD COMPETITION')
                ->assertPathIs('/competitions/create')
                ->assertQueryStringHas('season_id', $season->getId())
                ->assertSeeIn('@name-error', 'The name is required.');

            /** @var Competition $competition */
            $competition = factory(Competition::class)->make(['season_id' => $season->getId()]);
            // Brand new competition
            $browser->visit('/competitions/create?season_id=' . $season->getId())
                ->type('name', $competition->getName())
                ->press('ADD COMPETITION')
                ->assertPathIs('/competitions')
                ->assertQueryStringHas('season_id', $season->getId())
                ->assertSee('Competition added!');

            // Add the same competition
            $browser->visit('/competitions/create?season_id=' . $season->getId())
                ->type('name', $competition->getName())
                ->press('ADD COMPETITION')
                ->assertPathIs('/competitions/create')
                ->assertQueryStringHas('season_id', $season->getId())
                ->assertSeeIn('@name-error', 'The competition already exists in this season.');

            // Add same competition in different season
            $browser->visit('/competitions/create?season_id=' . $oldSeason->getId())
                ->type('name', $competition->getName())
                ->press('ADD COMPETITION')
                ->assertPathIs('/competitions')
                ->assertQueryStringHas('season_id', $oldSeason->getId())
                ->assertSee('Competition added!');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testEditCompetition(): void
    {
        $this->browse(function (Browser $browser) {
            $oldSeason = factory(Season::class)->state('last-year')->create();
            $season = factory(Season::class)->create();
            $competition = factory(Competition::class)->create(['season_id' => $season->getId()]);

            $browser->loginAs(factory(User::class)->create());

            // Check we can edit a competitions from the landing page
            $browser->visit('/competitions')
                ->with('@list', function (Browser $table) {
                    $table->clickLink('Update');
                })
                ->assertPathIs('/competitions/' . $competition->getId() . '/edit')
                ->assertSee('Edit the ' . $competition->getName() . ' competition in season ' . $season->getName());

            // Check the form
            $browser->visit('/competitions/' . $competition->getId() . '/edit')
                ->assertInputValue('@season-field', $season->getName())
                ->assertDisabled('@season-field')
                ->assertInputValue('name', $competition->getName())
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'SAVE CHANGES');

            // All fields missing
            $browser->visit('/competitions/' . $competition->getId() . '/edit?season_id=' . $season->getId())
                ->type('name', ' ') // This is to get around the HTML5 validation on the browser
                ->press('SAVE CHANGES')
                ->assertPathIs('/competitions/' . $competition->getId() . '/edit')
                ->assertQueryStringHas('season_id', $season->getId())
                ->assertSeeIn('@name-error', 'The name is required.');

            /** @var Competition $newCompetition */
            $browser->visit('/competitions/' . $competition->getId() . '/edit?season_id=' . $season->getId())
                ->type('name', 'Great Competition')
                ->press('SAVE CHANGES')
                ->assertPathIs('/competitions')
                ->assertQueryStringHas('season_id', $season->getId())
                ->assertSee('Competition updated!')
                ->assertSeeIn('@list', 'Great Competition');

            // Use the name of an already existing competition in this season
            $competition2 = factory(Competition::class)->create(['season_id' => $season->getId()]);
            $browser->visit('/competitions/' . $competition->getId() . '/edit?season_id=' . $season->getId())
                ->type('name', $competition2->getName())
                ->press('SAVE CHANGES')
                ->assertPathIs('/competitions/' . $competition->getId() . '/edit')
                ->assertQueryStringHas('season_id', $season->getId())
                ->assertSeeIn('@name-error', 'The competition already exists in this season.');

            // Add same competition in different season
            $competition3 = factory(Competition::class)->create(['season_id' => $oldSeason->getId()]);
            $browser->visit('/competitions/' . $competition->getId() . '/edit?season_id=' . $season->getId())
                ->type('name', $competition3->getName())
                ->press('SAVE CHANGES')
                ->assertPathIs('/competitions')
                ->assertQueryStringHas('season_id', $season->getId())
                ->assertSee('Competition updated!')
                ->assertSeeIn('@list', $competition3->getName());
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeleteSeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            /** @var Competition $competition */
            $competition = factory(Competition::class)->create();

            $browser->visit('/competitions')
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Cancel')
                        ->pause(1000);
                })
                ->assertDontSee('Competition deleted!')
                ->assertSeeIn('@list', $competition->getName());
            $browser->visit('/competitions')
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Competition deleted!')
                ->assertDontSeeIn('@list', $competition->getName());
        });
    }
}
