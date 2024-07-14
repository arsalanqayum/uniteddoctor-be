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
        User::updateOrCreate(['email'=>'superadmin@admin.com'],[
            'first_name' => 'AdminFirstName',   // Replace with desired first name
            'last_name' => 'AdminLastName',     // Replace with desired last name
            
            'password' => 'admin', // Best practice: hashed password
            'user_type' => 'admin',             // Admin user type
            'city' => 'CityName',               // Replace with desired city
            'avatar' => 'path/to/avatar.jpg',   // Replace with desired path to avatar
            'gender' => 'male',                 // or 'female', 'other', etc.
            'lat' => 0.0000,                    // Replace with desired latitude
            'long' => 0.0000,                   // Replace with desired longitude
        ]);
    }
}
