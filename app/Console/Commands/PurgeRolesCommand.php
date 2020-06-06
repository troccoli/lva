<?php

namespace App\Console\Commands;

use App\Helpers\RolesHelper;
use App\Jobs\DeleteRole;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class PurgeRolesCommand extends Command
{
    /** @var string */
    protected $signature = 'role:purge';

    /** @var string */
    protected $description = 'This command will remove all unnecessary roles. For example, a Division Administrator role for a division that has been deleted.';

    public function handle()
    {
        /** @var Collection $rolesToPurge */
        $rolesToPurge = Role::all()
            ->reduce(function (Collection $rolesToPurge, Role $role): Collection {
                if (RolesHelper::isSeasonAdmin($role) && null === RolesHelper::findSeason($role)) {
                    $rolesToPurge->push($role);
                } elseif (RolesHelper::isCompetitionAdmin($role) && null === RolesHelper::findCompetition($role)) {
                    $rolesToPurge->push($role);
                } elseif (RolesHelper::isDivisionAdmin($role) && null === RolesHelper::findDivision($role)) {
                    $rolesToPurge->push($role);
                } elseif (RolesHelper::isClubSecretary($role) && null === RolesHelper::findClub($role)) {
                    $rolesToPurge->push($role);
                } elseif (RolesHelper::isTeamSecretary($role) && null === RolesHelper::findTeam($role)) {
                    $rolesToPurge->push($role);
                }

                return $rolesToPurge;
            }, new Collection());

        if ($rolesToPurge->isNotEmpty()) {
            DeleteRole::dispatchNow($rolesToPurge);
        }
    }
}
