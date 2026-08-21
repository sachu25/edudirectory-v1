<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Logout from other sessions for security
        \Illuminate\Support\Facades\Auth::logoutOtherDevices($validated['current_password']);

        $request->user()->update([
            'password' => $validated['password'],
            'force_password_change' => false,
            'password_changed_at' => now(),
        ]);

        return back()->with('status', 'password-updated');
    }
}
