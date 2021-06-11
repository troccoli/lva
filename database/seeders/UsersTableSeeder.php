<?php

namespace Database\Seeders;

use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    use SeederProgressBar;

    public function run(): void
    {
        $this->initProgressBar(Role::count() + 1);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test-user@example.com',
        ]);
        $this->advanceProgressBar();

        $user = User::factory()->create([
            'name' => 'Site Administrator',
            'email' => 'site-administrator@example.com',
        ]);
        $user->assignRole('Site Administrator');
        $this->advanceProgressBar();

        $user = User::factory()->create([
            'name' => 'Referees Administrator',
            'email' => 'referees-administrator@example.com',
        ]);
        $user->assignRole('Referees Administrator');
        $this->advanceProgressBar();

        Season::all()->each(function (Season $season) {
            $user = User::factory()->create([
                'name' => "Season {$season->getId()} Administrator",
                'email' => "season-{$season->getId()}-administrator@example.com",
            ]);
            $user->assignRole(RolesHelper::seasonAdmin($season));
            $this->advanceProgressBar();
        });
        Competition::all()->each(function (Competition $competition) {
            $user = User::factory()->create([
                'name' => "Competition {$competition->getId()} Administrator",
                'email' => "competition-{$competition->getId()}-administrator@example.com",
            ]);
            $user->assignRole(RolesHelper::competitionAdmin($competition));
            $this->advanceProgressBar();
        });
        Division::all()->each(function (Division $division) {
            $user = User::factory()->create([
                'name' => "Division {$division->getId()} Administrator",
                'email' => "division-{$division->getId()}-administrator@example.com",
            ]);
            $user->assignRole(RolesHelper::divisionAdmin($division));
            $this->advanceProgressBar();
        });

        Club::all()->each(function (Club $club) {
            $user = User::factory()->create([
                'name' => "Club {$club->getId()} Secretary",
                'email' => "club-{$club->getId()}-secretary@example.com",
            ]);
            $user->assignRole(RolesHelper::clubSecretary($club));
            $this->advanceProgressBar();
        });
        Team::all()->each(function (Team $team) {
            $user = User::factory()->create([
                'name' => "Team {$team->getId()} Secretary",
                'email' => "team-{$team->getId()}-secretary@example.com",
            ]);
            $user->assignRole(RolesHelper::teamSecretary($team));
            $this->advanceProgressBar();
        });

        $this->finishProgressBar();
    }
}
