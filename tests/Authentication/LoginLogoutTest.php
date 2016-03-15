<?php

namespace Authentication;

use App;
use Tests\TestCase;

class LoginLogoutTest extends TestCase
{
    private $admin;

    protected function setUp()
    {
        parent::setUp();

        $this->admin = $this->getFakeUser();
    }

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
        $this->be($this->admin);

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
            ->type($this->admin->email, 'email')
            ->type($this->admin->clearPassword, 'password')
            ->press('Login')
            ->seePageIs(route('home'))
            ->seeInElement('nav .navbar-right li.dropdown a', $this->admin->name);
    }

    public function testWrongPassword()
    {
        $this->visit(route('login'))
            ->type($this->admin->email, 'email')
            ->type($this->admin->clearPassword . 'abc', 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs(route('login'));

    }

    public function testWrongEmail()
    {
        $this->visit(route('login'))
            ->type($this->admin->email . '.com', 'email')
            ->type($this->admin->clearPassword, 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs(route('login'));

    }

    public function testWrongEmailAndPassword()
    {
        $this->visit(route('login'))
            ->type($this->admin->email . '.com', 'email')
            ->type($this->admin->clearPassword . 'abc', 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs(route('login'));
    }

    public function testMissingEmail()
    {
        $this->visit(route('login'))
            ->type($this->admin->clearPassword . 'abc', 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'The email field is required.')
            ->seePageIs(route('login'));
    }

    public function testMissingPassword()
    {
        $this->visit(route('login'))
            ->type($this->admin->email . '.com', 'email')
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
        $this->be($this->admin);

        $this->visit(route('home'))
            ->seeLink('Logout', route('logout'));
    }

    public function testSuccessfulLogout()
    {
        unset($this->admin->clearPassword);
        $this->be($this->admin);
        
        $this->visit(route('home'))
            ->click('Logout')
            ->seePageIs(route('home'));
    }
}
