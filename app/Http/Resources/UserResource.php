<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'photo' => $this->whenHas('photo', $this->photo),
            'role' => $this->whenLoaded('role', new RoleResource($this->role)),
            'is_active' => $this->whenHas('is_active', $this->is_active),
            'employee' => $this->whenLoaded('employee', fn () => new EmployeeResource($this->employee)),
        ];
    }
}
