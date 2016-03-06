<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 27/02/2016
 * Time: 14:17
 */

namespace Authentication;

class RegistrationTest extends \TestCase
{
    private $admin;

    protected function setUp()
    {
        parent::setUp();

        $this->admin = $this->getFakeUser();
    }

    public function testRegistrationLinkExists()
    {
        $this->visit(route('home'))
            ->seeLink('Register', route('register'));
    }

    public function testBreadcrumbs()
    {
        $this->visit(route('register'))
            ->seeInElement('ol.breadcrumb li.active', 'Register');
    }

    public function testRegistrationPageExists()
    {
        $this->visit(route('register'))
            ->seeInElement('.panel-heading', 'Register')
            ->seeInField('name', '')
            ->seeInField('email', '')
            ->seeInField('password', null)
            ->seeInField('password_confirmation', null)
            ->seeInElement('button', 'Register');
    }

    public function testCannotRegisterWhenAlreadyLoggedIn()
    {
        $this->be($this->admin);

        $this->visit(route('register'))
            ->seePageIs(route('home'));
    }

    public function testSuccessfulRegistration()
    {
        $this->visit(route('register'))
            ->type('Test user 1', 'name')
            ->type('test1@example.com', 'email')
            ->type('password1', 'password')
            ->type('password1', 'password_confirmation')
            ->press('Register')
            ->seePageIs(route('home'))
            ->seeInElement('nav .navbar-right li.dropdown a', 'Test user 1');
    }

    public function testMandatoryField()
    {
        $this->visit(route('register'))
            ->press('Register')
            ->seeInElement('span.help-block', 'The name field is required.')
            ->seeInElement('span.help-block', 'The email field is required.')
            ->seeInElement('span.help-block', 'The password field is required.');
    }

    public function testMissingPasswordConfirmation()
    {
        $this->visit(route('register'))
            ->type('Test user 1', 'name')
            ->type('test1@example.com', 'email')
            ->type('password1', 'password')
            ->press('Register')
            ->seeInElement('span.help-block', 'The password confirmation does not match.')
            ->seeInField('name', 'Test user 1')
            ->seeInField('email', 'test1@example.com')
            ->seeInField('password', null);
    }

    public function testWrongPasswordConfirmation()
    {
        $this->visit(route('register'))
            ->type('Test user 1', 'name')
            ->type('test1@example.com', 'email')
            ->type('password1', 'password')
            ->type('password2', 'password_confirmation')
            ->press('Register')
            ->seeInElement('span.help-block', 'The password confirmation does not match.')
            ->seeInField('name', 'Test user 1')
            ->seeInField('email', 'test1@example.com')
            ->seeInField('password', null)
            ->seeInField('password_confirmation', null);
    }

    public function testInvalidEmail()
    {
        $this->visit(route('register'))
            ->type('Test user 1', 'name')
            ->type('test1@example', 'email')
            ->press('Register')
            ->seeInElement('span.help-block', 'The email must be a valid email address.')
            ->seeInField('name', 'Test user 1')
            ->seeInField('email', 'test1@example');
    }

    public function testAlreadyUsedEmail()
    {
            // Register one user
        $this->visit(route('register'))
            ->type('Test user 1', 'name')
            ->type('test1@example.com', 'email')
            ->type('password1', 'password')
            ->type('password1', 'password_confirmation')
            ->press('Register')
            // Logout after registration
            ->visit(route('logout'))
            // Try to register another user with the same email address
            ->visit(route('register'))
            ->type('Test user 2', 'name')
            ->type('test1@example.com', 'email')
            ->press('Register')
            ->seeInElement('span.help-block', 'The email has already been taken.')
            ->seeInField('name', 'Test user 2')
            ->seeInField('email', 'test1@example.com');
    }
}
