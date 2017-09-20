<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class FixtureResourceTest extends DuskTestCase
{
    const BASE_ROUTE = 'fixtures';

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testRedirectIfNotAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route(self::BASE_ROUTE . '.index'))
                ->assertRouteIs('login');

            $browser->visit(route(self::BASE_ROUTE . '.create'))
                ->assertRouteIs('login');

            $browser->visit(route(self::BASE_ROUTE . '.show', [1]))
                ->assertRouteIs('login');

            $browser->visit(route(self::BASE_ROUTE . '.edit', [1]))
                ->assertRouteIs('login');

        });
    }
}
