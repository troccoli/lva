<?php

namespace App\Policies;

use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;

trait CheckRoles
{
    public function hasAnySeasonAdminRole(User $user): bool
    {
        return $user->hasRole(Season::all()->map(function (Season $season): string {
            return RolesHelper::seasonAdmin($season);
        })->toArray());
    }

    public function hasAnyCompetitionAdminRole(User $user, ?Season $season = null): bool
    {
        $competitions = $season
            ? $season->getCompetitions()
            : Competition::all();

        return $user->hasRole($competitions->map(function (Competition $competition): string {
            return RolesHelper::competitionAdmin($competition);
        })->toArray());
    }

    public function hasAnyDivisionAdminRole(User $user, ?Competition $competition = null): bool
    {
        $divisions = $competition
            ? $competition->getDivisions()
            : Division::all();

        return $user->hasRole($divisions->map(function (Division $division): string {
            return RolesHelper::divisionAdmin($division);
        })->toArray());
    }

    public function hasAnyClubSecretaryRole(User $user): bool
    {
        return $user->hasRole(Club::all()->map(function (Club $club): string {
            return RolesHelper::clubSecretary($club);
        })->toArray());
    }

    public function hasAnyTeamSecretaryRole(User $user, ?Club $club = null): bool
    {
        $teams = $club
            ? $club->getTeams()
            : Team::all();

        return $user->hasRole($teams->map(function (Team $team): string {
            return RolesHelper::teamSecretary($team);
        })->toArray());
    }
}
