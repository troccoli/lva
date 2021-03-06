<?php

namespace Tests\Integration\Helpers;

use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesHelperTest extends TestCase
{
    public function testItReturnsTheSeasonAdministratorRoleName(): void
    {
        /** @var Season $season */
        $season = Season::factory()->create();
        $seasonId = $season->getId();

        $this->assertSame("Season $seasonId Administrator", RolesHelper::seasonAdmin($season));
    }

    public function testItCanCheckIfTheRoleIsSeasonAdministrator(): void
    {
        $seasonAdmin = aRole()
            ->named('Season 1 Administrator')
            ->build();
        $siteAdmin = aRole()
            ->named('Useless Role')
            ->build();

        $this->assertTrue(RolesHelper::isSeasonAdmin($seasonAdmin));
        $this->assertFalse(RolesHelper::isSeasonAdmin($siteAdmin));
    }

    public function testItGetsTheSeasonFromTheSeasonAdministratorRole(): void
    {
        /** @var Season $season */
        $season = Season::factory()->create();
        $seasonId = $season->getId();
        /** @var Role $role */
        $role = Role::findByName("Season $seasonId Administrator");

        $this->assertTrue(RolesHelper::findSeason($role)->is($season));
    }

    public function testItDoesNotGetTheSeasonIfTheSeasonDoesNotExist(): void
    {
        $role = aRole()
            ->named('Season 1 Administrator')
            ->build();

        $this->assertNull(RolesHelper::findSeason($role));
    }

    public function testItDoesNotGetTheSeasonIfTheRoleIsNotSeasonAdministrator(): void
    {
        $role = aRole()
            ->named('Competition 1 Administrator')
            ->build();

        $this->assertNull(RolesHelper::findSeason($role));
    }

    public function testItReturnsTheCompetitionAdministratorRoleName(): void
    {
        /** @var Competition $competition */
        $competition = Competition::factory()->create();
        $competitionId = $competition->getId();

        $this->assertSame("Competition $competitionId Administrator", RolesHelper::competitionAdmin($competition));
    }

    public function testItCanCheckIfTheRoleIsCompetitionAdministrator(): void
    {
        $competitionAdmin = aRole()
            ->named('Competition 1 Administrator')
            ->build();
        $siteAdmin = aRole()
            ->named('Useless Role')
            ->build();

        $this->assertTrue(RolesHelper::isCompetitionAdmin($competitionAdmin));
        $this->assertFalse(RolesHelper::isCompetitionAdmin($siteAdmin));
    }

    public function testItGetsTheCompetitionFromTheCompetitionAdministratorRole(): void
    {
        /** @var Competition $competition */
        $competition = Competition::factory()->create();
        $competitionId = $competition->getId();
        /** @var Role $role */
        $role = Role::findByName("Competition $competitionId Administrator");

        $this->assertTrue(RolesHelper::findCompetition($role)->is($competition));
    }

    public function testItDoesNotGetTheCompetitionIfTheCompetitionDoesNotExist(): void
    {
        $role = aRole()
            ->named('Competition 1 Administrator')
            ->build();

        $this->assertNull(RolesHelper::findCompetition($role));
    }

    public function testItDoesNotGetTheCompetitionIfTheRoleIsNotCompetitionAdministrator(): void
    {
        $role = aRole()
            ->named('Season 1 Administrator')
            ->build();

        $this->assertNull(RolesHelper::findCompetition($role));
    }

    public function testItReturnsTheDivisionAdministratorRoleName(): void
    {
        /** @var Division $division */
        $division = Division::factory()->create();
        $divisionId = $division->getId();

        $this->assertSame("Division $divisionId Administrator", RolesHelper::divisionAdmin($division));
    }

    public function testItCanCheckIfTheRoleIsDivisionAdministrator(): void
    {
        $divisionAdministrator = aRole()
            ->named('Division 1 Administrator')
            ->build();
        $siteAdmin = aRole()
            ->named('Useless Role')
            ->build();

        $this->assertTrue(RolesHelper::isDivisionAdmin($divisionAdministrator));
        $this->assertFalse(RolesHelper::isDivisionAdmin($siteAdmin));
    }

    public function testItGetsTheDivisionFromTheDivisionAdministratorRole(): void
    {
        /** @var Division $division */
        $division = Division::factory()->create();
        $divisionId = $division->getId();
        /** @var Role $role */
        $role = Role::findByName("Division $divisionId Administrator");

        $this->assertTrue(RolesHelper::findDivision($role)->is($division));
    }

    public function testItDoesNotGetTheDivisionIfTheDivisionDoesNotExist(): void
    {
        $role = aRole()
            ->named('Division 1 Administrator')
            ->build();

        $this->assertNull(RolesHelper::findDivision($role));
    }

    public function testItDoesNotGetTheDivisionIfTheRoleIsNotDivisionAdministrator(): void
    {
        $role = aRole()
            ->named('Season 1 Administrator')
            ->build();

        $this->assertNull(RolesHelper::findDivision($role));
    }

    public function testItReturnsTheClubSecretaryRoleName(): void
    {
        /** @var Club $club */
        $club = Club::factory()->create();
        $clubId = $club->getId();

        $this->assertSame("Club $clubId Secretary", RolesHelper::clubSecretary($club));
    }

    public function testItCanCheckIfTheRoleIsClubSecretary(): void
    {
        $clubSecretary = aRole()
            ->named('Club 1 Secretary')
            ->build();
        $siteAdmin = aRole()
            ->named('Useless Role')
            ->build();

        $this->assertTrue(RolesHelper::isClubSecretary($clubSecretary));
        $this->assertFalse(RolesHelper::isClubSecretary($siteAdmin));
    }

    public function testItGetsTheClubFromTheClubSecretaryRole(): void
    {
        /** @var Club $club */
        $club = Club::factory()->create();
        $clubId = $club->getId();
        /** @var Role $role */
        $role = Role::findByName("Club $clubId Secretary");

        $this->assertTrue(RolesHelper::findClub($role)->is($club));
    }

    public function testItDoesNotGetTheClubIfTheClubDoesNotExist(): void
    {
        $role = aRole()
            ->named('Club 1 Secretary')
            ->build();

        $this->assertNull(RolesHelper::findClub($role));
    }

    public function testItDoesNotGetTheClubIfTheRoleIsNotClubSecretary(): void
    {
        $role = aRole()
            ->named('Season 1 Administrator')
            ->build();

        $this->assertNull(RolesHelper::findClub($role));
    }

    public function testItReturnsTheTeamSecretaryRoleName(): void
    {
        /** @var Team $team */
        $team = Team::factory()->create();
        $teamId = $team->getId();

        $this->assertSame("Team $teamId Secretary", RolesHelper::teamSecretary($team));
    }

    public function testItCanCheckIfTheRoleIsTeamSecretary(): void
    {
        $teamSecretary = aRole()
            ->named('Team 1 Secretary')
            ->build();
        $siteAdmin = aRole()
            ->named('Useless Role')
            ->build();

        $this->assertTrue(RolesHelper::isTeamSecretary($teamSecretary));
        $this->assertFalse(RolesHelper::isTeamSecretary($siteAdmin));
    }

    public function testItGetsTheTeamFromTheTeamSecretaryRole(): void
    {
        /** @var Team $team */
        $team = Team::factory()->create();
        $teamId = $team->getId();
        /** @var Role $role */
        $role = Role::findByName("Team $teamId Secretary");

        $this->assertTrue(RolesHelper::findTeam($role)->is($team));
    }

    public function testItDoesNotGetTheTeamIfTheTeamDoesNotExist(): void
    {
        $role = aRole()
            ->named('Team 1 Secretary')
            ->build();

        $this->assertNull(RolesHelper::findTeam($role));
    }

    public function testItDoesNotGetTheTeamIfTheRoleIsNotTeamSecretary(): void
    {
        $role = aRole()
            ->named('Season 1 Administrator')
            ->build();

        $this->assertNull(RolesHelper::findTeam($role));
    }
}
