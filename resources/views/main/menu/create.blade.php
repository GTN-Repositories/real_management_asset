<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Add New Menu</h3>
    <p class="text-muted">Add new menu to the system</p>
</div>
<form id="addMenuForm" class="row g-3" method="POST" action="{{ route('menu.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="col-12 col-md-2">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTrsaTeFqurvUDvMYOcgZAd-JPf-dtLogrrog&s"
            id="preview-icon" class="img-fluid rounded mb-3 pt-1" alt="Icon Preview">
    </div>
    <div class="col-12">
        <label class="form-label" for="icon">Icon</label>
        <input type="file" id="icon" name="icon" class="form-control" accept="image/*" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="name">Name Menu</label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Enter a name menu"
            required />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="order">Urutan</label>
        <input type="text" id="order" name="order" class="form-control" placeholder="Enter an order"
            value="{{ $nextOrder }}" required />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="route">URL</label>
        <input type="text" id="route" name="route" class="form-control" placeholder="Enter a URL" required />
    </div>
    <div class="col-12 col-md-6">
        <label for="exampleFormControlSelect1" class="form-label">Induk Menu</label>
        <select class="form-select" id="exampleFormControlSelect1"
        name="parent_id" aria-label="Default select example">
            <option selected value="{{ Crypt::encryptString(0) }}">none</option>
            @foreach ($menu as $data)
                <option value="{{ Crypt::encryptString($data->id) }}">{{ $data->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

<script>
    document.getElementById('icon').addEventListener('change', function(event) {
        const preview = document.getElementById('preview-icon');
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        } else {
            preview.src =
                "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTrsaTeFqurvUDvMYOcgZAd-JPf-dtLogrrog&s";
        }
    });

    document.getElementById('addMenuForm').addEventListener('submit', function(event) {
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
                        $("#modalAddMenu").modal("hide");

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
