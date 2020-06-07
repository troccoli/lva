<?php

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

        factory(User::class)->create([
            'name' => "Test User",
            'email' => "test-user@example.com",
        ]);
        $this->advanceProgressBar();

        $user = factory(User::class)->create([
            'name' => "Site Administrator",
            'email' => "site-administrator@example.com",
        ]);
        $user->assignRole("Site Administrator");
        $this->advanceProgressBar();

        $user = factory(User::class)->create([
            'name' => "Referees Administrator",
            'email' => "referees-administrator@example.com",
        ]);
        $user->assignRole("Referees Administrator");
        $this->advanceProgressBar();

        Season::all()->each(function (Season $season) {
            $user = factory(User::class)->create([
                'name' => "Season {$season->getId()} Administrator",
                'email' => "season-{$season->getId()}-administrator@example.com",
            ]);
            $user->assignRole(RolesHelper::seasonAdminName($season));
            $this->advanceProgressBar();
        });
        Competition::all()->each(function (Competition $competition) {
            $user = factory(User::class)->create([
                'name' => "Competition {$competition->getId()} Administrator",
                'email' => "competition-{$competition->getId()}-administrator@example.com",
            ]);
            $user->assignRole(RolesHelper::competitionAdminName($competition));
            $this->advanceProgressBar();
        });
        Division::all()->each(function (Division $division) {
            $user = factory(User::class)->create([
                'name' => "Division {$division->getId()} Administrator",
                'email' => "division-{$division->getId()}-administrator@example.com",
            ]);
            $user->assignRole(RolesHelper::divisionAdminName($division));
            $this->advanceProgressBar();
        });

        Club::all()->each(function (Club $club) {
            $user = factory(User::class)->create([
                'name' => "Club {$club->getId()} Secretary",
                'email' => "club-{$club->getId()}-secretary@example.com",
            ]);
            $user->assignRole(RolesHelper::clubSecretaryName($club));
            $this->advanceProgressBar();
        });
        Team::all()->each(function (Team $team) {
            $user = factory(User::class)->create([
                'name' => "Team {$team->getId()} Secretary",
                'email' => "team-{$team->getId()}-secretary@example.com",
            ]);
            $user->assignRole(RolesHelper::teamSecretaryName($team));
            $this->advanceProgressBar();
        });

        $this->finishProgressBar();
    }
}
