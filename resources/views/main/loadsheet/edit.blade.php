<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Loadsheet</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formEdit" action="{{ route('loadsheet.update', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="col-12 col-md-6">
        <label class="form-label" for="date">Tanggal <span class="text-danger">*</span></label>
        <input type="date" id="date" name="date" class="form-control" required value="{{ $data->date }}" />
    </div>
    <div class="col-12 col-md-6" id="managementRelation">
        <label class="form-label" for="management_project_id">Management Project <span
                class="text-danger">*</span></label>
        <select id="management_project_id" name="management_project_id" class="form-control select2" required>
            <!-- Options will be populated dynamically -->
        </select>
    </div>
    <div class="col-12 col-md-6" id="assetRelation">
        <label class="form-label" for="asset_id">Asset <span class="text-danger">*</span></label>
        <select id="asset_id" name="asset_id" class="form-control select2" required>
            <!-- Options will be populated dynamically -->
        </select>
    </div>
    <div class="col-12 col-md-6" id="karyawanRelation">
        <label class="form-label" for="employee_id">Karyawan <span class="text-danger">*</span></label>
        <select id="employee_id" name="employee_id" class="form-control select2" required>
            <!-- Options will be populated dynamically -->
        </select>
    </div>
    <div class="col-12 col-md-12">
        <label class="form-label" for="hours">Jam Kerja <span class="text-danger">*</span></label>
        <input type="text" id="hours" name="hours" class="form-control" required value="{{ $data->hours }}" />
    </div>
    <div class="col-12">
        <hr class="my-4">
    </div>
    <div class="col-md mb-4 mb-md-2">
        <div class="accordion mt-3" id="accordionExample">
            <div class="card accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                        data-bs-target="#accordionOne" aria-expanded="false" aria-controls="accordionOne">
                        Item Work
                    </button>
                </h2>

                <div id="accordionOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample"
                    style="">
                    <div class="accordion-body row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="type">Jenis <span class="text-danger">*</span></label>
                            <input type="text" id="type" name="type" class="form-control"
                                value="{{ $data->type }}" />
                        </div>
                        <div class="col-12 col-md-6" id="soilTypeRelation">
                            <label class="form-label" for="soil_type_id">Jenis Tanah <span
                                    class="text-danger">*</span></label>
                            <select id="soil_type_id" name="soil_type_id" class="form-control select2">
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="location">Lokasi <span class="text-danger">*</span></label>
                            <textarea id="location" name="location" class="form-control">{{ $data->location }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="bpit">BPIT <span class="text-danger">*</span></label>
                            <input type="text" id="bpit" name="bpit" class="form-control"
                                value="{{ $data->bpit }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="kilometer">Kilometer <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="kilometer" name="kilometer" class="form-control"
                                value="{{ $data->kilometer }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="loadsheet">Total Loadsheet <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="loadsheet" name="loadsheet" class="form-control"
                                value="{{ $data->loadsheet }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="perload">Per Load <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="perload" name="perload" class="form-control"
                                value="{{ $data->perload }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="lose_factor">Lose Factor <span
                                    class="text-danger">*</span></label>
                            <input type="number" id="lose_factor" name="lose_factor" class="form-control" value="{{ $data->lose_factor }}" step="0.01"/>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="billing_status">Status Penagihan</label>
                            <select name="billing_status" id="billing_status" class="select2 form-select">
                                <option value="Sudah Ditagih" @if ($data->billing_status == 'Sudah Ditagih') selected @endif>Sudah
                                    Ditagih</option>
                                <option value="Belum" @if ($data->billing_status == 'Belum') selected @endif>Belum</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="remarks">Catatan <span
                                    class="text-danger">*</span></label>
                            <textarea id="remarks" name="remarks" class="form-control">{{ $data->remarks }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                url: "{{ route('management-project.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        'search[value]': params.term,
                        start: 0,
                        length: 10
                    };
                },
                processResults: function(data) {
                    let uniqueResults = data.data.reduce((unique, item) => {
                        if (!unique.some((i) => i.text === item.name)) {
                            unique.push({
                                text: item.format_id + ' - ' + item.name,
                                id: item.managementRelationId,
                            });
                        }
                        return unique;
                    }, []);
                    return {
                        results: uniqueResults
                    };
                },
                cache: true
            }
        }).on('change', async function() {
            var projectId = $(this).val();

            $('#asset_id').empty().trigger('change');
            if (projectId) {
                $.ajax({
                    url: "{{ route('management-project.by_project') }}",
                    dataType: 'json',
                    delay: 250,
                    data: {
                        projectId: projectId,
                    },
                    success: function(data) {
                        if (data && typeof data === 'object' && Object.keys(data)
                            .length) {
                            var assetOptions = Object.entries(data).map(function([id,
                                name
                            ]) {
                                return {
                                    id: id,
                                    text: name
                                };
                            });

                            $('#asset_id').select2({
                                dropdownParent: $('#assetRelation'),
                                data: assetOptions,
                                allowClear: true
                            }).trigger('change');
                        } else {
                            $('#asset_id').select2({
                                dropdownParent: $('#assetRelation'),
                                data: [],
                                allowClear: true
                            }).trigger('change');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching assets:', xhr);
                        $('#asset_id').select2({
                            dropdownParent: $('#assetRelation'),
                            data: [],
                            allowClear: true
                        }).trigger('change');
                    }
                });
            }
        });

        var management_project_id = '{{ $data->management_project_id }}';
        var management_project_name = '{{ $data->management_project->name }}';

        if (management_project_id) {
            var projectOption = new Option(management_project_name, management_project_id, true, true);
            $('#management_project_id').append(projectOption).trigger('change');
        }

        var asset_id = '{{ $data->asset_id }}';
        var asset_name = '{{ $data->asset->name }}';
        var asset_text = '{{ $data->asset->asset_text }}';
        var asset_license_plate = '{{ $data->asset->license_plate }}';

        if (asset_id) {
            var assetOption = new Option(`${asset_license_plate} - ${asset_name} - ${asset_text}`, asset_id,
                true, true);
            $('#asset_id').append(assetOption).trigger('change');
        }

        $('#employee_id').select2({
            dropdownParent: $('#karyawanRelation'),
            placeholder: 'Pilih karyawan',
            ajax: {
                url: "{{ route('employee.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        'search[value]': params.term,
                        start: 0,
                        length: 10
                    };
                },
                processResults: function(data) {
                    apiResults = data.data
                        .map(function(item) {
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

        var employee_id = '{{ $data->employee_id }}';
        var employee_name = '{{ $data->employee->name }}';

        if (employee_id) {
            var employeeOption = new Option(employee_name, employee_id, true, true);
            $('#employee_id').append(employeeOption).trigger('change');
        }

        $('#soil_type_id').select2({
            dropdownParent: $('#soilTypeRelation'),
            placeholder: 'Pilih jenis tanah',
            ajax: {
                url: "{{ route('soil-type.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        'search[value]': params.term,
                        start: 0,
                        length: 10
                    };
                },
                processResults: function(data) {
                    apiResults = data.data
                        .map(function(item) {
                            return {
                                text: item.name,
                                id: item.id,
                            };
                        });

                    return {
                        results: apiResults
                    };
                },
                cache: true
            }
        });

        var soil_type_id = '{{ $data->soil_type_id ?? '' }}';
        var soil_type_name = '{{ $data->soilType->name ?? '' }}';

        if (soil_type_id) {
            var soilTypeOption = new Option(soil_type_name, soil_type_id, true, true);
            $('#soil_type_id').append(soilTypeOption).trigger('change');
        }
    })

    $(document).ready(function() {
        $('#hours, #kilometer, #loadsheet, #perload, #price').each(function() {
            const value = $(this).val();
            $(this).val(formatCurrency(value));
        });

        // $('#lose_factor').val($('#lose_factor').val().replace('.', ','));
    });

    $(document).on('input', '#hours, #kilometer, #loadsheet, #perload, #price', function() {
        const value = $(this).val();
        $(this).val(formatCurrency(value));
    });

    // $(document).on('input', '#lose_factor', function() {
    //     const value = $(this).val();
    //     $(this).val(formatCurrencyLoseFactor(value));
    // });

    function formatCurrency(angka, prefix) {
        if (!angka) {
            return (prefix || '') + '-';
        }

        angka = angka.toString();
        const splitDecimal = angka.split('.');
        let text_string = angka.replace(/[^,\d]/g, '').toString(),
            split = text_string.split(','),
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

    function formatCurrencyLoseFactor(angka, prefix) {
        if (!angka) {
            return (prefix || '') + '-';
        }

        angka = angka.toString();
        const splitDecimal = angka.split('.');
        let text_string = angka.replace(/[^,\d]/g, '').toString(),
            split = text_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            const separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        rupiah = rupiah.replace('.', ',');
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
