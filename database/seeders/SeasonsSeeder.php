<?php

namespace Database\Seeders;

use App\Models\Season;
use Illuminate\Database\Seeder;

class SeasonsSeeder extends Seeder
{
    public function run(): void
    {
        Season::factory()->count(2)->create();
    }
}
