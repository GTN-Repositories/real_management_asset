<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Request For Quotation</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="fromEdit" action="{{ route('procurement.rfq.updateItem', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
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
        <select id="vendor_comparation_id" name="vendor_comparation_id"
            class="select2 form-select select2-primary" data-allow-clear="true" required>    
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Qty</label>
        <input type="text" name="qty" class="form-control mb-3 mb-lg-0" placeholder="Jumlah" value="{{ $data->qty }}" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Harga</label>
        <input type="text" name="price" id="price" class="form-control mb-3 mb-lg-0" placeholder="Harga" value="{{ $data->price }}" />
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#price').each(function() {
            const value = $(this).val();
            $(this).val(formatCurrency(value));
        });

        $('#vendor_comparation_id').select2({
            dropdownParent: $('#vendorRelation'),
            placeholder: 'Pilih Vendor',
            ajax: {
                url: "{{ route('procurement.rfq.vendorComparationData') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        'search[value]': params.term,
                        start: 0,
                        length: 10,
                        request_order_id: '{{ $data->request_order_id }}'
                    };
                },
                processResults: function(data) {
                    apiResults = data.data
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

        $('#vendor_comparation_id').on('change', function() {
            var id = $(this).val();
            $.ajax({
                url: "{{ route('procurement.rfq.vendorComparationFilter') }}",
                dataType: 'json',
                delay: 250,
                data: {
                    id: id,
                    request_order_id: '{{ $data->request_order_id }}'
                },
                success: function(data) {
                    var price = data.price;
                    $('#price').val(formatCurrency(price));
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
