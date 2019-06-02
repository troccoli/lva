<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BreadcrumbsTest extends DuskTestCase
{
    /**
     * @dataProvider guestPagesBreadcrumbs
     */
    public function testBreadcrumbsForPagesThatDoNotRequiredAuthentication(string $url, array $crumbs): void
    {
        $this->browse(function (Browser $browser) use ($url, $crumbs) {
            $browser->visit($url)
                ->assertSeeIn('.breadcrumb', implode("\n", $crumbs));
        });
    }

    public function guestPagesBreadcrumbs(): array
    {
        return [
            'Homepage'           => ['/', ['Home']],
            'Login'              => ['/login', ['Home', 'Login']],
            'Register'           => ['/register', ['Home', 'Register']],
            'Forgotten password' => ['/password/reset', ['Home', 'Forgotten password']],
        ];
    }

    /**
     * @dataProvider authPagesBreadcrumbs
     */
    public function testBreadcrumbsForPagesThatRequiredAuthentication(string $url, array $crumbs): void
    {
        $this->browse(function (Browser $browser) use ($url, $crumbs) {
            $browser->loginAs(factory(User::class)->create())
                ->visit($url)
                ->assertSeeIn('.breadcrumb', implode("\n", $crumbs));
        });
    }

    public function authPagesBreadcrumbs(): array
    {
        return [
            'Dashboard' => ['/dashboard', ['Home', 'Dashboard']],
        ];
    }
}
