<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            "patient_id" => $this->patient_id,
            "doctor_id"=> $this->doctor_id,
            'amount'=>$this->amount,
            'invoice_no'=>$this->invoice_no,
            'status'=>$this->invoice_status,
            'billing_name'=>$this->patient->first_name. ' '.$this->patient->last_name,
            "appointment_id"=> $this->appointment_id,
            'patient'=>$this->patient,
            "due_date"=> $this->due_date,
            "items"=> $this->invoiceItems,
            "invoice_status"=> $this->invoic_status,
            'slip_pdf_url' => route('invoice.getInvoicePDF', $this->id),
            'insurance'=>$this->insurance ?? '',
        ];
    }
}
