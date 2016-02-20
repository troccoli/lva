<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::destroy(1);
        User::create([
            'id'       => 1,
            'name'     => 'Giulio Troccoli',
            'email'    => 'giulio@troccoli.it',
            'password' => bcrypt('MySpecialPassword'),
        ]);
    }
}
