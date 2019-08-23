<?php

use App\Models\Club;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class ClubsTableSeeder extends Seeder
{
    public function run(): void
    {
        Venue::each(function (Venue $venue): void {
            factory(Club::class)->create(['venue_id' => $venue->getId()]);
        });
    }
}
