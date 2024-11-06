<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Monitoring</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formEdit" action="{{ route('monitoring.update', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="col-12 col-md-12" id="assetRelation">
        <label class="form-label" for="asset_id">Nama Asset<span class="text-danger">*</span></label>
        <select id="asset_id" name="asset_id" class="select2 form-select select2-primary"data-allow-clear="true"
            required>
        </select>
    </div>
    <div class="col-12 col-md-6" id="driverRelation">
        <label class="form-label" for="longitude">Longitude<span class="text-danger">*</span></label>
        <input type="text" id="longitude" name="longitude" class="form-control" placeholder="Masukkan longitude"
            required value="{{ $data->longitude }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="latitude">Latitude<span class="text-danger">*</span></label>
        <input type="text" id="latitude" name="latitude" class="form-control" placeholder="Masukkan latitude"
            required value="{{ $data->latitude }}" />
    </div>
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

@include('components.select2_js')

<script>
    var asset_id = '{{ $data->asset_id }}';
    var asset_name = '{{ $data->asset->name }}';

    $(document).ready(function() {
        $('#asset_id').select2({
            dropdownParent: $('#assetRelation'),
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

        if (asset_id) {
            var assetOption = new Option(asset_name, asset_id, true, true);
            $('#asset_id').append(assetOption).trigger('change');
            $('#asset_id').val(asset_id).trigger('change');
        }
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
