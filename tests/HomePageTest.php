<?php


class HomePageTest extends TestCase
{
    public function testLandingPage()
    {
        $this->visit(route('home'))->assertResponseOk();
    }

    public function testBreadcrumbs()
    {
        $this->visit(route('home'))
            ->seeInElement('ol.breadcrumb li.active', 'Home');
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
