<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Fuel Consumtion</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formEdit" action="{{ route('fuel.update', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="col-12 col-md-6" id="managementRelation">
        <label class="form-label" for="management_project_id">nama projek<span class="text-danger">*</span></label>
        <select id="management_project_id" name="management_project_id"
            class="select2 form-select select2-primary"data-allow-clear="true" required>
        </select>
    </div>
    <div class="col-12 col-md-6" id="assetRelation">
        <label class="form-label" for="asset_id">nama aset<span class="text-danger">*</span></label>
        <select id="asset_id" name="asset_id" class="select2 form-select select2-primary"data-allow-clear="true"
            required>
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="receiver">receiver<span class="text-danger">*</span></label>
        <input type="text" id="receiver" name="receiver" class="form-control" placeholder="Masukkan receiver"
            required value="{{ $data->receiver }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="date">date<span class="text-danger">*</span></label>
        <input type="date" id="date" name="date" class="form-control" placeholder="Masukkan date"
            value="{{ date('Y-m-d') }}" required value="{{ $data->date }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="liter">liter<span class="text-danger">*</span></label>
        <input type="number" min="1" id="liter" name="liter" class="form-control"
            placeholder="Masukkan liter" required value="{{ $data->liter }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="price">price<span class="text-danger">*</span></label>
        <input type="text" id="price" name="price" class="form-control" placeholder="Masukkan price" required
            value="{{ $data->price }}" />
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
        $('#management_project_id').select2({
            dropdownParent: $('#managementRelation'),
            placeholder: 'Pilih projek',
            ajax: {
                url: "{{ route('management.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function(data) {
                    apiResults = data.data.reduce((unique, item) => {
                        if (!unique.some((i) => i.text === item.name)) {
                            unique.push({
                                text: item.name,
                                id: item.managementRelationId,
                            });
                        }
                        return unique;
                    }, []);

                    return {
                        results: apiResults
                    };
                },
                cache: true
            }
        }).on('change', function() {
            var projectName = $('#management_project_id option:selected').text();
            var projectId = $(this).val();

            $('#asset_id').empty().trigger('change');
            if (projectName) {
                $.ajax({
                    url: "{{ route('management.by_project') }}",
                    dataType: 'json',
                    delay: 250,
                    data: {
                        projectName: projectName
                    },
                    success: function(data) {
                        var assetOptions = Object.entries(data).map(function([assetId,
                            assetName
                        ]) {
                            return {
                                id: assetId,
                                text: assetName
                            };
                        });

                        $('#asset_id').select2({
                            dropdownParent: $('#assetRelation'),
                            data: assetOptions,
                            allowClear: true
                        });
                    }
                });
            }
        });
        // const autoSelectProjectId = "{{ $data->management_project_id }}";
        // $('#management_project_id').val(autoSelectProjectId).trigger('change');
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
