<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Pelanggan</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formEdit" action="{{ route('customer.update', $data->id) }}" enctype="multipart/form-data">
    @csrf
    @method('put')
    
    <div class="col-12 col-md-6">
        <label class="form-label" for="name">Nama <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" value="{{ $data->name }}" class="form-control" placeholder="Masukkan Nama"/>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="alias">Alias <span class="text-danger">*</span></label>
        <input type="text" id="alias" name="alias" value="{{ $data->alias }}" class="form-control" placeholder="Masukkan Alias"/>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="phone_number">No Telepon <span class="text-danger">*</span></label>
        <input type="text" id="phone_number" name="phone_number" value="{{ $data->phone_number }}" class="form-control" placeholder="Masukkan No Telepon"/>
    </div>
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
    </div>
</form>

<script>
    document.getElementById('formEdit').addEventListener('submit', function(event) {
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
            }else if(!data.status){
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
                    $("#modal-ce").modal("hide");

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