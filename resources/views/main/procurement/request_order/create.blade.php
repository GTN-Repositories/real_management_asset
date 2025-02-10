<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Request Order</h3>
    <p class="text-muted">Silahkan isi form untuk melakukan Request Order</p>
</div>
<form method="POST" class="row g-3" id="formCreate" action="{{ route('procurement.request-order.store') }}"
    enctype="multipart/form-data">
    @csrf

    <div class="col-12 col-md-6" id="warehouseRelation">
        <label class="form-label" for="warehouse_id">Gudang<span class="text-danger">*</span></label>
        <select id="warehouse_id" name="warehouse_id"
            class="select2 form-select select2-primary" data-allow-clear="true" required>    
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Tanggal</label>
        <input type="date" name="date" id="date" class="form-control mb-3 mb-lg-0" placeholder="date" required />
    </div>

    <div class="col-12 col-md-12" id="itemRelation">
        <label for="item_id" class="form-label">Sparepart</label>
        <select id="item_id" class="form-select select2">
        </select>
    </div>
    
    <div class="col-12 col-md-12">
        <table class="table table-striped mb-3">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Sparepart</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="requestOrderDetailContent">
            </tbody>
        </table>
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
                                code: item.format_id,
                                name: item.name,
                                uom: item.uom
                            };
                        });

                    return {
                        results: apiResults
                    };
                },
                cache: true
            }
        }).on('change', function(e) {
            const selectedData = $(this).select2('data')[0];
            if (selectedData) {
                const newRow = `
                    <tr>
                        <td>${selectedData.code}<input type="hidden" name="item_id[]" value="${selectedData.id}"></td>
                        <td>${selectedData.name} (${selectedData.uom})</td>
                        <td><input type="number" name="quantity[]" class="form-control quantity" placeholder="Jumlah" required></td>
                        <td><input type="text" name="price[]" class="form-control price" placeholder="Harga" required></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
                    </tr>
                `;
                $('#requestOrderDetailContent').append(newRow);
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

    $(document).on('input', '.price', function() {
        const value = formatCurrency($(this).val());
        $(this).val(value);
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
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

        if (ribuan) {
            const separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix === undefined ? rupiah : rupiah ? (prefix || '') + rupiah : '';
    }

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