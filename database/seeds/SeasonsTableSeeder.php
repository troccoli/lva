<?php

use App\Models\Season;
use Illuminate\Database\Seeder;

class SeasonsTableSeeder extends Seeder
{
    public function run(): void
    {
        factory(Season::class)->times(2)->create();
    }
}
