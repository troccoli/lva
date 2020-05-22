<?php

use App\Models\Season;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SeasonsTableSeeder extends Seeder
{
    public function run(): void
    {
        factory(Season::class)->times(2)->create();

        $this->createRoles();
    }

    private function createRoles(): void
    {
        Season::all()->each(function (Season $season) {
            Role::create(['name' => "Season {$season->getName()} Admin"]);
        });
    }
}
