<?php

namespace App\Http\Resources;

use App\Models\DoctorService;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $paymentMethods = $this->paymentMethods();
        // $allPaymentMethods = [];
        // foreach ($paymentMethods as $paymentMethod) {
        //     $data['payment_method_id'] = $paymentMethod->id;
        //     $data['card_brand'] = $paymentMethod->card->brand;
        //     $data['card_last_digits'] = $paymentMethod->card->last4;
        //     array_push($allPaymentMethods, $data);
        // }
        // $defaultPaymentMethod = $this->defaultPaymentMethod();
        // $data['payment_method_id'] = $defaultPaymentMethod->id;
        // $data['card_brand'] = $defaultPaymentMethod->card->brand;
        // $data['card_last_digits'] = $defaultPaymentMethod->card->last4;
        // $defaultPaymentMethod = $data;
        $doctorServices = "";
        if ($this->role_id == 4) {
            $doctorServices = Service::whereHas('doctorServices', function ($query) {
                $query->where('doctor_id', $this->doctor->id);
            })->get();
        }
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'role_id' => $this->role_id,
            'roles' => $this->roles,
            'staff' => $this->staff,
            'doctor' => $this->doctor,
            'patient' => $this->patient,
            'subscription' => $this->subscriptions->where('stripe_status', 'active')->first(),
            'subscription_status' => $this->isSubscribedToAnyPlan(),
            // 'payment_methods' => $allPaymentMethods,
            // 'default_payment_methods' => $defaultPaymentMethod,
            'permissions' => $this->perimissions,
            'all_services' => Service::all(),
            'doctor_services' => $doctorServices,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
