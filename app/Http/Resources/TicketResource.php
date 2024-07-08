<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'ticket_number' => $this->whenHas('ticket_number'),
            'title' => $this->whenHas('title'),
            'slug' => $this->slug,
            'description' => $this->whenHas('description'),
            'image' => $this->whenHas('image'),
            'user' => UserResource::make($this->whenLoaded('user')),
            'status' => $this->whenHas('status'),
            'respond' => RespondResource::make($this->whenLoaded('respond')),
            'ticket_chats' => TicketChatResource::collection($this->whenLoaded('ticketChats')),
            'created_at' => DateTimeResource::make($this->whenHas('created_at')),
            'updated_at' => DateTimeResource::make($this->whenHas('updated_at')),
        ];
    }
}
