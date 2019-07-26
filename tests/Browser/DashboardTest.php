<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class DashboardTest extends DuskTestCase
{
    public function testForGuests(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/dashboard')
                ->assertPathIs('/login');
        });
    }

    public function testForUnverifiedUsers(): void
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->state('unverified')->create();
            $browser->loginAs($user)
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
        $this->browse(function (Browser $browser):void {
            $user = factory(User::class)->create();
            $browser->loginAs($user)
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
                });
        });
    }
}
