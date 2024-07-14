<?php

namespace Database\Seeders;

use App\Models\DoctorSpeciality;
use App\Models\Specialization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DoctorSpecilities extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        $specialties = [
            'Allergy & Immunology',
            'Anesthesiology',
            'Cardiology',
            'Dermatology',
            'Emergency Medicine',
            'Endocrinology',
            'Gastroenterology',
            'Geriatrics',
            'Hematology',
            'Infectious Disease',
            'Internal Medicine',
            'Neonatology',
            'Nephrology',
            'Neurology',
            'Obstetrics & Gynecology',
            'Oncology',
            'Ophthalmology',
            'Orthopedics',
            'Otolaryngology',
            'Pathology',
            'Pediatrics',
            'Physical Medicine & Rehabilitation',
            'Plastic Surgery',
            'Psychiatry',
            'Pulmonology',
            'Radiology',
            'Rheumatology',
            'Sleep Medicine',
            'Sports Medicine',
            'Surgery',
            'Urology',
            // Add more specialties as needed to reach 100
        ];
        foreach ($specialties as $specialtyName) {
            // Use Eloquent to create a new MedicalSpecialty record
            Specialization::create([
                'name' => $specialtyName,
            ]);
        }
    }
}
