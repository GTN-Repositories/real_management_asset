<button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>

<div class="text-center mb-4">
    <h3 class="role-title mb-2">Edit User</h3>
    <p class="text-muted">Set User role</p>
</div>
<!-- Add User form -->
<form id="editUserForm" class="row g-3" method="POST" action="{{ route('user.update', $encryptedUserId) }}">
    @csrf
    @method('PUT')

    <div class="col-12 mb-4">
        <label class="form-label" for="name">Name</label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Enter a name"
            value="{{ $user->name }}" tabindex="-1" />
    </div>

    <div class="col-12 mb-4">
        <label class="form-label" for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="Enter an email"
            value="{{ $user->email }}" tabindex="-1" />
    </div>

    <div class="col-12 mb-4">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control"
            placeholder="Leave blank if unchanged" tabindex="-1" />
    </div>
    <div class="col-12 mb-4">
        <label class="form-label" for="phone">phone</label>
        <input type="text" id="phone" name="phone" class="form-control" placeholder="Leave blank if unchanged"
            value="{{ $user->phone }}" tabindex="-1" />
    </div>

    <div class="col-12">
        <h5>User Roles</h5>
        <div class="table-responsive">
            <table class="table table-flush-spacing">
                <tbody>
                    <tr>
                        <td>
                            <div class="d-flex flex-wrap">
                                @foreach ($roles as $data)
                                    <div class="form-check me-3 me-lg-5">
                                        <input class="form-check-input" name="roles[]"
                                            value="{{ Crypt::encrypt($data->id) }}" type="checkbox"
                                            id="role{{ Crypt::encrypt($data->id) }}"
                                            {{ in_array($data->id, $userRoles) ? 'checked' : '' }} />
                                        <label class="form-check-label" for="role{{ Crypt::encrypt($data->id) }}">
                                            {{ $data->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-12 text-center mt-4">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">
            Cancel
        </button>
    </div>
</form>


<!--/ Add role form -->

<script>
    function formatIndonesianPhoneNumber(angka, prefix) {
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

    $(document).on('input', '#phone', function() {
        value = formatIndonesianPhoneNumber($(this).val());
        $(this).val(value);
    });
</script>

<script>
    document.getElementById('editUserForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const url = form.action;

        fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.errors) {
                    let errorMessages = '';
                    for (const [field, messages] of Object.entries(data.errors)) {
                        errorMessages += messages.join('<br>') + '<br>';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: errorMessages
                    });
                } else if (!data.status) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message
                    })
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message
                    }).then(() => {
                        $("#modalEditUser").modal("hide");

                        $('#data-table').DataTable().ajax.reload();
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!'
                });
            });
    });
</script>
