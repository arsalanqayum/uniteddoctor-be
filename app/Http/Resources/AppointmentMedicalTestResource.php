<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentMedicalTestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'appointment_id' => $this->appointment_id,
            'doctor_id' => $this->appointment->doctor_id,
            'doctor_name' => $this->appointment->doctor->first_name . ' ' . $this->appointment->doctor->last_name,
            'patient_id' => $this->appointment->patient_id,
            'patient_name' => $this->appointment->patient->first_name . ' ' . $this->appointment->patient->last_name,            
            'test_name' => $this->test_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
