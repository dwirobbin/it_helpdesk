<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Models\Respond;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Department;
use App\Policies\TicketPolicy;
use App\Policies\RespondPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\PositionPolicy;
use App\Policies\DepartmentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Position::class, PositionPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(Respond::class, RespondPolicy::class);
    }
}
