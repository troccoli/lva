<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 27/02/2016
 * Time: 15:20
 */

namespace Authentication;

use LVA\User;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    public function testBreadcrumbs()
    {
        $this->breadcrumbsTests('password.request', 'Reset Password');
    }

    public function testPasswordResetLinkExist()
    {
        $this->visit(route('login'))
            ->seeLink('Forgot Your Password?', route('password.request'));
    }

    public function testPasswordResetPageExists()
    {
        $this->visit(route('password.request'))
            ->seeInField('email', '')
            ->seeInElement('button', 'Send Password Reset Link');
    }

    public function testSuccessfulPasswordReset()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $this->visit(route('password.request'))
            ->type($user->email, 'email')
            ->press('Send Password Reset Link')
            ->seeInElement('.alert', 'We have e-mailed your password reset link!')
            ->seeInField('email', '');
    }

    public function testMissingEmail()
    {
        $this->visit(route('password.request'))
            ->press('Send Password Reset Link')
            ->seeInElement('span.help-block', 'The email field is required.');
    }

    public function testInvalidEmail()
    {
        $this->visit(route('password.request'))
            ->type('test1@example', 'email')
            ->press('Send Password Reset Link')
            ->seeInElement('span.help-block', 'The email must be a valid email address.')
            ->seeInField('email', 'test1@example');
    }

    public function testNonExistingEmail()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $this->visit(route('password.request'))
            ->type($user->email . '.com', 'email')
            ->press('Send Password Reset Link')
            ->seeInElement('span.help-block', 'We can\'t find a user with that e-mail address.')
            ->seeInField('email', '');
    }
}
