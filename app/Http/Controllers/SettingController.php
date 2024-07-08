<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\SettingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Handle the edit view.
     */
    public function edit()
    {
        Gate::authorize('viewAny', Setting::class);

        return Inertia::render('Settings/Edit');
    }

    /**
     * Handle the updateAppInformation spesified data in storage.
     */
    public function updateAppInformation(SettingRequest $request): RedirectResponse
    {
        $setting = Setting::query()->first();
        if (is_null($setting)) {
            return back()->with('message', [
                'type' => 'error',
                'text' => 'Terjadi suatu kesalahan.',
            ]);
        }

        Gate::authorize('update', $setting);

        $validatedData = $request->validated();

        try {
            if ($request->hasFile('app_logo')) {
                $filePath = 'image';
                if (Storage::disk('public')->exists($filePath . '/' . $setting->getRawOriginal('app_logo'))) {
                    Storage::disk('public')->delete($filePath . '/' . $setting->getRawOriginal('app_logo'));
                };

                $newFile = $request->file('app_logo')->store($filePath, 'public');
                $validatedData['app_logo'] = str_replace("$filePath/", '', $newFile);
            } elseif (is_null($validatedData['app_logo'])) {
                $filePath = 'image';
                if (Storage::disk('public')->exists($filePath . '/' . $setting->getRawOriginal('app_logo'))) {
                    Storage::disk('public')->delete($filePath . '/' . $setting->getRawOriginal('app_logo'));
                };

                $validatedData['app_logo'] =  null;
            } else {
                $validatedData['app_logo'] =  $setting->getRawOriginal('app_logo');
            }

            $setting->update(Arr::only($validatedData, [
                'app_name', 'app_footer', 'app_logo',
            ]));

            session()->flash('message', [
                'type' => 'success',
                'text' => 'Berhasil diperbarui.',
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'type' => 'error',
                'text' => 'Terjadi suatu kesalahan.',
            ]);
        }

        return back();
    }

    /**
     * Handle the updateAppInformation spesified data in storage.
     */
    public function updateCompanyInformation(SettingRequest $request)
    {
        $setting = Setting::query()->first();
        if (is_null($setting)) {
            return back()->with('message', [
                'type' => 'error',
                'text' => 'Terjadi suatu kesalahan.',
            ]);
        }

        Gate::authorize('update', $setting);

        $validatedData = $request->validated();

        try {
            if ($request->hasFile('company_logo')) {
                $filePath = 'image';
                if (Storage::disk('public')->exists($filePath . '/' . $setting->getRawOriginal('company_logo'))) {
                    Storage::disk('public')->delete($filePath . '/' . $setting->getRawOriginal('company_logo'));
                };

                $newFile = $request->file('company_logo')->store($filePath, 'public');
                $validatedData['company_logo'] = str_replace("$filePath/", '', $newFile);
            } elseif (is_null($validatedData['company_logo'])) {
                $filePath = 'image';
                if (Storage::disk('public')->exists($filePath . '/' . $setting->getRawOriginal('company_logo'))) {
                    Storage::disk('public')->delete($filePath . '/' . $setting->getRawOriginal('company_logo'));
                };

                $validatedData['company_logo'] =  null;
            } else {
                $validatedData['company_logo'] =  $setting->getRawOriginal('company_logo');
            }

            $setting->update(Arr::only($validatedData, [
                'company_name', 'company_telp', 'company_address', 'company_logo',
            ]));

            session()->flash('message', [
                'type' => 'success',
                'text' => 'Berhasil diperbarui.',
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'type' => 'error',
                'text' => 'Terjadi suatu kesalahan.',
            ]);
        }

        return back();
    }
}
