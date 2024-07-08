<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'nik' => $this->whenHas('nik'),
            'user' => UserResource::make($this->whenLoaded('user')),
            'position' => PositionResource::make($this->whenLoaded('position')),
            'department' => DepartmentResource::make($this->whenLoaded('department')),
        ];
    }
}
