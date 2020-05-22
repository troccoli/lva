<?php

use App\Models\Competition;
use App\Models\Season;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CompetitionsTableSeeder extends Seeder
{
    public function run(): void
    {
        Season::each(function (Season $season): void {
            factory(Competition::class)->create(['season_id' => $season->id]);
        });

        $this->createRoles();
    }

    private function createRoles(): void
    {
        Competition::all()->each(function (Competition $competition) {
            Role::create(['name' => "Competition {$competition->getId()} Admin"]);
        });
    }
}
