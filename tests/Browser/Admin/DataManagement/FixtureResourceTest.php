<?php

namespace Tests\Browser\Admin\DataManagement;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Dusk\Browser;
use LVA\Models\AvailableAppointment;
use LVA\Models\Fixture;
use LVA\User;
use Tests\Browser\Pages\Resources\FixturesPage;
use Tests\DuskTestCase;

class FixtureResourceTest extends DuskTestCase
{
    const BASE_ROUTE = 'fixtures';

    public function testRedirectIfNotAdmin()
    {
        $page = new FixturesPage();

        $this->browse(function (Browser $browser) use ($page) {
            $browser->visit($page->indexUrl())
                ->assertRouteIs('login');

            $browser->visit($page->createUrl())
                ->assertRouteIs('login');

            $browser->visit($page->showUrl(1))
                ->assertRouteIs('login');

            $browser->visit($page->editUrl(1))
                ->assertRouteIs('login');

        });
    }

    public function testListFixtures()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Collection $fixtures */
            $fixtures = factory(Fixture::class)->times(20)->create();

            $page1 = $fixtures->slice(0, 15);
            $page2 = $fixtures->slice(15, 5);

            $page = new FixturesPage();
            $browser->visit($page)
                ->assertSeeIn($page->breadcrumb, 'Fixtures')
                ->assertSeeLink('New fixture')
                ->with('tbody', function ($table) use ($page1) {
                    $child = 1;
                    foreach ($page1 as $fixture) {
                        $table->with("tr:nth-child($child)", function ($row) use ($fixture) {
                            $linkText = $fixture->division . ':' . $fixture->match_number;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $linkText)
                                ->assertSeeIn('td:nth-child(2)', $fixture->match_date->format('j M Y'))
                                ->assertSeeIn('td:nth-child(3)', $fixture->warm_up_time->format('H:i'))
                                ->assertSeeIn('td:nth-child(4)', $fixture->start_time->format('H:i'))
                                ->assertSeeIn('td:nth-child(5)', (string)$fixture->home_team)
                                ->assertSeeIn('td:nth-child(6)', (string)$fixture->away_team)
                                ->assertSeeIn('td:nth-child(7)', (string)$fixture->venue)
                            ;
                        });
                        $child++;
                    }
                })
                ->with($page->pageNavigation, function ($nav) {
                    $nav->clickLink(2);
                })
                ->assertPathIs($page->indexUrl())
                ->with('tbody', function ($table) use ($page2) {
                    $child = 1;
                    foreach ($page2 as $fixture) {
                        $table->with("tr:nth-child($child)", function ($row) use ($fixture) {
                            $linkText = $fixture->division . ':' . $fixture->match_number;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $linkText)
                                ->assertSeeIn('td:nth-child(2)', $fixture->match_date->format('j M Y'))
                                ->assertSeeIn('td:nth-child(3)', $fixture->warm_up_time->format('H:i'))
                                ->assertSeeIn('td:nth-child(4)', $fixture->start_time->format('H:i'))
                                ->assertSeeIn('td:nth-child(5)', (string)$fixture->home_team)
                                ->assertSeeIn('td:nth-child(6)', (string)$fixture->away_team)
                                ->assertSeeIn('td:nth-child(7)', (string)$fixture->venue)
                            ;
                        });
                        $child++;
                    }
                });
        });
    }

    public function testAddFixture()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            $page = new FixturesPage();

            $browser->visit($page)
                ->clickLink('New fixture')
                ->assertPathIs($page->createUrl())
                // All fields missing
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@division-id-error', 'The division id field is required.')
                ->assertSeeIn('@match-number-error', 'The match number field is required.')
                ->assertSeeIn('@match-date-error', 'The match date field is required.')
                ->assertSeeIn('@warm-up-time-error', 'The warm up time field is required.')
                ->assertSeeIn('@start-time-error', 'The start time field is required.')
                ->assertSeeIn('@home-team-id-error', 'The home team id field is required.')
                ->assertSeeIn('@away-team-id-error', 'The away team id field is required.')
                ->assertSeeIn('@venue-id-error', 'The venue id field is required.')
                ->assertVisible('@form-errors');
            // @todo add a test when the division is not selected (#15)
            // @todo add a test when the home team is not selected (#15)
            // @todo add a test when the away team is not selected (#15)
            // @todo add a test when the venue is not selected (#15)

            // Make a fixture now. This will create a division, two teams (and two clubs) and a venue
            // which is why this is done here so that the test above does not have any of those
            // pre-selected

            /** @var Fixture $fixture */
            $fixture = factory(Fixture::class)->make();
            // Brand new fixture
            $browser->visit($page->createUrl())
                ->select('division_id', $fixture->division_id)
                ->select('home_team_id', $fixture->home_team_id)
                ->select('away_team_id', $fixture->away_team_id)
                ->select('venue_id', $fixture->venue_id)
                ->type('match_number', $fixture->match_number)
                ->keys('#match_date', [$fixture->match_date->format('dmY')])
                ->keys('#warm_up_time', [$fixture->warm_up_time->format('Hi')])
                ->keys('#start_time', [$fixture->start_time->format('Hi')])
                ->type('notes', $fixture->notes)
                ->pressSubmit('Add')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Fixture added!');

            /** @var Fixture $fixture2 */
            $fixture2 = factory(Fixture::class)->make();
            // Brand new fixture without notes
            $browser->visit($page->createUrl())
                ->select('division_id', $fixture2->division_id)
                ->select('home_team_id', $fixture2->home_team_id)
                ->select('away_team_id', $fixture2->away_team_id)
                ->select('venue_id', $fixture2->venue_id)
                ->type('match_number', $fixture2->match_number)
                ->keys('#match_date', [$fixture2->match_date->format('dmY')])
                ->keys('#warm_up_time', [$fixture2->warm_up_time->format('Hi')])
                ->keys('#start_time', [$fixture2->start_time->format('Hi')])
                ->pressSubmit('Add')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Fixture added!');

            /** @var Fixture $fixture3 */
            $fixture3 = factory(Fixture::class)->make();
            // New fixture with same home and away team
            $browser->visit($page->createUrl())
                ->select('division_id', $fixture3->division_id)
                ->select('home_team_id', $fixture3->home_team_id)
                ->select('away_team_id', $fixture3->home_team_id)
                ->select('venue_id', $fixture3->venue_id)
                ->type('match_number', $fixture3->match_number)
                ->keys('#match_date', [$fixture3->match_date->format('dmY')])
                ->keys('#warm_up_time', [$fixture3->warm_up_time->format('Hi')])
                ->keys('#start_time', [$fixture3->start_time->format('Hi')])
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@away-team-id-error', 'The away team cannot be the same as the home team.');

            // New fixture with same division, home and away teams of existing one
            $browser->visit($page->createUrl())
                ->select('division_id', $fixture->division_id)
                ->select('home_team_id', $fixture->home_team_id)
                ->select('away_team_id', $fixture->away_team_id)
                ->select('venue_id', $fixture3->venue_id)
                ->type('match_number', $fixture3->match_number)
                ->keys('#match_date', [$fixture3->match_date->format('dmY')])
                ->keys('#warm_up_time', [$fixture3->warm_up_time->format('Hi')])
                ->keys('#start_time', [$fixture3->start_time->format('Hi')])
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@division-id-error', 'The fixture for these two teams have already been added in this division.');

            // New fixture with same division and match number of existing one
            $browser->visit($page->createUrl())
                ->select('division_id', $fixture->division_id)
                ->select('home_team_id', $fixture3->home_team_id)
                ->select('away_team_id', $fixture3->away_team_id)
                ->select('venue_id', $fixture3->venue_id)
                ->type('match_number', $fixture->match_number)
                ->keys('#match_date', [$fixture3->match_date->format('dmY')])
                ->keys('#warm_up_time', [$fixture3->warm_up_time->format('Hi')])
                ->keys('#start_time', [$fixture3->start_time->format('Hi')])
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@match-number-error', 'There is already a match with the same number in this division.');
        });
    }

    public function testEditFixture()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Fixture $fixture */
            $fixture = factory(Fixture::class)->create();

            $page = new FixturesPage();

            // Don't change anything
            $browser->visit($page)
                ->clickLink('Update')
                ->assertPathIs($page->editUrl($fixture->id))
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Fixture updated!');

            /** @var Fixture $newFixture */
            $newFixture = factory(Fixture::class)->make(['id' => $fixture->id]);

            // Edit all  details
            $browser->visit($page->editUrl($fixture->id))
                ->select('division_id', $newFixture->division_id)
                ->select('home_team_id', $newFixture->home_team_id)
                ->select('away_team_id', $newFixture->away_team_id)
                ->select('venue_id', $newFixture->venue_id)
                ->type('match_number', $newFixture->match_number)
                ->keys('#match_date', [$newFixture->match_date->format('dmY')])
                ->keys('#warm_up_time', [$newFixture->warm_up_time->format('Hi')])
                ->keys('#start_time', [$newFixture->start_time->format('Hi')])
                ->type('notes', $newFixture->notes)
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Fixture updated!');

            $fixture = $newFixture;
            unset($newFixture);

            /** @var Fixture $newFixture */
            $newFixture = factory(Fixture::class)->make(['id' => $fixture->id]);

            // Remove the notes
            $browser->visit($page->editUrl($fixture->id))
                ->select('division_id', $newFixture->division_id)
                ->select('home_team_id', $newFixture->home_team_id)
                ->select('away_team_id', $newFixture->away_team_id)
                ->select('venue_id', $newFixture->venue_id)
                ->type('match_number', $newFixture->match_number)
                ->keys('#match_date', [$newFixture->match_date->format('dmY')])
                ->keys('#warm_up_time', [$newFixture->warm_up_time->format('Hi')])
                ->keys('#start_time', [$newFixture->start_time->format('Hi')])
                ->clear('notes')
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Fixture updated!');

            $fixture = $newFixture;
            unset($newFixture);

            // Use the same team for home and away
            $browser->visit($page->editUrl($fixture->id))
                ->select('home_team_id', $fixture->home_team_id)
                ->select('away_team_id', $fixture->home_team_id)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($fixture->id))
                ->assertSeeIn('@away-team-id-error', 'The away team cannot be the same as the home team.');

            /** @var Fixture $newFixture */
            $newFixture = factory(Fixture::class)->create();

            // Use the same division, home and away teams of an existing fixture
            $browser->visit($page->editUrl($fixture->id))
                ->select('division_id', $newFixture->division_id)
                ->select('home_team_id', $newFixture->home_team_id)
                ->select('away_team_id', $newFixture->away_team_id)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($fixture->id))
                ->assertSeeIn('@division-id-error', 'The fixture for these two teams have already been added in this division.');

            // Use the same division and match number of an existing fixture
            $browser->visit($page->editUrl($fixture->id))
                ->select('division_id', $newFixture->division_id)
                ->type('match_number', $newFixture->match_number)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($fixture->id))
                ->assertSeeIn('@match-number-error', 'There is already a match with the same number in this division.');
        });
    }

    public function testShowFixture()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Fixture $fixture */
            $fixture = factory(Fixture::class)->create();
            $linkText = $fixture->division . ':' . $fixture->match_number;

            $page = new FixturesPage();

            $browser->visit($page)
                ->clickLink($linkText)
                ->assertPathIs($page->showUrl($fixture->id))
                ->assertSeeIn('tbody tr td:nth-child(1)', (string)$fixture->division->season)
                ->assertSeeIn('tbody tr td:nth-child(2)', $fixture->division->division)
                ->assertSeeIn('tbody tr td:nth-child(3)', $fixture->match_number)
                ->assertSeeIn('tbody tr td:nth-child(4)', $fixture->match_date->format('j M Y'))
                ->assertSeeIn('tbody tr td:nth-child(5)', $fixture->warm_up_time->format('H:i'))
                ->assertSeeIn('tbody tr td:nth-child(6)', $fixture->start_time->format('H:i'))
                ->assertSeeIn('tbody tr td:nth-child(7)', (string)$fixture->home_team)
                ->assertSeeIn('tbody tr td:nth-child(8)', (string)$fixture->away_team)
                ->assertSeeIn('tbody tr td:nth-child(9)', (string)$fixture->venue)
                ->assertSeeIn('tbody tr td:nth-child(10)', $fixture->notes);
        });
    }

    public function testDeleteFixture()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Fixture $fixture */
            $fixture = factory(Fixture::class)->create();

            $page = new FixturesPage();

            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('No');
                })
                ->assertDontSee('Fixture deleted!')
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Fixture deleted!');

            // Delete fixture with existing appointment
            $fixture = factory(Fixture::class)->create();
            $appointment = factory(AvailableAppointment::class)->create(['fixture_id' => $fixture->id]);
            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Cannot delete because they are existing appointments for this fixture.');
        });
    }
}
