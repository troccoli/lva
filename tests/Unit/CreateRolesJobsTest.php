<?php

use App\Jobs\Roles\CreateClubSecretaryRole;
use App\Jobs\Roles\CreateCompetitionAdminRole;
use App\Jobs\Roles\CreateDivisionAdminRole;
use App\Jobs\Roles\CreateSeasonAdminRole;
use App\Jobs\Roles\CreateTeamSecretaryRole;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;

it('creates the season admin role', function () {
    $seasonId = fake()->numberBetween(1, 1_000);
    $season = Mockery::mock(Season::class)
        ->shouldReceive('getKey')->andReturn($seasonId)
        ->getMock();

    $sut = new CreateSeasonAdminRole($season);

    $sut->handle();

    $this->assertDatabaseHas('roles', ['name' => "Season $seasonId Administrator"]);
});

it('creates the competition admin role', function () {
    $competitionId = fake()->numberBetween(1, 1_000);
    $competition = Mockery::mock(Competition::class)
        ->shouldReceive('getKey')->andReturn($competitionId)
        ->getMock();

    $sut = new CreateCompetitionAdminRole($competition);

    $sut->handle();

    $this->assertDatabaseHas('roles', ['name' => "Competition $competitionId Administrator"]);
});

it('creates the division admin role', function () {
    $divisionId = fake()->numberBetween(1, 1_000);
    $division = Mockery::mock(Division::class)
        ->shouldReceive('getKey')->andReturn($divisionId)
        ->getMock();

    $sut = new CreateDivisionAdminRole($division);

    $sut->handle();

    $this->assertDatabaseHas('roles', ['name' => "Division $divisionId Administrator"]);
});

it('creates the club secretary role', function () {
    $clubId = fake()->numberBetween(1, 1_000);
    $club = Mockery::mock(Club::class)
        ->shouldReceive('getKey')->andReturn($clubId)
        ->getMock();

    $sut = new CreateClubSecretaryRole($club);

    $sut->handle();

    $this->assertDatabaseHas('roles', ['name' => "Club $clubId Secretary"]);
});

it('creates the team secretary role', function () {
    $teamId = fake()->numberBetween(1, 1_000);
    $team = Mockery::mock(Team::class)
        ->shouldReceive('getKey')->andReturn($teamId)
        ->getMock();

    $sut = new CreateTeamSecretaryRole($team);

    $sut->handle();

    $this->assertDatabaseHas('roles', ['name' => "Team $teamId Secretary"]);
});
