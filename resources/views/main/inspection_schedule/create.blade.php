<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Tambah Jadwal Inspeksi</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>

<form method="POST" class="row g-3" id="formCreate" action="{{ route('inspection-schedule.store') }}"
    enctype="multipart/form-data">
    @csrf
    <div class="col-12 col-md-12">
        <label class="form-label">Judul Inspeksi</label>
        <input type="text" name="name" id="name" class="form-control mb-3 mb-lg-0" placeholder="Masukan Nama Item"
            value="{{ old('name') }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Tanggal</label>
        <input type="date" name="date" id="date" class="form-control mb-3 mb-lg-0" placeholder="Masukan Nama Item" value="{{ old('date', date('Y-m-d')) }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label for="exampleFormControlSelect1" class="form-label">Jenis Inspeksi</label>
        <select class="form-select" id="exampleFormControlSelect1" name="type" aria-label="Select Type">
            <option value="p2h">P2H</option>
            <option value="pm">PM</option>
        </select>
    </div>

    <div class="col-12" id="select2relation">
        <label for="select2Basic" class="form-label">Plat Nomor</label>
        <select id="select2Basic" class="select2 form-select form-select-lg" name="unit_id" data-allow-clear="true">
            <option></option>
        </select>
    </div>

    <div class="col-12 col-md-12">
        <label class="form-label" for="alias">Catatan</label>
        <textarea name="note" id="" cols="30" rows="10" class="form-control"
            placeholder="Masukkan Deskripsi"></textarea>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Mulai Inspeksi</button>
        <button type="button" class="btn btn-label-secondary">Simpan</button>
    </div>
</form>

<script type="text/javascript">
    const relationData = @json($relation);
        $(document).ready(function() {
            $('#select2Basic').select2({
                dropdownParent: $('#select2relation'),
                placeholder: 'Masukan Plat Nomor',
                data: relationData.map(function(relation) {
                    return {
                        id: relation.id,
                        text: relation.police_number + ' - ' + relation.merk + ' ' + relation.type
                    };
                })
            });
        });

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
                            window.location.href = "{{ route('inspection-schedule.show', ['inspection_schedule' => ':id']) }}".replace(':id', data.schedule_id);
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