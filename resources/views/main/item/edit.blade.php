<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Tambah Barang</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="fromEdit" action="{{ route('item.update', $data->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="col-8 col-md-2">
        <img src="https://ca.shop.runningroom.com/media/catalog/product/placeholder/default/placeholder-image-square.jpg"
            id="preview-image" class="img-fluid rounded mb-3 pt-1" alt="Image Preview">
    </div>
    <div class="col-12">
        <label class="form-label" for="image">Image</label>
        <input type="file" id="image" name="image" class="form-control"
            accept="image/png, image/jpeg, image/jpg" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label">Kode Barang</label>
        <input type="text" name="code" id="code" class="form-control mb-3 mb-lg-0"
            placeholder="Kode Barang (Otomatis)" value="{{ $data->code }}" required readonly />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Part Number</label>
        <input type="text" name="part" class="form-control mb-3 mb-lg-0" placeholder="Part Number"
            value="{{ $data->part }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="name" id="name" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" value="{{ $data->name }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Ukuran</label>
        <input type="text" name="size" class="form-control mb-3 mb-lg-0" placeholder="Ukuran"
            value="{{ $data->size }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Merk / Brand</label>
        <input type="text" name="brand" class="form-control mb-3 mb-lg-0" placeholder="Merk"
            value="{{ $data->brand }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label for="exampleFormControlSelect1" class="form-label">Status</label>
        <select class="form-select" id="exampleFormControlSelect1" name="status" aria-label="Select Status">
            <option selected value="">None</option>
            <option value="uom" @if ($data->status == 'uom') selected @endif>UOM</option>
            <option value="genuine" @if ($data->status == 'genuine') selected @endif>Genuine</option>
        </select>
    </div>

    <div class="col-12 col-md-6 mb-4" id="select2relation">
        <label for="select2Basic" class="form-label">Kategori</label>
        <select id="select2Basic" class="select2 form-select form-select-lg" name="category_id" data-allow-clear="true">
            <option></option>
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Warna</label>
        <input type="color" name="color" value="{{ $data->color }}" class="form-control mb-lg-0" placeholder="Warna" required />
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
        var relationData = @json($relation);
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
</script>
<script>
    document.getElementById('fromEdit').addEventListener('submit', function(event) {
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
