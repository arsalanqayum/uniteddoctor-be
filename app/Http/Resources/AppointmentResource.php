<?php

namespace App\Http\Resources;

use App\Models\Appointment;
use App\Models\AppointmentClinicalNote;
use App\Models\AppointmentMedicalTest;
use App\Models\AppointmentPatientNotes;
use App\Models\AppointmentPractioner;
use App\Models\Diagnosis;
use App\Models\MedicalHistory;
use App\Models\MedicalReferral;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'doctor_id' => $this->doctor_id,
            
            'doctor_name' => $this->doctor->first_name,
            'patient_id' => $this->patient_id,
            
            'department_id'=>$this->doctor->department_id,
            'patient_name' => $this->patient->first_name,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'day' => $this->day,
            'patient_user_id' => $this->patient->user_id,
            'other'=>Appointment::where('patient_id',$this->patient_id)->where('id','!=',$this->id)->with(['doctor','patient'])->orderBy('date','desc')->get(),
            'allPrescription'=> $this->prescriptionData,
            'clinical_notes'=> $this->clinicalNotes,
            'medical_test'=> $this->medicalTest,
            'medical_referral'=> $this->medicalReferral,
            'follow_up_appointment'=> Appointment::where("appointment_id",$this->id)->with(['doctor','patient'])->get(),
            'appointment_id' => $this->appointment_id,
            'practitioner_notes' => $this->PractionerNote ?? [],
            'patient'=> $this->patientWithUser,
            'patientNotes' => $this->patientNotes,
            'invoice' => $this->invoice,
            'daignoses' => Diagnosis::where('appointment_id',$this->id)->with(['patient','doctor'])->get(),
            'medical_history' => $this->medicalHistory() ?? [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'doctor' => $this->doctor
        ];
    }
}
