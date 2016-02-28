<?php

namespace Authentication;

use App;

class LoginLogoutTest extends \TestCase
{
    private $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = $this->getFakeUser();
    }

    public function testLoginLinkExists()
    {
        $this->visit(route('home'))
            ->seeLink('Login', route('login'));
    }

    public function testCannotLoginWhenAlreadyLoggedIn()
    {
        $this->be($this->user);

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
        $this->visit(route('login'))
            ->type($this->user->email, 'email')
            ->type($this->user->clearPassword, 'password')
            ->press('Login')
            ->seePageIs(route('home'))
            ->seeInElement('.panel-heading', 'Dashboard')
            ->seeInElement('nav .navbar-right li.dropdown a', $this->user->name);
    }

    public function testWrongPassword()
    {
        $this->visit(route('login'))
            ->type($this->user->email, 'email')
            ->type($this->user->clearPassword . 'abc', 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs(route('login'));

    }

    public function testWrongEmail()
    {
        $this->visit(route('login'))
            ->type($this->user->email . '.com', 'email')
            ->type($this->user->clearPassword, 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs(route('login'));

    }

    public function testWrongEmailAndPassword()
    {
        $this->visit(route('login'))
            ->type($this->user->email . '.com', 'email')
            ->type($this->user->clearPassword . 'abc', 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs(route('login'));
    }

    public function testMissingEmail()
    {
        $this->visit(route('login'))
            ->type($this->user->clearPassword . 'abc', 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'The email field is required.')
            ->seePageIs(route('login'));
    }

    public function testMissingPassword()
    {
        $this->visit(route('login'))
            ->type($this->user->email . '.com', 'email')
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
        $this->be($this->user);

        $this->visit(route('home'))
            ->seeLink('Logout', route('logout'));
    }

    public function testSuccessfulLogout()
    {
        unset($this->user->clearPassword);
        $this->be($this->user);
        
        $this->visit(route('home'))
            ->click('Logout')
            ->seePageIs(route('home'));
    }
}
