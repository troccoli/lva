<?php


class HomePageTest extends TestCase
{
    public function testLandingPage()
    {
        $this->visit(route('home'))->assertResponseOk();
    }
    
    public function testLoginAndRegisterLinksExist()
    {
        $this->visit(route('home'))
            ->seeLink('Login', route('login'))
            ->seeLink('Register', route('register'))
            ->dontSeeLink('Logout');
    }
}
