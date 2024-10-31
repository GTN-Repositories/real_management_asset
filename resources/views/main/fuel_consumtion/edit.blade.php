<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Fuel Consumtion</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formEdit" action="{{ route('fuel.update', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="col-12 col-md-12" id="managementRelation">
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
    <div class="col-12 col-md-6" id="driverRelation">
        <label class="form-label" for="user_id">Nama Driver<span class="text-danger">*</span></label>
        <select id="user_id" name="user_id" class="select2 form-select select2-primary"data-allow-clear="true"
            required>
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="date">tanggal<span class="text-danger">*</span></label>
        <input type="date" id="date" name="date" class="form-control" placeholder="Masukkan tanggal"
            value="{{ date('Y-m-d') }}" required value="{{ $data->date }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="loadsheet">loadsheet<span class="text-danger">*</span></label>
        <input type="number" min="1" id="loadsheet" name="loadsheet" class="form-control"
            placeholder="Masukkan loadsheet" required value="{{ $data->loadsheet }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="liter">liter<span class="text-danger">*</span></label>
        <input type="number" min="1" id="liter" name="liter" class="form-control"
            placeholder="Masukkan liter" required value="{{ $data->liter }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="price">harga/liter<span class="text-danger">*</span></label>
        <input type="text" id="price" name="price" class="form-control" placeholder="Masukkan harga" required
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
    var management_project_id = '{{ $data->management_project_id }}';
    var management_project_name = '{{ $data->management_project->name }}';
    var asset_id = '{{ $data->asset_id }}';
    var asset_name = '{{ $data->asset->name }}';
    var user_id = '{{ $data->user_id }}';
    var user_name = '{{ $data->user->name }}';

    $(document).ready(function() {
        $('#management_project_id').select2({
            dropdownParent: $('#managementRelation'),
            placeholder: 'Pilih projek',
            ajax: {
                url: "{{ route('management-project.data') }}",
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
                    url: "{{ route('management-project.by_project') }}",
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

        if (management_project_id) {
            var option = new Option(management_project_name, management_project_id, true, true);
            $('#management_project_id').append(option).trigger('change');
        }
        if (asset_id) {
            var option = new Option(asset_name, asset_id, true, true);
            $('#asset_id').append(option).trigger('change');
        }


        $('#user_id').select2({
            dropdownParent: $('#driverRelation'),
            placeholder: 'Pilih penerima',
            ajax: {
                url: "{{ route('user.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function(data) {
                    apiResults = data.data
                        .filter(function(item) {
                            return item.idRelation !== null;
                        })
                        .map(function(item) {
                            return {
                                text: item.name,
                                id: item.idRelation,
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

    if (user_id) {
        var option = new Option(user_name, user_id, true, true);
        $('#user_id').append(option).trigger('change');
    }
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
