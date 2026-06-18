<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-floating mb-4">
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required autofocus autocomplete="username" style="border-radius: 10px; border: 1px solid #E2E8F0;">
            <label for="email" style="color: #64748B;">Email address</label>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-floating mb-3">
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required autocomplete="current-password" style="border-radius: 10px; border: 1px solid #E2E8F0;">
            <label for="password" style="color: #64748B;">Password</label>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="d-flex justify-content-between align-items-center mb-4 login-options">
            <div class="form-check d-flex align-items-center mb-0">
                <input type="checkbox" class="form-check-input me-2" id="remember_me" name="remember" style="border-radius: 4px; border-color: #CBD5E1;">
                <label class="form-check-label text-muted" for="remember_me" style="font-size: 0.9rem;">Remember me</label>
            </div>
            
            @if (Route::has('password.request'))
                <a class="text-decoration-none text-muted" href="{{ route('password.request') }}" style="font-size: 0.85rem; transition: color 0.2s;">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold" style="border-radius: 10px; font-size: 1rem; letter-spacing: 0.5px;">
            Sign In <i class="fas fa-sign-in-alt ms-2"></i>
        </button>
    </form>
</x-guest-layout>
