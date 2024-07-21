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
            'slug' => $this->whenHas('slug'),
            'email' => $this->whenHas('email'),
            'photo' => $this->whenHas('photo'),
            'role' => RoleResource::make($this->whenLoaded('role')),
            'is_active' => $this->whenHas('is_active'),
            'employee' => EmployeeResource::make($this->whenLoaded('employee')),
        ];
    }
}
