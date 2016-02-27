<?php


class HomePageTest extends TestCase
{
    public function testLandingPage()
    {
        $this->visit(route('home'))->assertResponseOk();
    }
}
