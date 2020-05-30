<?php

namespace Tests\Feature\CRUD;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\User;
use Tests\TestCase;

class FixtureTest extends TestCase
{
    public function testAccessForGuests(): void
    {
        $fixture = aFixture()->build();

        $this->get('/fixtures')
            ->assertRedirect('/login');
    }

    public function testAccessForUserWithoutThePermission(): void
    {
        $fixture = aFixture()->build();

        $this->actingAs(factory(User::class)->create());

        $this->get('/fixtures')
            ->assertForbidden();
    }

    public function testAccessForUnverifiedUsers(): void
    {
        $fixture = aFixture()->build();

        $this->actingAs(factory(User::class)->state('unverified')->create());

        $this->get('/fixtures')
            ->assertRedirect('/email/verify');
    }

    public function testAccessForSiteAdministrators(): void
    {
        $fixture = aFixture()->build();

        $this->actingAs($this->siteAdmin);

        $this->get('/fixtures')
            ->assertOk();
    }

    public function testAccessForSeasonAdministrators(): void
    {
        /** @var Season $season */
        $season = factory(Season::class)->create();

        $this
            ->actingAs(factory(User::class)->create()->assignRole($season->getAdminRole()))
            ->get('/fixtures')
            ->assertOk();
    }

    public function testAccessForCompetitionAdministrators(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();

        $this
            ->actingAs(factory(User::class)->create()->assignRole($competition->getAdminRole()))
            ->get('/fixtures')
            ->assertOk();
    }

    public function testAccessForDivisionAdministrators(): void
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();

        $this
            ->actingAs(factory(User::class)->create()->assignRole($division->getAdminRole()))
            ->get('/fixtures')
            ->assertOk();
    }
}
