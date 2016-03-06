<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 27/02/2016
 * Time: 15:20
 */

namespace Authentication;

use Illuminate\Auth\Passwords\PasswordResetServiceProvider;

class PasswordResetTest extends \TestCase
{
    private $admin;

    protected function setUp()
    {
        parent::setUp();

        $this->admin = $this->getFakeUser();
    }

    public function testBreadcrumbs()
    {
        $this->visit(route('passwordReset'))
            ->seeInElement('ol.breadcrumb li.active', 'Reset Password');
    }

    public function testPasswordResetLinkExist()
    {
        $this->visit(route('login'))
            ->seeLink('Forgot Your Password?', route('passwordReset'));
    }

    public function testPasswordResetPageExists()
    {
        $this->visit(route('passwordReset'))
            ->seeInField('email', '')
            ->seeInElement('button', 'Send Password Reset Link');
    }

    public function testSuccessfulPasswordReset()
    {
        $this->visit(route('passwordReset'))
            ->type($this->admin->email, 'email')
            ->press('Send Password Reset Link')
            ->seeInElement('.alert', 'We have e-mailed your password reset link!')
            ->seeInField('email', '');
    }

    public function testMissingEmail()
    {
        $this->visit(route('passwordReset'))
            ->press('Send Password Reset Link')
            ->seeInElement('span.help-block', 'The email field is required.');
    }

    public function testInvalidEmail()
    {
        $this->visit(route('passwordReset'))
            ->type('test1@example', 'email')
            ->press('Send Password Reset Link')
            ->seeInElement('span.help-block', 'The email must be a valid email address.')
            ->seeInField('email', 'test1@example');
    }

    public function testNonExistingEmail()
    {
        $this->visit(route('passwordReset'))
            ->type($this->admin->email . '.com', 'email')
            ->press('Send Password Reset Link')
            ->seeInElement('span.help-block', 'We can\'t find a user with that e-mail address.')
            ->seeInField('email', '');
    }
}
