<?php

namespace Authentication;

use LVA\User;
use Tests\TestCase;

class LoginLogoutTest extends TestCase
{
    public function testLoginLinkExists()
    {
        $this->visit(route('home'))
            ->seeLink('Login', route('login'));
    }

    public function testBreadcrumbs()
    {
        $this->breadcrumbsTests('login', 'Login');
    }

    public function testCannotLoginWhenAlreadyLoggedIn()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('login'))
            ->seePageIs(route('home'));
    }


    public function testLoginPageExists()
    {
        $this->visit(route('login'))
            ->seeInElement('.panel-heading', 'Login')
            ->seeInField('email', '')
            ->seeInField('password', null)
            ->seeInElement('button', 'Login');
    }

    public function testSuccessfulLogin()
    {
        $password = $this->faker->unique()->password;
        /** @var User $user */
        $user = factory(User::class)->create(['password' => bcrypt($password)]);

        $this->visit(route('login'))
            ->type($user->email, 'email')
            ->type($password, 'password')
            ->press('Login')
            ->seePageIs(route('home'))
            ->seeInElement('nav .navbar-right li.dropdown a', $user->name);
    }

    public function testWrongPassword()
    {
        $password = $this->faker->unique()->password;
        /** @var User $user */
        $user = factory(User::class)->create(['password' => bcrypt($password)]);

        $this->visit(route('login'))
            ->type($user->email, 'email')
            ->type($password . 'abc', 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs(route('login'));

    }

    public function testWrongEmail()
    {
        $password = $this->faker->unique()->password;
        /** @var User $user */
        $user = factory(User::class)->create(['password' => bcrypt($password)]);

        $this->visit(route('login'))
            ->type($user->email . '.com', 'email')
            ->type($password, 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs(route('login'));

    }

    public function testWrongEmailAndPassword()
    {
        $password = $this->faker->unique()->password;
        /** @var User $user */
        $user = factory(User::class)->create(['password' => bcrypt($password)]);

        $this->visit(route('login'))
            ->type($user->email . '.com', 'email')
            ->type($password . 'abc', 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs(route('login'));
    }

    public function testMissingEmail()
    {
        $password = $this->faker->unique()->password;
        /** @var User $user */
        $user = factory(User::class)->create(['password' => bcrypt($password)]);

        $this->visit(route('login'))
            ->type($password, 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'The email field is required.')
            ->seePageIs(route('login'));
    }

    public function testMissingPassword()
    {
        $password = $this->faker->unique()->password;
        /** @var User $user */
        $user = factory(User::class)->create(['password' => bcrypt($password)]);

        $this->visit(route('login'))
            ->type($user->email, 'email')
            ->press('Login')
            ->seeInElement('span.help-block', 'The password field is required.')
            ->seePageIs(route('login'));
    }

    public function testMissingEmailAndPassword()
    {
        $this->visit(route('login'))
            ->press('Login')
            ->seeInElement('span.help-block', 'The email field is required.')
            ->seeInElement('span.help-block', 'The password field is required.')
            ->seePageIs(route('login'));
    }

    public function testLogoutLinkExist()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $this->be($user);

        $this->visit(route('home'))
            ->seeLink('Logout', route('logout'));
    }

    public function testSuccessfulLogout()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $this->be($user);

        $this->visit(route('home'))
            ->click('Logout')
            ->seePageIs(route('home'));
    }
}
