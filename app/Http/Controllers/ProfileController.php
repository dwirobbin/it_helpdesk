<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Requests\ProfilePictureUpdateRequest;
use App\Http\Requests\ProfileInformationUpdateRequest;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateAvatar(ProfilePictureUpdateRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $user = $request->user();

            if ($request->hasFile('photo')) {
                $filePath = 'image/avatars';
                if (Storage::disk('public')->exists($filePath . '/' . $user->getRawOriginal('photo'))) {
                    Storage::disk('public')->delete($filePath . '/' . $user->getRawOriginal('photo'));
                };

                $newFile = $request->file('photo')->store($filePath, 'public');
                $validatedData['photo'] = str_replace("$filePath/", '', $newFile);
            } else {
                $validatedData['photo'] = $user->getRawOriginal('photo');
            }

            $user->update([
                'photo' => $validatedData['photo'],
            ]);
        } catch (\Throwable) {
            return back()->with('message', [
                'text' => 'Terjadi Suatu Kesalahan.',
                'type' => 'error',
            ]);
        }

        return back()->with('message', [
            'text' => 'Data Berhasil Diperbaharui.',
            'type' => 'success',
        ]);
    }

    public function deleteAvatar(Request $request)
    {
        try {
            $user = $request->user();

            $filePath = 'image/avatars';
            $fileName = $user->getRawOriginal('photo');

            $user->update([
                'photo' => null,
            ]);

            if (Storage::disk('public')->exists($filePath . '/' . $fileName)) {
                Storage::disk('public')->delete($filePath . '/' . $fileName);
            }
        } catch (\Throwable) {
            return back()->with('message', [
                'text' => 'Terjadi Suatu Kesalahan.',
                'type' => 'error',
            ]);
        }

        return back()->with('message', [
            'text' => 'Foto Profil berhasil dihapus.',
            'type' => 'success',
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileInformationUpdateRequest $request): RedirectResponse
    {
        try {
            $request->user()->fill($request->validated());

            if ($request->user()->isDirty('email')) {
                $request->user()->email_verified_at = null;
            }

            $request->user()->save();
        } catch (\Throwable) {
            return back()->with('message', [
                'text' => 'Terjadi Suatu Kesalahan.',
                'type' => 'error',
            ]);
        }

        return back()->with('message', [
            'text' => 'Data berhasil diperbarui.',
            'type' => 'success',
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
