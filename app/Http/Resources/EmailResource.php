<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailResource extends JsonResource
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
            'date' => Carbon::parse($this->created_at)->format('m/d/Y'),
            'html' => $this->body,
            'subject' => $this->subject,
            'from' => $this->from,
            'to' => $this->to,
            'starred' => $this->starred == 1 ? true : false,
            'bookmarked' => $this->bookmarked == 1 ? true : false,
            'attachments' => $this->attachments,
        ];
    }
}
