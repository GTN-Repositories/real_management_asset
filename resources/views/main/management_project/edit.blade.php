<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Management Project</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formEdit" action="{{ route('management-project.update', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="col-12 col-md-12">
        <label class="form-label" for="name">nama manajemen<span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan name" required
            value="{{ $data->name }}" />
    </div>
    <div class="col-12 col-md-12" id="relationId">
        <label class="form-label" for="asset_id">nama aset<span class="text-danger">*</span></label>
        <select id="asset_id" name="asset_id" class="select2 form-select " data-allow-clear="true" required>
            <option value="{{ $data->asset->id }}" selected>{{ $data->asset->name }}</option>
        </select>
    </div>
    <div class="col-12 col-md-12">
        <label for="date-range-picker" class="form-label">Periode Waktu</label>
        <div class="input-group" id="date-range-picker">
            <input type="date" id="start_date" name="start_date" class="form-control" placeholder="Start Date"
                value="{{ $data->start_date }}" required>
            <span class="input-group-text">to</span>
            <input type="date" id="end_date" name="end_date" class="form-control" placeholder="End Date"
                value="{{ $data->end_date }}" required>
        </div>
    </div>
    <div class="col-12 col-md-12">
        <label class="form-label" for="calculation_method">Metode Kalkulasi</label>
        <select name="calculation_method" id="calculation_method" class="form-select select2">
            <option value="">Pilih</option>
            <option value="Kubic" @if ($data->calculation_method == 'Kubic') selected @endif>Kubic</option>
            <option value="Tonase" @if ($data->calculation_method == 'Tonase') selected @endif>Tonase</option>
        </select>
    </div>
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

@include('components.select2_js')
<script>
    $(document).ready(function() {
        $('#asset_id').select2({
            dropdownParent: $('#relationId'),
            placeholder: 'Pilih aset',
            ajax: {
                url: "{{ route('asset.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function(data) {
                    apiResults = data.data.map(function(item) {
                        return {
                            text: item.name,
                            id: item.relationId,
                        };
                    });

                    return {
                        results: apiResults
                    };
                },
                cache: true
            }
        });
    });
</script>
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
