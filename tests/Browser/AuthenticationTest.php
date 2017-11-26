<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use LVA\User;
use Tests\Browser\Pages\ForgotPasswordPage;
use Tests\Browser\Pages\HomePage;
use Tests\Browser\Pages\LoginPage;
use Tests\DuskTestCase;

class AuthenticationTest extends DuskTestCase
{
    public function testPasswordResetLinkInLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new LoginPage())
                ->assertSeeLink('Forgot Your Password?')
                ->clickLink('Forgot Your Password?')
                ->assertRouteIs('password.request');
        });
    }

    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $page = new LoginPage();

            $browser->visit($page)
                // Missing email and password
                ->press('Login')
                ->assertPathIs($page->url())
                ->assertSeeIn('@email-error', 'The email field is required.')
                ->assertSeeIn('@password-error', 'The password field is required.')
                // Missing password
                ->type('email', $user->email)
                ->press('Login')
                ->assertPathIs($page->url())
                ->assertSeeIn('@password-error', 'The password field is required.')
                // Missing email
                ->clear('email')
                ->type('password', 'password')
                ->press('Login')
                ->assertPathIs($page->url())
                ->assertSeeIn('@email-error', 'The email field is required.')
                // Wrong password
                ->clear('email')->type('email', $user->email)
                ->clear('password')->type('password', 'password')
                ->press('Login')
                ->assertPathIs($page->url())
                ->assertSeeIn('@email-error', 'These credentials do not match our records.')
                // Wrong email and password
                ->clear('email')->type('email', $user->email.'.com')
                ->clear('password')->type('password', 'password')
                ->press('Login')
                ->assertPathIs($page->url())
                ->assertSeeIn('@email-error', 'These credentials do not match our records.')
                // Wrong email
                ->clear('email')->type('email', $user->email.'.com')
                ->clear('password')->type('password', 'secret')
                ->press('Login')
                ->assertPathIs($page->url())
                ->assertSeeIn('@email-error', 'These credentials do not match our records.')
                // Successful login
                ->clear('email')->type('email', $user->email)
                ->clear('password')->type('password', 'secret')
                ->press('Login')
                ->assertRouteIs('home')
                ->assertAuthenticatedAs($user);
            // Cannot login twice
            $browser->loginAs($user)
                ->visitRoute('login')
                ->assertRouteIs('home');
        });
    }

    public function testLogout()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $browser->loginAs($user)
                ->visit(new HomePage())
                ->clickLink($user->name)
                ->clickLink('Logout')
                ->assertGuest();
        });
    }

    public function testResettingPassword()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $page = new ForgotPasswordPage();
            $browser->visit($page)
                // Missing email
                ->press('Send Password Reset Link')
                ->assertPathIs($page->url())
                ->assertSeeIn('@email-error', 'The email field is required.')
                ->assertMissing('.alert-success')
                ->assertDontSee('We have e-mailed your password reset link!')
                // Not an email
                ->type('email', 'test1@example')
                ->press('Send Password Reset Link')
                ->assertPathIs($page->url())
                ->assertSeeIn('@email-error', 'The email must be a valid email address.')
                ->assertMissing('.alert-success')
                ->assertDontSee('We have e-mailed your password reset link!')
                // Not recognized email
                ->clear('email')->type('email', $user->email.'.com')
                ->press('Send Password Reset Link')
                ->assertPathIs($page->url())
                ->assertSeeIn('@email-error', 'We can\'t find a user with that e-mail address.')
                ->assertMissing('.alert-success')
                ->assertDontSee('We have e-mailed your password reset link!')
                // Valid email
                ->clear('email')->type('email', $user->email)
                ->press('Send Password Reset Link')
                ->assertPathIs($page->url())
                ->assertSeeIn('.alert.alert-success', 'We have e-mailed your password reset link!');
        });
    }
}
