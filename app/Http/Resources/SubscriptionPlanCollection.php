<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;

class SubscriptionPlanCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plan_for' => $this->plan_for,
            'name' => $this->name,
            'amount' => $this->amount,
            'features' => $this->features,
            'is_discount' => $this->is_discount,
            'image' => $this->image ? url(Storage::url($this->image)) : '',
        ];
    }
}
