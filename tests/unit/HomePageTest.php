<?php

namespace Tests;

class HomePageTest extends TestCase
{
    public function testLandingPage()
    {
        $this->visit(route('home'))->assertResponseOk();
    }

    public function testBreadcrumbs()
    {
        $page = $this->visit(route('home'));
        $this->assertEquals(0, $page->crawler->filter('#breadcrumbs')->count());
    }
    
    public function testLoginAndRegisterLinksExist()
    {
        $this->visit(route('home'))
            ->seeLink('Login', route('login'))
            ->seeLink('Register', route('register'))
            ->dontSeeLink('Logout');
    }

    public function testPanelsForNonAdminUser()
    {
        $this->visit(route('home'))
            //->seeLink('Available matches', route('availableMatches'))
            ->seeLink('Available matches')
            //->dontSeeLink('Data management', route('admin::dataManagement'));
            ->dontSeeLink('Data management');
    }

    public function testPanelForAdmin()
    {
        $this->be($this->getFakeUser());

        $this->visit(route('home'))
            //->seeLink('Available matches', route('availableMatches'))
            ->seeLink('Available matches')
            //->seeLink('Data management', route('admin::dataManagement'));
            ->seeLink('Data management');
    }
}
