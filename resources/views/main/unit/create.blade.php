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
        <label class="form-label" for="name">Nama Asset<span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan name" required />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="manager">Manajer<span class="text-danger">*</span></label>
        <select id="manager" name="manager" class="select2 form-select " data-allow-clear="true" required>
            <option value="">Pilih</option>
            <option value="lenz creative">lenz creative</option>
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="category">Kategori<span class="text-danger">*</span></label>
        <select name="category" id="category" class="select2 form-select " data-allow-clear="true" required>
            <option value="">Pilih</option>
            <option value="Technology">Technology</option>
            <option value="Construction">Construction</option>
            <option value="Medical Assets">Medical Assets</option>
            <option value="Education">Education</option>
            <option value="Lisences">Lisences</option>
            <option value="Real Estate">Real Estate</option>
            <option value="Legal Claims">Legal Claims</option>
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="cost">Biaya Pembelian</label>
        <input type="number" min="1" id="cost" name="cost" class="form-control"
            placeholder="Masukkan cost" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="unit">Unit</label>
        <input type="text" id="unit" name="unit" class="form-control" placeholder="Masukkan unit" />
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
        <label class="form-label" for="machine_number">Nomor Mesin</label>
        <input type="text" id="machine_number" name="machine_number" class="form-control"
            placeholder="Masukkan nomor mesin" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="color">Warna</label>
        <input type="text" id="color" name="color" class="form-control" placeholder="Masukkan warna" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="nik">Nik</label>
        <input type="text" id="nik" name="nik" class="form-control" placeholder="Masukkan nik" />
    </div>
    <div class="col-12 col-md-12">
        <label class="form-label" for="description">Keterangan</label>
        <textarea name="description" id="description" class="form-control" cols="30" rows="5"></textarea>
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
                                placeholder="Masukkan serial_number" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="model_number">Nomor Model</label>
                            <input type="text" id="model_number" name="model_number" class="form-control"
                                placeholder="Masukkan model_number" />
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="warranty_period">Waktu Garansi</label>
                            <input type="number" min="1" id="warranty_period" name="warranty_period"
                                class="form-control" placeholder="Masukkan warranty_period" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="assets_location">Lokasi</label>
                            <select name="assets_location" id="assets_location" class="form-select select2">
                                <option value="">Pilih</option>
                                <option value="Jatim">Jatim</option>
                                <option value="Jateng">Jateng</option>
                                <option value="Jabar">Jabar</option>
                                <option value="Kaltim">Kaltim</option>
                                <option value="Kalteng">Kalteng</option>
                                <option value="Kalsel">Kalsel</option>
                                <option value="Bali">Bali</option>
                                <option value="DKI">DKI</option>
                                <option value="Aceh">Aceh</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="purchase_date">Tanggal Pembelian</label>
                            <input type="date" id="purchase_date" name="purchase_date" class="form-control"
                                placeholder="Masukkan purchase_date" />
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
                                class="form-control" placeholder="Masukkan depreciation" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="depreciation_percentage">Presentase Penyusutan</label>
                            <input type="text" id="depreciation_percentage" name="depreciation_percentage"
                                class="form-control" placeholder="Masukkan depreciation_percentage" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="depreciation_method">Metode Penyusutan</label>
                            <select name="depreciation_method" id="depreciation_method" class="select2 form-select">
                                <option value="">Pilih</option>
                                <option value="Penyusutan Saldo Menurun">Penyusutan Saldo Menurun</option>
                                <option value="Penyusutan Garis Lurus">Penyusutan Garis Lurus</option>
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
                        Informasi Apresiasi Asset
                    </button>
                </h2>
                <div id="accordionThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="appreciation_rate">Tingkat Apresiasi</label>
                            <input type="number" min="1" id="appreciation_rate" name="appreciation_rate"
                                class="form-control" placeholder="Masukkan appreciation_rate" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="appreciation_period">Periode Apresiasi</label>
                            <input type="number" min="1" id="appreciation_period" name="appreciation_period"
                                class="form-control" placeholder="Masukkan appreciation_period" />
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
                                placeholder="Masukkan supplier_name" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="supplier_phone_number">Nomor Telepon Pemasok</label>
                            <input type="text" id="supplier_phone_number" name="supplier_phone_number"
                                class="form-control" placeholder="Masukkan supplier_phone_number" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="supplier_address">Alamat Pemasok</label>
                            <input type="text" id="supplier_address" name="supplier_address" class="form-control"
                                placeholder="Masukkan supplier_address" />
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
