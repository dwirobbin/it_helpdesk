<?php

namespace App\Http\Middleware;

use App\Http\Resources\AppSettingResource;
use App\Http\Resources\AuthResource;
use App\Http\Resources\PermissionResource;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user()
                    ? AuthResource::make($request->user()->load('role:id,name'))
                    : null,
            ],
            'setting' => !is_null(AppSetting::query()->first())
                ? AppSettingResource::make(AppSetting::query()->first())
                : null,
            'flash' => $request->session()->get('message'),
        ];
    }
}
