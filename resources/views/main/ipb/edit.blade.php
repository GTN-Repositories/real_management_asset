<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Request Fuel</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formEdit" action="{{ route('fuel-ipb.update', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="col-12 col-md-12" id="managementRelation">
        <label class="form-label" for="management_project_id">Nama Management Project<span
                class="text-danger">*</span></label>
        <select id="management_project_id" name="management_project_id" class="select2 form-select select2-primary"
            data-allow-clear="true" required></select>
    </div>
    <div class="col-12 col-md-6" id="driverRelation">
        <label class="form-label" for="employee_id">Karyawan<span class="text-danger">*</span></label>
        <select id="employee_id" name="employee_id" class="select2 form-select select2-primary" data-allow-clear="true"
            required></select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="date">Tanggal<span class="text-danger">*</span></label>
        <input type="date" id="date" name="date" class="form-control" required value="{{ $data->date }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="issued_liter">Liter yang dikeluarkan</label>
        <input type="text" id="issued_liter" name="issued_liter" class="form-control"
            value="{{ $data->issued_liter }}" placeholder="Masukkan liter yang digunakan" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="usage_liter">Liter yang digunakan<span class="text-danger">*</span></label>
        <input type="text" id="usage_liter" name="usage_liter" class="form-control" value="{{ $data->usage_liter }}"
            placeholder="otomatis terisi" required readonly />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="unit_price">Harga<span class="text-danger">*</span></label>
        <input type="text" id="unit_price" name="unit_price" class="form-control" value="{{ $data->unit_price }}"
            placeholder="Masukkan harga" required />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="location">Lokasi<span class="text-danger">*</span></label>
        <input type="text" id="location" name="location" class="form-control" value="{{ $data->location }}"
            placeholder="masukkan lokasi" required />
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
        var managementProjectId = '{{ $data->management_project_id }}';
        var managementProjectName = '{{ $data->management_project->name }}';

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
                    var results = data.data.map(item => ({
                        text: item.name,
                        id: item.managementRelationId
                    }));
                    return {
                        results
                    };
                },
                cache: true
            }
        });

        if (managementProjectId) {
            const projectOption = new Option(managementProjectName, managementProjectId, true, true);
            $('#management_project_id').append(projectOption).trigger('change');
        }

        $('#employee_id').select2({
            dropdownParent: $('#driverRelation'),
            placeholder: 'Pilih karyawan',
            ajax: {
                url: "{{ route('employee.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function(data) {
                    var results = data.data.map(item => ({
                        text: item.name,
                        id: item.relationId
                    }));
                    return {
                        results
                    };
                },
                cache: true
            }
        });

        let userId = '{{ $data->employee_id ?? '' }}';
        let userName = '{{ $data->employee->name ?? '' }}';
        if (userId) {
            let userOption = new Option(userName, userId, true, true);
            $('#employee_id').append(userOption).trigger('change');
        }

        var today = new Date();
        var formattedDate = today.toISOString().split('T')[0];
        $('#date').val(formattedDate);

        $('#management_project_id, #date').on('change', function() {
            var projectId = $('#management_project_id').val();
            var date = $('#date').val();

            if (projectId && date) {
                fetchTotalLiter(projectId, date);
            }
        });

        function fetchTotalLiter(projectId, date) {
            $.ajax({
                url: "{{ route('fuel-ipb.total-liter') }}",
                method: 'POST',
                data: {
                    management_project_id: projectId,
                    date: date,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.total_liter !== undefined) {
                        var formattedValue = formatCurrency(response.total_liter);
                        $('#usage_liter').prop('readonly', false).val(formattedValue).prop(
                            'readonly', true);
                    } else {
                        $('#usage_liter').prop('readonly', false).val('0').prop('readonly', true);
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Failed to fetch total liter!'
                    });
                }
            });
        }

        $(document).ready(function() {
            $('#issued_liter, #usage_liter, #unit_price, #price, #liter').each(function() {
                var value = $(this).val();
                $(this).val(formatCurrency(value));
            });
        });

        $(document).on('input', '#issued_liter, #usage_liter, #unit_price, #price, #liter', function() {
            var value = $(this).val();
            $(this).val(formatCurrency(value));
        });

        function formatCurrency(angka, prefix) {
            if (!angka) {
                return (prefix || '') + '-';
            }

            angka = angka.toString();
            var splitDecimal = angka.split('.');
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix === undefined ? rupiah : rupiah ? (prefix || '') + rupiah : '';
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
