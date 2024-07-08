<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ticket' => TicketResource::make($this->whenLoaded('ticket')),
            'user' => UserResource::make($this->whenLoaded('user')),
            'text' => $this->text,
            'seen_for_staff' => $this->seen_for_staff,
            'seen_for_admin' => $this->seen_for_admin,
            'is_readed' => $this->is_readed,
            'created_at' => DateTimeResource::make($this->whenHas('created_at')),
            'updated_at' => DateTimeResource::make($this->whenHas('updated_at')),
        ];
    }
}
