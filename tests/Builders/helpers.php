<?php

use Tests\Builders\ClubBuilder;
use Tests\Builders\TeamBuilder;

function aClub(): ClubBuilder
{
    return new ClubBuilder();
}

function aTeam(): TeamBuilder
{
    return new TeamBuilder();
}