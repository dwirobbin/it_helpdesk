<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Position;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PositionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
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
    public function update(User $user, Position $position): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
        ]) && auth()->check()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Position $position): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
        ]) && auth()->check()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }
}
