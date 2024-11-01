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
        <label class="form-label" for="image">gambar</label>
        <input type="file" id="image" name="image" class="form-control" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="name">nama<span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama"
            value="{{ $data->name }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="manager">manajer<span class="text-danger">*</span></label>
        <select id="manager" name="manager" class="select2 form-select" data-allow-clear="true">
            <option value="">Pilih</option>
            <option value="lenz creative" {{ $data->manager == 'lenz creative' ? 'selected' : '' }}>lenz creative
            </option>
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="category">kategori<span class="text-danger">*</span></label>
        <select name="category" id="category" class="select2 form-select" data-allow-clear="true">
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
        <label class="form-label" for="cost">biaya</label>
        <input type="number" min="1" id="cost" name="cost" class="form-control"
            placeholder="Masukkan biaya" value="{{ $data->cost }}" />
    </div>
    <div class="col-12 col-md-12">
        <label class="form-label" for="description">deskripsi</label>
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
                        informasi aset
                    </button>
                </h2>

                <div id="accordionOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample"
                    style="">
                    <div class="accordion-body row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="serial_number">nomor seri</label>
                            <input type="text" id="serial_number" name="serial_number" class="form-control"
                                placeholder="Masukkan nomor seri" value="{{ $data->serial_number }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="model_number">nomor model</label>
                            <input type="text" id="model_number" name="model_number" class="form-control"
                                placeholder="Masukkan nomor model" value="{{ $data->model_number }}" />
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="warranty_period">waktu garansi</label>
                            <input type="number" min="1" id="warranty_period" name="warranty_period"
                                class="form-control" placeholder="Masukkan waktu garansi"
                                value="{{ $data->warranty_period }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="assets_location">lokasi aset</label>
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
                            <label class="form-label" for="purchase_date">tanggal pembelian</label>
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
                        informasi penyusutan aset
                    </button>
                </h2>
                <div id="accordionTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="depreciation">penyusutan</label>
                            <input type="number" min="1" id="depreciation" name="depreciation"
                                class="form-control" placeholder="Masukkan penyusutan"
                                value="{{ $data->depreciation }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="depreciation_percentage">persentase penyusutan</label>
                            <input type="text" id="depreciation_percentage" name="depreciation_percentage"
                                class="form-control" placeholder="Masukkan persentase penyusutan"
                                value="{{ $data->depreciation_percentage }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="depreciation_method">metode penyusutan</label>
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
                            <label class="form-label" for="residual_value">nilai sisa</label>
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
                        informasi apresiasi aset
                    </button>
                </h2>
                <div id="accordionThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="appreciation_rate">tingkat apresiasi</label>
                            <input type="number" min="1" id="appreciation_rate" name="appreciation_rate"
                                class="form-control" placeholder="Masukkan tingkat apresiasi"
                                value="{{ $data->appreciation_rate }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="appreciation_period">periode apresiasi</label>
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
                        informasi pemasok aset
                    </button>
                </h2>
                <div id="accordionFour" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body row">
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="supplier_name">nama pemasok</label>
                            <input type="text" id="supplier_name" name="supplier_name" class="form-control"
                                placeholder="Masukkan nama pemasok" value="{{ $data->supplier_name }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="supplier_phone_number">nomor telepon pemasok</label>
                            <input type="text" id="supplier_phone_number" name="supplier_phone_number"
                                class="form-control" placeholder="Masukkan nomor telepon pemasok"
                                value="{{ $data->supplier_phone_number }}" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="supplier_address">alamat pemasok</label>
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
