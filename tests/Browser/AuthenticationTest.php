<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use LVA\User;
use Tests\Browser\Pages\HomePage;
use Tests\Browser\Pages\LoginPage;
use Tests\DuskTestCase;

class AuthenticationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testLogin()
    {

    }
    /**
     * @test
     */
    public function users_can_login()
    {
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new LoginPage())
                ->assertSee('E-Mail Address')
                ->assertSee('Password')
                ->assertSee('Remember Me')
                ->assertSeeIn('@submit', 'Login')
                ->assertSeeLink('Forgot Your Password?')
                ->type('email', $user->email)
                ->type('password', 'secret')
                ->click('@submit')
                ->on(new HomePage());
        });
    }

    /**
     * @test
     */
    public function users_cannot_login_if_already_logged_in()
    {
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)
                ->visitRoute('login')
                ->assertPathIs('/');
        });
    }

    /**
     * @test
     */
    public function user_get_error_if_uses_wrong_password()
    {
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $page = new LoginPage();
            $browser->visit($page)
                ->type('email', $user->email)
                ->type('password', 'secret2')
                ->click('@submit')
                ->assertSee('These credentials do not match our records.')
                ->assertPathIs($page->url());
        });
    }
}
