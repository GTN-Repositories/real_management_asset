<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Tambah Vendor Comparation</h3>
    <p class="text-muted">Silahkan isi form untuk melakukan Request Order</p>
</div>
<form method="POST" class="row g-3" id="formCreate" action="{{ route('procurement.rfq.store') }}"
    enctype="multipart/form-data">
    @csrf

    <input type="hidden" name="request_order_id" id="request_order_id" value="{{ $ro->id }}">
    <div class="col-12 col-md-12" id="vendorRelation">
        <label class="form-label" for="vendor_id">Vendor<span class="text-danger">*</span></label>
        <select id="vendor_id" name="vendor_id"
            class="select2 form-select select2-primary" data-allow-clear="true" required>    
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Harga</label>
        <input type="text" name="price" id="price" class="form-control mb-3 mb-lg-0" placeholder="Harga" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Lampiran</label>
        <input type="file" name="attachment" id="attachment" class="form-control mb-3 mb-lg-0" placeholder="Harga" required />
    </div>

    <div class="col-12 col-md-12">
        <label class="form-label">Catatan</label>
        <textarea name="note" id="note" class="form-control mb-3 mb-lg-0" placeholder="Catatan"></textarea>
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
        CKEDITOR.replace('note');

        $(document).on('input', '#price', function() {
            const value = $(this).val();
            $(this).val(formatCurrency(value));
        });

        $('#vendor_id').select2({
            dropdownParent: $('#vendorRelation'),
            placeholder: 'Pilih Vendor',
            ajax: {
                url: "{{ route('vendor.data') }}",
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