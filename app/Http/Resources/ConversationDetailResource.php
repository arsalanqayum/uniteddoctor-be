<?php

namespace App\Http\Resources;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ConversationDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $messageModel = Message::where('conversation_id', $this->id)->latest()->first();
        $currentTimestamp = strtotime(now());
        $givenTimestamp = strtotime($messageModel->created_at);
        // dd($currentTimestamp, $givenTimestamp, 'in');
        $timeDifferenceInSeconds = $currentTimestamp - $givenTimestamp;
        $timeDifferenceInMinutes = round($timeDifferenceInSeconds / 60);
        $avatar = "";
        if (auth('api')->user()->id == $this->sender_id) {
            $avatar = User::find($this->sender_id)->avatar;
        }
        if (auth('api')->user()->id == $this->receiver_id) {
            $avatar = User::find($this->receiver_id)->avatar;
        }
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'sender_name' => User::find($this->sender_id)->name,
            'receiver_name' =>  User::find($this->receiver_id)->name,
            'sender_avatar' => User::find($this->sender_id)->avatar,
            'receiver_avatar' =>  User::find($this->receiver_id)->avatar,
            'last_message' =>  $messageModel->body,
            'appointment_id' => $this->appointment_id,
            'last_message_time' => $messageModel->created_at,

            'avatar' => $avatar ? $avatar : 'https://th.bing.com/th/id/R.79759c6986c4b4edaeb9ec4733cb00c3?rik=BqRXvbxKwVjx3g&pid=ImgRaw&r=0',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
