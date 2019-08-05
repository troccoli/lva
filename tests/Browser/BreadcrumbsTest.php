<?php

namespace Tests\Browser;

use App\Models\Division;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BreadcrumbsTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testBreadcrumbsForPagesThatDoNotRequiredAuthentication(): void
    {
        $breadcrumbs = $this->guestPagesBreadcrumbs();

        $this->browse(function (Browser $browser) use ($breadcrumbs): void {
            foreach ($breadcrumbs as $url => $crumbs) {
                $browser->visit($url)
                    ->assertSeeIn('.breadcrumb', strtoupper(implode("\n", $crumbs)));
            }
        });
    }

    public function guestPagesBreadcrumbs(): array
    {
        return [
            '/'               => ['Home'],
            '/login'          => ['Home', 'Login'],
            '/register'       => ['Home', 'Register'],
            '/password/reset' => ['Home', 'Forgotten password'],
        ];
    }

    /**
     * @throws \Throwable
     */
    public function testBreadcrumbsForPagesThatRequiredAuthentication(): void
    {
        $breadcrumbs = $this->authPagesBreadcrumbs();

        $this->browse(function (Browser $browser) use ($breadcrumbs): void {
            $browser->loginAs(factory(User::class)->create());
            foreach ($breadcrumbs as $url => $crumbs) {
                $browser->visit($url)
                    ->assertSeeIn('.breadcrumb', strtoupper(implode("\n", $crumbs)));
            }
        });
    }

    public function authPagesBreadcrumbs(): array
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();
        $divisionId = $division->getId();
        $competition = $division->getCompetition();
        $competitionId = $competition->getId();
        $season = $competition->getSeason();
        $seasonId = $season->getId();

        /** @var Team $team */
        $team = aTeam()->build();
        $teamId = $team->getId();
        $club = $team->getClub();
        $clubId = $club->getId();

        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();
        $venueId = $venue->getId();

        return [
            "/dashboard"                                              => ['Home', 'Dashboard'],
            "/seasons"                                                => ['Home', 'Dashboard', 'Seasons'],
            "/seasons/create"                                         => ['Home', 'Dashboard', 'Seasons', 'New season'],
            "/seasons/$seasonId/edit"                                 => [
                'Home',
                'Dashboard',
                'Seasons',
                'Edit season',
            ],
            "/seasons/$seasonId/competitions"                         => [
                'Home',
                'Dashboard',
                'Seasons',
                'Competitions',
            ],
            "/seasons/$seasonId/competitions/create"                  => [
                'Home',
                'Dashboard',
                'Seasons',
                'Competitions',
                'New competition',
            ],
            "/seasons/$seasonId/competitions/$competitionId/edit"     => [
                'Home',
                'Dashboard',
                'Seasons',
                'Competitions',
                'Edit competition',
            ],
            "/competitions/$competitionId/divisions"                  => [
                'Home',
                'Dashboard',
                'Seasons',
                'Competitions',
                'Divisions',
            ],
            "/competitions/$competitionId/divisions/create"           => [
                'Home',
                'Dashboard',
                'Seasons',
                'Competitions',
                'Divisions',
                'New division',
            ],
            "/competitions/$competitionId/divisions/$divisionId/edit" => [
                'Home',
                'Dashboard',
                'Seasons',
                'Competitions',
                'Divisions',
                'Edit division',
            ],

            "/clubs"                            => ['Home', 'Dashboard', 'Clubs'],
            "/clubs/create"                     => ['Home', 'Dashboard', 'Clubs', 'New club'],
            "/clubs/$clubId/edit"               => ['Home', 'Dashboard', 'Clubs', 'Edit club'],
            "/clubs/$clubId/teams"              => ['Home', 'Dashboard', 'Clubs', 'Teams'],
            "/clubs/$clubId/teams/create"       => ['Home', 'Dashboard', 'Clubs', 'Teams', 'New team'],
            "/clubs/$clubId/teams/$teamId/edit" => ['Home', 'Dashboard', 'Clubs', 'Teams', 'Edit team'],

            "/venues"               => ['Home', 'Dashboard', 'Venues'],
            "/venues/$venueId"      => ['Home', 'Dashboard', 'Venues', $venue->getName()],
            "/venues/create"        => ['Home', 'Dashboard', 'Venues', 'New venue'],
            "/venues/$venueId/edit" => ['Home', 'Dashboard', 'Venues', 'Edit venue'],
        ];
    }
}
