<?php

namespace App\Http\Resources;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (!$this->resource->relationLoaded('employee')) {
            $this->resource->load(['employee', 'employee.position', 'employee.department']);
        }

        $tickets = $this->resource->tickets()->exists() ? $this->resource->load('tickets') : null;

        return [
            'position' => [
                'viewAny' => $this->can('viewAny', Position::class),
                'create' => $this->can('create', Position::class),
                'update' => $this->can('update', $this->employee->position ?? null),
                'delete' => $this->can('delete', $this->employee->position ?? null),
            ],
            'department' => [
                'viewAny' => $this->can('viewAny', Department::class),
                'create' => $this->can('create', Department::class),
                'update' => $this->can('update', $this->employee->department ?? null),
                'delete' => $this->can('delete', $this->employee->department ?? null),
            ],
            'employee' => [
                'viewAny' => $this->can('viewAny', Employee::class),
                'create' => $this->can('create', Employee::class),
                'update' => $this->can('update', $this->employee),
                'delete' => $this->can('delete', $this->employee),
            ],
            'ticket' => [
                'viewAny' => $this->can('viewAny', Ticket::class),
                'view' => $this->can('view', $tickets),
                'create' => $this->can('create', Ticket::class),
                'update' => $this->can('update', $tickets),
                'delete' => $this->can('delete', $tickets),
                'viewAnyReport' => $this->can('viewAnyReport', Ticket::class),
                'createReport' => $this->can('createReport', Ticket::class),
            ],
        ];
    }
}
