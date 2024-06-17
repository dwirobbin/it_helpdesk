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
            'ticket_number' => $this->whenHas('ticket_number', $this->ticket_number),
            'title' => $this->whenHas('title', $this->title),
            'slug' => $this->slug,
            'description' => $this->whenHas('description', $this->description),
            'image' => $this->whenHas('image', $this->image),
            'user' => $this->whenLoaded('user', fn () => UserResource::make($this->user)),
            'status' => $this->whenHas('status', $this->status),
            'respond' => $this->whenLoaded('respond', fn () => RespondResource::make($this->respond)),
            'created_at' => $this->whenHas('created_at', DateTimeResource::make($this->created_at)),
            'updated_at' => $this->whenHas('updated_at', DateTimeResource::make($this->updated_at)),
        ];
    }
}
