<section class="mb-5">
    <header class="mb-4">
        <h2 class="h5 fw-bold">{{ __('Profile Information') }}</h2>
        <p class="text-muted">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mb-4">
        @csrf
        @method('patch')

        <div class="mb-3">
            <x-input-label for="name" :value="__('Name')" class="form-label" />
            <x-text-input id="name" name="name" type="text" class="form-control" :value="old('name', $user->name)" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="invalid-feedback" />
        </div>

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" class="form-label" />
            <x-text-input id="email" name="email" type="email" class="form-control" :value="old('email', $user->email)" required
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="invalid-feedback" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-3">
                    <p class="text-muted">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="btn btn-link text-decoration-none">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="mb-3">
            <x-input-label for="phone" :value="__('Phone')" class="form-label" />
            <x-text-input id="phone" name="phone" type="text" class="form-control phone" :value="old('phone', $user->phone)"
                required autofocus autocomplete="phone" />
            <x-input-error :messages="$errors->get('phone')" class="invalid-feedback" />
        </div>

        <div class="d-flex justify-content-end">
            <x-primary-button class="btn btn-primary">{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-success ms-2">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

@push('js')
    <script>
        function formatPhoneNumber(angka, prefix) {
            if (!angka) {
                return (prefix || '') + '-';
            }

            angka = angka.toString();
            const number_string = angka.replace(/[^0-9]/g, '').toString();
            let formattedNumber = '';

            if (number_string.length > 3) {
                const first = number_string.substring(0, 4);
                const middle = number_string.substring(4, 8);
                const last = number_string.substring(8);

                formattedNumber += first + '-' + middle + '-' + last;
            } else {
                formattedNumber += number_string;
            }

            return prefix === undefined ? formattedNumber : formattedNumber ? (prefix || '') + formattedNumber : '';
        }

        $(document).on('input', '.phone', function() {
            value = formatPhoneNumber($(this).val());
            $(this).val(value);
        });
    </script>
@endpush
