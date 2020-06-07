<?php

namespace Tests\Builders;

class TestModelFactory
{
    public static function aRole(): RoleBuilder
    {
        return new RoleBuilder();
    }
}
