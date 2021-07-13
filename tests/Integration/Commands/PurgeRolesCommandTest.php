<?php

namespace Tests\Integration\Commands;

use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use Tests\TestCase;

class PurgeRolesCommandTest extends TestCase
{
    public function testItPurgesSeasonAdministratorRoles(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        $user1 = User::factory()->create()->assignRole(RolesHelper::seasonAdmin($season1));
        $user2 = User::factory()->create()->assignRole(RolesHelper::seasonAdmin($season2));
        $user3 = User::factory()->create()->assignRole(
            RolesHelper::seasonAdmin($season1),
            RolesHelper::seasonAdmin($season2)
        );

        $season2->delete();

        $this->artisan('role:purge');

        $this->assertDatabaseHas('roles', ['name' => RolesHelper::seasonAdmin($season1)]);
        $this->assertDatabaseMissing('roles', ['name' => RolesHelper::seasonAdmin($season2)]);

        $user1->refresh();
        $user2->refresh();
        $user3->refresh();

        $this->assertTrue($user1->hasRole(RolesHelper::seasonAdmin($season1)));
        $this->assertFalse($user2->hasRole(RolesHelper::seasonAdmin($season2)));
        $this->assertTrue($user3->hasRole(RolesHelper::seasonAdmin($season1)));
        $this->assertFalse($user3->hasRole(RolesHelper::seasonAdmin($season2)));
    }

    public function testItPurgesCompetitionAdministratorRoles(): void
    {
        $season = Season::factory()->create();

        $competition1 = Competition::factory()->create(['season_id' => $season->getId()]);
        $competition2 = Competition::factory()->create(['season_id' => $season->getId()]);

        $user1 = User::factory()->create()->assignRole(RolesHelper::competitionAdmin($competition1));
        $user2 = User::factory()->create()->assignRole(RolesHelper::competitionAdmin($competition2));
        $user3 = User::factory()->create()->assignRole(
            RolesHelper::competitionAdmin($competition1),
            RolesHelper::competitionAdmin($competition2)
        );

        $competition2->delete();

        $this->artisan('role:purge');

        $this->assertDatabaseHas('roles', ['name' => RolesHelper::competitionAdmin($competition1)]);
        $this->assertDatabaseMissing('roles', ['name' => RolesHelper::competitionAdmin($competition2)]);

        $user1->refresh();
        $user2->refresh();
        $user3->refresh();

        $this->assertTrue($user1->hasRole(RolesHelper::competitionAdmin($competition1)));
        $this->assertFalse($user2->hasRole(RolesHelper::competitionAdmin($competition2)));
        $this->assertTrue($user3->hasRole(RolesHelper::competitionAdmin($competition1)));
        $this->assertFalse($user3->hasRole(RolesHelper::competitionAdmin($competition2)));
    }

    public function testItPurgesDivisionAdministratorRoles(): void
    {
        $competition = Competition::factory()->create();

        $division1 = Division::factory()->create(['competition_id' => $competition->getId()]);
        $division2 = Division::factory()->create(['competition_id' => $competition->getId()]);

        $user1 = User::factory()->create()->assignRole(RolesHelper::divisionAdmin($division1));
        $user2 = User::factory()->create()->assignRole(RolesHelper::divisionAdmin($division2));
        $user3 = User::factory()->create()->assignRole(
            RolesHelper::divisionAdmin($division1),
            RolesHelper::divisionAdmin($division2)
        );

        $division2->delete();

        $this->artisan('role:purge');

        $this->assertDatabaseHas('roles', ['name' => RolesHelper::divisionAdmin($division1)]);
        $this->assertDatabaseMissing('roles', ['name' => RolesHelper::divisionAdmin($division2)]);

        $user1->refresh();
        $user2->refresh();
        $user3->refresh();

        $this->assertTrue($user1->hasRole(RolesHelper::divisionAdmin($division1)));
        $this->assertFalse($user2->hasRole(RolesHelper::divisionAdmin($division2)));
        $this->assertTrue($user3->hasRole(RolesHelper::divisionAdmin($division1)));
        $this->assertFalse($user3->hasRole(RolesHelper::divisionAdmin($division2)));
    }

    public function testItPurgesClubSecretariesRoles(): void
    {
        $club1 = Club::factory()->create();
        $club2 = Club::factory()->create();

        $user1 = User::factory()->create()->assignRole(RolesHelper::clubSecretary($club1));
        $user2 = User::factory()->create()->assignRole(RolesHelper::clubSecretary($club2));
        $user3 = User::factory()->create()->assignRole(
            RolesHelper::clubSecretary($club1),
            RolesHelper::clubSecretary($club2)
        );

        $club2->delete();

        $this->artisan('role:purge');

        $this->assertDatabaseHas('roles', ['name' => RolesHelper::clubSecretary($club1)]);
        $this->assertDatabaseMissing('roles', ['name' => RolesHelper::clubSecretary($club2)]);

        $user1->refresh();
        $user2->refresh();
        $user3->refresh();

        $this->assertTrue($user1->hasRole(RolesHelper::clubSecretary($club1)));
        $this->assertFalse($user2->hasRole(RolesHelper::clubSecretary($club2)));
        $this->assertTrue($user3->hasRole(RolesHelper::clubSecretary($club1)));
        $this->assertFalse($user3->hasRole(RolesHelper::clubSecretary($club2)));
    }

    public function testItPurgesTeamSecretaryRoles(): void
    {
        $club = Club::factory()->create();

        $team1 = Team::factory()->for($club)->create();
        $team2 = Team::factory()->for($club)->create();

        $user1 = User::factory()->create()->assignRole(RolesHelper::teamSecretary($team1));
        $user2 = User::factory()->create()->assignRole(RolesHelper::teamSecretary($team2));
        $user3 = User::factory()->create()->assignRole(
            RolesHelper::teamSecretary($team1),
            RolesHelper::teamSecretary($team2)
        );

        $team2->delete();

        $this->artisan('role:purge');

        $this->assertDatabaseHas('roles', ['name' => RolesHelper::teamSecretary($team1)]);
        $this->assertDatabaseMissing('roles', ['name' => RolesHelper::teamSecretary($team2)]);

        $user1->refresh();
        $user2->refresh();
        $user3->refresh();

        $this->assertTrue($user1->hasRole(RolesHelper::teamSecretary($team1)));
        $this->assertFalse($user2->hasRole(RolesHelper::teamSecretary($team2)));
        $this->assertTrue($user3->hasRole(RolesHelper::teamSecretary($team1)));
        $this->assertFalse($user3->hasRole(RolesHelper::teamSecretary($team2)));
    }
}
