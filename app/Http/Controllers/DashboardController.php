<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Ticket;
use App\Enums\RoleEnum;
use App\Enums\TicketStatusEnum;
use App\Http\Resources\DashboardResource;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $tickets = Ticket::selectRaw('status, COUNT(*) as total')
            ->whereIn('status', [
                TicketStatusEnum::WAITING->value,
                TicketStatusEnum::RESPONSE->value,
                TicketStatusEnum::PROCESS->value,
                TicketStatusEnum::SOLVED->value,
            ])
            ->groupBy('status')
            ->get()
            ->toArray();

        $total = [
            'tickets' => $tickets,
            'employees' => User::query()->where('role_id', '!=', RoleEnum::SUPER_ADMIN)->count(),
        ];

        return Inertia::render('Dashboard', [
            'total' => DashboardResource::make($total),
        ]);
    }
}
