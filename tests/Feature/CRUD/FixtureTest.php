<?php

namespace Tests\Feature\CRUD;

use App\Helpers\RolesHelper;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Season;
use App\Models\User;
use Tests\TestCase;

class FixtureTest extends TestCase
{
    public function testAccessForGuests(): void
    {
        Fixture::factory()->create();

        $this->get('/fixtures')
             ->assertRedirect('/login');
    }

    public function testAccessForUserWithoutThePermission(): void
    {
        Fixture::factory()->create();

        $this->actingAs(User::factory()->create())
             ->get('/fixtures')
             ->assertForbidden();
    }

    public function testAccessForUnverifiedUsers(): void
    {
        Fixture::factory()->create();

        $this->actingAs(User::factory()->unverified()->create())
             ->get('/fixtures')
             ->assertRedirect('/email/verify');
    }

    public function testAccessForSiteAdministrators(): void
    {
        Fixture::factory()->create();

        $this->actingAs($this->siteAdmin)
             ->get('/fixtures')
             ->assertOk();
    }

    public function testAccessForSeasonAdministrators(): void
    {
        /** @var Season $season */
        $season = Season::factory()->create();

        $this->actingAs(User::factory()->create()->assignRole(RolesHelper::seasonAdmin($season)))
             ->get('/fixtures')
             ->assertOk();
    }

    public function testAccessForCompetitionAdministrators(): void
    {
        /** @var Competition $competition */
        $competition = Competition::factory()->create();

        $this->actingAs(User::factory()->create()->assignRole(RolesHelper::competitionAdmin($competition)))
             ->get('/fixtures')
             ->assertOk();
    }

    public function testAccessForDivisionAdministrators(): void
    {
        /** @var Division $division */
        $division = Division::factory()->create();

        $this->actingAs(User::factory()->create()->assignRole(RolesHelper::divisionAdmin($division)))
             ->get('/fixtures')
             ->assertOk();
    }
}
