<?php

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
        $this->initProgressBar(Role::count());

        $user = factory(User::class)->create([
            'name' => "Site Admin",
            'email' => "site-admin@example.com",
        ]);
        $user->assignRole("Site Admin");
        $this->advanceProgressBar();

        Season::all()->each(function (Season $season) {
            $user = factory(User::class)->create([
                'name' => "Season {$season->getId()} Admin",
                'email' => "season-{$season->getId()}-admin@example.com",
            ]);
            $user->assignRole("Season {$season->getId()} Admin");
            $this->advanceProgressBar();
        });
        Competition::all()->each(function (Competition $competition) {
            $user = factory(User::class)->create([
                'name' => "Competition {$competition->getId()} Admin",
                'email' => "competition-{$competition->getId()}-admin@example.com",
            ]);
            $user->assignRole("Competition {$competition->getId()} Admin");
            $this->advanceProgressBar();
        });
        Division::all()->each(function (Division $division) {
            $user = factory(User::class)->create([
                'name' => "Division {$division->getId()} Admin",
                'email' => "division-{$division->getId()}-admin@example.com",
            ]);
            $user->assignRole("Division {$division->getId()} Admin");
            $this->advanceProgressBar();
        });

        Club::all()->each(function (Club $club) {
            $user = factory(User::class)->create([
                'name' => "Club {$club->getId()} Secretary",
                'email' => "club-{$club->getId()}-secretary@example.com",
            ]);
            $user->assignRole("Club {$club->getId()} Secretary");
            $this->advanceProgressBar();
        });
        Team::all()->each(function (Team $team) {
            $user = factory(User::class)->create([
                'name' => "Team {$team->getId()} Secretary",
                'email' => "team-{$team->getId()}-secretary@example.com",
            ]);
            $user->assignRole("Team {$team->getId()} Secretary");
            $this->advanceProgressBar();
        });

        $this->finishProgressBar();
    }
}
