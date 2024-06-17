<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppSettingResource extends JsonResource
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
            'slug' => $this->slug,
            'footer' => $this->footer,
            'company_name' => $this->company_name,
            'company_telp' => $this->company_telp,
            'company_address' => $this->company_address,
            'logo' => $this->logo,
        ];
    }
}
