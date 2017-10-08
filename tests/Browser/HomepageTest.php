<?php

namespace Tests\Browser;

use LVA\User;
use Tests\Browser\Pages\HomePage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class HomepageTest extends DuskTestCase
{
    public function testLoginLinkExist()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomePage())
                ->assertSeeLink('Login')
                ->assertSeeLink('Referees')
                ->assertDontSeeLink('Logout')
                ->assertDontSeeLink('Register')
                ->assertDontSeeLink('Administrators')
                ->assertDontSeeLink('Available matches')
                ->assertDontSeeLink('Data management')
                ->clickLink('Referees')
                ->assertSeeLink('Available matches');

            /** @var User $user */
            $user = factory(User::class)->create();
            $browser->loginAs($user)
                ->visit(new HomePage())
                ->assertSeeLink($user->name)
                ->assertSeeLink('Referees')
                ->assertSeeLink('Administrators')
                ->assertDontSeeLink('Login')
                ->assertDontSeeLink('Register')
                ->assertDontSeeLink('Available matches')
                ->assertDontSeeLink('Data management')
                ->clickLink($user->name)
                ->assertSeeLink('Logout')
                ->clickLink('Referees')
                ->assertSeeLink('Available matches')
                ->clickLink('Administrators')
                ->assertSeeLink('Data management');
        });
    }
}
