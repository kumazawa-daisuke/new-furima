<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'id' => 1,
                'name' => 'テストユーザー',
                'email' => 'test@example.com',
                'password' => bcrypt('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
