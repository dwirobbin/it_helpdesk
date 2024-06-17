<?php

namespace App\Http\Resources;

use App\Enums\TicketStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tickets = $this['tickets'];

        return [
            'tickets' => [
                'waiting' => $this->getTicketTotal($tickets, TicketStatusEnum::WAITING),
                'processing' => $this->getTicketTotal($tickets, TicketStatusEnum::RESPONSE),
                'in_process' => $this->getTicketTotal($tickets, TicketStatusEnum::PROCESS),
                'completed' => $this->getTicketTotal($tickets, TicketStatusEnum::SOLVED),
            ],
            'employees' => $this['employees'],
        ];
    }

    private function getTicketTotal($tickets, $status)
    {
        $ticket = collect($tickets)->firstWhere('status', $status->value);
        return $ticket ? $ticket['total'] : 0;
    }
}
