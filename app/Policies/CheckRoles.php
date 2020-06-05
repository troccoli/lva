<?php

namespace App\Policies;

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
            return $season->getAdminRole();
        })->toArray());
    }

    public function hasAnyCompetitionAdminRole(User $user, ?Season $season = null): bool
    {
        $competitions = $season
            ? $season->getCompetitions()
            : Competition::all();

        return $user->hasRole($competitions->map(function (Competition $competition): string {
            return $competition->getAdminRole();
        })->toArray());
    }

    public function hasAnyDivisionAdminRole(User $user, ?Competition $competition = null): bool
    {
        $divisions = $competition
            ? $competition->getDivisions()
            : Division::all();

        return $user->hasRole($divisions->map(function (Division $division): string {
            return $division->getAdminRole();
        })->toArray());
    }

    public function hasAnyClubSecretaryRole(User $user): bool
    {
        return $user->hasRole(Club::all()->map(function (Club $club): string {
            return $club->getSecretaryRole();
        })->toArray());
    }

    public function hasAnyTeamSecretaryRole(User $user): bool
    {
        return $user->hasRole(Team::all()->map(function (Team $team): string {
            return $team->getSecretaryRole();
        })->toArray());
    }
}
