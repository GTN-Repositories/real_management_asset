<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Tambah Barang</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formCreate" action="{{ route('item.store') }}" enctype="multipart/form-data">
    @csrf
    {{-- <div class="col-8 col-md-2">
        <img src="https://ca.shop.runningroom.com/media/catalog/product/placeholder/default/placeholder-image-square.jpg"
            id="preview-image" class="img-fluid rounded mb-3 pt-1" alt="Image Preview">
    </div> --}}
    {{-- <div class="col-12">
        <label class="form-label" for="image">Image</label>
        <input type="file" id="image" name="image" class="form-control"
            accept="image/png, image/jpeg, image/jpg" />
    </div> --}}
    <div class="col-12 col-md-6">
        <label class="form-label">Kode Barang</label>
        <input type="text" name="code" id="code" class="form-control mb-3 mb-lg-0"
            placeholder="Kode Barang (Otomatis)" value="{{ old('code') }}" required readonly />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Part Number</label>
        <input type="text" name="part" class="form-control mb-3 mb-lg-0" placeholder="Part Number"
            value="{{ old('part') }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="name" id="name" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" value="{{ old('name') }}" required />
    </div>

    {{-- <div class="col-12 col-md-6">
        <label class="form-label">Ukuran</label>
        <input type="text" name="size" class="form-control mb-3 mb-lg-0" placeholder="Ukuran"
            value="{{ old('size') }}" required />
    </div> --}}

    <div class="col-12 col-md-6">
        <label class="form-label">Merk / Brand</label>
        <input type="text" name="brand" class="form-control mb-3 mb-lg-0" placeholder="Merk"
            value="{{ old('brand') }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Harga</label>
        <input type="text" name="price" id="price" class="form-control mb-3 mb-lg-0" placeholder="Harga"
            required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" id="minimum_stock">Stok Minimal</label>
        <input type="number" name="minimum_stock" class="form-control mb-3 mb-lg-0" placeholder="Stok Minimal"
            value="{{ old('minimum_stock') }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label for="exampleFormControlSelect1" class="form-label">Status</label>
        <select class="form-select" id="exampleFormControlSelect1" name="status" aria-label="Select Status">
            <option selected value="">None</option>
            <option value="uom">UOM</option>
            <option value="genuine">Genuine</option>
        </select>
    </div>

    <div class="col-12 col-md-6" id="select2relation">
        <label for="select2Basic" class="form-label">Kategori</label>
        <select id="select2Basic" class="select2 form-select form-select-lg" name="category_id" data-allow-clear="true">
            <option></option>
        </select>
    </div>
    {{--
    <div class="col-12 col-md-6">
        <label class="form-label">Warna</label>
        <input type="color" name="color" class="form-control mb-lg-0" placeholder="Warna" required />
    </div> --}}

    <div class="col-12 col-md-6" id="oumRelation">
        <label for="oum_id" class="form-label">UOM Tipe<span class="text-danger">*</span></label>
        <div class="select2-primary">
            <div class="position-relative">
                <select id="oum_id" name="oum_id" class="select2 form-select" required>
                    <!-- Options will be populated dynamically -->
                </select>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">No Invoice</label>
        <input type="text" name="no_invoice" id="no_invoice" class="form-control mb-3 mb-lg-0"
            placeholder="nomor invoice" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Nama Supplier</label>
        <input type="text" name="supplier_name" id="supplier_name" class="form-control mb-3 mb-lg-0"
            placeholder="Nama Supplier" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Alamat Supplier</label>
        <input type="text" name="supplier_addrees" id="supplier_addrees" class="form-control mb-3 mb-lg-0"
            placeholder="Alamat Supplier" required />
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

<script>
    function removeVowels(text) {
        return text.replace(/[aeiouAEIOU]/g, '');
    }

    function getRandomNumber() {
        return Math.floor(10 + Math.random() * 90);
    }

    function generateTimeStamp() {
        const currentTime = new Date();
        const timeStamp = ('0' + currentTime.getDate()).slice(-2) +
            ('0' + (currentTime.getMonth() + 1)).slice(-2) +
            ('0' + currentTime.getHours()).slice(-2) +
            ('0' + currentTime.getMinutes()).slice(-2) +
            ('0' + currentTime.getSeconds()).slice(-2);
        return timeStamp;
    }

    function generateItemCode() {
        const itemName = document.getElementById('name').value.trim();
        if (itemName === '') {
            document.getElementById('code').value = '';
            return;
        }

        const nameWithoutVowels = removeVowels(itemName.replace(/\s+/g, ''));
        const timeStamp = generateTimeStamp();
        const randomNum = getRandomNumber();
        const itemCode = nameWithoutVowels.toUpperCase() + '-' + timeStamp + randomNum;

        document.getElementById('code').value = itemCode;
    }

    document.getElementById('name').addEventListener('input', function() {
        generateItemCode();
    });


    // Select2
    $(document).ready(function() {
        var relationData = @json($data);
        $('#select2Basic').select2({
            dropdownParent: $('#select2relation'),
            placeholder: 'Pilih Kategori',
            data: relationData.map(function(relation) {
                return {
                    id: relation.id,
                    text: relation.name
                };
            })
        });

        $('#oum_id').select2({
            dropdownParent: $('#oumRelation'),
            placeholder: 'Pilih oum tipe',
            ajax: {
                url: "{{ route('oum.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function(data) {
                    apiResults = data.data.map(function(item) {
                        return {
                            text: item.name,
                            id: item.id,
                        };
                    });

                    return {
                        results: apiResults
                    };
                },
                limit: 10,
                cache: true
            }
        });
    });

    // image
    document.getElementById('image').addEventListener('change', function(event) {
        const preview = document.getElementById('preview-image');
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        } else {
            preview.src =
                "https://ca.shop.runningroom.com/media/catalog/product/placeholder/default/placeholder-image-square.jpg";
        }
    });

    $(document).on('input', '#price', function() {
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
