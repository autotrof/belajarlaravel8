<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::insert([
            [
                'email'=>'user1@email.com',
                'password'=>bcrypt('12345')
            ],
            [
                'email'=>'user2@email.com',
                'password'=>bcrypt('12345')
            ]
        ]);
    }
}
