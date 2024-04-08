<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MessageResource extends JsonResource
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
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'sender_name' => User::find($this->sender_id)->name ?? '',
            'receiver_name' =>  User::find($this->receiver_id)->name,
            'sender_avatar' => User::find($this->sender_id)->avatar,
            'receiver_avatar' =>  User::find($this->receiver_id)->avatar,
            'conversation_id' => $this->conversation_id,
            'body' => $this->body,
            'read' => $this->read,
            'attachment' => $this->attachment ? url(Storage::url($this->attachment)) : '',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
