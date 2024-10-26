<button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>

<div class="text-center mb-4">
    <h3 class="role-title mb-2">Add New Role</h3>
    <p class="text-muted">Set role permissions</p>
</div>
<!-- Add role form -->
<form id="addRoleForm" class="row g-3" method="POST" action="{{ route('role.store') }}">
    @csrf
    <div class="col-12 mb-4">
        <label class="form-label" for="name">Role Name</label>
        <input type="text" id="name" name="name" class="form-control"
            placeholder="Enter a role name" tabindex="-1" />
    </div>
    <div class="col-12">
        <h5>Role Permissions</h5>
        <!-- Permission table -->
        <div class="table-responsive">
            <table class="table table-flush-spacing">
                <tbody>
                    <tr>
                        <td>
                            <div class="d-flex flex-wrap">
                                @foreach ($permissions as $data)
                                    <div class="form-check me-3 me-lg-5">
                                        <input class="form-check-input" name="permissions[]" value="{{ Crypt::encrypt($data->id); }}" type="checkbox" id="userManagementRead" />
                                        <label class="form-check-label" for="userManagementRead"> {{ $data->name }} </label>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Permission table -->
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
    document.getElementById('addRoleForm').addEventListener('submit', function(event) {
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
                        $("#modalAddRole").modal("hide");

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
