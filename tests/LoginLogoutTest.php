<?php


class LoginLogoutTest extends TestCase
{
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
            ->seeInField('password', null);
    }

    public function testSuccessfulLogin()
    {
        $this->visit('/login')
            ->type('giulio@troccoli.it', 'email')
            ->type('MySpecialPassword', 'password')
            ->press('Login')
            ->seePageIs('/')
            ->seeInElement('.panel-heading', 'Welcome');
    }

    public function testWrongPassword()
    {
        $this->visit('/login')
            ->type('giulio@troccoli.it', 'email')
            ->type('wrongpassword', 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs('/login');

    }

    public function testWrongEmail()
    {
        $this->visit('/login')
            ->type('giulio@troccoli.com', 'email')
            ->type('MySpecialPassword', 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs('/login');

    }

    public function testWrongEmailAndPassword()
    {
        $this->visit('/login')
            ->type('giulio@troccoli.com', 'email')
            ->type('wrongpassword', 'password')
            ->press('Login')
            ->seeInElement('span.help-block', 'These credentials do not match our records.')
            ->seePageIs('/login');
    }

    public function testLogoutLinkExist()
    {
        // @todo Mock logged-in user
        $this->visit('/')
            ->seeLink('Logout', '/logout');
    }

    public function testSuccessfulLogout()
    {
        // @todo Mock logged-in user
        $this->visit('/')
            ->click('Logout')
            ->seeInElement('message', 'Successfully logged out');
    }
}
