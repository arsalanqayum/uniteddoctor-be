<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'service_name' => 'One-Time Visit',
                'service_type' => 1,
                'service_percentage' => 0,
                'service_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_name' => 'Urgent Care',
                'service_type' => 3,
                'service_percentage' => 0,
                'service_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_name' => 'Non-Urgent Care',
                'service_type' => 1,
                'service_percentage' => 0,
                'service_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_name' => 'Primary Care',
                'service_type' => 1,
                'service_percentage' => 0,
                'service_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_name' => 'Holistic Medicine',
                'service_type' => 1,
                'service_percentage' => 0,
                'service_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_name' => "Women's Care",
                'service_type' => 1,
                'service_percentage' => 0,
                'service_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_name' => "Men's Care",
                'service_type' => 1,
                'service_percentage' => 0,
                'service_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_name' => 'Second Opinion',
                'service_type' => 1,
                'service_percentage' => 0,
                'service_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_name' => 'Private Practice',
                'service_type' => 1,
                'service_percentage' => 0,
                'service_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_name' => 'Senior Care',
                'service_type' => 1,
                'service_percentage' => 0,
                'service_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_name' => 'Weight Loss',
                'service_type' => 1,
                'service_percentage' => 0,
                'service_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_name' => 'DNA Consultation',
                'service_type' => 1,
                'service_percentage' => 0,
                'service_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_name' => 'Mental Health',
                'service_type' => 1,
                'service_percentage' => 0,
                'service_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $service = new Service();
        $service->insert($data);
    }
}
