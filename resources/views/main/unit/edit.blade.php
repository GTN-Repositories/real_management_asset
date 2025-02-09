<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Kendaraan/Unit</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<div id="wizard-create-app" class="bs-stepper vertical mt-2 shadow-none">
    <div class="bs-stepper-header border-0 p-1">
        <div class="step" data-target="#asset">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle"><i class="ti ti-car ti-sm"></i></span>
                <span class="bs-stepper-label">
                    <span class="bs-stepper-title text-uppercase">Asset</span>
                    <span class="bs-stepper-subtitle">Edit Asset</span>
                </span>
            </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#information">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle"><i class="ti ti-notes ti-sm"></i></span>
                <span class="bs-stepper-label">
                    <span class="bs-stepper-title text-uppercase">INFORMASI ASSET</span>
                    <span class="bs-stepper-subtitle">Informasi Asset</span>
                </span>
            </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#depreciation">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle"><i class="ti ti-receipt ti-sm"></i></span>
                <span class="bs-stepper-label">
                    <span class="bs-stepper-title text-uppercase">DEPRECIATION ASSET</span>
                    <span class="bs-stepper-subtitle">Informasi depreciation asset</span>
                </span>
            </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#apreciation">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle"><i class="ti ti-receipt ti-sm"></i></span>
                <span class="bs-stepper-label">
                    <span class="bs-stepper-title text-uppercase">APRECIATION ASSET</span>
                    <span class="bs-stepper-subtitle">Informasi apreciation asset</span>
                </span>
            </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#supplier">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle"><i class="ti ti-file-text ti-sm"></i></span>
                <span class="bs-stepper-label">
                    <span class="bs-stepper-title text-uppercase">SUPPLIER ASSET</span>
                    <span class="bs-stepper-subtitle">Informasi supplier asset</span>
                </span>
            </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#submit">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle"><i class="ti ti-focus-centered ti-sm"></i></span>
                <span class="bs-stepper-label">
                    <span class="bs-stepper-title text-uppercase">FIELD DINAMIS</span>
                    <span class="bs-stepper-subtitle">Tambahkan field dinamis</span>
                </span>
            </button>
        </div>
    </div>
    <div class="bs-stepper-content p-1">
        <form method="POST" class="row g-3" id="formCreate" action="{{ route('asset.update', $data->id) }}"
            enctype="multipart/form-data">
            @csrf
            @method('put')
            <!-- Asset -->
            <div id="asset" class="content ms-3 pt-3 pt-lg-0">
                <span class="text-muted">Gambar</span>
                <div class="card card-body shadow-none dropzone" id="customDropzone"
                    style="background-color: #F8F8F8; border-radius: 4px; border: 2px dashed #000;">
                    <div class="text-center">
                        <h3 class="fw-bold mb-1">Drop your files here!</h3>
                        <span class="note needsclick">or click to upload</span>
                    </div>
                </div>
                <input type="file" class="form-control" id="image" name="image" style="display: none;"
                    multiple>
                <div class="row mt-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="name">Merek<span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control"
                            placeholder="Masukkan name" value="{{ $data->name ?? '' }}" required />
                    </div>
                    <div class="col-12 col-md-4" id="categoryParent">
                        <label class="form-label" for="category">Kategori</label>
                        <input type="text" id="category" name="category" class="form-control"
                            placeholder="Masukkan Kategori" value="{{ $data->category ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="unit">Unit</label>
                        <input type="text" id="unit" name="unit" class="form-control"
                            placeholder="Masukkan unit" value="{{ $data->unit ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="type">Type</label>
                        <input type="text" id="type" name="type" class="form-control"
                            placeholder="Masukkan type" value="{{ $data->type ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="license_plate">Nomor Polisi</label>
                        <input type="text" id="license_plate" name="license_plate" class="form-control"
                            placeholder="Masukkan nomor polisi" value="{{ $data->license_plate ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="classification">Klasifikasi</label>
                        <input type="text" id="classification" name="classification" class="form-control"
                            placeholder="Masukkan classification" value="{{ $data->classification ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="chassis_number">Nomor Rangka</label>
                        <input type="text" id="chassis_number" name="chassis_number" class="form-control"
                            placeholder="Masukkan chassis_number" value="{{ $data->chassis_number ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="machine_number">Nomor Mesin</label>
                        <input type="text" id="machine_number" name="machine_number" class="form-control"
                            placeholder="Masukkan nomor mesin" value="{{ $data->machine_number ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="nik">NIK</label>
                        <input type="text" id="nik" name="nik" class="form-control"
                            placeholder="Masukkan nik" value="{{ $data->nik ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="color">Warna</label>
                        <input type="text" id="color" name="color" class="form-control"
                            placeholder="Masukkan warna" value="{{ $data->color ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-4" id="managerParent">
                        <label class="form-label" for="manager">Asset Manager</label>
                        <input type="text" id="manager" name="manager" class="form-control"
                            placeholder="Masukkan manager" value="{{ $data->manager ?? '' }}" />
                        {{-- <select id="manager_id" name="manager" class="select2 form-select select2-primary"data-allow-clear="true">
                        </select> --}}
                    </div>
                    <div class="col-12 col-md-4" id="picRelation">
                        <label class="form-label" for="pic">PIC<span class="text-danger">*</span></label>
                        <select id="pic" name="pic"
                            class="select2 form-select select2-primary"data-allow-clear="true">
                        </select>
                    </div>
                    <div class="col-12 col-md-6" id="assets_locationParent">
                        <label class="form-label" for="assets_location">Lokasi</label>
                        <input type="text" id="assets_location" name="assets_location" class="form-control"
                            placeholder="Masukkan Lokasi" value="{{ $data->assets_location ?? '' }}" />
                        {{-- <select id="assets_location_id" name="assets_location"
                            class="select2 form-select select2-primary"data-allow-clear="true">
                        </select> --}}
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select select2" id="status" name="status" aria-label="Select status">
                            <option value="Active" {{ $data->status == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ $data->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            {{-- <option value="UnderMaintenance" {{ $data->status == 'UnderMaintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            <option value="UnderRepair" {{ $data->status == 'UnderRepair' ? 'selected' : '' }}>Under Repair</option>
                            <option value="Waiting" {{ $data->status == 'Waiting' ? 'selected' : '' }}>Waiting</option> --}}
                            <option value="Scrap" {{ $data->status == 'Scrap' ? 'selected' : '' }}>Scrap</option>
                            {{-- <option value="RFU" {{ $data->status == 'RFU' ? 'selected' : '' }}>RFU</option> --}}
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="payment_status">Status Pembayaran</label>
                        <select id="payment_status" name="payment_status"
                            class="select2 form-select select2-primary"data-allow-clear="true">
                            <option value="Lunas" {{ $data->payment_status == 'Lunas' ? 'selected' : '' }}>Paid
                            </option>
                            <option value="Leasing" {{ $data->payment_status == 'Leasing' ? 'selected' : '' }}>Leased
                            </option>
                            <option value="Belum Lunas"
                                {{ $data->payment_status == 'Belum Lunas' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6" id="management_project_idRelation">
                        <label class="form-label" for="management_project_id">Assign Project</label>
                        <select id="management_project_id" name="management_project_id"
                            class="select2 form-select select2-primary"data-allow-clear="true">
                        </select>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-between mt-4">
                    <button class="btn btn-label-secondary btn-prev" disabled>
                        <i class="ti ti-arrow-left ti-xs me-sm-1 me-0"></i>
                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                    </button>
                    <button class="btn btn-primary btn-next" type="button">
                        <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                        <i class="ti ti-arrow-right ti-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Information -->
            <div id="information" class="content ms-3 pt-3 pt-lg-0">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="purchase_date">Tanggal Pembelian</label>
                        <input type="date" id="purchase_date" name="purchase_date" class="form-control"
                            placeholder="Masukkan purchase_date" value="{{ $data->purchase_date ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="type_purchase">Type Purchase</label>
                        <select name="type_purchase" id="type_purchase" class="select2 form-select">
                            <option value="">Pilih Tipe Pembelian</option>
                            <option value="buy" {{ $data->type_purchase == 'buy' ? 'selected' : '' }}>Buy</option>
                            <option value="rent" {{ $data->type_purchase == 'rent' ? 'selected' : '' }}>Rent
                            </option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="cost">Biaya</label>
                        <input type="number" min="1" id="cost" name="cost" class="form-control"
                            placeholder="Masukkan cost" {{ $data->cost ?? '' }} />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="contract_period">Contract Period</label>
                        <input type="date" id="contract_period" name="contract_period" class="form-control"
                            placeholder="Masukkan contract_period" value="{{ $data->contract_period ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="file_reminder">Upload File Reminder</label>
                        <input type="file" id="file_reminder" name="file_reminder" class="form-control"
                            placeholder="Masukkan file_reminder" value="{{ $data->file_reminder ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="date_reminder">Tanggal Reminder</label>
                        <input type="date" id="date_reminder" name="date_reminder" class="form-control"
                            placeholder="Masukkan date_reminder" value="{{ $data->date_reminder ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-12">
                        <label class="form-label" for="serial_number">Nomor Seri</label>
                        <input type="text" id="serial_number" name="serial_number" class="form-control"
                            placeholder="Masukkan serial_number" {{ $data->serial_number ?? '' }} />
                    </div>
                    <div class="col-12 col-md-12 mb-3">
                        <label class="form-label" for="description">Keterangan</label>
                        <textarea name="description" id="description" class="form-control" cols="30" rows="5">{{ $data->description ?? '' }}</textarea>
                    </div>
                    <hr>
                        <h5>Informasi Insurance</h5>
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
                            <input type="date" id="tax_period" name="tax_period" class="form-control"
                                placeholder="Masukkan tax_period" value="{{ $data->tax_period ?? '' }}" />
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

                <div class="col-12 d-flex justify-content-between mt-4">
                    <button class="btn btn-label-secondary btn-prev">
                        <i class="ti ti-arrow-left ti-xs me-sm-1 me-0"></i>
                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                    </button>
                    <button class="btn btn-primary btn-next" type="button">
                        <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                        <i class="ti ti-arrow-right ti-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Depreciation -->
            <div id="depreciation" class="content ms-3 pt-3 pt-lg-0">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="depreciation">Bulan</label>
                        <select id="depreciation" name="depreciation" class="form-control"aria-label="Pilih Bulan">
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
                            class="form-control" placeholder="Masukkan depreciation_percentage"
                            value="{{ $data->depreciation_percentage ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="depreciation_method">Metode Depreciation</label>
                        <select name="depreciation_method" id="depreciation_method" class="select2 form-select">
                            <option value="">Pilih Metode</option>
                            <option value="Resuding Balance Depreciation"
                                {{ $data->depreciation_method == 'Resuding Balance Depreciation' ? 'selected' : '' }}>
                                Resuding Balance Depreciation</option>
                            <option value="Straight-Line Deprecitaion"
                                {{ $data->depreciation_method == 'Straight-Line Deprecitaion' ? 'selected' : '' }}>
                                Straight-Line Deprecitaion</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="residual_value">Nilai Sisa</label>
                        <input type="number" min="1" id="residual_value" name="residual_value"
                            class="form-control" placeholder="Masukkan residual_value"
                            value="{{ $data->residual_value ?? '' }}" />
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-between mt-4">
                    <button class="btn btn-label-secondary btn-prev">
                        <i class="ti ti-arrow-left ti-xs me-sm-1 me-0"></i>
                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                    </button>
                    <button class="btn btn-primary btn-next" type="button">
                        <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                        <i class="ti ti-arrow-right ti-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Apreciation -->
            <div id="apreciation" class="content ms-3 pt-3 pt-lg-0">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="appreciation_rate">Appreciation Rate</label>
                        <input type="number" min="1" id="appreciation_rate" name="appreciation_rate"
                            class="form-control" placeholder="Masukkan appreciation_rate"
                            value="{{ $data->appreciation_rate ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="appreciation_period">Periode Appreciation</label>
                        <input type="date" id="appreciation_period" name="appreciation_period"
                            class="form-control" placeholder="Masukkan appreciation_period"
                            value="{{ $data->appreciation_period ?? '' }}" />
                    </div>
                    <div class="col-12 d-flex justify-content-between mt-4">
                        <button class="btn btn-label-secondary btn-prev">
                            <i class="ti ti-arrow-left ti-xs me-sm-1 me-0"></i>
                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                        </button>
                        <button class="btn btn-primary btn-next" type="button">
                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                            <i class="ti ti-arrow-right ti-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Supplier -->
            <div id="supplier" class="content ms-3 pt-3 pt-lg-0">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="supplier_name">Nama Supplier</label>
                        <input type="text" id="supplier_name" name="supplier_name" class="form-control"
                            placeholder="Masukkan Nama Supplier" value="{{ $data->supplier_name ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="supplier_phone_number">Nomor Telepon Supplier</label>
                        <input type="text" id="supplier_phone_number" name="supplier_phone_number"
                            class="form-control" placeholder="Masukkan Nomor Telpon Supplier"
                            value="{{ $data->supplier_phone_number ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="supplier_pic_name">Nama PIC Supplier</label>
                        <input type="text" id="supplier_pic_name" name="supplier_pic_name" class="form-control"
                            placeholder="Masukkan Nama PIC Supplier" value="{{ $data->supplier_pic_name ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="supplier_pic_phone">Nomor PIC Telepon Supplier</label>
                        <input type="text" id="supplier_pic_phone" name="supplier_pic_phone" class="form-control"
                            placeholder="Masukkan Nomor PIC Telepon Supplier"
                            value="{{ $data->supplier_pic_phone ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-12">
                        <label class="form-label" for="supplier_office_number">Nomor Kantor Supplier</label>
                        <input type="text" id="supplier_office_number" name="supplier_office_number"
                            class="form-control" placeholder="Masukkan Nomor Kantor Supplier"
                            value="{{ $data->supplier_office_number ?? '' }}" />
                    </div>
                    <div class="col-12 col-md-12">
                        <label class="form-label" for="supplier_address">Alamat Supplier</label>
                        <textarea id="supplier_address" name="supplier_address" class="form-control" cols="30" rows="5">{{ $data->supplier_address ?? '' }}</textarea>
                    </div>
                    <div class="col-12 d-flex justify-content-between mt-5">
                        <button class="btn btn-label-secondary btn-prev">
                            <i class="ti ti-arrow-left ti-xs me-sm-1 me-0"></i>
                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                        </button>
                        <button class="btn btn-primary btn-next" type="button">
                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                            <i class="ti ti-arrow-right ti-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- submit -->
            <div id="submit" class="content ms-3 pt-3 pt-lg-0">
                <h5>Tambah Field Dinamis</h5>
                <div class="custom-fields">
                    @foreach ($customFields as $customField)
                        <div class="custom-field">
                            <div class="row">
                                <div class="col-md-4">
                                    <select name="custom_field_type[]" class="form-control"
                                        id="custom_field_type_{{ $loop->index }}">
                                        <option value="">Pilih Tipe</option>
                                        <option value="text"
                                            {{ $customField->tipe_field == 'text' ? 'selected' : '' }}>
                                            Text
                                        </option>
                                        <option value="number"
                                            {{ $customField->tipe_field == 'number' ? 'selected' : '' }}>Number
                                        </option>
                                        <option value="date"
                                            {{ $customField->tipe_field == 'date' ? 'selected' : '' }}>
                                            Date
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="custom_field_name[]" class="form-control"
                                        placeholder="Masukkan nama field" value="{{ $customField->nama_field }}">
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="{{ $customField->tipe_field }}" name="custom_field_value[]"
                                            class="form-control" placeholder="Masukkan nilai field"
                                            id="custom_field_value_{{ $loop->index }}"
                                            value="{{ $customField->nilai_field }}">
                                        <button class="btn btn-danger remove-field"><i class="ti ti-trash ti-sm"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-primary tambah-field mt-3">Tambah Field</button>
                </div>
                <div class="col-12 d-flex justify-content-between mt-4 pt-2">
                    <button class="btn btn-label-secondary btn-prev">
                        <i class="ti ti-arrow-left ti-xs me-sm-1 me-0"></i>
                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                    </button>
                    <button class="btn btn-primary btn-submit" data-bs-dismiss="modal" aria-label="Close">
                        <span class="align-middle d-sm-inline-block d-none me-sm-1">Submit</span>
                        <i class="ti ti-check ti-xs"></i>
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

@include('components.select2_js')
<script>
    $(document).ready(function() {
        var count = 1;

        // Panggil fungsi ubahTipeField untuk setiap field
        $('.custom-field select[name^="custom_field_type"]').each(function() {
            ubahTipeField(this);
        });

        // Tambah field baru
        $('.tambah-field').on('click', function() {
            count++;
            var html = `<div class="custom-field">
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
                            <button class="btn btn-danger remove-field"><i class="ti ti-trash ti-sm"></i></button>
                        </div>
                    </div>
                </div>
            </div>`;
            $('.custom-fields').append(html);
            ubahTipeField('#custom_field_type_' + count);
        });

        // Hapus field
        $(document).on('click', '.remove-field', function() {
            $(this).parent('.input-group').parent('.col-md-4').parent('.row').parent('.custom-field')
                .remove();
        });

        function ubahTipeField(selector) {
            $(selector).on('change', function() {
                var tipe = $(this).val();
                var id = $(this).attr('id').replace('custom_field_type_', 'custom_field_value_');
                if (tipe == 'text') {
                    $('#' + id).attr('type', 'text');
                } else if (tipe == 'number') {
                    $('#' + id).attr('type', 'number');
                } else if (tipe == 'date') {
                    $('#' + id).attr('type', 'date');
                }
            });
        }
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
                        'search[value]': params.term,
                        start: 0,
                        length: 10
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
                        'search[value]': params.term,
                        start: 0,
                        length: 10
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

        // $('#pic').select2({
        //     dropdownParent: $('#userRelation'),
        //     placeholder: 'Pilih PIC',
        //     ajax: {
        //         url: "{{ route('user.data') }}",
        //         dataType: 'json',
        //         delay: 250,
        //         data: function(params) {
        //             return {
        //                 keyword: params.term
        //             };
        //         },
        //         processResults: function(data) {
        //             apiResults = data.data
        //                 .filter(function(item) {
        //                     return item.idRelationAll !== null;
        //                 })
        //                 .map(function(item) {
        //                     return {
        //                         text: item.name,
        //                         id: item.idRelationAll,
        //                     };
        //                 });

        //             return {
        //                 results: apiResults
        //             };
        //         },
        //         cache: true
        //     }
        // });
        $('#pic').select2({
            dropdownParent: $('#picRelation'),
            placeholder: 'Pilih PIC',
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

        var picId = '{{ $data->pic ?? '' }}';
        var picName = '{{ $data->pics->name ?? '' }}';
        if (picId) {
            var picOption = new Option(picName, picId, true, true);
            $('#pic').append(picOption).trigger('change');
        }

        $('#management_project_id').select2({
            dropdownParent: $('#management_project_idRelation'),
            placeholder: 'Pilih Project',
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
                    return {
                        results: data.data.map(function(item) {
                            return {
                                text: item.format_id + ' - ' + item.name,
                                id: item.managementRelationId
                            };
                        })
                    };
                },
                cache: true
            }
        });
        var management_projectId = '{{ $data->management_project_ids ?? '' }}';
        var management_projectName = '{{ $data->management_project_name ?? '' }}';
        if (management_projectId) {
            var management_projectOption = new Option(management_projectName, management_projectId, true, true);
            $('#management_project_id').append(management_projectOption).trigger('change');
        }
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
{{-- script dropzone file --}}
<script>
    $(document).ready(function() {
        const dropzone = document.getElementById("customDropzone");
        const fileInput = document.getElementById("image");

        // Event untuk menangani drag-over
        dropzone.addEventListener("dragover", function(e) {
            e.preventDefault();
            dropzone.style.backgroundColor = "#D0D0D0";
        });

        // Event untuk menangani drag-leave
        dropzone.addEventListener("dragleave", function() {
            dropzone.style.backgroundColor = "#F8F8F8";
        });

        // Event untuk menangani drop file
        dropzone.addEventListener("drop", function(e) {
            e.preventDefault();
            dropzone.style.backgroundColor = "#F8F8F8";

            const files = e.dataTransfer.files;
            handleFiles(files);
        });

        // Event untuk klik
        dropzone.addEventListener("click", function() {
            fileInput.click();
        });

        // Event untuk perubahan pada input file
        fileInput.addEventListener("change", function() {
            handleFiles(fileInput.files);
        });

        // Fungsi untuk menangani file yang diunggah
        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];

                console.log(file);

                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                };

                reader.readAsDataURL(file);
            }
        }
    });
</script>
