<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminNewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = "admin213";
        $user = User::updateOrCreate(['email' => 'admin@admin.com'],
        [
            'name' => 'Admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'username' => 'admin',
            'role_id' => 1,
            'password' => $password
        ]);
    }
}
