<?php

use App\Jobs\Permissions\CreateClubPermissions;
use App\Jobs\Permissions\CreateCompetitionPermissions;
use App\Jobs\Permissions\CreateDivisionPermissions;
use App\Jobs\Permissions\CreateSeasonPermissions;
use App\Jobs\Permissions\CreateTeamPermissions;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;

it('creates the season permissions', function () {
    $seasonId = fake()->numberBetween(1, 1_000);
    $season = Mockery::mock(Season::class)
        ->shouldReceive('getKey')->andReturn($seasonId)
        ->getMock();

    $sut = new CreateSeasonPermissions($season);

    $sut->handle();

    $this->assertDatabaseCount('permissions', 4);
    $this->assertDatabaseHas('permissions', ['name' => "view-season-$seasonId"]);
    $this->assertDatabaseHas('permissions', ['name' => "edit-season-$seasonId"]);
    $this->assertDatabaseHas('permissions', ['name' => "delete-season-$seasonId"]);
    $this->assertDatabaseHas('permissions', ['name' => "add-competition-in-season-$seasonId"]);
});

it('creates the competition permissions', function () {
    $competitionId = fake()->numberBetween(1, 1_000);
    $competition = Mockery::mock(Competition::class)
        ->shouldReceive('getKey')->andReturn($competitionId)
        ->getMock();

    $sut = new CreateCompetitionPermissions($competition);

    $sut->handle();

    $this->assertDatabaseCount('permissions', 4);
    $this->assertDatabaseHas('permissions', ['name' => "view-competition-$competitionId"]);
    $this->assertDatabaseHas('permissions', ['name' => "edit-competition-$competitionId"]);
    $this->assertDatabaseHas('permissions', ['name' => "delete-competition-$competitionId"]);
    $this->assertDatabaseHas('permissions', ['name' => "add-division-in-competition-$competitionId"]);
});

it('creates the division permissions', function () {
    $divisionId = fake()->numberBetween(1, 1_000);
    $division = Mockery::mock(Division::class)
        ->shouldReceive('getKey')->andReturn($divisionId)
        ->getMock();

    $sut = new CreateDivisionPermissions($division);

    $sut->handle();

    $this->assertDatabaseCount('permissions', 7);
    $this->assertDatabaseHas('permissions', ['name' => "view-division-$divisionId"]);
    $this->assertDatabaseHas('permissions', ['name' => "edit-division-$divisionId"]);
    $this->assertDatabaseHas('permissions', ['name' => "delete-division-$divisionId"]);
    $this->assertDatabaseHas('permissions', ['name' => "add-fixtures-in-division-$divisionId"]);
    $this->assertDatabaseHas('permissions', ['name' => "edit-fixtures-in-division-$divisionId"]);
    $this->assertDatabaseHas('permissions', ['name' => "delete-fixtures-in-division-$divisionId"]);
    $this->assertDatabaseHas('permissions', ['name' => "view-fixtures-in-division-$divisionId"]);
});

it('creates the club permissions', function () {
    $clubId = fake()->numberBetween(1, 1_000);
    $club = Mockery::mock(Club::class)
        ->shouldReceive('getKey')->andReturn($clubId)
        ->getMock();

    $sut = new CreateClubPermissions($club);

    $sut->handle();

    $this->assertDatabaseCount('permissions', 4);
    $this->assertDatabaseHas('permissions', ['name' => "view-club-$clubId"]);
    $this->assertDatabaseHas('permissions', ['name' => "edit-club-$clubId"]);
    $this->assertDatabaseHas('permissions', ['name' => "delete-club-$clubId"]);
    $this->assertDatabaseHas('permissions', ['name' => "add-team-in-club-$clubId"]);
});

it('creates the team permissions', function () {
    $teamId = fake()->numberBetween(1, 1_000);
    $team = Mockery::mock(Team::class)
        ->shouldReceive('getKey')->andReturn($teamId)
        ->getMock();

    $sut = new CreateTeamPermissions($team);

    $sut->handle();

    $this->assertDatabaseCount('permissions', 3);
    $this->assertDatabaseHas('permissions', ['name' => "view-team-$teamId"]);
    $this->assertDatabaseHas('permissions', ['name' => "edit-team-$teamId"]);
    $this->assertDatabaseHas('permissions', ['name' => "delete-team-$teamId"]);
});
