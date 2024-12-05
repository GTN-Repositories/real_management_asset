<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Kendaraan/Unit</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formEdit" action="{{ route('asset.update', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="col-12 col-md-12">
        <label class="form-label" for="image">Gambar</label>
        <input type="file" id="image" name="image" class="form-control" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="name">Nama Asset<span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan name" required
            value="{{ $data->name ?? '' }}" />
    </div>
    <div class="col-12 col-md-6" id="categoryParent">
        <label class="form-label" for="category">Kategori</label>
        <select id="category_id" name="category" class="select2 form-select select2-primary"data-allow-clear="true">
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="brand">Brand</label>
        <input type="text" id="brand" name="brand" class="form-control" placeholder="Masukkan brand"
            value="{{ $data->brand ?? '' }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="unit">Unit</label>
        <input type="text" id="unit" name="unit" class="form-control" placeholder="Masukkan unit"
            value="{{ $data->unit ?? '' }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="type">Type</label>
        <input type="text" id="type" name="type" class="form-control" placeholder="Masukkan type"
            value="{{ $data->type ?? '' }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="license_plate">Nomor Polisi</label>
        <input type="text" id="license_plate" name="license_plate" class="form-control"
            placeholder="Masukkan nomor polisi" value="{{ $data->license_plate ?? '' }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="classification">Klasifikasi</label>
        <input type="text" id="classification" name="classification" class="form-control"
            placeholder="Masukkan classification" value="{{ $data->classification ?? '' }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="chassis_number">Nomor Rangka</label>
        <input type="text" id="chassis_number" name="chassis_number" class="form-control"
            placeholder="Masukkan chassis_number" value="{{ $data->chassis_number ?? '' }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="machine_number">Nomor Mesin</label>
        <input type="text" id="machine_number" name="machine_number" class="form-control"
            placeholder="Masukkan nomor mesin" value="{{ $data->machine_number ?? '' }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="nik">Nik</label>
        <input type="text" id="nik" name="nik" class="form-control" placeholder="Masukkan nik"
            value="{{ $data->nik ?? '' }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="color">Warna</label>
        <input type="text" id="color" name="color" class="form-control" placeholder="Masukkan warna"
            value="{{ $data->color ?? '' }}" />
    </div>
    <div class="col-12 col-md-6" id="managerParent">
        <label class="form-label" for="manager">Pemilik</label>
        <select id="manager_id" name="manager" class="select2 form-select select2-primary"data-allow-clear="true">
        </select>
    </div>
    <div class="col-12 col-md-6" id="assets_locationParent">
        <label class="form-label" for="assets_location">Lokasi</label>
        <select id="assets_location_id" name="assets_location"
            class="select2 form-select select2-primary"data-allow-clear="true">
        </select>
    </div>
    <div class="col-12 col-md-6" id="userRelation">
        <label class="form-label" for="pic">pic<span class="text-danger">*</span></label>
        <select id="pic" name="pic" class="select2 form-select select2-primary"data-allow-clear="true"
            required>
        </select>
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
                        Informasi Asset
                    </button>
                </h2>

                <div id="accordionOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample"
                    style="">
                    <div class="accordion-body row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="purchase_date">Tanggal Pembelian</label>
                            <input type="date" id="purchase_date" name="purchase_date" class="form-control"
                                placeholder="Masukkan purchase_date" value="{{ $data->purchase_date ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="type_purchase">Type Purchase</label>
                            <select name="type_purchase" id="type_purchase" class="select2 form-select">
                                <option value="">Pilih Tipe Pembelian</option>
                                <option value="buy" @if ($data->type_purchase == 'buy') selected @endif>Buy</option>
                                <option value="rent" @if ($data->type_purchase == 'rent') selected @endif>Rent</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="cost">Biaya</label>
                            <input type="number" min="1" id="cost" name="cost" class="form-control"
                                placeholder="Masukkan cost" value="{{ $data->cost ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="contract_period">Contract Period</label>
                            <input type="number" min="1" id="contract_period" name="contract_period"
                                class="form-control" placeholder="Masukkan contract_period"
                                value="{{ $data->contract_period ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="file_reminder">Upload File Reminder</label>
                            <input type="file" id="file_reminder" name="file_reminder" class="form-control"
                                placeholder="Masukkan file_reminder" value="{{ $data->file_reminder ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="date_reminder">Tanggal Reminder</label>
                            <input type="date" id="date_reminder" name="date_reminder" class="form-control"
                                placeholder="Masukkan date_reminder" value="{{ $data->date_reminder ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="description">Keterangan</label>
                            <textarea name="description" id="description" class="form-control" cols="30" rows="5">{{ $data->description ?? '' }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="serial_number">Nomor Seri</label>
                            <input type="text" id="serial_number" name="serial_number" class="form-control"
                                placeholder="Masukkan serial_number" value="{{ $data->serial_number ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="model_number">Nomor Model</label>
                            <input type="text" id="model_number" name="model_number" class="form-control"
                                placeholder="Masukkan model_number" value="{{ $data->model_number ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-12 mb-3">
                            <label class="form-label" for="warranty_period">Waktu Garansi</label>
                            <input type="number" min="1" id="warranty_period" name="warranty_period"
                                class="form-control" placeholder="Masukkan warranty_period"
                                value="{{ $data->warranty_period ?? '' }}" />
                        </div>
                        {{-- assurance --}}
                        <hr>
                        <h5>Informasi Assurance</h5>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="no_policy">Nomor Policy</label>
                            <input type="text" id="no_policy" name="no_policy" class="form-control"
                                placeholder="Masukkan no_policy" value="{{ $data->no_policy ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="insurance_name">Nama Asuransi</label>
                            <input type="text" id="insurance_name" name="insurance_name" class="form-control"
                                placeholder="Masukkan insurance_name" value="{{ $data->insurance_name ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="insurance_cost">Biaya Asuransi</label>
                            <input type="text" id="insurance_cost" name="insurance_cost" class="form-control"
                                placeholder="Masukkan insurance_cost" value="{{ $data->insurance_cost ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="asuransi_date">Tanggal Asuransi</label>
                            <input type="date" id="asuransi_date" name="asuransi_date" class="form-control"
                                placeholder="Masukkan asuransi" value="{{ $data->asuransi_date ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-12 mb-3">
                            <label class="form-label" for="asuransi">Upload File Asuransi</label>
                            <input type="file" id="asuransi" name="asuransi" class="form-control"
                                placeholder="Masukkan asuransi" value="{{ $data->asuransi ?? '' }}" />
                        </div>
                        {{-- tax --}}
                        <hr>
                        <h5>Informasi Tax</h5>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="tax_cost">Biaya Pajak</label>
                            <input type="text" id="tax_cost" name="tax_cost" class="form-control"
                                placeholder="Masukkan tax_cost" value="{{ $data->tax_cost ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="tax_period">Waktu Pajak</label>
                            <input type="number" min="1" id="tax_period" name="tax_period"
                                class="form-control" placeholder="Masukkan tax_period"
                                value="{{ $data->tax_period ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="date_tax">Tanggal Pengingat Pajak</label>
                            <input type="date" id="date_tax" name="date_tax" class="form-control"
                                placeholder="Masukkan date_tax" value="{{ $data->date_tax ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="file_tax">Upload File Pajak</label>
                            <input type="file" id="file_tax" name="file_tax" class="form-control"
                                placeholder="Masukkan file_tax" value="{{ $data->file_tax ?? '' }}" />
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
                            <label class="form-label" for="depreciation">Month</label>
                            <input type="number" min="1" id="depreciation" name="depreciation"
                                class="form-control" placeholder="Masukkan depreciation"
                                value="{{ $data->depreciation ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="depreciation_percentage">Presentase Depreciation</label>
                            <input type="text" id="depreciation_percentage" name="depreciation_percentage"
                                class="form-control" placeholder="Masukkan depreciation_percentage"
                                value="{{ $data->depreciation_percentage ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="depreciation_method">Metode Depreciation</label>
                            <select name="depreciation_method" id="depreciation_method" class="select2 form-select">
                                <option value="">Pilih Metode</option>
                                <option value="Resuding Balance Depreciation"
                                    @if ($data->depreciation_method == 'Resuding Balance Depreciation') selected @endif>Resuding Balance Depreciation
                                </option>
                                <option value="Straight-Line Deprecitaion"
                                    @if ($data->depreciation_method == 'Straight-Line Deprecitaion') selected @endif>Straight-Line Deprecitaion
                                </option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="residual_value">Nilai Sisa</label>
                            <input type="number" min="1" id="residual_value" name="residual_value"
                                class="form-control" placeholder="Masukkan residual_value"
                                value="{{ $data->residual_value ?? '' }}" />
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
                                class="form-control" placeholder="Masukkan appreciation_rate"
                                value="{{ $data->appreciation_rate ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="appreciation_period">Periode Appreciation</label>
                            <input type="number" min="1" id="appreciation_period" name="appreciation_period"
                                class="form-control" placeholder="Masukkan appreciation_period"
                                value="{{ $data->appreciation_period ?? '' }}" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                        data-bs-target="#accordionFour" aria-expanded="false" aria-controls="accordionFour">
                        Informasi Pemasok Asset
                    </button>
                </h2>
                <div id="accordionFour" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body row">
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="supplier_name">Nama Pemasok</label>
                            <input type="text" id="supplier_name" name="supplier_name" class="form-control"
                                placeholder="Masukkan supplier_name" value="{{ $data->supplier_name ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="supplier_phone_number">Nomor Telepon Pemasok</label>
                            <input type="text" id="supplier_phone_number" name="supplier_phone_number"
                                class="form-control" placeholder="Masukkan supplier_phone_number"
                                value="{{ $data->supplier_phone_number ?? '' }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="supplier_address">Alamat Pemasok</label>
                            <input type="text" id="supplier_address" name="supplier_address" class="form-control"
                                placeholder="Masukkan supplier_address"
                                value="{{ $data->supplier_address ?? '' }}" />
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

        var categoryId = '{{ $data->category ?? '' }}';
        var categoryName = '{{ $data->asset_category->name ?? '' }}';
        if (categoryId) {
            var categoryOption = new Option(categoryName, categoryId, true, true);
            $('#category_id').append(categoryOption).trigger('change');
        }

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

        var managerId = '{{ $data->manager ?? '' }}';
        var managerName = '{{ $data->asset_manager->name ?? '' }}';
        if (managerId) {
            var managerOption = new Option(managerName, managerId, true, true);
            $('#manager_id').append(managerOption).trigger('change');
        }

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

        var assetsLocationId = '{{ $data->assets_location ?? '' }}';
        var assetsLocationName = '{{ $data->location->name ?? '' }}';
        if (assetsLocationId) {
            var assetsLocationOption = new Option(assetsLocationName, assetsLocationId, true, true);
            $('#assets_location_id').append(assetsLocationOption).trigger('change');
        }

        $('#pic').select2({
            dropdownParent: $('#userRelation'),
            placeholder: 'Pilih PIC',
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

        var picId = '{{ $data->pic ?? '' }}';
        var picName = '{{ $data->pics->name ?? '' }}';
        if (picId) {
            var picOption = new Option(picName, picId, true, true);
            $('#pic').append(picOption).trigger('change');
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
