<?php

namespace Tests;

use LVA\User;

class HomePageOldStyleTest extends OldStyleTestCase
{
    public function testBreadcrumbs()
    {
        $page = $this->visit(route('home'));
        $this->assertEquals(0, $page->crawler->filter('#breadcrumbs')->count());
    }
}
