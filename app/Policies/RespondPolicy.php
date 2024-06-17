<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\RoleEnum;
use App\Models\Respond;
use Illuminate\Auth\Access\Response;

class RespondPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
            RoleEnum::IT_SUPPORT->value,
        ]) && auth()->check()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Respond $respond): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
            RoleEnum::IT_SUPPORT->value,
        ]) || (auth()->check() && $respond->user_id === $user->id)
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }
}
