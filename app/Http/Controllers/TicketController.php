<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Ticket;
use App\Enums\RoleEnum;
use Illuminate\Support\Arr;
use App\Enums\TicketStatusEnum;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TicketRequest;
use App\Http\Resources\RoomResource;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\TicketResource;
use App\Models\Room;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(array $modalProps = [])
    {
        Gate::authorize('viewAny', Ticket::class);

        $perPage = request()->query('per_page') ?: 5;
        $searchTerm = trim(request()->query('search'));
        $column = request()->query('column') ?: 'created_at';
        $direction = request()->query('direction') ?: 'desc';

        $tickets = Ticket::query()
            ->with(['user:id,name,slug', 'ticketChats'])
            ->select(['id', 'ticket_number', 'title', 'slug', 'status', 'user_id', 'created_at'])
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->where('ticket_number', 'LIKE', "%$searchTerm%")
                    ->orWhere('title', 'LIKE', "%$searchTerm%")
                    ->orWhere('status', 'LIKE', "%$searchTerm%")
                    ->orWhere('created_at', 'LIKE', "%$searchTerm%");
            })
            ->when(auth()->user()->role_id === RoleEnum::STAFF->value, function ($query) {
                $query->where('user_id', '=', auth()->id());
            })
            ->when(auth()->user()->role_id === RoleEnum::IT_SUPPORT->value, function ($query) {
                $query->where('status', '!=', TicketStatusEnum::WAITING);
            })
            ->when(auth()->user()->role_id === RoleEnum::SUPER_ADMIN->value, function ($query) {
                $query->where('status', '=', TicketStatusEnum::WAITING);
            })
            ->orderBy($column, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Tickets/Index', array_merge([
            'tickets' => fn () => TicketResource::collection($tickets),
        ], $modalProps));
    }

    public function create()
    {
        $rooms = Room::query()
            ->select(['slug', 'name'])
            ->get();

        return $this->index(modalProps: [
            'rooms' => RoomResource::collection($rooms),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketRequest $request)
    {
        Gate::authorize('create', Ticket::class);

        $validatedData = $request->validated();

        try {
            $image = $request->hasFile('image')
                ? $request->file('image')->hashName()
                : null;

            $room = Room::query()
                ->where('slug', '=', data_get($validatedData, 'room.slug'))
                ->firstOrFail();

            Ticket::query()
                ->create([
                    ...Arr::only($validatedData, ['title', 'description', 'user_id', 'status']),
                    'image' => $image,
                    'room_id' => $room->id,
                ]);

            if ($request->hasFile('image')) {
                $path = 'image/complaints';
                $request->file('image')->storeAs($path, $image, 'public');
            }

            session()->flash('message', [
                'text' => 'Tiket berhasil dibuat.',
                'type' => 'success',
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'text' => 'Terjadi suatu kesalahan.',
                'type' => 'error',
            ]);
        }

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);

        $data = $ticket->load([
            'user:id,name,email',
            'room:id,name',
            'user.employee:id,user_id,position_id,department_id,nik',
            'user.employee.position:id,title',
            'user.employee.department:id,section',
            'respond:id,ticket_id,user_id,text,image',
            'respond.user:id,name',
        ]);

        return Inertia::render('Tickets/Show', [
            'ticket' =>  new TicketResource($data),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $data = $ticket->load(['room:id,slug,name']);

        $rooms = Room::query()
            ->select(['slug', 'name'])
            ->get();

        return $this->index([
            'ticket' => TicketResource::make($data),
            'rooms' => RoomResource::collection($rooms),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $validatedData = $request->validated();

        try {
            $originalPhoto = $ticket->getRawOriginal('image');

            $image = match (true) {
                $request->hasFile('image') => $request->file('image')->hashName(),
                is_null($validatedData['image']) => null,
                default => $originalPhoto
            };

            $room = Room::query()
                ->where('slug', '=', data_get($validatedData, 'room.slug'))
                ->firstOrFail();

            $ticket->update([
                ...Arr::only($validatedData, ['title', 'description', 'user_id', 'status',]),
                'image' => $image,
                'room_id' => $room->id,
            ]);

            if ($request->hasFile('image')) {
                $filePath = 'image/complaints';
                if (Storage::disk('public')->exists($filePath . '/' . $originalPhoto)) {
                    Storage::disk('public')->delete($filePath . '/' . $originalPhoto);
                };

                if ($request->hasFile('image')) {
                    $request->file('image')->storeAs($filePath, $image, 'public');
                }
            }

            session()->flash('message', [
                'text' => 'Tiket berhasil diperbarui.',
                'type' => 'success',
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'text' => 'Terjadi suatu kesalahan.',
                'type' => 'error',
            ]);
        }

        $redirectToPage = ($request->current_page <= $request->last_page)
            ? $request->current_page
            : $request->last_page;

        return to_route('tickets.index', ['page' => $redirectToPage]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        Gate::authorize('delete', $ticket);

        try {
            $respond = $ticket->respond()->firstOrFail();

            $complaintImage = $ticket->getRawOriginal('image');
            $respondImage = $respond->getRawOriginal('image');

            DB::transaction(function () use ($ticket, $respond) {
                $respond->delete();
                $ticket->delete();
            });

            $complaintFilePath = 'image/complaints';
            if (Storage::disk('public')->exists($complaintFilePath . '/' . $complaintImage)) {
                Storage::disk('public')->delete($complaintFilePath . '/' . $complaintImage);
            };

            $respondFilePath = 'image/responds';
            if (Storage::disk('public')->exists($respondFilePath . '/' . $respondImage)) {
                Storage::disk('public')->delete($respondFilePath . '/' . $respondImage);
            };

            session()->flash('message', [
                'text' => 'Tiket berhasil dihapus.',
                'type' => 'success',
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'text' => 'Terjadi suatu kesalahan.',
                'type' => 'error',
            ]);
        }

        return back();
    }

    public function confirm(Ticket $ticket)
    {
        Gate::authorize('updateStatusToConfirm', $ticket);

        try {
            $ticket->update([
                'status' => TicketStatusEnum::RESPONSE->value,
            ]);

            session()->flash('message', [
                'text' => 'Tiket berhasil dikonfirmasi.',
                'type' => 'success',
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'text' => 'Terjadi suatu kesalahan.',
                'type' => 'error',
            ]);
        }

        return back();
    }

    public function solved(Ticket $ticket)
    {
        Gate::authorize('updateStatusToSolve', $ticket);

        try {
            $ticket->update([
                'status' => TicketStatusEnum::SOLVED->value,
            ]);

            session()->flash('message', [
                'text' => 'Tiket berhasil diselesaikan dan telah ditutup.',
                'type' => 'success',
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'text' => 'Terjadi suatu kesalahan.',
                'type' => 'error',
            ]);
        }

        return back();
    }
}
