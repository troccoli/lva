<?php

use App\Models\Club;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ClubsTableSeeder extends Seeder
{
    public function run(): void
    {
        Venue::each(function (Venue $venue): void {
            factory(Club::class)->create(['venue_id' => $venue->getId()]);
        });

        $this->createRoles();
    }

    private function createRoles(): void
    {
        Club::all()->each(function (Club $club) {
            Role::create(['name' => "Club {$club->getId()} Secretary"]);
        });
    }
}
