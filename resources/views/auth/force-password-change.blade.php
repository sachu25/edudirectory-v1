<x-guest-layout>
    <div class="mb-4 text-center">
        <h4 class="fw-bold text-danger mb-2">
            <i class="fas fa-shield-alt me-2"></i>Security Action Required
        </h4>
        <p class="text-muted small">
            Please update your password to a strong, secure password before continuing to your account.
        </p>
    </div>

    @if (session('warning'))
        <div class="alert alert-warning border-0 shadow-sm mb-4 text-small" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
        </div>
    @endif

    <form method="POST" action="{{ route('force-password-change.update') }}">
        @csrf

        <!-- Current Password -->
        <div class="form-floating mb-3 position-relative">
            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="Current Password" required autocomplete="current-password" style="border-radius: 10px; border: 1px solid #E2E8F0; padding-right: 48px;">
            <label for="current_password" style="color: #64748B;">Current Password</label>
            <button type="button" class="btn border-0 text-secondary position-absolute end-0 top-50 translate-middle-y me-2 toggle-password-btn" data-target="current_password" style="z-index: 10; background: transparent;" title="Toggle Password Visibility">
                <i class="fas fa-eye"></i>
            </button>
            @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- New Password -->
        <div class="form-floating mb-3 position-relative">
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="New Password" required autocomplete="new-password" style="border-radius: 10px; border: 1px solid #E2E8F0; padding-right: 48px;">
            <label for="password" style="color: #64748B;">New Password</label>
            <button type="button" class="btn border-0 text-secondary position-absolute end-0 top-50 translate-middle-y me-2 toggle-password-btn" data-target="password" style="z-index: 10; background: transparent;" title="Toggle Password Visibility">
                <i class="fas fa-eye"></i>
            </button>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password Criteria Box -->
        <div class="p-3 mb-3 bg-light rounded-3 border text-start" style="font-size: 0.82rem; color: #475569;">
            <div class="fw-semibold mb-1 text-dark"><i class="fas fa-info-circle text-primary me-1"></i> Password Security Requirements:</div>
            <ul class="mb-0 ps-3">
                <li>At least <strong>12 characters</strong> in length</li>
                <li>Must include <strong>uppercase</strong> &amp; <strong>lowercase</strong> letters</li>
                <li>Must include at least <strong>1 number</strong> and <strong>1 special symbol</strong></li>
                <li>Must not be a leaked or compromised password</li>
            </ul>
        </div>

        <!-- Confirm Password -->
        <div class="form-floating mb-4 position-relative">
            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="Confirm New Password" required autocomplete="new-password" style="border-radius: 10px; border: 1px solid #E2E8F0; padding-right: 48px;">
            <label for="password_confirmation" style="color: #64748B;">Confirm New Password</label>
            <button type="button" class="btn border-0 text-secondary position-absolute end-0 top-50 translate-middle-y me-2 toggle-password-btn" data-target="password_confirmation" style="z-index: 10; background: transparent;" title="Toggle Password Visibility">
                <i class="fas fa-eye"></i>
            </button>
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold mb-3" style="border-radius: 10px; font-size: 1rem; letter-spacing: 0.5px;">
            Update Password &amp; Continue <i class="fas fa-lock ms-2"></i>
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="text-center">
        @csrf
        <button type="submit" class="btn btn-link text-decoration-none text-muted small p-0">
            <i class="fas fa-sign-out-alt me-1"></i> Log Out
        </button>
    </form>
</x-guest-layout>
