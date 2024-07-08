<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'app_name' => $this->app_name,
            'app_footer' => $this->app_footer,
            'app_logo' => $this->app_logo,
            'company_name' => $this->company_name,
            'company_telp' => $this->company_telp,
            'company_address' => $this->company_address,
            'company_logo' => $this->company_logo,
        ];
    }
}
