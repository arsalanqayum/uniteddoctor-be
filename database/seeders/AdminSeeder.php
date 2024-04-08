<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = "admin213";
        $user = User::create([
            'name' => 'Admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'username' => 'admin',
            'role_id' => 1,            
            'email' => 'admin@admin.com',
            'password' => bcrypt($password),
        ]);
        $user->addRole(1);
    }
}
