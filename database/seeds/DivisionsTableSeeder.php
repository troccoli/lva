<?php

use App\Models\Competition;
use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionsTableSeeder extends Seeder
{
    public function run(): void
    {
        Competition::each(function (Competition $competition): void {
            factory(Division::class)->times(5)->create(['competition_id' => $competition->id]);
        });
    }
}
