<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Management Project</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formEdit" action="{{ route('management-project.update', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="col-12 col-md-6">
        <label class="form-label" for="name">Nama Management Project<span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan name" required
            value="{{ $data->name }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="value_project">Value Project<span class="text-danger">*</span></label>
        <input type="text" id="value_project" name="value_project" class="form-control"
            placeholder="Masukkan value project" required value="{{ $data->value_project }}" />
    </div>
    <div class="col-12 col-md-6">
        <label for="date-range-pickers" class="form-label">Periode Waktu</label>
        <input type="text" id="date-range-pickers" name="date_range" class="form-control"
            placeholder="Select Date Range">
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="calculation_method">Metode Kalkulasi</label>
        <select name="calculation_method" id="calculation_method" class="form-select select2">
            <option value="">Pilih</option>
            <option value="Kubic" @if ($data->calculation_method == 'Kubic') selected @endif>Kubic</option>
            <option value="Tonase" @if ($data->calculation_method == 'Tonase') selected @endif>Tonase</option>
        </select>
    </div>
    <div class="col-12 col-md-12">
        <label class="form-label" for="location">Lokasi Project<span class="text-danger">*</span></label>
        <textarea name="location" id="location" cols="30" rows="5" class="form-control">{{ $data->location }}</textarea>
    </div>
    <div class="col-12 col-md-12" id="relationId">
        <label class="form-label" for="asset_id">Nama Asset<span class="text-danger">*</span></label>
        <div class="select2-primary">
            <div class="position-relative">
                <select id="asset_id" name="asset_id[]" class="select2 form-select" multiple required>
                    <!-- Options will be populated dynamically -->
                </select>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-12" id="employeeId">
        <label for="employee_id" class="form-label">Karyawan<span class="text-danger">*</span></label>
        <div class="select2-primary">
            <div class="position-relative">
                <select id="employee_id" name="employee_id[]" class="select2 form-select" multiple required>
                    <!-- Options will be populated dynamically -->
                </select>
            </div>
        </div>
    </div>
    {{-- <div class="col-12 col-md-6">
        <label for="date-range-pickers" class="form-label">Periode Waktu</label>
        <div class="input-group" id="date-range-pickers">
            <input type="date" id="start_date" name="start_date" class="form-control" placeholder="Start Date"
                value="{{ $data->start_date }}" required>
            <span class="input-group-text">to</span>
            <input type="date" id="end_date" name="end_date" class="form-control" placeholder="End Date"
                value="{{ $data->end_date }}" required>
        </div>
    </div> --}}
    
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

@include('components.select2_js')
<script>
    $(document).ready(function() {
        $('#date-range-pickers').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        var startDate = "{{ $data->start_date }}";
        var endDate = "{{ $data->end_date }}";

        // Menggunakan moment untuk memformat tanggal
        var formatStartDate = moment(startDate);
        var formatEndDate = moment(endDate);

        $('#date-range-pickers').daterangepicker({
            startDate: formatStartDate,
            endDate: formatEndDate,
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        }, function(start, end) {
            $('#date-range-pickers').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
        });

        // Set default value pada input
        $('#date-range-pickers').val(formatStartDate.format('MM/DD/YYYY') + ' - ' + formatEndDate.format('MM/DD/YYYY'));
        
        $('#asset_id').select2({
            dropdownParent: $('#relationId'),
            placeholder: 'Pilih aset',
            ajax: {
                url: "{{ route('asset.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term,
                        limit: 10
                    };
                },
                processResults: function(data) {
                    var apiResults = data.data.map(function(item) {
                        return {
                            text: item.nameWithNumber,
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

        $('#employee_id').select2({
            dropdownParent: $('#employeeId'),
            placeholder: 'Pilih karyawan',
            ajax: {
                url: "{{ route('employee.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term,
                        limit: 10
                    };
                },
                processResults: function(data) {
                    apiResults = data.data.map(function(item) {
                        return {
                            text: item.nameTitle,
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

        var asset_ids = {!! json_encode($data->asset_id ?? []) !!};
        var asset_id = {!! json_encode($data->assets->pluck('id')->map(fn($id) => Crypt::decrypt($id)) ?? []) !!};
        var asset_names = {!! json_encode($data->assets->pluck('name') ?? []) !!};
        var asset_numbers = {!! json_encode($data->assets->pluck('asset_number') ?? []) !!};

        if (asset_ids && asset_names && asset_numbers) {
            asset_ids.forEach(function(id, index) {
                var option = new Option(
                    `${asset_id[index]} - ${asset_names[index]} - ${asset_numbers[index]}`, id,
                    true,
                    true);
                $('#asset_id').append(option);
            });
            $('#asset_id').trigger('change');
        }

        var employee_ids = {!! json_encode($data->employee_id ?? []) !!};
        var employee_names = {!! json_encode($data->employees->pluck('name') ?? []) !!};
        var job_title_names = {!! json_encode($data->employees->map(function($employee) {
            return $employee->jobTitle ? $employee->jobTitle->name : null;
        })->toArray() ?? []) !!};

        employee_ids = Array.isArray(employee_ids) ? employee_ids : [];
        employee_names = Array.isArray(employee_names) ? employee_names : [];
        job_title_names = Array.isArray(job_title_names) ? job_title_names : [];

        if (employee_ids.length > 0 && employee_names.length > 0 && job_title_names.length > 0) {
            employee_ids.forEach(function(id, index) {
                var name = `${employee_names[index]} - ${job_title_names[index] || 'Undefined'}`;
                var option = new Option(name, id, true, true);
                $('#employee_id').append(option);
            });
            $('#employee_id').trigger('change');
        } else {
            $('#employee_id').empty();
            $('#employee_id').trigger('change');
        }


    });

    $(document).ready(function() {
        $('#value_project').each(function() {
            const value = $(this).val();
            $(this).val(formatCurrency(value));
        });
    });

    $(document).on('input', '#value_project', function() {
        const value = $(this).val();
        $(this).val(formatCurrency(value));
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
