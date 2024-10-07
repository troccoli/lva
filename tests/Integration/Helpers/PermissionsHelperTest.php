<?php

use App\Helpers\PermissionsHelper;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;

it('returns the permission to add a season', function () {
    expect(PermissionsHelper::addSeason())->toBe('add-season');
});
it('returns the permission to view a season', function () {
    $season = Season::factory()->create();

    expect(PermissionsHelper::viewSeason($season))->toBe("view-season-{$season->getKey()}");
});
it('returns the permission to edit a season', function () {
    $season = Season::factory()->create();

    expect(PermissionsHelper::editSeason($season))->toBe("edit-season-{$season->getKey()}");
});
it('returns the permission to delete a season', function () {
    $season = Season::factory()->create();

    expect(PermissionsHelper::deleteSeason($season))->toBe("delete-season-{$season->getKey()}");
});
it('returns the permission to add a competition', function () {
    $season = Season::factory()->create();

    expect(PermissionsHelper::addCompetition($season))->toBe("add-competition-in-season-{$season->getKey()}");
});
it('returns the permission to view a competition', function () {
    $competition = Competition::factory()->create();

    expect(PermissionsHelper::viewCompetition($competition))->toBe("view-competition-{$competition->getKey()}");
});
it('returns the permission to edit a competition', function () {
    $competition = Competition::factory()->create();

    expect(PermissionsHelper::editCompetition($competition))->toBe("edit-competition-{$competition->getKey()}");
});
it('returns the permission to delete a competition', function () {
    $competition = Competition::factory()->create();

    expect(PermissionsHelper::deleteCompetition($competition))->toBe("delete-competition-{$competition->getKey()}");
});
it('returns the permission to add a division', function () {
    $competition = Competition::factory()->create();

    expect(PermissionsHelper::addDivision($competition))->toBe("add-division-in-competition-{$competition->getKey()}");
});
it('returns the permission to view a division', function () {
    $division = Division::factory()->create();

    expect(PermissionsHelper::viewDivision($division))->toBe("view-division-{$division->getKey()}");
});
it('returns the permission to edit a division', function () {
    $division = Division::factory()->create();

    expect(PermissionsHelper::editDivision($division))->toBe("edit-division-{$division->getKey()}");
});
it('returns the permission to delete a division', function () {
    $division = Division::factory()->create();

    expect(PermissionsHelper::deleteDivision($division))->toBe("delete-division-{$division->getKey()}");
});
it('returns the permission to view fixtures', function () {
    $division = Division::factory()->create();

    expect(PermissionsHelper::viewFixtures($division))->toBe("view-fixtures-in-division-{$division->getKey()}");
});
it('returns the permission to add a fixture', function () {
    $division = Division::factory()->create();

    expect(PermissionsHelper::addFixtures($division))->toBe("add-fixtures-in-division-{$division->getKey()}");
});
it('returns the permission to edit a fixture', function () {
    $division = Division::factory()->create();

    expect(PermissionsHelper::editFixtures($division))->toBe("edit-fixtures-in-division-{$division->getKey()}");
});
it('returns the permission to delete a fixture', function () {
    $division = Division::factory()->create();

    expect(PermissionsHelper::deleteFixtures($division))->toBe("delete-fixtures-in-division-{$division->getKey()}");
});
it('returns the permission to add a club', function () {
    expect(PermissionsHelper::addClub())->toBe('add-club');
});
it('returns the permission to view a club', function () {
    $club = Club::factory()->create();

    expect(PermissionsHelper::viewClub($club))->toBe("view-club-{$club->getKey()}");
});
it('returns the permission to edit a club', function () {
    $club = Club::factory()->create();

    expect(PermissionsHelper::editClub($club))->toBe("edit-club-{$club->getKey()}");
});
it('returns the permission to delete a club', function () {
    $club = Club::factory()->create();

    expect(PermissionsHelper::deleteClub($club))->toBe("delete-club-{$club->getKey()}");
});
it('returns the permission to add a team', function () {
    $club = Club::factory()->create();

    expect(PermissionsHelper::addTeam($club))->toBe("add-team-in-club-{$club->getKey()}");
});
it('returns the permission to view a team', function () {
    $team = Team::factory()->create();

    expect(PermissionsHelper::viewTeam($team))->toBe("view-team-{$team->getKey()}");
});
it('returns the permission to edit a team', function () {
    $team = Team::factory()->create();

    expect(PermissionsHelper::editTeam($team))->toBe("edit-team-{$team->getKey()}");
});
it('returns the permission to delete a team', function () {
    $team = Team::factory()->create();

    expect(PermissionsHelper::deleteTeam($team))->toBe("delete-team-{$team->getKey()}");
});
