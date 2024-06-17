<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'photo' => $this->photo,
            'role' => $this->whenLoaded('role', new RoleResource($this->role)),
        ];
    }
}
