<?php

use Tests\Builders\ClubBuilder;
use Tests\Builders\FixtureBuilder;
use Tests\Builders\TeamBuilder;

function aClub(): ClubBuilder
{
    return new ClubBuilder();
}

function aTeam(): TeamBuilder
{
    return new TeamBuilder();
}

function aFixture(): FixtureBuilder
{
    return new FixtureBuilder();
}
