<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class DashboardTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testForGuests(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/dashboard')
                ->assertPathIs('/login');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testForUnverifiedUsers(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->state('unverified')->create())
                ->visit('/dashboard')
                ->assertSee('Verify your email address!')
                ->assertPathIs('/email/verify');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDashboardContent(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create())
                ->visit('/dashboard')
                ->assertSee('Welcome to your dashboard')
                ->assertSee('From here you can access all the sections of the site you need as a League Administrator.');

            $browser->visit('/dashboard')
                ->within('@raw-data-panel', function (Browser $panel): void {
                    $panel->assertSeeIn('.card-header', 'RAW DATA')
                        ->assertSeeIn('.card-body',
                            'From here you can access the raw data for your competitions, teams, venues, etc.')
                        ->assertSeeIn('.card-body',
                            'If you need to start a new season, or move teams between divisions, please use the')
                        ->assertSeeIn('.card-body', 'Manage Data panel below');
                });

            $browser->visit('/dashboard')
                ->within('@raw-data-panel', function (Browser $panel): void {
                    $panel->assertSeeIn('@structure-tab-header', 'Structure')
                        ->within('@structure-tab-content', function (Browser $tab): void {
                            $tab->assertSeeIn('@header', 'Seasons, competitions and divisions')
                                ->assertSee('This is where you create and edit all the data for the seasons, competitions and divisions.')
                                ->assertSeeLink('Manage seasons');
                        })
                        ->assertSeeIn('@participants-tab-header', 'Participants')
                        ->assertMissing('@participants-tab-content')
                        ->assertSeeIn('@venues-tab-header', 'Venues')
                        ->assertMissing('@venues-tab-content');
                });

            $browser->visit('/dashboard')
                ->clickLink('Participants')
                ->waitFor('@participants-tab-content')
                ->within('@raw-data-panel', function (Browser $panel): void {
                    $panel->assertSeeIn('@structure-tab-header', 'Structure')
                        ->assertMissing('@structure-tab-content')
                        ->assertSeeIn('@participants-tab-header', 'Participants')
                        ->within('@participants-tab-content', function (Browser $tab): void {
                            $tab->assertSeeIn('@header', 'Clubs and teams')
                                ->assertSee('This is where you create and edit all the data for clubs and their teams.')
                                ->assertSeeLink('Manage clubs');
                        })
                        ->assertSeeIn('@venues-tab-header', 'Venues')
                        ->assertMissing('@venues-tab-content');
                });

            $browser->visit('/dashboard')
                ->clickLink('Venues')
                ->waitFor('@venues-tab-content')
                ->within('@raw-data-panel', function (Browser $panel): void {
                    $panel->assertSeeIn('@structure-tab-header', 'Structure')
                        ->assertMissing('@structure-tab-content')
                        ->assertSeeIn('@participants-tab-header', 'Participants')
                        ->assertMissing('@participants-tab-content')
                        ->assertSeeIn('@venues-tab-header', 'Venues')
                        ->within('@venues-tab-content', function (Browser $tab): void {
                            $tab->assertSeeIn('@header', 'Venues')
                                ->assertSee('This is where you manage all the venues for all clubs and teams, regardless of season or competition.')
                                ->assertSeeLink('Manage venues');
                        });
                });
        });
    }
}
