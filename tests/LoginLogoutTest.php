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
            ->seeHeader('header', 'Login')
            ->seeInField('email', '')
            ->seeInField('password', '');
    }

    public function testSuccessfulLogin()
    {
        $this->visit('/login')
            ->type('giulio@troccoli.it', 'email')
            ->type('MySpecialPassword', 'password')
            ->press('Login')
            ->seePageIs('/')
            ->seeInElement('message', 'Successfully logged in');
    }

    public function testWrongPassword()
    {
        $this->visit('/login')
            ->type('giulio@troccoli.it', 'email')
            ->type('wrongpassword', 'password')
            ->press('Login')
            ->seeInElement('message', 'We cannot verify your email and password.')
            ->seePageIs('/login');

    }

    public function testWrongEmail()
    {
        $this->visit('/login')
            ->type('giulio@troccoli.com', 'email')
            ->type('MySpecialPassword', 'password')
            ->seeInElement('messsage', 'We cannot verify your email and password.')
            ->seePageIs('/login');

    }

    public function testWrongEmailAndPassword()
    {
        $this->visit('/login')
            ->type('giulio@troccoli.com', 'email')
            ->type('wrongpassword', 'password')
            ->seeInElement('messsage', 'We cannot verify your email and password.')
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
