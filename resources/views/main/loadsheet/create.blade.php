<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Tambah Loadsheet</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formCreate" action="{{ route('loadsheet.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="col-12 col-md-6">
        <label class="form-label" for="date">Tanggal <span class="text-danger">*</span></label>
        <input type="date" id="date" name="date" class="form-control" required
            value="{{ now()->format('Y-m-d') }}" />
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
        <label class="form-label" for="hours">Jam Operasional <span class="text-danger">*</span></label>
        <input type="text" id="hours" name="hours" class="form-control" required />
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
                            <label class="form-label" for="type">Jenis Pekerjaan <span class="text-danger">*</span></label>
                            <input type="text" id="type" name="type" class="form-control" />
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
                            <textarea id="location" name="location" class="form-control"></textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="bpit">Loading Area <span class="text-danger">*</span></label>
                            <input type="text" id="bpit" name="bpit" class="form-control" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="kilometer">Jarak (KM) <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="kilometer" name="kilometer" class="form-control" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="loadsheet">Load/Ritase <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="loadsheet" name="loadsheet" class="form-control" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="perload">Tonase/Kubikasi <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="perload" name="perload" class="form-control" />
                        </div>
                        <div class="col-12 col-md-6">
                            @if (session('selected_project_id'))
                                @if (\App\Helpers\Helper::projectSelected()->calculation_method == 'Kubic')
                                    <th>Lose Factor</th>
                                @elseif (\App\Helpers\Helper::projectSelected()->calculation_method == 'Tonase')
                                    <th>RF</th>
                                @endif
                            @else
                                <th>Lose Factor/RF</th>
                            @endif
                            <input type="text" id="lose_factor" name="lose_factor" class="form-control" value="0.75"/>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="billing_status">Status Penagihan</label>
                            <select name="billing_status" id="billing_status" class="select2 form-select">
                                <option value="Belum">Belum Ditagih</option>
                                <option value="Sudah Ditagih">Sudah Ditagih</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="remarks">Remarks <span
                                    class="text-danger">*</span></label>
                            <textarea id="remarks" name="remarks" class="form-control"></textarea>
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
                    let apiResults = data.data.map(item => ({
                        text: item.format_id + ' - ' + item.name,
                        id: item.managementRelationId,
                    }));
                    return {
                        results: apiResults
                    };
                },
                cache: true
            }
        }).on('change', function() {
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
                        if (data && typeof data === 'object' && Object.keys(data).length) {
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
                                text: item.name + ' ' + item.nameTitle,
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
    });

    $(document).on('input', '#hours, #kilometer, #loadsheet, #perload, #price', function() {
        value = formatCurrency($(this).val());
        $(this).val(value);
    });

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
</script>
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
