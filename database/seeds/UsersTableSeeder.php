<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $user->assignRole('Site Admin');
    }
}
