<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use LVA\User;
use Tests\Browser\Pages\HomePage;
use Tests\DuskTestCase;

class HomeTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testHomePageAsGuest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomePage())
                ->assertSee('London Volleyball Association')
                ->assertMissing('@breadcrumbs')
                ->assertSeeLink('Login')
                ->assertDontSeeLink('Administrators')
                ->assertDontSeeLink('Register')
                ->assertDontSeeLink('Logout');
        });
    }

    public function testHomePageAsUser()
    {
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)
                ->visit(new HomePage())
                ->assertSee('London Volleyball Association')
                ->assertMissing('@breadcrumbs')
                ->assertDontSeeLink('Register')
                ->assertDontSeeLink('Login')
                ->assertSee($user->name)
                ->click($user->name)
                ->assertSeeLink('Logout')
                ->assertSeeLink('Administrators')
                ->click('Administrators')
                ->assertSeeLink('Data management');
        });
    }
}
