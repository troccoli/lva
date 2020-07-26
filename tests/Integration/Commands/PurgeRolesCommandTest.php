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
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        /** @var User $user1 */
        $user1 = factory(User::class)->create()->assignRole(RolesHelper::seasonAdmin($season1));
        /** @var User $user2 */
        $user2 = factory(User::class)->create()->assignRole(RolesHelper::seasonAdmin($season2));
        /** @var User $user3 */
        $user3 = factory(User::class)->create()->assignRole(RolesHelper::seasonAdmin($season1), RolesHelper::seasonAdmin($season2));

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
        /** @var Season $season */
        $season = factory(Season::class)->create();

        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create(['season_id' => $season->getId()]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create(['season_id' => $season->getId()]);

        /** @var User $user1 */
        $user1 = factory(User::class)->create()->assignRole(RolesHelper::competitionAdmin($competition1));
        /** @var User $user2 */
        $user2 = factory(User::class)->create()->assignRole(RolesHelper::competitionAdmin($competition2));
        /** @var User $user3 */
        $user3 = factory(User::class)->create()->assignRole(RolesHelper::competitionAdmin($competition1), RolesHelper::competitionAdmin($competition2));

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
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();

        /** @var Division $division1 */
        $division1 = factory(Division::class)->create(['competition_id' => $competition->getId()]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create(['competition_id' => $competition->getId()]);

        /** @var User $user1 */
        $user1 = factory(User::class)->create()->assignRole(RolesHelper::divisionAdmin($division1));
        /** @var User $user2 */
        $user2 = factory(User::class)->create()->assignRole(RolesHelper::divisionAdmin($division2));
        /** @var User $user3 */
        $user3 = factory(User::class)->create()->assignRole(RolesHelper::divisionAdmin($division1), RolesHelper::divisionAdmin($division2));

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
        /** @var Club $club1 */
        $club1 = aClub()->build();
        /** @var Club $club2 */
        $club2 = aClub()->build();

        /** @var User $user1 */
        $user1 = factory(User::class)->create()->assignRole(RolesHelper::clubSecretary($club1));
        /** @var User $user2 */
        $user2 = factory(User::class)->create()->assignRole(RolesHelper::clubSecretary($club2));
        /** @var User $user3 */
        $user3 = factory(User::class)->create()->assignRole(
            RolesHelper::clubSecretary($club1),
            RolesHelper::clubSecretary($club2));

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
        /** @var Club $club */
        $club = aClub()->build();

        /** @var Team $team1 */
        $team1 = aTeam()->inClub($club)->build();
        /** @var Team $team2 */
        $team2 = aTeam()->inClub($club)->build();

        /** @var User $user1 */
        $user1 = factory(User::class)->create()->assignRole(RolesHelper::teamSecretary($team1));
        /** @var User $user2 */
        $user2 = factory(User::class)->create()->assignRole(RolesHelper::teamSecretary($team2));
        /** @var User $user3 */
        $user3 = factory(User::class)->create()->assignRole(
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
