<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Super Admin', 'Admin', 'Customer Support Parsonnel', 'Doctor', 'Patient', 'Member'];
        foreach ($roles as $key => $role) {
            // Replace spaces with dashes
            $role_name = str_replace(' ', '-', $role);

            // Convert the string to lowercase
            $role_name = strtolower($role_name);
            Role::create([
                'name' => $role_name,
                'display_name' => $role,
            ]);
        }
    }
}
