<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Department;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\DepartmentRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\DepartmentResource;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(array $modalProps = [])
    {
        Gate::authorize('viewAny', Department::class);

        $perPage = request()->query('per_page') ?: 5;
        $searchTerm = trim(request()->query('search'));
        $column = request()->query('column') ?: 'created_at';
        $direction = request()->query('direction') ?: 'desc';

        $departments = Department::query()
            ->select(['id', 'section', 'slug'])
            ->when($searchTerm, fn (Builder $query) => $query->where('section', 'LIKE', "%$searchTerm%"))
            ->when($column, fn (Builder $query) => $query->orderBy($column, $direction))
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Departments/Index', array_merge([
            'departments' => fn () => DepartmentResource::collection($departments),
            'filters' => fn () => request()->only(['search', 'per_page', 'column', 'direction']),
        ], $modalProps));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepartmentRequest $request)
    {
        Gate::authorize('create', Department::class);

        $validatedData = $request->validated();

        try {
            Department::query()
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
    public function edit(Department $department)
    {
        Gate::authorize('update', $department);

        return $this->index(modalProps: [
            'department' => DepartmentResource::make($department),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepartmentRequest $request, Department $department)
    {
        Gate::authorize('update', $department);

        $validatedData = $request->validated();

        try {
            $department->update($validatedData);

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

        return to_route('departments.index', ['page' => $redirectToPage]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        Gate::authorize('delete', $department);

        try {
            $department->delete();

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
