<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use App\Enums\RoleEnum;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\EmployeeRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\PositionResource;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\DepartmentResource;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Employee::class);

        $perPage = $request->query('per_page') ?: 5;
        $searchTerm = trim($request->query('search'));
        $column = $request->query('column') ?: 'created_at';
        $direction = $request->query('direction') ?: 'desc';

        $employees = Employee::query()
            ->with([
                'user:id,name,slug,email,role_id,is_active',
                'user.role:id,name',
                'position:id,title',
                'department:id,section',
            ])
            ->select(['id', 'nik', 'user_id', 'position_id', 'department_id'])
            ->when(
                $searchTerm,
                fn (Builder $query) =>
                $query
                    ->where('nik', 'LIKE', "%$searchTerm%")
                    ->orWhereRelation('user', 'name', 'LIKE', "%$searchTerm%")
                    ->orWhereRelation('user.role', 'name', 'LIKE', "%$searchTerm%")
                    ->orWhereRelation('position', 'title', 'LIKE', "%$searchTerm%")
                    ->orWhereRelation('department', 'section', 'LIKE', "%$searchTerm%")
            )
            ->when(
                in_array($column, ['users.name', 'users.email']),
                fn (Builder $query) => $query->orderBy(
                    User::query()
                        ->select($column)
                        ->whereColumn('users.id', 'employees.user_id')
                        ->limit(1),
                    $direction
                )
            )
            ->when(
                $column === 'positions.title',
                fn (Builder $query) => $query->orderBy(
                    Position::query()
                        ->select('positions.title')
                        ->whereColumn('positions.id', 'employees.position_id')
                        ->limit(1),
                    $direction
                )
            )
            ->when(
                $column === 'departments.section',
                fn (Builder $query) => $query->orderBy(
                    Department::query()
                        ->select('departments.section')
                        ->whereColumn('departments.id', 'employees.department_id')
                        ->limit(1),
                    $direction
                )
            )
            ->when(
                $column === 'roles.name',
                fn (Builder $query) => $query->orderBy(
                    Role::query()
                        ->select('roles.name')
                        ->leftJoin('users', 'roles.id', '=', 'users.role_id')
                        ->whereColumn('users.id', 'employees.user_id')
                        ->limit(1),
                    $direction
                )
            )
            ->when(
                !in_array($column, ['users.name', 'users.email', 'roles.name', 'positions.title', 'departments.section']),
                fn (Builder $query) => $query->orderBy($column, $direction)
            )
            ->when(in_array(auth()->user()->role_id, [RoleEnum::IT_SUPPORT->value, RoleEnum::STAFF->value]), function ($query) {
                $query->where('user_id', '=', auth()->id());
            })
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Employees/Index', [
            'employees' => fn () => EmployeeResource::collection($employees),
            'filters' => fn () => $request->only(['search', 'per_page', 'column', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Employee::class);

        $positions = Position::query()
            ->select(['id', 'title', 'slug'])
            ->get();

        $departments = Department::query()
            ->select(['id', 'section', 'slug'])
            ->get();

        $roles = Role::query()
            ->select(['id', 'name'])
            ->where('id', '!=', RoleEnum::SUPER_ADMIN->value)
            ->get();

        return Inertia::render('Employees/Create', [
            'positions' => PositionResource::collection($positions),
            'departments' => DepartmentResource::collection($departments),
            'roles' => RoleResource::collection($roles),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request)
    {
        Gate::authorize('create', Employee::class);

        $validatedData = $request->validated();

        try {
            DB::transaction(function () use ($validatedData) {
                $user = User::query()
                    ->create([
                        'name' => $validatedData['name'],
                        'email' => $validatedData['email'],
                        'password' => $validatedData['password'],
                        'role_id' => data_get($validatedData, 'role.id'),
                        'is_active' => $validatedData['is_active_account'],
                    ]);

                Employee::query()->create([
                    'user_id' => $user->id,
                    'position_id' => data_get($validatedData, 'position.id'),
                    'department_id' => data_get($validatedData, 'department.id'),
                    'nik' => $validatedData['nik'],
                ]);
            });

            session()->flash('message', [
                'type' => 'success',
                'text' => 'Data berhasil disimpan.'
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'type' => 'error',
                'text' => 'Terjadi suatu kesalahan.'
            ]);
        }

        return to_route('employees.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        Gate::authorize('update', $employee);

        $data = $employee->load([
            'user:id,name,slug,email,role_id,is_active',
            'user.role:id,name',
            'position:id,title,slug',
            'department:id,section,slug'
        ]);

        $positions = Position::query()
            ->select(['id', 'title', 'slug'])
            ->get();

        $departments = Department::query()
            ->select(['id', 'section', 'slug'])
            ->get();

        $roles = Role::query()
            ->select(['id', 'name'])
            ->where('id', '!=', RoleEnum::SUPER_ADMIN->value)
            ->get();

        return Inertia::render('Employees/Edit', [
            'employee' => EmployeeResource::make($data),
            'positions' => PositionResource::collection($positions),
            'departments' => DepartmentResource::collection($departments),
            'roles' => RoleResource::collection($roles),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        Gate::authorize('update', $employee);

        $validatedData = $request->validated();

        try {
            $user = auth()->user();

            if ($user->role_id === RoleEnum::SUPER_ADMIN->value) {
                $validatedData['role']['id'] = data_get($validatedData, 'role.id');
                $validatedData['is_active_account'] = $validatedData['is_active_account'];
            } else {
                $validatedData['role']['id'] = $user->role_id;
                $validatedData['is_active_account'] = $user->is_active;
            }

            DB::transaction(function () use ($employee, $validatedData) {
                $employee->user()->update([
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'password' => $validatedData['password'],
                    'role_id' => $validatedData['role']['id'],
                    'is_active' => $validatedData['is_active_account'],
                ]);

                $employee->update([
                    'user_id' => $employee->user->id,
                    'position_id' => data_get($validatedData, 'position.id'),
                    'department_id' => data_get($validatedData, 'department.id'),
                    'nik' => $validatedData['nik'],
                ]);
            });

            session()->flash('message', [
                'type' => 'success',
                'text' => 'Data berhasil diperbarui.'
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'type' => 'error',
                'text' => 'Terjadi suatu kesalahan.'
            ]);
        }

        return to_route('employees.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        Gate::authorize('delete', $employee);

        try {
            $user = $employee->user()->firstOrFail();

            $userAvatar = $user->getRawOriginal('photo');

            DB::transaction(function () use ($employee, $user) {
                $user->delete();
                $employee->delete();
            });

            $avatarFilePath = 'image/avatars';
            if (Storage::disk('public')->exists($avatarFilePath . '/' . $userAvatar)) {
                Storage::disk('public')->delete($avatarFilePath . '/' . $userAvatar);
            };

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

    /**
     * Update the value of is_active for status account in storage.
     */
    public function changeAccountStatus(Request $request, Employee $employee)
    {
        Gate::authorize('updateAccountStatus', $employee);

        try {
            $validatedData = $request->validate([
                'is_active_account' => ['required', 'boolean'],
            ], [
                'required' => ':attribute harus diisi.',
                'boolean' => ':attribute harus bernilai benar atau salah.',
            ], [
                'is_active_account' => 'Akun Aktif?',
            ]);

            $employee->user()->update([
                'is_active' => $validatedData['is_active_account'],
            ]);
        } catch (ValidationException $e) {
            return session()->flash('message', [
                'type' => 'error',
                'text' => $e->getMessage()
            ]);
        } catch (\Throwable) {
            return session()->flash('message', [
                'type' => 'error',
                'text' => 'Terjadi suatu kesalahan.'
            ]);
        }

        return session()->flash('message', [
            'type' => 'success',
            'text' => 'Status Akun berhasil diperbarui.'
        ]);
    }
}
