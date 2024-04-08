<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'username' => $this->user->username,
            'education' => $this->education,
            'mobile' => $this->mobile,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'designation' => $this->designation,
            'department_id' => $this->department_id,
            'department_name' => optional($this->department)->name,
            'address' => $this->address,
            'biography' => $this->biography,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'avatar' => $this->user->avatar,
            'postal_code' => $this->postal_code,
            'status' => $this->status,
            'practice_state' => $this->practice_state,
            'online_status' => userOnlineStatus($this->user_id),
            'license_image' => $this->license_image ? url(Storage::url($this->license_image)) : '',
            'advisor' => $this->advisor ? url(Storage::url($this->advisor)) : '',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return array[]
     */
    public function withResponse($request, $response)
    {
        return [
            'pagination' => [
                'total' => $this->resource->total(),
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
                'from' => $this->resource->firstItem(),
                'to' => $this->resource->lastItem(),
            ],
        ];
    }
}
