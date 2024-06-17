<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordRequest;
use Illuminate\Http\RedirectResponse;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(PasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $request->user()->update($validated);
        } catch (\Throwable) {
            return back()->with('message', [
                'text' => 'Terjadi suatu kesalahan.',
                'type' => 'error',
            ]);
        }

        return back()->with('message', [
            'text' => 'Password berhasil diubah.',
            'type' => 'success',
        ]);
    }
}
