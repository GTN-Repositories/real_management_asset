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
            value="{{ $data->name }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="manager">Manajer<span class="text-danger">*</span></label>
        <select id="manager" name="manager" class="select2 form-select " data-allow-clear="true" required>
            <option value="">Pilih</option>
            <option value="lenz creative" {{ $data->manager == 'lenz creative' ? 'selected' : '' }}>lenz creative
            </option>
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="category">Kategori<span class="text-danger">*</span></label>
        <select name="category" id="category" class="select2 form-select " data-allow-clear="true" required>
            <option value="">Pilih</option>
            <option value="Technology" {{ $data->category == 'Technology' ? 'selected' : '' }}>Technology</option>
            <option value="Construction" {{ $data->category == 'Construction' ? 'selected' : '' }}>Construction</option>
            <option value="Medical Assets" {{ $data->category == 'Medical Assets' ? 'selected' : '' }}>Medical Assets
            </option>
            <option value="Education" {{ $data->category == 'Education' ? 'selected' : '' }}>Education</option>
            <option value="Lisences" {{ $data->category == 'Lisences' ? 'selected' : '' }}>Lisences</option>
            <option value="Real Estate" {{ $data->category == 'Real Estate' ? 'selected' : '' }}>Real Estate</option>
            <option value="Legal Claims" {{ $data->category == 'Legal Claims' ? 'selected' : '' }}>Legal Claims</option>
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="cost">Biaya Pembelian</label>
        <input type="number" min="1" id="cost" name="cost" class="form-control"
            placeholder="Masukkan cost" value="{{ $data->cost }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="unit">Unit</label>
        <input type="text" id="unit" name="unit" class="form-control" placeholder="Masukkan unit"
            value="{{ $data->unit }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="license_plate">Nomor Polisi</label>
        <input type="text" id="license_plate" name="license_plate" class="form-control"
            placeholder="Masukkan nomor polisi" value="{{ $data->license_plate }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="classification">Klasifikasi</label>
        <input type="text" id="classification" name="classification" class="form-control"
            placeholder="Masukkan classification" value="{{ $data->classification }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="machine_number">Nomor Mesin</label>
        <input type="text" id="machine_number" name="machine_number" class="form-control"
            placeholder="Masukkan nomor mesin" value="{{ $data->machine_number }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="color">Warna</label>
        <input type="text" id="color" name="color" class="form-control" placeholder="Masukkan warna"
            value="{{ $data->color }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="nik">Nik</label>
        <input type="text" id="nik" name="nik" class="form-control" placeholder="Masukkan nik" value="{{ $data->nik }}"/>
    </div>
    <div class="col-12 col-md-12">
        <label class="form-label" for="status">Status<span class="text-danger">*</span></label>
        <select name="status" id="status" class="select2 form-select " data-allow-clear="true" required>
            <option value="">Pilih</option>
            <option value="Idle" {{ $data->status == 'Idle' ? 'selected' : '' }}>Idle</option>
            <option value="StandBy" {{ $data->status == 'StandBy' ? 'selected' : '' }}>Stand By</option>
            <option value="OnHold" {{ $data->status == 'OnHold' ? 'selected' : '' }}>On Hold</option>
            <option value="Finish" {{ $data->status == 'Finish' ? 'selected' : '' }}>Finish</option>
            <option value="Damaged" {{ $data->status == 'Damaged' ? 'selected' : '' }}>Damaged</option>
            <option value="Fair" {{ $data->status == 'Fair' ? 'selected' : '' }}>Fair</option>
            <option value="UnderMaintenance" {{ $data->status == 'UnderMaintenance' ? 'selected' : '' }}>
                Under Maintenance</option>
            <option value="Active" {{ $data->status == 'Active' ? 'selected' : '' }}>Active</option>
            <option value="Scheduled" {{ $data->status == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
            <option value="InProgress" {{ $data->status == 'InProgress' ? 'selected' : '' }}>In Progress</option>
            <option value="NeedsRepair" {{ $data->status == 'NeedsRepair' ? 'selected' : '' }}>Needs Repair</option>
            <option value="Good" {{ $data->status == 'Good' ? 'selected' : '' }}>Good</option>
        </select>
    </div>
    <div class="col-12 col-md-12">
        <label class="form-label" for="description">Keterangan</label>
        <textarea name="description" id="description" class="form-control" cols="30" rows="5">{{ $data->description }}</textarea>
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
                            <label class="form-label" for="serial_number">Nomor Seri</label>
                            <input type="text" id="serial_number" name="serial_number" class="form-control"
                                placeholder="Masukkan nomor seri" value="{{ $data->serial_number }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="model_number">Nomor Model</label>
                            <input type="text" id="model_number" name="model_number" class="form-control"
                                placeholder="Masukkan nomor model" value="{{ $data->model_number }}" />
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="warranty_period">Waktu Garansi</label>
                            <input type="number" min="1" id="warranty_period" name="warranty_period"
                                class="form-control" placeholder="Masukkan waktu garansi"
                                value="{{ $data->warranty_period }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="assets_location">Lokasi Asset</label>
                            <select name="assets_location" id="assets_location" class="form-select select2">
                                <option value="">Pilih</option>
                                <option value="Jatim" {{ $data->assets_location == 'Jatim' ? 'selected' : '' }}>Jatim
                                </option>
                                <option value="Jateng" {{ $data->assets_location == 'Jateng' ? 'selected' : '' }}>
                                    Jateng</option>
                                <option value="Jabar" {{ $data->assets_location == 'Jabar' ? 'selected' : '' }}>Jabar
                                </option>
                                <option value="Kaltim" {{ $data->assets_location == 'Kaltim' ? 'selected' : '' }}>
                                    Kaltim</option>
                                <option value="Kalteng" {{ $data->assets_location == 'Kalteng' ? 'selected' : '' }}>
                                    Kalteng</option>
                                <option value="Kalsel" {{ $data->assets_location == 'Kalsel' ? 'selected' : '' }}>
                                    Kalsel</option>
                                <option value="Bali" {{ $data->assets_location == 'Bali' ? 'selected' : '' }}>Bali
                                </option>
                                <option value="DKI" {{ $data->assets_location == 'DKI' ? 'selected' : '' }}>DKI
                                </option>
                                <option value="Aceh" {{ $data->assets_location == 'Aceh' ? 'selected' : '' }}>Aceh
                                </option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="purchase_date">Tanggal Pembelian</label>
                            <input type="date" id="purchase_date" name="purchase_date" class="form-control"
                                placeholder="Masukkan tanggal pembelian" value="{{ $data->purchase_date }}" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                        data-bs-target="#accordionTwo" aria-expanded="false" aria-controls="accordionTwo">
                        Informasi Penyusutan Asset
                    </button>
                </h2>
                <div id="accordionTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="depreciation">Penyusutan</label>
                            <input type="number" min="1" id="depreciation" name="depreciation"
                                class="form-control" placeholder="Masukkan penyusutan"
                                value="{{ $data->depreciation }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="depreciation_percentage">Persentase Penyusutan</label>
                            <input type="text" id="depreciation_percentage" name="depreciation_percentage"
                                class="form-control" placeholder="Masukkan persentase penyusutan"
                                value="{{ $data->depreciation_percentage }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="depreciation_method">Metode Penyusutan</label>
                            <select name="depreciation_method" id="depreciation_method" class="select2 form-select">
                                <option value="">Pilih</option>
                                <option value="Penyusutan Saldo Menurun"
                                    {{ $data->depreciation_method == 'Penyusutan Saldo Menurun' ? 'selected' : '' }}>
                                    Penyusutan Saldo Menurun</option>
                                <option value="Penyusutan Garis Lurus"
                                    {{ $data->depreciation_method == 'Penyusutan Garis Lurus' ? 'selected' : '' }}>
                                    Penyusutan Garis Lurus</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="residual_value">Nilai Sisa</label>
                            <input type="number" min="1" id="residual_value" name="residual_value"
                                class="form-control" placeholder="Masukkan nilai sisa"
                                value="{{ $data->residual_value }}" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                        data-bs-target="#accordionThree" aria-expanded="false" aria-controls="accordionThree">
                        Informasi Apresiasi Asset
                    </button>
                </h2>
                <div id="accordionThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="appreciation_rate">Tingkat Apresiasi</label>
                            <input type="number" min="1" id="appreciation_rate" name="appreciation_rate"
                                class="form-control" placeholder="Masukkan tingkat apresiasi"
                                value="{{ $data->appreciation_rate }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="appreciation_period">Periode Apresiasi</label>
                            <input type="number" min="1" id="appreciation_period" name="appreciation_period"
                                class="form-control" placeholder="Masukkan periode apresiasi"
                                value="{{ $data->appreciation_period }}" />
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
                                placeholder="Masukkan nama pemasok" value="{{ $data->supplier_name }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="supplier_phone_number">Nomor Telepon Pemasok</label>
                            <input type="text" id="supplier_phone_number" name="supplier_phone_number"
                                class="form-control" placeholder="Masukkan nomor telepon pemasok"
                                value="{{ $data->supplier_phone_number }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="supplier_address">Alamat Pemasok</label>
                            <input type="text" id="supplier_address" name="supplier_address" class="form-control"
                                placeholder="Masukkan alamat pemasok" value="{{ $data->supplier_address }}" />
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
