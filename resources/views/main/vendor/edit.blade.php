<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Vendor</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="fromEdit" action="{{ route('vendor.update', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="col-12 col-md-6">
        <label class="form-label">Nama Vendor</label>
        <input type="text" name="name" id="name" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Vendor" value="{{ old('name', $data->name) }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">PIC</label>
        <input type="text" name="pic" id="pic" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan PIC" value="{{ old('pic', $data->pic) }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Email" value="{{ old('email', $data->email) }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">No Telp</label>
        <input type="text" name="phone_number" id="phone_number" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan No Telp" value="{{ old('phone_number', $data->phone_number) }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">NPWP</label>
        <input type="text" name="npwp" id="npwp" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan NPWP" value="{{ old('npwp', $data->npwp) }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Bank</label>
        <input type="text" name="bank_name" id="bank_name" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Bank" value="{{ old('bank_name', $data->bank_name) }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Nomor Rekening</label>
        <input type="text" name="bank_number" id="bank_number" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nomor Rekening" value="{{ old('bank_number', $data->bank_number) }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">A/N Bank</label>
        <input type="text" name="bank_account_name" id="bank_account_name" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan A/N Bank" value="{{ old('bank_account_name', $data->bank_account_name) }}" required />
    </div>

    <div class="col-12 col-md-12">
        <label class="form-label">Alamat</label>
        <textarea name="address" id="address" cols="30" rows="5" class="form-control">{{ old('address', $data->address) }}</textarea>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        // 
    });
</script>
<script>
    document.getElementById('fromEdit').addEventListener('submit', function(event) {
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
