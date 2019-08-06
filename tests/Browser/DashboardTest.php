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
                ->assertSee('From here you can access all the sections of the site you need as a League Administrator.')
                ->within('@seasons-teams-panel', function (Browser $panel): void {
                    $panel->assertSee('Seasons, competitions and divisions')
                        ->assertSee('This is where you create and edit all the data for the seasons, competitions and divisions.')
                        ->assertSeeLink('Manage seasons');
                })
                ->within('@clubs-teams-panel', function (Browser $panel): void {
                    $panel->assertSee('Clubs and teams')
                        ->assertSee('This is where you create and edit all the data for clubs and their teams.')
                        ->assertSeeLink('Manage clubs');
                })
                ->within('@venues-panel', function (Browser $panel): void {
                    $panel->assertSee('Venues')
                        ->assertSee('This is where you manage all the venues for all clubs and teams, regardless of season or competition.')
                        ->assertSeeLink('Manage venues');
                });
        });
    }
}
