<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Detail Jadwal Inspeksi</h3>
    <p class="text-muted">Detail Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>

<form method="POST" class="row g-3" id="formUpdate" action="{{ route('inspection-schedule.update', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="col-12 col-md-12">
        <label class="form-label">Judul Inspeksi</label>
        <input type="text" name="name" id="name" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" value="{{ old('name', $data->name) }}" required disabled />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Tanggal</label>
        <input type="date" name="date" id="date" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Tanggal" value="{{ old('date', $data->date) }}" required disabled />
    </div>

    <div class="col-12 col-md-6">
        <label for="exampleFormControlSelect1" class="form-label">Jenis Inspeksi</label>
        <select class="form-select" id="exampleFormControlSelect1" name="type" aria-label="Select Type" disabled>
            <option value="p2h" {{ $data->type == 'p2h' ? 'selected' : '' }}>P2H</option>
            <option value="pm" {{ $data->type == 'pm' ? 'selected' : '' }}>PM</option>
        </select>
    </div>

    <div class="col-12" id="selectAsset">
        <label for="asset_id" class="form-label">Plat Nomor</label>
        <select id="asset_id" class="form-select" name="asset_id" disabled>
            @if ($data->asset)
                <option value="{{ encrypt($data->asset_id) }}" selected>
                    {{ $data->asset->license_plate . ' - ' . $data->asset->name . ' - ' . $data->asset->asset_number }}
                </option>
            @endif
        </select>
        <input type="hidden" name="asset_id" value="{{ $data->asset_id }}">
    </div>

    <div class="col-12 col-md-12">
        <label class="form-label" for="alias">Catatan</label>
        <div>
            {!! $data->note !!}
        </div>
    </div>

    <div class="col-12 mt-3" id="selectedItemsContainer">
        <label class="form-label">Item yang Dipilih:</label>
        <table class="table" id="selectedItemsTable">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Stok Normal</th>
                    <th>Stok Kanibal</th>
                    <th>Asset Kanibal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $key => $item)
                    <tr>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>
                            {{ $item->stock_in_schedule }}
                        </td>
                        <td>
                            {{ $item->kanibal_stock_in_schedule }}
                        </td>
                        <td>
                            {{ $item->assetKanibalName ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

@include('components.select2_js')
<script>
    CKEDITOR.replace('comment');
</script>
<script type="text/javascript">
    selectedItems = [];

    $(document).ready(function() {
        $('#asset_id').select2({
            dropdownParent: $('#selectAsset'),
            placeholder: 'Pilih Asset',
            ajax: {
                url: "{{ route('asset.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.data.map(item => ({
                            text: item.nameWithNumber,
                            id: item.relationId,
                        }))
                    };
                },
                cache: true
            }
        });
    });

    document.getElementById('formUpdate').addEventListener('submit', function(event) {
        event.preventDefault();

        for (let instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }

        const form = event.target;
        const formData = new FormData(form);

        selectedItems.forEach((item, index) => {
            formData.append(`selected_items[${index}][id]`, item.id);
            const assetKanibalId = $(`#asset_kanibal_id_${item.id}`).val();
            formData.append(`selected_items[${index}][asset_kanibal_id]`, assetKanibalId);
            if (item.jenisMetode === 'stock') {
                formData.append(`selected_items[${index}][item_stock]`, item.stock);
            } else if (item.jenisMetode === 'kanibal') {
                formData.append(`selected_items[${index}][kanibal_stock]`, item.kanibalStock);
            }
        });

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
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message
                    }).then(() => {
                        $("#modal-ce").modal("hide");
                        // REDIRECT TO ROUTE INDEX
                        window.location.href = "{{ route('inspection-schedule.index') }}";
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
