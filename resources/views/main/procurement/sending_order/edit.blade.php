<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Tambah Sending Order</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="fromEdit" action="{{ route('procurement.upload-invoice.updateItem', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="request_order_id" id="request_order_id" value="{{ $data->request_order_id }}">
    <div class="col-12 col-md-6">
        <label class="form-label">Kode Barang</label>
        <input type="text" name="code" id="code" class="form-control mb-3 mb-lg-0"
            placeholder="Kode Barang (Otomatis)" value="{{ $data->item?->code ?? '' }}" disabled />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="name" id="name" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" value="{{ $data->item?->name ?? '' }}" disabled />
    </div>

    <div class="col-12 col-md-12" id="vendorRelation">
        <label class="form-label" for="vendor_comparation_id">Vendor<span class="text-danger">*</span></label>
        <input type="text" name="vendor_comparation_id" id="vendor_comparation_id" class="form-control" value="{{ $data->vendorComparation?->vendor?->name ?? '' }}" disabled readonly="readonly">
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Qty</label>
        <input type="text" name="qty" class="form-control mb-3 mb-lg-0" placeholder="Jumlah" value="{{ $data->qty }}" disabled />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Harga</label>
        <input type="text" name="price" id="price" class="form-control mb-3 mb-lg-0" placeholder="Harga" value="{{ $data->price }}" disabled />
    </div>

    <hr>

    @foreach ($uploadInvoice as $item)
        <div class='card'>
            <div class='card-header'>
                <h5 class='card-title'>Upload Invoice</h5>
            </div>
            <div class='card-body'>
                <div class="row">
                    <input type="hidden" name="id[]" value="{{ $item->id }}">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name[]" id="name" class="form-control mb-3 mb-lg-0" value="{{ $item->name }}" placeholder="Masukan Nama Item" />
                    </div>
        
                    <div class="col-12 col-md-6">
                        <label class="form-label mr-2">Lampiran</label>
                        <small>*Only For Change</small>
                        <input type="file" name="attachment[]" id="attachment" class="form-control mb-3 mb-lg-0" placeholder="Masukan Attachment" />
                        <a href="{{ asset($item->attachment) }}" class="badge bg-label-primary mt-2" download="{{ $item->name }}"><i class="fas fa-download me-2"></i> Download</a>
                    </div>

                    <div class="col-12 col-md-12">
                        <label class="form-label">Catatan</label>
                        <textarea name="note[]" id="note" class="form-control mb-3 mb-lg-0" placeholder="Catatan">{{ $item->note }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class='card'>
        <div class='card-header'>
            <h5 class='card-title'>Upload Invoice</h5>
        </div>
        <div class='card-body'>
            <div class="row">
                <div class="col-12 col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name[]" id="name" class="form-control mb-3 mb-lg-0" placeholder="Masukan Nama Item" />
                </div>
    
                <div class="col-12 col-md-6">
                    <label class="form-label">Lampiran</label>
                    <input type="file" name="attachment[]" id="attachment" class="form-control mb-3 mb-lg-0" placeholder="Masukan Attachment" />
                </div>

                <div class="col-12 col-md-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="note[]" id="note" class="form-control mb-3 mb-lg-0" placeholder="Catatan"></textarea>
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
    $(document).ready(function() {
        CKEDITOR.replace('note');

        $('#price').each(function() {
            const value = $(this).val();
            $(this).val(formatCurrency(value));
        });
    });

    $(document).on('input', '#price', function() {
        const value = $(this).val();
        $(this).val(formatCurrency(value));
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

                        location.reload();
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
