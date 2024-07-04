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
            $originalPhoto = $user->getRawOriginal('photo');

            $avatar = match (true) {
                $request->hasFile('photo') => $request->file('photo')->hashName(),
                is_null($validatedData['photo']) => null,
                default => $originalPhoto
            };

            $user->update([
                'photo' => $avatar,
            ]);

            if ($request->hasFile('photo') || is_null($avatar)) {
                $path = 'image/avatars';

                if (Storage::disk('public')->exists($path . '/' . $originalPhoto)) {
                    Storage::disk('public')->delete($path . '/' . $originalPhoto);
                };

                if ($request->hasFile('photo')) {
                    $request->file('photo')->storeAs($path, $avatar, 'public');
                }
            }

            session()->flash('message', [
                'text' => 'Data Berhasil Diperbaharui.',
                'type' => 'success',
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'text' => 'Terjadi Suatu Kesalahan.',
                'type' => 'error',
            ]);
        }

        return back();
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

            session()->flash('message', [
                'text' => 'Foto Profil berhasil dihapus.',
                'type' => 'success',
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'text' => 'Terjadi Suatu Kesalahan.',
                'type' => 'error',
            ]);
        }

        return back();
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

            session()->flash('message', [
                'text' => 'Data berhasil diperbarui.',
                'type' => 'success',
            ]);
        } catch (\Throwable) {
            session()->flash('message', [
                'text' => 'Terjadi Suatu Kesalahan.',
                'type' => 'error',
            ]);
        }

        return back();
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
