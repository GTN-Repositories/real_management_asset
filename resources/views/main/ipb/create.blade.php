<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Tambah Fuel Consumtion</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formCreate" action="{{ route('fuel-ipb.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="col-12 col-md-12" id="managementRelation">
        <label class="form-label" for="management_project_id">Nama Management Project<span
                class="text-danger">*</span></label>
        <select id="management_project_id" name="management_project_id"
            class="select2 form-select select2-primary"data-allow-clear="true" required>
        </select>
    </div>
    <div class="col-12 col-md-6" id="driverRelation">
        <label class="form-label" for="user_id">Pengguna<span class="text-danger">*</span></label>
        <select id="user_id" name="user_id" class="select2 form-select select2-primary"data-allow-clear="true"
            required>
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="date">Tanggal<span class="text-danger">*</span></label>
        <input type="date" id="date" name="date" class="form-control" placeholder="Masukkan date" required />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="issued_liter">Liter yang dikeluarkan</label>
        <input type="text" id="issued_liter" name="issued_liter" class="form-control"
            placeholder="Masukkan liter yang digunakan" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="usage_liter">Liter yang digunakan<span class="text-danger">*</span></label>
        <input type="text" id="usage_liter" name="usage_liter" class="form-control" placeholder="otomatis terisi"
            required readonly />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="unit_price">Harga<span class="text-danger">*</span></label>
        <input type="text" id="unit_price" name="unit_price" class="form-control" placeholder="masukkan harga"
            required />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="location">Lokasi<span class="text-danger">*</span></label>
        <input type="text" id="location" name="location" class="form-control" placeholder="masukkan location"
            required />
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
                        keyword: params.term
                    };
                },
                processResults: function(data) {
                    let apiResults = data.data.map(item => ({
                        text: item.name,
                        id: item.managementRelationId,
                    }));
                    return {
                        results: apiResults
                    };
                },
                cache: true
            }
        });

        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        $('#date').val(formattedDate);

        $('#management_project_id, #date').on('change', function() {
            const projectId = $('#management_project_id').val();
            const date = $('#date').val();

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
                        const formattedValue = formatCurrency(response.total_liter);
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
                            return item.idRelationAll !== null;
                        })
                        .map(function(item) {
                            return {
                                text: item.name,
                                id: item.idRelationAll,
                            };
                        });

                    return {
                        results: apiResults
                    };
                },
                cache: true
            }
        });
    })

    $(document).on('input', '#issued_liter, #usage_liter, #unit_price', function() {
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
