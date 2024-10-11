<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class ClubsSeeder extends Seeder
{
    public function run(): void
    {
        Venue::query()->each(function (Venue $venue): void {
            Club::factory()->for($venue)->create();
        });
    }
}
