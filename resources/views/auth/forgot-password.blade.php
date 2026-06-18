<x-guest-layout>
    <div class="mb-4 text-muted" style="font-size: 0.9rem; line-height: 1.5;">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-floating mb-4">
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required autofocus style="border-radius: 10px; border: 1px solid #E2E8F0;">
            <label for="email" style="color: #64748B;">Email address</label>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary py-3 fw-semibold" style="border-radius: 10px; font-size: 1rem; letter-spacing: 0.5px;">
                Email Password Reset Link <i class="fas fa-paper-plane ms-2"></i>
            </button>
            <a href="{{ route('login') }}" class="btn btn-secondary py-2 fw-semibold mt-2 text-center" style="border-radius: 10px; font-size: 0.9rem;">
                <i class="fas fa-arrow-left me-2"></i> Back to Login
            </a>
        </div>
    </form>
</x-guest-layout>
