<?php

namespace App\Helpers;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;

final class PermissionsHelper
{
    final public static function addSeason(): string
    {
        return "add-season";
    }

    final public static function viewSeason(Season $season): string
    {
        return "view-season-{$season->getId()}";
    }

    final public static function editSeason(Season $season): string
    {
        return "edit-season-{$season->getId()}";
    }

    final public static function deleteSeason(Season $season): string
    {
        return "delete-season-{$season->getId()}";
    }

    final public static function addCompetition(Season $season): string
    {
        return "add-competition-in-season-{$season->getId()}";
    }

    final public static function editCompetition(Competition $competition): string
    {
        return "edit-competition-{$competition->getId()}";
    }

    final public static function deleteCompetition(Competition $competition): string
    {
        return "delete-competition-{$competition->getId()}";
    }

    final public static function viewDivisions(Competition $competition): string
    {
        return "view-divisions-in-competition-{$competition->getId()}";
    }

    final public static function addDivision(Competition $competition): string
    {
        return "add-division-in-competition-{$competition->getId()}";
    }

    final public static function editDivision(Division $division): string
    {
        return "edit-division-{$division->getId()}";
    }

    final public static function deleteDivision(Division $division): string
    {
        return "delete-division-{$division->getId()}";
    }

    final public static function viewFixtures(Division $division): string
    {
        return "view-fixtures-in-division-{$division->getId()}";
    }

    final public static function addFixtures(Division $division): string
    {
        return "add-fixtures-in-division-{$division->getId()}";
    }

    final public static function editFixtures(Division $division): string
    {
        return "edit-fixtures-in-division-{$division->getId()}";
    }

    final public static function deleteFixtures(Division $division): string
    {
        return "delete-fixtures-in-division-{$division->getId()}";
    }

    final public static function editClub(Club $club): string
    {
        return "edit-club-{$club->getId()}";
    }

    final public static function deleteClub(Club $club): string
    {
        return "delete-club-{$club->getId()}";
    }

    final public static function viewTeams(Club $club): string
    {
        return "view-teams-in-club-{$club->getId()}";
    }

    final public static function addTeam(Club $club): string
    {
        return "add-team-in-club-{$club->getId()}";
    }

    final public static function editTeam(Team $team): string
    {
        return "edit-team-{$team->getId()}";
    }

    final public static function deleteTeam(Team $team): string
    {
        return "delete-team-{$team->getId()}";
    }
}
