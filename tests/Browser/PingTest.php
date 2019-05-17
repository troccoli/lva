<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use LVA\User;
use Tests\Browser\Pages\HomePage;
use Tests\DuskTestCase;

class PingTest extends DuskTestCase
{
    public function testPing()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('London Volleyball Association');
        });
    }
}
