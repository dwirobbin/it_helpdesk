<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Position;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\PositionRequest;
use App\Http\Resources\PositionResource;
use Illuminate\Database\Eloquent\Builder;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(array $modalProps = [])
    {
        Gate::authorize('viewAny', Position::class);

        $perPage = request()->query('per_page') ?: 5;
        $searchTerm = trim(request()->query('search'));
        $column = request()->query('column') ?: 'created_at';
        $direction = request()->query('direction') ?: 'desc';

        $positions = Position::query()
            ->select(['id', 'title', 'slug'])
            ->when($searchTerm, fn (Builder $query) => $query->where('title', 'LIKE', "%$searchTerm%"))
            ->when($column, fn (Builder $query) => $query->orderBy($column, $direction))
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Positions/Index', array_merge([
            'positions' => fn () => PositionResource::collection($positions),
            'filters' => fn () => request()->only(['search', 'per_page', 'column', 'direction']),
        ], $modalProps));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PositionRequest $request)
    {
        Gate::authorize('create', Position::class);

        $validatedData = $request->validated();

        try {
            Position::query()
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
    public function edit(Position $position)
    {
        Gate::authorize('update', $position);

        return $this->index(modalProps: [
            'position' => PositionResource::make($position),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PositionRequest $request, Position $position)
    {
        Gate::authorize('update', $position);

        $validatedData = $request->validated();

        try {
            $position->update($validatedData);

            session()->flash('message', [
                'type' => 'success',
                'text' => 'Data berhasil diubah.'
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'type' => 'error',
                'text' => 'Terjadi suatu kesalahan.'
            ]);
        }

        $redirectToPage = ($request->current_page <= $request->last_page)
            ? $request->current_page
            : $request->last_page;

        return to_route('positions.index', ['page' => $redirectToPage]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        Gate::authorize('delete', $position);

        try {
            $position->delete();

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

        return back();
    }
}
