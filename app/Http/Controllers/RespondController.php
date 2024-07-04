<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Respond;
use Illuminate\Http\Request;
use App\Enums\TicketStatusEnum;
use App\Http\Requests\RespondRequest;
use App\Http\Resources\TicketResource;
use App\Http\Resources\RespondResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class RespondController extends Controller
{
    public function create(Request $request)
    {
        Gate::authorize('create', Respond::class);

        $ticket = Ticket::query()
            ->select(['id', 'title', 'slug', 'description', 'image'])
            ->where('slug', '=', $request->query('ticket'))
            ->first();

        return app(TicketController::class)->index([
            'ticket' => TicketResource::make($ticket),
            'is_respond' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RespondRequest $request)
    {
        Gate::authorize('create', Respond::class);

        $validatedData = $request->validated();

        try {
            if ($request->hasFile('image')) {
                $path = 'image/responds';
                $file = $request->file('image')->store($path, 'public');
                $validatedData['image'] = str_replace("$path/", '', $file);
            }

            $respond = Respond::query()
                ->create([
                    ...$request->safe()->only(['ticket_id', 'text']),
                    ...[
                        'user_id' => auth()->id(),
                        'image' => $validatedData['image'],
                    ]
                ]);

            if ($respond->wasRecentlyCreated) {
                $respond->ticket()->update([
                    'status' => TicketStatusEnum::PROCESS->value,
                ]);
            }

            session()->flash('message', [
                'text' => 'Berhasil ditanggapi.',
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
     * Show the form for editing the specified resource.
     */
    public function edit(Respond $respond)
    {
        Gate::authorize('update', $respond);

        $respondData = $respond->load('ticket:id,title,slug,description,image');

        return app(TicketController::class)->index([
            'ticket' => TicketResource::make($respondData->ticket),
            'respond' => RespondResource::make($respondData),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RespondRequest $request, Respond $respond)
    {
        Gate::authorize('update', $respond);

        $validatedData = $request->validated();

        try {
            $originalPhoto = $respond->getRawOriginal('image');

            $image = match (true) {
                $request->hasFile('image') => $request->file('image')->hashName(),
                is_null($validatedData['image']) => null,
                default => $originalPhoto
            };

            $respond->update([
                ...$request->safe()->only(['ticket_id', 'text']),
                ...[
                    'image' => $image,
                    'user_id' => auth()->id(),
                ]
            ]);

            if ($request->hasFile('image') || is_null($image)) {
                $filePath = 'image/responds';
                if (Storage::disk('public')->exists($filePath . '/' . $originalPhoto)) {
                    Storage::disk('public')->delete($filePath . '/' . $originalPhoto);
                };

                if ($request->hasFile('image')) {
                    $request->file('image')->storeAs($filePath, $image, 'public');
                }
            }

            session()->flash('message', [
                'text' => 'Data Tanggapan berhasil diperbarui.',
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
}
