<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionsTableSeeder extends Seeder
{
    public function run(): void
    {
        Competition::each(function (Competition $competition): void {
            Division::factory()->for($competition)->count(5)->create();
        });
    }
}
