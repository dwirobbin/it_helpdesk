<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;
use App\Enums\RoleEnum;
use Illuminate\Auth\Access\Response;

class TicketPolicy
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
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
            RoleEnum::IT_SUPPORT->value,
        ]) || (auth()->check() && $ticket->user_id === $user->id)
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return in_array($user->role_id, [
            RoleEnum::STAFF->value,
        ]) && auth()->check()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): Response
    {
        return in_array($user->role_id, [
            RoleEnum::STAFF->value,
        ]) || (auth()->check() && $ticket->user_id === $user->id)
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
            RoleEnum::IT_SUPPORT->value,
        ]) && auth()->check()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Determine whether the user can update status to confirm the model.
     */
    public function updateStatus(User $user, Ticket $ticket): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
            RoleEnum::IT_SUPPORT->value,
        ]) && auth()->check()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Determines whether users can view reports.
     */
    public function viewAnyReport(User $user): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
            RoleEnum::IT_SUPPORT->value,
        ]) && auth()->check()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Determine <whether></whether> the user can create reports.
     */
    public function createReport(User $user): Response
    {
        return in_array($user->role_id, [
            RoleEnum::SUPER_ADMIN->value,
            RoleEnum::IT_SUPPORT->value,
        ]) && auth()->check()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }
}
