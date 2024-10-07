<?php

use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use Spatie\Permission\Models\Role;

it('returns the season administrator role name', function () {
    /** @var Season $season */
    $season = Season::factory()->create();
    $seasonId = $season->getKey();

    expect(RolesHelper::seasonAdmin($season))->toBe("Season $seasonId Administrator");
});

it('can check if the role is season administrator', function () {
    $seasonAdmin = Role::create(['name' => 'Season 1 Administrator']);
    $siteAdmin = Role::create(['name' => 'Useless Role']);

    expect(RolesHelper::isSeasonAdmin($seasonAdmin))->toBeTrue()
        ->and(RolesHelper::isSeasonAdmin($siteAdmin))->toBeFalse();
});

it('gets the season from the season administrator role', function () {
    /** @var Season $season */
    $season = Season::factory()->create();
    $seasonId = $season->getKey();

    /** @var Role $role */
    $role = Role::findByName("Season $seasonId Administrator");

    expect(RolesHelper::findSeason($role)->is($season))->toBeTrue();
});

it('does not get the season if the season does not exist', function () {
    $role = Role::create(['name' => 'Season 1 Administrator']);

    expect(RolesHelper::findSeason($role))->toBeNull();
});

it('does not get the season if the role is not season administrator', function () {
    $role = Role::create(['name' => 'Competition 1 Administrator']);

    expect(RolesHelper::findSeason($role))->toBeNull();
});

it('returns the competition administrator role name', function () {
    /** @var Competition $competition */
    $competition = Competition::factory()->create();
    $competitionId = $competition->getKey();

    expect(RolesHelper::competitionAdmin($competition))->toBe("Competition $competitionId Administrator");
});

it('can check if the role is competition administrator', function () {
    $competitionAdmin = Role::create(['name' => 'Competition 1 Administrator']);
    $siteAdmin = Role::create(['name' => 'Useless Role']);

    expect(RolesHelper::isCompetitionAdmin($competitionAdmin))->toBeTrue()
        ->and(RolesHelper::isCompetitionAdmin($siteAdmin))->toBeFalse();
});

it('gets the competition from the competition administrator role', function () {
    /** @var Competition $competition */
    $competition = Competition::factory()->create();
    $competitionId = $competition->getKey();

    /** @var Role $role */
    $role = Role::findByName("Competition $competitionId Administrator");

    expect(RolesHelper::findCompetition($role)->is($competition))->toBeTrue();
});

it('does not get the competition if the competition does not exist', function () {
    $role = Role::create(['name' => 'Competition 1 Administrator']);

    expect(RolesHelper::findCompetition($role))->toBeNull();
});

it('does not get the competition if the role is not competition administrator', function () {
    $role = Role::create(['name' => 'Season 1 Administrator']);

    expect(RolesHelper::findCompetition($role))->toBeNull();
});

it('returns the division administrator role name', function () {
    /** @var Division $division */
    $division = Division::factory()->create();
    $divisionId = $division->getKey();

    expect(RolesHelper::divisionAdmin($division))->toBe("Division $divisionId Administrator");
});

it('can check if the role is division administrator', function () {
    $divisionAdministrator = Role::create(['name' => 'Division 1 Administrator']);
    $siteAdmin = Role::create(['name' => 'Useless Role']);

    expect(RolesHelper::isDivisionAdmin($divisionAdministrator))->toBeTrue()
        ->and(RolesHelper::isDivisionAdmin($siteAdmin))->toBeFalse();
});

it('gets the division from the division administrator role', function () {
    /** @var Division $division */
    $division = Division::factory()->create();
    $divisionId = $division->getKey();

    /** @var Role $role */
    $role = Role::findByName("Division $divisionId Administrator");

    expect(RolesHelper::findDivision($role)->is($division))->toBeTrue();
});

it('does not get the division if the division does not exist', function () {
    $role = Role::create(['name' => 'Division 1 Administrator']);

    expect(RolesHelper::findDivision($role))->toBeNull();
});

it('does not get the division if the role is not division administrator', function () {
    $role = Role::create(['name' => 'Season 1 Administrator']);

    expect(RolesHelper::findDivision($role))->toBeNull();
});

it('returns the club secretary role name', function () {
    /** @var Club $club */
    $club = Club::factory()->create();
    $clubId = $club->getKey();

    expect(RolesHelper::clubSecretary($club))->toBe("Club $clubId Secretary");
});

it('can check if the role is club secretary', function () {
    $clubSecretary = Role::create(['name' => 'Club 1 Secretary']);
    $siteAdmin = Role::create(['name' => 'Useless Role']);

    expect(RolesHelper::isClubSecretary($clubSecretary))->toBeTrue()
        ->and(RolesHelper::isClubSecretary($siteAdmin))->toBeFalse();
});

it('gets the club from the club secretary role', function () {
    /** @var Club $club */
    $club = Club::factory()->create();
    $clubId = $club->getKey();

    /** @var Role $role */
    $role = Role::findByName("Club $clubId Secretary");

    expect(RolesHelper::findClub($role)->is($club))->toBeTrue();
});

it('does not get the club if the club does not exist', function () {
    $role = Role::create(['name' => 'Club 1 Secretary']);

    expect(RolesHelper::findClub($role))->toBeNull();
});

it('does not get the club if the role is not club secretary', function () {
    $role = Role::create(['name' => 'Season 1 Administrator']);

    expect(RolesHelper::findClub($role))->toBeNull();
});

it('returns the team secretary role name', function () {
    /** @var Team $team */
    $team = Team::factory()->create();
    $teamId = $team->getKey();

    expect(RolesHelper::teamSecretary($team))->toBe("Team $teamId Secretary");
});

it('can check if the role is team secretary', function () {
    $teamSecretary = Role::create(['name' => 'Team 1 Secretary']);
    $siteAdmin = Role::create(['name' => 'Useless Role']);

    expect(RolesHelper::isTeamSecretary($teamSecretary))->toBeTrue()
        ->and(RolesHelper::isTeamSecretary($siteAdmin))->toBeFalse();
});

it('gets the team from the team secretary role', function () {
    /** @var Team $team */
    $team = Team::factory()->create();
    $teamId = $team->getKey();

    /** @var Role $role */
    $role = Role::findByName("Team $teamId Secretary");

    expect(RolesHelper::findTeam($role)->is($team))->toBeTrue();
});

it('does not get the team if the team does not exist', function () {
    $role = Role::create(['name' => 'Team 1 Secretary']);

    expect(RolesHelper::findTeam($role))->toBeNull();
});

it('does not get the team if the role is not team secretary', function () {
    $role = Role::create(['name' => 'Season 1 Administrator']);

    expect(RolesHelper::findTeam($role))->toBeNull();
});
