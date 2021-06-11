<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenuesTableSeeder extends Seeder
{
    public function run(): void
    {
        Venue::factory()->count(20)->create();
    }
}
