<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AppSettingRequest;

class AppSettingController extends Controller
{
    /**
     * Handle the edit view.
     */
    public function edit()
    {
        Gate::authorize('viewAny', AppSetting::class);

        return Inertia::render('Settings/Application');
    }

    /**
     * Handle the update spesified data in storage.
     */
    public function update(AppSettingRequest $request)
    {
        $appSetting = AppSetting::query()->first();

        Gate::authorize('update', $appSetting);

        $validatedData = $request->validated();

        try {
            if ($request->hasFile('logo')) {
                $filePath = 'image';
                if (Storage::disk('public')->exists($filePath . '/' . $appSetting->getRawOriginal('logo'))) {
                    Storage::disk('public')->delete($filePath . '/' . $appSetting->getRawOriginal('logo'));
                };

                $newFile = $request->file('logo')->store($filePath, 'public');
                $validatedData['logo'] = str_replace("$filePath/", '', $newFile);
            } elseif (is_null($validatedData['logo'])) {
                $filePath = 'image';
                if (Storage::disk('public')->exists($filePath . '/' . $appSetting->getRawOriginal('logo'))) {
                    Storage::disk('public')->delete($filePath . '/' . $appSetting->getRawOriginal('logo'));
                };

                $validatedData['logo'] =  null;
            } else {
                $validatedData['logo'] =  $appSetting->getRawOriginal('logo');
            }

            $appSetting->update($validatedData);
        } catch (\Throwable) {
            return back()->with('message', [
                'type' => 'error',
                'text' => 'Terjadi suatu kesalahan.',
            ]);
        }

        return back()->with('message', [
            'type' => 'success',
            'text' => 'Pengaturan Aplikasi berhasil diubah.',
        ]);
    }
}
