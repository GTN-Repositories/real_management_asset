<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Penerimaan Barang</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="fromEdit" action="{{ route('procurement.goods-receipt.updateItem') }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="col-12 col-md-12" id="vendorRelation">
        <label class="form-label" for="status">Status<span class="text-danger">*</span></label>
        <select id="status" name="status" class="select2 form-select select2-primary" data-allow-clear="true" required>
            <option value="1" selected>Diterima</option>
            <option value="2">Issue</option>
            <option value="3">Return</option>
        </select>
    </div>

    <table class="table table-borderless table-poppins table-striped table-hover mb-3">
        <thead>
            <tr>
                <th>Item</th>
                <th>Diterima</th>
                <th>Lampiran</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $value)
                <tr>
                    <td>
                        <input type="hidden" name="id[]" value="{{ $value->id }}">
                        {{ $value->item?->code ?? '' }} - {{ $value->item?->name ?? '' }}
                    </td>
                    <td>
                        <input type="text" name="accepted[]" class="form-control mb-3 mb-lg-0 qty" placeholder="Jumlah" value="{{ ($value->accepted != 0) ? $value->accepted : $value->qty }}" />
                    </td>
                    <td>
                        <input type="file" name="attachment_accepted[]" id="attachment" class="form-control mb-3 mb-lg-0" placeholder="Masukan Attachment" />
                    </td>
                    <td>
                        <textarea name="note_accepted[]" cols="30" rows="3" id="note" class="form-control mb-3 mb-lg-0" placeholder="Catatan">{{ $value->note_accepted }}</textarea>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        
    });

    $(document).on('input', '.qty', function() {
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
