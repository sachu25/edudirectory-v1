<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="position-relative">
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full pr-10" autocomplete="current-password" />
            <button type="button" class="btn border-0 text-secondary position-absolute end-0 bottom-0 mb-1 me-2 toggle-password-btn" data-target="update_password_current_password" style="z-index: 10; background: transparent;" title="Toggle Password Visibility">
                <i class="fas fa-eye"></i>
            </button>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="position-relative">
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full pr-10" autocomplete="new-password" />
            <button type="button" class="btn border-0 text-secondary position-absolute end-0 bottom-0 mb-1 me-2 toggle-password-btn" data-target="update_password_password" style="z-index: 10; background: transparent;" title="Toggle Password Visibility">
                <i class="fas fa-eye"></i>
            </button>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="p-3 bg-light rounded-3 border text-start" style="font-size: 0.82rem; color: #475569;">
            <div class="fw-semibold mb-1 text-dark"><i class="fas fa-info-circle text-primary me-1"></i> Password Security Requirements:</div>
            <ul class="mb-0 ps-3">
                <li>At least <strong>12 characters</strong> in length</li>
                <li>Must include <strong>uppercase</strong> &amp; <strong>lowercase</strong> letters</li>
                <li>Must include at least <strong>1 number</strong> and <strong>1 special symbol</strong></li>
                <li>Must not be a leaked or compromised password</li>
            </ul>
        </div>

        <div class="position-relative">
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full pr-10" autocomplete="new-password" />
            <button type="button" class="btn border-0 text-secondary position-absolute end-0 bottom-0 mb-1 me-2 toggle-password-btn" data-target="update_password_password_confirmation" style="z-index: 10; background: transparent;" title="Toggle Password Visibility">
                <i class="fas fa-eye"></i>
            </button>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 4000)"
                    class="text-success fw-semibold small d-inline-flex align-items-center ms-2"
                >
                    <i class="fas fa-check-circle me-1"></i> {{ __('Password updated successfully.') }}
                </span>
            @endif
        </div>
    </form>
</section>
