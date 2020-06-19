<?php

namespace App\Helpers;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

final class RolesHelper
{
    public const SITE_ADMIN = 'Site Administrator';
    public const REF_ADMIN = 'Referees Administrator';

    private const SEASON_ADMIN_TEMPLATE = 'Season %s Administrator';
    private const COMPETITION_ADMIN_TEMPLATE = 'Competition %s Administrator';
    private const DIVISION_ADMIN_TEMPLATE = 'Division %s Administrator';
    private const CLUB_SECRETARY_TEMPLATE = 'Club %s Secretary';
    private const TEAM_SECRETARY_TEMPLATE = 'Team %s Secretary';

    final public static function seasonAdminName(Season $season): string
    {
        return sprintf(self::SEASON_ADMIN_TEMPLATE, $season->getId());
    }

    final public static function isSeasonAdmin(Role $role): bool
    {
        return (bool)preg_match(self::buildPattern(self::SEASON_ADMIN_TEMPLATE), $role->name);
    }

    final public static function findSeason(Role $role): ?Season
    {
        if (preg_match(self::buildPattern(self::SEASON_ADMIN_TEMPLATE), $role->name, $matches)) {
            return Season::find($matches[1]);
        }

        return null;
    }

    final public static function competitionAdminName(Competition $competition): string
    {
        return sprintf(self::COMPETITION_ADMIN_TEMPLATE, $competition->getId());
    }

    final public static function isCompetitionAdmin(Role $role): bool
    {
        return (bool)preg_match(self::buildPattern(self::COMPETITION_ADMIN_TEMPLATE), $role->name);
    }

    final public static function findCompetition(Role $role): ?Competition
    {
        if (preg_match(self::buildPattern(self::COMPETITION_ADMIN_TEMPLATE), $role->name, $matches)) {
            return Competition::find($matches[1]);
        }

        return null;
    }

    final public static function divisionAdminName(Division $competition): string
    {
        return sprintf(self::DIVISION_ADMIN_TEMPLATE, $competition->getId());
    }

    final public static function isDivisionAdmin(Role $role): bool
    {
        return (bool)preg_match(self::buildPattern(self::DIVISION_ADMIN_TEMPLATE), $role->name);
    }

    final public static function findDivision(Role $role): ?Division
    {
        if (preg_match(self::buildPattern(self::DIVISION_ADMIN_TEMPLATE), $role->name, $matches)) {
            return Division::find($matches[1]);
        }

        return null;
    }

    final public static function clubSecretaryName(Club $club): string
    {
        return sprintf(self::CLUB_SECRETARY_TEMPLATE, $club->getId());
    }

    final public static function isClubSecretary(Role $role): bool
    {
        return (bool)preg_match(self::buildPattern(self::CLUB_SECRETARY_TEMPLATE), $role->name);
    }

    final public static function findClub(Role $role): ?Club
    {
        if (preg_match(self::buildPattern(self::CLUB_SECRETARY_TEMPLATE), $role->name, $matches)) {
            return Club::find($matches[1]);
        }

        return null;
    }

    final public static function teamSecretaryName(Team $team): string
    {
        return sprintf(self::TEAM_SECRETARY_TEMPLATE, $team->getId());
    }

    final public static function isTeamSecretary(Role $role): bool
    {
        return (bool)preg_match(self::buildPattern(self::TEAM_SECRETARY_TEMPLATE), $role->name);
    }

    final public static function findTeam(Role $role): ?Team
    {
        if (preg_match(self::buildPattern(self::TEAM_SECRETARY_TEMPLATE), $role->name, $matches)) {
            return Team::find($matches[1]);
        }

        return null;
    }

    final private static function buildPattern(string $template): string
    {
        return '/^' . Str::replaceFirst('%s', '(\d+)', $template) . '$/';
    }
}
