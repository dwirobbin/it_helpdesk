<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\RoleEnum;
use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
            RoleEnum::IT_SUPPORT->value,
            RoleEnum::STAFF->value,
        ]) && auth()->check()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
        ]) && auth()->check()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Employee $employee): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
        ]) || (auth()->check() && $employee->user_id === $user->id)
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Employee $employee): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
        ]) && auth()->check()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateAccountStatus(User $user, Employee $employee): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
        ]) && auth()->check()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }
}
