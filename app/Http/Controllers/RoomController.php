<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\RoomRequest;
use App\Http\Resources\RoomResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(array $modalProps = [])
    {
        Gate::authorize('viewAny', Room::class);

        $perPage = request()->query('per_page') ?: 5;
        $searchTerm = trim(request()->query('search'));
        $column = request()->query('column') ?: 'created_at';
        $direction = request()->query('direction') ?: 'desc';

        $rooms = Room::query()
            ->select(['id', 'name', 'slug'])
            ->when($searchTerm, fn (Builder $query) => $query->where('name', 'LIKE', "%$searchTerm%"))
            ->when($column, fn (Builder $query) => $query->orderBy($column, $direction))
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Rooms/Index', array_merge([
            'rooms' => fn () => RoomResource::collection($rooms),
            'filters' => fn () => request()->only(['search', 'per_page', 'column', 'direction']),
        ], $modalProps));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomRequest $request)
    {
        Gate::authorize('create', Room::class);

        $validatedData = $request->validated();

        try {
            Room::query()
                ->create($validatedData);

            session()->flash('message', [
                'type' => 'success',
                'text' => 'Data berhasil ditambahkan.'
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'type' => 'error',
                'text' => 'Terjadi suatu kesalahan.'
            ]);
        }

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        Gate::authorize('update', $room);

        return $this->index(modalProps: [
            'room' => RoomResource::make($room),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoomRequest $request, Room $room)
    {
        Gate::authorize('update', $room);

        $validatedData = $request->validated();

        try {
            $room->update($validatedData);

            session()->flash('message', [
                'type' => 'success',
                'text' => 'Data berhasil diperbarui.'
            ]);
        } catch (\Throwable) {
            return back()->with('message', [
                'type' => 'error',
                'text' => 'Terjadi suatu kesalahan.'
            ]);
        }

        $redirectToPage = ($request->current_page <= $request->last_page)
            ? $request->current_page
            : $request->last_page;

        return to_route('rooms.index', ['page' => $redirectToPage]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Room $room)
    {
        Gate::authorize('delete', $room);

        try {
            $room->delete();

            session()->flash('message', [
                'text' => 'Data berhasil dihapus.',
                'type' => 'success',
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'text' => 'Terjadi suatu kesalahan.',
                'type' => 'error',
            ]);
        }

        $paginator = Room::paginate($request->get('limit'), ['id']);

        $redirectToPage = ($request->get('current_page') <= $paginator->lastPage())
            ? $request->get('current_page')
            : $paginator->lastPage();

        return to_route('rooms.index', ['page' => $redirectToPage]);
    }
}
