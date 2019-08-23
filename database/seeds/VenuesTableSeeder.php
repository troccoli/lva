<?php

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenuesTableSeeder extends Seeder
{
    public function run(): void
    {
        factory(Venue::class)->times(20)->create();
    }
}
