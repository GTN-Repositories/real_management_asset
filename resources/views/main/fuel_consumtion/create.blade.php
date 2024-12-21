<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Tambah Fuel Consumtion</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formCreate" action="{{ route('fuel.store') }}" enctype="multipart/form-data">
    @csrf

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
    <div class="col-12 col-md-6" id="karyawanRelation">
        <label class="form-label" for="user_id">Nama Karyawan<span class="text-danger">*</span></label>
        <select id="user_id" name="user_id" class="select2 form-select select2-primary"data-allow-clear="true"
            required>
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="date">Tanggal<span class="text-danger">*</span></label>
        <input type="date" id="date" name="date" class="form-control" placeholder="Masukkan tanggal"
            value="{{ date('Y-m-d') }}" required />
    </div>
    {{-- <div class="col-12 col-md-6">
        <label class="form-label" for="loadsheet">Loadsheet<span class="text-danger">*</span></label>
        <input type="text" id="loadsheet" name="loadsheet" class="form-control" placeholder="Masukkan loadsheet"
            required />
    </div> --}}
    <div class="col-12 col-md-6">
        <label class="form-label" for="liter">Liter<span class="text-danger">*</span></label>
        <input type="text" id="liter" name="liter" class="form-control" placeholder="Masukkan liter"
            required />
    </div>
    {{-- <div class="col-12 col-md-6">
        <label class="form-label" for="price">Harga/Liter<span class="text-danger">*</span></label>
        <input type="text" id="price" name="price" class="form-control" placeholder="Masukkan harga"
            required />
    </div> --}}
    <div class="col-12 col-md-6">
        <label class="form-label" for="category">Kategori<span class="text-danger">*</span></label>
        <input type="text" id="category" name="category" class="form-control" placeholder="Masukkan kategori"
            required />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="hm">HM<span class="text-danger">*</span></label>
        <input type="text" id="hm" name="hm" class="form-control" placeholder="Masukkan HM" required />
    </div>
    {{-- <div class="col-12 col-md-6">
        <label class="form-label" for="hours">Jam Kerja<span class="text-danger">*</span></label>
        <input type="text" id="hours" name="hours" class="form-control" placeholder="Masukkan jam kerja"
            required />
    </div> --}}
    <div class="col-12 col-md-6">
        <label class="form-label" for="lasted_km_asset">KM Terakhir Asset<span class="text-danger">*</span></label>
        <input type="text" id="lasted_km_asset" name="lasted_km_asset" class="form-control"
            placeholder="Masukkan km terakhir asset" required />
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



        $('#user_id').select2({
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
                        .filter(function(item) {
                            return item.relationId !== null;
                        })
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
    })

    $(document).on('input', '#price, #loadsheet, #liter, #hours,#hm, #lasted_km_asset', function() {
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
