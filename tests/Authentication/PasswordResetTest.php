<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 27/02/2016
 * Time: 15:20
 */

namespace Authentication;

class PasswordResetTest extends \TestCase
{
    private $pageUrl = '/password/reset';
    
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
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
    
    public function testMissingEmail()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
    
    public function testInvalidEmail()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
    
    public function testNonExistingEmail()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
