<section>
    <h2 class="h4">{{ __('Delete Account') }}</h2>

    <p class="text-muted">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </p>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
        {{ __('Delete Account') }}
    </button>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ __('Delete Account') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('delete')

                        <p class="text-muted">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <div class="mt-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <x-input-error :messages="$errors->userDeletion->get('password')" class="invalid-feedback" />
                        </div>

                        <div class="mt-3 d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="btn btn-danger ms-2">
                                {{ __('Delete Account') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

