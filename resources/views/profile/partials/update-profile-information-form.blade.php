<section>
    <header class="mb-4">
        <h4 class="h5 fw-bold text-dark">
            {{ __('Profile Information') }}
        </h4>

        <p class="small text-muted">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="username" class="form-label">{{ __('Name') }}</label>
            <input type="text" class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}" id="username"
                name="username" value="{{ old('username', $user->username) }}" required autofocus
                autocomplete="username">

            @if ($errors->has('username'))
                <div class="invalid-feedback">
                    {{ $errors->first('username') }}
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email"
                name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">

            @if ($errors->has('email'))
                <div class="invalid-feedback">
                    {{ $errors->first('email') }}
                </div>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="small text-warning mb-1">
                        {{ __('Your email address is unverified.') }}
                    </p>

                    <button form="send-verification" class="btn btn-link p-0 text-decoration-none small">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 small text-success fw-bold">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="telepon" class="form-label">{{ __('Phone') }}</label>
            <input type="numeric" class="form-control {{ $errors->has('telepon') ? 'is-invalid' : '' }}" id="telepon"
                name="telepon" value="{{ old('telepon', $user->telepon) }}" required autofocus
                autocomplete="telepon">

            @if ($errors->has('telepon'))
                <div class="invalid-feedback">
                    {{ $errors->first('telepon') }}
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="mb-0 text-success fw-bold small">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
