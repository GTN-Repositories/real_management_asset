<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Request Stock</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formCreate" action="{{ route('item.stock.create') }}"
    enctype="multipart/form-data">
    @csrf

    <div class="col-12 col-md-12" id="itemRelation">
        <label for="item_id" class="form-label">Sparepart</label>
        <select name="item_id" id="item_id" class="form-select select2">
        </select>
    </div>

    <div class="col-12 col-md-12">
        <label class="form-label">Stock</label>
        <input type="text" name="stock" id="stock" class="form-control mb-3 mb-lg-0" placeholder="stock"
            required />
    </div>

    <div class="col-12 col-md-12">
        <label class="form-label">Stok Saat Ini</label>
        <input type="text" name="balance" id="balance" class="form-control mb-3 mb-lg-0" placeholder="Balance"
            required readonly />
    </div>

    <div class="col-12 col-md-12" id="warehouseRelation">
        <label class="form-label" for="warehouse_id">Nama Gudang<span class="text-danger">*</span></label>
        <select id="warehouse_id" name="warehouse_id"
            class="select2 form-select select2-primary"data-allow-clear="true" required>    
        </select>
    </div>

    <div class="col-12 col-md-12">
        <label class="form-label" for="metode">Jenis Metode<span class="text-danger">*</span></label>
        <select id="metode" name="metode" class="select2 form-select " data-allow-clear="true" required>
            <option value="">Pilih</option>
            <option value="increase">Increase</option>
            <option value="decrease">Decrease</option>
        </select>
    </div>

    <div class="col-12 col-md-12">
        <label class="form-label">Harga</label>
        <input type="text" name="price" id="price" class="form-control mb-3 mb-lg-0"
            placeholder="Masukkan Harga" required />
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

@include('components.select2_js')

<script>
    $(document).ready(function() {
        $('#item_id').select2({
            dropdownParent: $('#itemRelation'),
            placeholder: 'Pilih sparepart',
            ajax: {
                url: "{{ route('item.data') }}",
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
                        .map(function(item) {
                            return {
                                text: item.format_id + ' - ' + item.name + '(' + item.uom + ')',
                                id: item.id,
                                stock: item
                                    .stock // Assuming this is the field for current stock
                            };
                        });

                    return {
                        results: apiResults
                    };
                },
                cache: true
            }
        }).on('change', function() {
            const selectedOption = $(this).select2('data')[0];
            if (selectedOption) {
                $('#balance').val(selectedOption.stock);
            }
        });

        $('#warehouse_id').select2({
            dropdownParent: $('#warehouseRelation'),
            placeholder: 'Pilih Gudang',
            ajax: {
                url: "{{ route('werehouse.data') }}",
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
                        .map(function(item) {
                            return {
                                text: item.name,
                                id: item.ids,
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

    $(document).on('input', '#stock', function() {
        value = formatCurrency($(this).val());
        $(this).val(value);
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
