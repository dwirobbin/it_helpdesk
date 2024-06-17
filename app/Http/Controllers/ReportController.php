<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatusEnum;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Ticket;
use App\Http\Requests\ReportRequest;
use App\Http\Resources\TicketResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function indexTicketReport(array $props = [])
    {
        Gate::authorize('viewAnyReport', Ticket::class);

        return Inertia::render('Reports/Ticket', array_merge([
            'types' => fn () => TicketStatusEnum::values(),
        ], $props));
    }

    public function createTicketReport(ReportRequest $request)
    {
        Gate::authorize('createReport', Ticket::class);

        $validatedData = $request->validated();

        $validatedData['start_date'] = Carbon::createFromFormat('d/m/Y', $validatedData['start_date'])->format('Y-m-d');
        $validatedData['end_date'] = Carbon::createFromFormat('d/m/Y', $validatedData['end_date'])->format('Y-m-d');

        $tickets = Ticket::query()
            ->with(['user:id,name'])
            ->whereDate('created_at', '>=', $validatedData['start_date'])
            ->whereDate('updated_at', '<=', $validatedData['end_date'])
            ->when($request->filled('type'), fn (Builder $query) => $query->where('status', '=', $validatedData['type']))
            ->get();

        return $this->indexTicketReport([
            'tickets' => fn () => TicketResource::collection($tickets),
        ]);
    }
}
