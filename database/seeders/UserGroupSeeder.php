<?php

namespace Database\Seeders;

use App\Models\InboxUserGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //default User Groups
        $groups = ['All', 'Doctors', 'Patient', 'Staff'];
        foreach ($groups as $key => $group) {
            InboxUserGroup::create(['group_name'=>$group]);
        }
    }
}
