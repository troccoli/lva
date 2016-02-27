<?php

namespace Authentication;

use App;

class LoginLogoutTest extends \TestCase
{
    private $user;

    protected function setUp()
    {
        parent::setUp();

        $password = str_random(10);
        $this->user = factory(App\User::class)->create(['password' => bcrypt($password)]);
        $this->user->clearPassword = $password;
    }

    public function testLoginLinkExists()
    {
        $this->visit('/')
            ->seeLink('Login', '/login');
    }

    public function testLoginPageExists()
    {
        $this->visit('/login')
            ->seeInElement('.panel-heading', 'Login')
            ->seeInField('email', '')
            ->seeInField('password', null)
            ->seeInElement('button', 'Login');
    }

    public function testSuccessfulLogin()
    {
        $this->visit('/login')
            ->type($this->user->email, 'email')
            ->type($this->user->clearPassword, 'password')
            ->press('Login')
            ->seePageIs('/')
            ->seeInElement('.panel-heading', 'Welcome');
    }

    public function testWrongPassword()
    {
        $this->visit('/login')
            ->type($this->user->email, 'email')
            ->type($this->user->clearPassword . 'abc', 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs('/login');

    }

    public function testWrongEmail()
    {
        $this->visit('/login')
            ->type($this->user->email . '.com', 'email')
            ->type($this->user->clearPassword, 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs('/login');

    }

    public function testWrongEmailAndPassword()
    {
        $this->visit('/login')
            ->type($this->user->email . '.com', 'email')
            ->type($this->user->clearPassword . 'abc', 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs('/login');
    }

    public function testMissingEmail()
    {
        $this->visit('/login')
            ->type($this->user->clearPassword . 'abc', 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'The email field is required.')
            ->seePageIs('/login');
    }

    public function testMissingPassword()
    {
        $this->visit('/login')
            ->type($this->user->email . '.com', 'email')
            ->press('Login')
            ->seeInElement('span.help-block', 'The password field is required.')
            ->seePageIs('/login');
    }

    public function testMissingEmailAndPassword()
    {
        $this->visit('/login')
            ->press('Login')
            ->seeInElement('span.help-block', 'The email field is required.')
            ->seeInElement('span.help-block', 'The password field is required.')
            ->seePageIs('/login');
    }

    public function testLogoutLinkExist()
    {
        $this->be($this->user);

        $this->visit('/')
            ->seeLink('Logout', '/logout');
    }

    public function testSuccessfulLogout()
    {
        unset($this->user->clearPassword);
        $this->be($this->user);
        
        $this->visit('/')
            ->click('Logout')
            ->seePageIs('/');
    }
}
