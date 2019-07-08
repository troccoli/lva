<?php

namespace Tests\Browser;

use App\Models\Competition;
use App\Models\Season;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BreadcrumbsTest extends DuskTestCase
{
    public function testBreadcrumbsForPagesThatDoNotRequiredAuthentication(): void
    {
        $breadcrumbs = $this->guestPagesBreadcrumbs();

        $this->browse(function (Browser $browser) use ($breadcrumbs) {
            foreach ($breadcrumbs as $url => $crumbs) {
                $browser->visit($url)
                    ->assertSeeIn('.breadcrumb', strtoupper(implode("\n", $crumbs)));
            }
        });
    }

    public function guestPagesBreadcrumbs(): array
    {
        return [
            '/' => ['Home'],
            '/login' => ['Home', 'Login'],
            '/register' => ['Home', 'Register'],
            '/password/reset' => ['Home', 'Forgotten password'],
        ];
    }

    public function testBreadcrumbsForPagesThatRequiredAuthentication(): void
    {
        $breadcrumbs = $this->authPagesBreadcrumbs();

        $this->browse(function (Browser $browser) use ($breadcrumbs) {
            $browser->loginAs(factory(User::class)->create());
            foreach ($breadcrumbs as $url => $crumbs) {
                $browser->visit($url)
                    ->assertSeeIn('.breadcrumb', strtoupper(implode("\n", $crumbs)));
            }
        });
    }

    public function authPagesBreadcrumbs(): array
    {
        $season = factory(Season::class)->create();
        $competition = factory(Competition::class)->create();
        return [
            '/dashboard' => ['Home', 'Dashboard'],
            '/seasons' => ['Home', 'Seasons'],
            '/seasons/create' => ['Home', 'Seasons', 'New season'],
            '/seasons/' . $season->id . '/edit' => ['Home', 'Seasons', 'Edit season'],
            '/competitions' => ['Home', 'Seasons', 'Competitions'],
//            '/competitions/create' => ['Home', 'Seasons', 'Competitions', 'New competition'],
//            '/competitions/' . $competition->id . '/edit' => ['Home', 'Seasons', 'Competitions', 'Edit competition'],
        ];
    }
}
