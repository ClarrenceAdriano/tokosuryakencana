<section>
    <header class="mb-4">
        <h4 class="h5 fw-bold text-dark">
            {{ __('Update Password') }}
        </h4>

        <p class="small text-muted">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">{{ __('Current Password') }}</label>
            <input type="password" 
                   class="form-control {{ $errors->updatePassword->has('current_password') ? 'is-invalid' : '' }}" 
                   id="update_password_current_password" 
                   name="current_password" 
                   autocomplete="current-password">
            
            @if($errors->updatePassword->has('current_password'))
                <div class="invalid-feedback">
                    {{ $errors->updatePassword->first('current_password') }}
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">{{ __('New Password') }}</label>
            <input type="password" 
                   class="form-control {{ $errors->updatePassword->has('password') ? 'is-invalid' : '' }}" 
                   id="update_password_password" 
                   name="password" 
                   autocomplete="new-password">

            @if($errors->updatePassword->has('password'))
                <div class="invalid-feedback">
                    {{ $errors->updatePassword->first('password') }}
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input type="password" 
                   class="form-control {{ $errors->updatePassword->has('password_confirmation') ? 'is-invalid' : '' }}" 
                   id="update_password_password_confirmation" 
                   name="password_confirmation" 
                   autocomplete="new-password">
            
            @if($errors->updatePassword->has('password_confirmation'))
                <div class="invalid-feedback">
                    {{ $errors->updatePassword->first('password_confirmation') }}
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="mb-0 text-success fw-bold small"
                >
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>