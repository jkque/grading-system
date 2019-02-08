<?php

use Illuminate\Database\Seeder;
use App\User;

class AdminUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'mobile_number' => '0999999',
            'address' => 'your address',
            'email' => 'johndoe@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('admin');
    }
}
