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
    public function testRegistrationLinkExists()
    {
        $this->visit('/')
            ->seeLink('Register', '/register');
    }

    public function testRegistrationPageExists()
    {
        $this->visit('/register')
            ->seeInElement('.panel-heading', 'Register')
            ->seeInField('name' , '')
            ->seeInField('email', '')
            ->seeInField('password', null)
            ->seeInField('password_confirmation', null)
            ->seeInElement('button', 'Register');
    }

    public function testSuccessfulRegistration()
    {
        $this->visit('/register')
            ->type('Test user 1', 'name')
            ->type('test1@example.com', 'email')
            ->type('password1', 'password')
            ->type('password1', 'password_confirmation')
            ->press('Register')
            ->seePageIs('/')
            ->seeInElement('.panel-heading', 'Welcome');
    }

    public function testMandatoryField()
    {
        $this->visit('/register')
            ->press('Register')
            ->seeInElement('span.help-block', 'The name field is required.')
            ->seeInElement('span.help-block', 'The email field is required.')
            ->seeInElement('span.help-block', 'The password field is required.');
    }

    public function testMissingPasswordConfirmation()
    {
        $this->visit('/register')
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
        $this->visit('/register')
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
        $this->visit('/register')
            ->type('Test user 1', 'name')
            ->type('test1@example', 'email')
            ->press('Register')
            ->seeInElement('span.help-block', 'The email must be a valid email address.')
            ->seeInField('name', 'Test user 1')
            ->seeInField('email', 'test1@example');
    }
}
