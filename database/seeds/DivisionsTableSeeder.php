<?php

use App\Models\Competition;
use App\Models\Division;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DivisionsTableSeeder extends Seeder
{
    public function run(): void
    {
        Competition::each(function (Competition $competition): void {
            factory(Division::class)->times(5)->create(['competition_id' => $competition->id]);
        });

        $this->createRoles();
    }

    private function createRoles(): void
    {
        Division::all()->each(function (Division $division) {
            Role::create(['name' => "Division {$division->getId()} Admin"]);
        });
    }
}
