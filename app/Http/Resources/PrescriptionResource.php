<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
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
            'location' => $this->location,
            'pharmacy' => $this->pharmacy,
            'prescription_no' => $this->prescription_no,
            'prescriptionData' => $this->prescriptionData,
            'slip_pdf_url' => route('prescription.getPrescriptionPDF', $this->id),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
