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
        <label class="form-label" for="management_project_id">Nama Management Project<span
                class="text-danger">*</span></label>
        <select id="management_project_id" name="management_project_id"
            class="select2 form-select select2-primary"data-allow-clear="true" required>
        </select>
    </div>
    <div class="col-12 col-md-6" id="assetRelation">
        <label class="form-label" for="asset_id">Nama Asset<span class="text-danger">*</span></label>
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
        <label class="form-label" for="date">Tanggal<span class="text-danger">*</span></label>
        <input type="date" id="date" name="date" class="form-control" placeholder="Masukkan tanggal"
            value="{{ date('Y-m-d') }}" required value="{{ $data->date }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="loadsheet">Loadsheet<span class="text-danger">*</span></label>
        <input type="text" id="loadsheet" name="loadsheet" class="form-control" placeholder="Masukkan loadsheet"
            required value="{{ $data->loadsheet }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="liter">Liter<span class="text-danger">*</span></label>
        <input type="text" id="liter" name="liter" class="form-control" placeholder="Masukkan liter" required
            value="{{ $data->liter }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="price">Harga/Liter<span class="text-danger">*</span></label>
        <input type="text" id="price" name="price" class="form-control" placeholder="Masukkan harga" required
            value="{{ $data->price }}" />
    </div>
    <div class="col-12 col-md-12">
        <label class="form-label" for="category">Kategori<span class="text-danger">*</span></label>
        <input type="text" id="category" name="category" class="form-control" placeholder="Masukkan kategori"
            required value="{{ $data->category }}" />
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

    async function encryptData(value) {
        try {
            const response = await $.ajax({
                url: "{{ route('encrypt') }}",
                method: 'POST',
                data: {
                    value: value,
                    _token: '{{ csrf_token() }}'
                }
            });
            return response.encrypted;
        } catch (error) {
            console.error('Encryption failed:', error);
            return null;
        }
    }

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
        }).on('change', async function() {
            var projectId = $(this).val();

            $('#asset_id').empty().trigger('change');
            if (projectId) {
                const encryptedProjectId = await encryptData(projectId);

                if (encryptedProjectId) {
                    $.ajax({
                        url: "{{ route('management-project.by_project') }}",
                        dataType: 'json',
                        delay: 250,
                        data: {
                            projectId: encryptedProjectId
                        },
                        success: function(data) {
                            var assetOptions = Object.entries(data).map(function([
                                assetId,
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
                            }).trigger('change');
                        }
                    });
                }
            }
        });

        if (management_project_id) {
            var option = new Option(management_project_name, management_project_id, true, true);
            $('#management_project_id').append(option).trigger('change');
            $('#management_project_id').val(management_project_id).trigger('change');
        }

        if (asset_id) {
            var assetOption = new Option(asset_name, asset_id, true, true);
            $('#asset_id').append(assetOption).trigger('change');
            $('#asset_id').val(asset_id).trigger('change');
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

    $(document).on('input', '#price, #loadsheet, #liter', function() {
        value = formatCurrency($(this).val());
        $(this).val(value);
    });

    function formatCurrency(angka, prefix) {
        if (!angka) {
            return (prefix || '') + '-';
        }

        angka = angka.toString();
        const splitDecimal = angka.split('.');
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            const separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix === undefined ? rupiah : rupiah ? (prefix || '') + rupiah : '';
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
