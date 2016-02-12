<?php


class HomePageTest extends TestCase
{
    public function testLandingPage()
    {
        $this->visit('/')
            ->see('Laravel 5');
    }
}
