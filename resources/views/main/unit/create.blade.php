<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Tambah Kendaraan/Unit</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formCreate" action="{{ route('unit.store') }}" enctype="multipart/form-data">
    @csrf
    
    <div class="col-12 col-md-6">
        <label class="form-label" for="police_number">No Polisi <span class="text-danger">*</span></label>
        <input type="text" id="police_number" name="police_number" class="form-control" placeholder="Masukkan No Polisi"/>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="old_police_number">No Polisi  <span class="text-danger">*</span></label>
        <input type="text" id="old_police_number" name="old_police_number" class="form-control" placeholder="Masukkan No Polisi "/>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="frame_number">No Rangka <span class="text-danger">*</span></label>
        <input type="text" id="frame_number" name="frame_number" class="form-control" placeholder="Masukkan No Rangka"/>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="merk">Merek <span class="text-danger">*</span></label>
        <input type="text" id="merk" name="merk" class="form-control" placeholder="Masukkan Merek"/>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="type_vehicle">Jenis Kendaraan <span class="text-danger">*</span></label>
        <input type="text" id="type_vehicle" name="type_vehicle" class="form-control" placeholder="Masukkan Jenis Kendaraan"/>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="type">Tipe <span class="text-danger">*</span></label>
        <input type="text" id="type" name="type" class="form-control" placeholder="Masukkan Tipe"/>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="year">Tahun <span class="text-danger">*</span></label>
        <input type="number" id="year" name="year" class="form-control" value="{{ date('Y') }}" placeholder="Masukkan Tahun"/>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="color">Warna <span class="text-danger">*</span></label>
        <input type="text" id="color" name="color" class="form-control" placeholder="Masukkan Warna"/>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="physical_status">Status Fisik <span class="text-danger">*</span></label>
        <input type="text" id="physical_status" name="physical_status" class="form-control" placeholder="Masukkan Status Fisik"/>
    </div>
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
    </div>
</form>

<script>
    document.getElementById('formCreate').addEventListener('submit', function(event) {
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