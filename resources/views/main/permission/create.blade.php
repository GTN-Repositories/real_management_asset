<button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>

<div class="text-center mb-4">
    <h3 class="mb-2">Add New Permission</h3>
    <p class="text-muted">Permissions you may use and assign to your users.</p>
</div>
<form id="addPermissionForm" class="row" method="POST" action="{{ route('permision.store') }}">
    @csrf
    <div class="col-12 mb-3">
        <label class="form-label" for="name">Permission Name</label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Permission Name"
            autofocus />
    </div>
    <div class="col-12 text-center demo-vertical-spacing">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Create Permission</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">
            Discard
        </button>
    </div>
</form>

<script>
    document.getElementById('addPermissionForm').addEventListener('submit', function(event) {
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
                        $("#modalAddPermission").modal("hide");

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
