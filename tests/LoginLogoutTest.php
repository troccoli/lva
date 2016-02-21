<?php

class LoginLogoutTest extends TestCase
{
    private $user;

    protected function setUp()
    {
        parent::setUp();

        $password = str_random(10);
        $this->user = factory(App\User::class)->create(['password' => bcrypt($password)]);
        $this->user->clearPassword = $password;
    }

    private function createUser()
    {
        $password = str_random(10);
        $user = factory(App\User::class)->create(['password' => bcrypt($password)]);
        $user->clearPassword = $password;

        return $user;
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
            ->seeInField('password', null);
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
