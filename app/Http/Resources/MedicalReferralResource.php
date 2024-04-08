<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalReferralResource extends JsonResource
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
            'recommended_by_doctor_id' => $this->recommended_by_doctor_id,
            'recommended_by_doctor_name' => $this->recommendedByDoctor->first_name . ' ' . $this->recommendedByDoctor->last_name,
            'doctor_name' => $this->doctor_name,        
            'doctor_clinic' => $this->doctor_clinic,        
            'doctor_phone' => $this->doctor_phone,        
            'email' => $this->doctor_name,        
            'message' => $this->message,
            'slip_pdf_url' => route('appointment.referral.getMedicalReferralPDF', $this->id),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
