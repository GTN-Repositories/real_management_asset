<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Asset</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formFiles" enctype="multipart/form-data"
    action="{{ route('asset.update', $data->id) }}">
    @csrf
    @method('put')
    @if ($data->kategori == 'asset')
        <div class="col-12 col-md-12 mb-3">
            <label class="form-label" for="image">Gambar Asset</label>
            <input type="file" id="image" name="image" class="form-control" />
        </div>
    @elseif ($data->kategori == 'stnk')
        <div class="col-12 col-md-12 mb-3">
            <label class="form-label" for="stnk">Gambar</label>
            <input type="file" id="stnk" name="stnk" class="form-control" />
        </div>
        <div class="col-12 col-md-12">
            <label class="form-label" for="stnk_date">Tanggal Bayar Stnk<span
                    class="text-danger">*</span></label>
            <input type="date" id="stnk_date" name="stnk_date" class="form-control"
                placeholder="Masukkan stnk_date" />
        </div>
    @elseif ($data->kategori == 'tax')
        <div class="col-12 col-md-12 mb-3">
            <label class="form-label" for="file_tax">Gambar</label>
            <input type="file" id="file_tax" name="file_tax" class="form-control" />
        </div>
        <div class="col-12 col-md-12">
            <label class="form-label" for="date_tax">Tanggal Bayar Pajak<span
                    class="text-danger">*</span></label>
            <input type="date" id="date_tax" name="date_tax" class="form-control"
                placeholder="Masukkan date_tax" />
        </div>
    @else
        <div class="col-12 col-md-12 mb-3">
            <label class="form-label" for="asuransi">Gambar</label>
            <input type="file" id="asuransi" name="asuransi" class="form-control" />
        </div>
        <div class="col-12 col-md-12">
            <label class="form-label" for="asuransi_date">Tanggal Bayar Asuransi<span
                    class="text-danger">*</span></label>
            <input type="date" id="asuransi_date" name="asuransi_date" class="form-control"
                placeholder="Masukkan asuransi_date" />
        </div>
    @endif
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

<script>
    document.getElementById('formFiles').addEventListener('submit', function(event) {
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

                        window.location.reload();
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
