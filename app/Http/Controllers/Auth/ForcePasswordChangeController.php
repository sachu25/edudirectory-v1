<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ForcePasswordChangeController extends Controller
{
    /**
     * Display the force password change notice view.
     */
    public function notice(Request $request): View|RedirectResponse
    {
        if (! $request->user()->force_password_change) {
            return redirect()->route('dashboard');
        }

        return view('auth.force-password-change');
    }

    /**
     * Update the user's password and reset the force flag.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();

        // Logout from all other devices/sessions for security
        Auth::logoutOtherDevices($request->current_password);

        $user->update([
            'password' => $request->password,
            'force_password_change' => false,
            'password_changed_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('status', 'Your password has been successfully updated to meet security requirements.');
    }
}
