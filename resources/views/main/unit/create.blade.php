<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Tambah Asset</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formCreate" action="{{ route('asset.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="col-12 col-md-12">
        <label class="form-label" for="image">Gambar</label>
        <input type="file" id="image" name="image" class="form-control" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="name">Merek<span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan name" required />
    </div>
    <div class="col-12 col-md-6" id="categoryParent">
        <label class="form-label" for="category">Kategori</label>
        <input type="text" id="category" name="category" class="form-control" placeholder="Masukkan Kategori"
            required />
    </div>
    {{-- <div class="col-12 col-md-6">
        <label class="form-label" for="brand">Brand</label>
        <input type="text" id="brand" name="brand" class="form-control" placeholder="Masukkan brand" />
    </div> --}}
    <div class="col-12 col-md-6">
        <label class="form-label" for="unit">Unit</label>
        <input type="text" id="unit" name="unit" class="form-control" placeholder="Masukkan unit" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="type">Type</label>
        <input type="text" id="type" name="type" class="form-control" placeholder="Masukkan type" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="license_plate">Nomor Polisi</label>
        <input type="text" id="license_plate" name="license_plate" class="form-control"
            placeholder="Masukkan nomor polisi" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="classification">Klasifikasi</label>
        <input type="text" id="classification" name="classification" class="form-control"
            placeholder="Masukkan classification" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="chassis_number">Nomor Rangka</label>
        <input type="text" id="chassis_number" name="chassis_number" class="form-control"
            placeholder="Masukkan chassis_number" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="machine_number">Nomor Mesin</label>
        <input type="text" id="machine_number" name="machine_number" class="form-control"
            placeholder="Masukkan nomor mesin" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="nik">NIK</label>
        <input type="text" id="nik" name="nik" class="form-control" placeholder="Masukkan nik" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="color">Warna</label>
        <input type="text" id="color" name="color" class="form-control" placeholder="Masukkan warna" />
    </div>
    <div class="col-12 col-md-6" id="managerParent">
        <label class="form-label" for="owner">Pemilik</label>
        <input type="text" id="owner" name="owner" class="form-control" placeholder="Masukkan Pemilik" />
        {{-- <select id="manager_id" name="manager" class="select2 form-select select2-primary"data-allow-clear="true">
        </select> --}}
    </div>
    <div class="col-12 col-md-6" id="picRelation">
        <label class="form-label" for="pic">PIC<span class="text-danger">*</span></label>
        <select id="pic" name="pic" class="select2 form-select select2-primary"data-allow-clear="true"
            required>
        </select>
    </div>
    <div class="col-12 col-md-12" id="assets_locationParent">
        <label class="form-label" for="assets_location">Lokasi</label>
        <input type="text" id="assets_location" name="assets_location" class="form-control"
            placeholder="Masukkan Lokasi" />
        {{-- <select id="assets_location_id" name="assets_location"
            class="select2 form-select select2-primary"data-allow-clear="true">
        </select> --}}
    </div>
    {{-- <div class="col-12 col-md-6" id="userRelation">
        <label class="form-label" for="pic">pic<span class="text-danger">*</span></label>
        <select id="pic" name="pic" class="select2 form-select select2-primary"data-allow-clear="true"
            required>
        </select>
    </div> --}}

    <div class="col-12">
        <hr class="my-4">
    </div>
    <div class="col-md mb-4 mb-md-2">
        <div class="accordion mt-3" id="accordionExample">
            <div class="card accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                        data-bs-target="#accordionOne" aria-expanded="false" aria-controls="accordionOne">
                        Informasi Asset
                    </button>
                </h2>

                <div id="accordionOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample"
                    style="">
                    <div class="accordion-body row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="purchase_date">Tanggal Pembelian</label>
                            <input type="date" id="purchase_date" name="purchase_date" class="form-control"
                                placeholder="Masukkan purchase_date" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="type_purchase">Type Purchase</label>
                            <select name="type_purchase" id="type_purchase" class="select2 form-select">
                                <option value="">Pilih Tipe Pembelian</option>
                                <option value="buy">Buy</option>
                                <option value="rent">Rent</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="cost">Biaya</label>
                            <input type="number" min="1" id="cost" name="cost" class="form-control"
                                placeholder="Masukkan cost" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="contract_period">Contract Period</label>
                            <input type="date" id="contract_period" name="contract_period" class="form-control"
                                placeholder="Masukkan contract_period" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="file_reminder">Upload File Reminder</label>
                            <input type="file" id="file_reminder" name="file_reminder" class="form-control"
                                placeholder="Masukkan file_reminder" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="date_reminder">Tanggal Reminder</label>
                            <input type="date" id="date_reminder" name="date_reminder" class="form-control"
                                placeholder="Masukkan date_reminder" />
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="serial_number">Nomor Seri</label>
                            <input type="text" id="serial_number" name="serial_number" class="form-control"
                                placeholder="Masukkan serial_number" />
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="description">Keterangan</label>
                            <textarea name="description" id="description" class="form-control" cols="30" rows="5"></textarea>
                        </div>
                        {{-- <div class="col-12 col-md-6">
                            <label class="form-label" for="model_number">Nomor Model</label>
                            <input type="text" id="model_number" name="model_number" class="form-control"
                                placeholder="Masukkan model_number" />
                        </div>
                        <div class="col-12 col-md-12 mb-3">
                            <label class="form-label" for="warranty_period">Waktu Garansi</label>
                            <input type="number" min="1" id="warranty_period" name="warranty_period"
                                class="form-control" placeholder="Masukkan warranty_period" />
                        </div> --}}
                        {{-- Insurance --}}
                        <hr>
                        <h5>Informasi Insurance</h5>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="no_policy">Nomor Policy</label>
                            <input type="text" id="no_policy" name="no_policy" class="form-control"
                                placeholder="Masukkan no_policy" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="insurance_name">Nama Asuransi</label>
                            <input type="text" id="insurance_name" name="insurance_name" class="form-control"
                                placeholder="Masukkan insurance_name" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="insurance_cost">Biaya Asuransi</label>
                            <input type="text" id="insurance_cost" name="insurance_cost" class="form-control"
                                placeholder="Masukkan insurance_cost" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="asuransi_date">Tanggal Asuransi</label>
                            <input type="date" id="asuransi_date" name="asuransi_date" class="form-control"
                                placeholder="Masukkan asuransi" />
                        </div>
                        <div class="col-12 col-md-12 mb-3">
                            <label class="form-label" for="asuransi">Upload File Asuransi</label>
                            <input type="file" id="asuransi" name="asuransi" class="form-control"
                                placeholder="Masukkan asuransi" />
                        </div>
                        {{-- tax --}}
                        <hr>
                        <h5>Informasi Tax</h5>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="tax_cost">Biaya Pajak</label>
                            <input type="text" id="tax_cost" name="tax_cost" class="form-control"
                                placeholder="Masukkan tax_cost" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="tax_period">Waktu Pajak</label>
                            <input type="date" id="tax_period" name="tax_period" class="form-control"
                                placeholder="Masukkan tax_period" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="date_tax">Tanggal Pengingat Pajak</label>
                            <input type="date" id="date_tax" name="date_tax" class="form-control"
                                placeholder="Masukkan date_tax" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="file_tax">Upload File Pajak</label>
                            <input type="file" id="file_tax" name="file_tax" class="form-control"
                                placeholder="Masukkan file_tax" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                        data-bs-target="#accordionTwo" aria-expanded="false" aria-controls="accordionTwo">
                        Informasi Depreciation Asset
                    </button>
                </h2>
                <div id="accordionTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="depreciation">Bulan</label>
                            <select id="depreciation" name="depreciation"
                                class="form-control"aria-label="Pilih Bulan">
                                <option value="" selected>Pilih Bulan</option>
                                @foreach (\App\Helpers\Helper::bulan() as $key => $bln)
                                    <option value="{{ $key }}" {{ now()->month == $key ? 'selected' : '' }}>
                                        {{ $bln }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="depreciation_percentage">Presentase Depreciation</label>
                            <input type="number" id="depreciation_percentage" name="depreciation_percentage"
                                class="form-control" placeholder="Masukkan depreciation_percentage" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="depreciation_method">Metode Depreciation</label>
                            <select name="depreciation_method" id="depreciation_method" class="select2 form-select">
                                <option value="">Pilih Metode</option>
                                <option value="Resuding Balance Depreciation">Resuding Balance Depreciation</option>
                                <option value="Straight-Line Deprecitaion">Straight-Line Deprecitaion</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="residual_value">Nilai Sisa</label>
                            <input type="number" min="1" id="residual_value" name="residual_value"
                                class="form-control" placeholder="Masukkan residual_value" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                        data-bs-target="#accordionThree" aria-expanded="false" aria-controls="accordionThree">
                        Informasi Appreciation Asset
                    </button>
                </h2>
                <div id="accordionThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="appreciation_rate">Appreciation Rate</label>
                            <input type="number" min="1" id="appreciation_rate" name="appreciation_rate"
                                class="form-control" placeholder="Masukkan appreciation_rate" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="appreciation_period">Periode Appreciation</label>
                            <input type="date" id="appreciation_period" name="appreciation_period"
                                class="form-control" placeholder="Masukkan appreciation_period" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                        data-bs-target="#accordionFour" aria-expanded="false" aria-controls="accordionFour">
                        Informasi Supplier Asset
                    </button>
                </h2>
                <div id="accordionFour" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="supplier_name">Nama Supplier</label>
                            <input type="text" id="supplier_name" name="supplier_name" class="form-control"
                                placeholder="Masukkan Nama Supplier" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="supplier_phone_number">Nomor Telepon Supplier</label>
                            <input type="text" id="supplier_phone_number" name="supplier_phone_number"
                                class="form-control" placeholder="Masukkan Nomor Telpon Supplier" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="supplier_pic_name">Nama PIC Supplier</label>
                            <input type="text" id="supplier_pic_name" name="supplier_pic_name"
                                class="form-control" placeholder="Masukkan Nama PIC Supplier" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="supplier_pic_phone">Nomor PIC Telepon Supplier</label>
                            <input type="text" id="supplier_pic_phone" name="supplier_pic_phone"
                                class="form-control" placeholder="Masukkan Nomor PIC Telepon Supplier" />
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="supplier_office_number">Nomor Kantor Supplier</label>
                            <input type="text" id="supplier_office_number" name="supplier_office_number"
                                class="form-control" placeholder="Masukkan Nomor Kantor Supplier" />
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="supplier_address">Alamat Supplier</label>
                            <textarea id="supplier_address" name="supplier_address" class="form-control" cols="30" rows="5"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h5>Tambah Field Dinamis</h5>
    <div class="custom-fields">
        <div class="custom-field">
            <div class="row">
                <div class="col-md-4">
                    <select name="custom_field_type[]" class="form-control" id="custom_field_type_1">
                        <option value="">Pilih Tipe</option>
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="custom_field_name[]" class="form-control"
                        placeholder="Masukkan nama field">
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" name="custom_field_value[]" class="form-control"
                            placeholder="Masukkan nilai field" id="custom_field_value_1">
                        <button class="btn btn-danger remove-field">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-primary tambah-field">Tambah Field</button>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

@include('components.select2_js')
<script>
    $(document).ready(function() {
        var count = 1;
        $('.tambah-field').on('click', function() {
            count++;
            var html = `
      <div class="custom-field">
        <div class="row">
          <div class="col-md-4">
            <select name="custom_field_type[]" class="form-control" id="custom_field_type_${count}">
              <option value="">Pilih Tipe</option>
              <option value="text">Text</option>
              <option value="number">Number</option>
              <option value="date">Date</option>
            </select>
          </div>
          <div class="col-md-4">
            <input type="text" name="custom_field_name[]" class="form-control" placeholder="Masukkan nama field">
          </div>
          <div class="col-md-4">
            <div class="input-group">
              <input type="text" name="custom_field_value[]" class="form-control" placeholder="Masukkan nilai field" id="custom_field_value_${count}">
              <button class="btn btn-danger remove-field">Hapus</button>
            </div>
          </div>
        </div>
      </div>
    `;
            $('.custom-fields').append(html);
            ubahTipeField(count);
        });

        $(document).on('click', '.remove-field', function() {
            $(this).parent('.input-group').parent('.col-md-4').parent('.row').parent('.custom-field')
                .remove();
        });

        function ubahTipeField(index) {
            $(`#custom_field_type_${index}`).on('change', function() {
                var tipe = $(this).val();
                if (tipe == 'text') {
                    $(`#custom_field_value_${index}`).attr('type', 'text');
                } else if (tipe == 'number') {
                    $(`#custom_field_value_${index}`).attr('type', 'number');
                } else if (tipe == 'date') {
                    $(`#custom_field_value_${index}`).attr('type', 'date');
                }
            });
        }
        ubahTipeField(1);
    });

    $('document').ready(function() {
        $('#category_id').select2({
            dropdownParent: $('#categoryParent'),
            placeholder: 'Pilih Kategori',
            ajax: {
                url: "{{ route('category.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.data.map(function(item) {
                            return {
                                text: item.name,
                                id: item.id
                            };
                        })
                    };
                },
                cache: true
            }
        });

        $('#manager_id').select2({
            dropdownParent: $('#managerParent'),
            placeholder: 'Pilih pemilik',
            ajax: {
                url: "{{ route('manager.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.data.map(function(item) {
                            return {
                                text: item.name,
                                id: item.id
                            };
                        })
                    };
                },
                cache: true
            }
        });

        $('#assets_location_id').select2({
            dropdownParent: $('#assets_locationParent'),
            placeholder: 'Pilih lokasi',
            ajax: {
                url: "{{ route('location.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.data.map(function(item) {
                            return {
                                text: item.name,
                                id: item.id
                            };
                        })
                    };
                },
                cache: true
            }
        });

        $('#pic').select2({
            dropdownParent: $('#picRelation'),
            placeholder: 'Pilih PIC',
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
                    apiResults = data.data
                        .filter(function(item) {
                            return item.relationId !== null;
                        })
                        .map(function(item) {
                            return {
                                text: item.name + ' (' + item.nameTitle + ')',
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
    });
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
