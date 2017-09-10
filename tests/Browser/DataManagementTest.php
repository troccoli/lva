<?php

namespace Tests\Browser;

use LVA\User;
use Tests\Browser\Pages\DataManagementPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DataManagementTest extends DuskTestCase
{
    public function testSeasonsTableButton()
    {
        $this->browse(function (Browser $browser) {
            /** @var User $user */
            $user = factory(User::class)->create();

            $browser->visit(new DataManagementPage())
                ->loginAs($user)
                ->assertSeeLink('Season')
                ->clickLink('Season')
                ->assertRouteIs('seasons.index');
        });
    }
}
