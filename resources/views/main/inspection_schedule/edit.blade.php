<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Jadwal Maintenance</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>

<form method="POST" class="row g-3" id="formUpdate" action="{{ route('inspection-schedule.update', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="col-12 col-md-12">
        <label class="form-label">Judul Maintenance</label>
        <input type="text" name="name" id="name" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" value="{{ old('name', $data->name) }}" required readonly />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Tanggal</label>
        <input type="date" name="date" id="date" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Tanggal" value="{{ old('date', $data->date) }}" required readonly />
    </div>

    <div class="col-12 col-md-6">
        <label for="exampleFormControlSelect1" class="form-label">Jenis Maintenance</label>
        <select class="form-select" id="exampleFormControlSelect1" name="type" aria-label="Select Type" disabled>
            <option value="p2h" {{ $data->type == 'p2h' ? 'selected' : '' }}>P2H</option>
            <option value="pm" {{ $data->type == 'pm' ? 'selected' : '' }}>PM</option>
        </select>
    </div>

    <div class="col-12" id="selectAsset">
        <label for="asset_id" class="form-label">Plat Nomor</label>
        <select id="asset_id" class="form-select form-select-lg" name="asset_id" disabled>
            @if ($data->asset)
                <option value="{{ encrypt($data->asset_id) }}" selected>{{ $data->asset->name . ' - ' . $data->asset->asset_number }}</option>
            @endif
        </select>
    </div>

    <div class="col-12 col-md-12">
        <label class="form-label" for="alias">Catatan</label>
        <div>
            {!! $data->note !!}
        </div>
    </div>

    <div class="col-12 col-md-12">
        <label for="exampleFormControlSelect1" class="form-label">Status</label>
        <select class="form-select select2" id="exampleFormControlSelect1" name="status" aria-label="Select Status">
            <option value="scheduled" {{ $data->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            <option value="in_progress" {{ $data->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="on_hold" {{ $data->status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
            <option value="finish" {{ $data->status == 'finish' ? 'selected' : '' }}>Finish</option>
        </select>
    </div>

    <div class="col-12 col-md-12" id="selectItem">
        <label for="item_id" class="form-label">Sparepart</label>
        <select id="item_id" class="form-select form-select-lg" name="item_id" disabled>
        </select>
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
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>
                            <input type="number" class="form-control item-stock" data-item-id="{{ $item->id }}"
                                value="{{ $item->stock_in_schedule }}" readonly>
                        </td>
                        <td>
                            <input type="number" class="form-control kanibal-stock" data-item-id="{{ $item->id }}"
                                value="{{ $item->kanibal_stock_in_schedule }}" readonly>
                        </td>
                        <td>
                            {{ $assetKanibalIds[$item->id] ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-12 col-md-12">
        <label class="form-label" for="alias">Komentar</label>
        <textarea name="comment" id="editor" cols="30" rows="10" class="form-control"
            placeholder="Masukkan Komentar"></textarea>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Komentar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($comments as $comment)
                <tr>
                    <td>{{ strip_tags($comment->comment) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"
            onclick="window.location.reload();">Cancel</button>
    </div>
</form>

@include('components.select2_js')
<script>
    CKEDITOR.replace('editor', {
        language: 'id', // Indonesian language
        height: 300,
        toolbar: [
            ['Bold', 'Italic', 'Underline'],
            ['NumberedList', 'BulletedList'],
            ['JustifyLeft', 'JustifyCenter', 'JustifyRight'],
            ['Link', 'Unlink'],
            ['Undo', 'Redo']
        ]
    });
</script>
<script type="text/javascript">
    let selectedItems = [
        @php
            $existingItemStocks = json_decode($data->item_stock ?? '{}', true) ?? [];
            $existingKanibalStocks = json_decode($data->kanibal_stock ?? '{}', true) ?? [];
            $existingAssetKanibalIds = json_decode($data->asset_kanibal_id ?? '{}', true) ?? [];
        @endphp
        @foreach ($items as $item)
            {
                id: "{{ $item->id }}",
                name: "{{ $item->name }}",
                code: "{{ $item->code }}",
                stock: {{ $existingItemStocks[Crypt::decrypt($item->id)] ?? 0 }},
                kanibalStock: {{ $existingKanibalStocks[Crypt::decrypt($item->id)] ?? 0 }},
                assetKanibalId: {{ $existingAssetKanibalIds[Crypt::decrypt($item->id)] ?? null }},
                assetKanibalName: "{{ $existingAssetKanibalIds[Crypt::decrypt($item->id)] ? \App\Models\Asset::find($existingAssetKanibalIds[Crypt::decrypt($item->id)])->name . ' - ' . \App\Models\Asset::find($existingAssetKanibalIds[Crypt::decrypt($item->id)])->asset_number : '-' }}",
            },
        @endforeach
    ];

    $(document).ready(function() {
        function updateSelectedItemsTable() {
            const tableBody = $('#selectedItemsTable tbody');
            tableBody.empty();

            selectedItems.forEach(function(item) {
                tableBody.append(
                    `<tr>
                <td>${item.code}</td>
                <td>${item.name}</td>
                <td>
                    <input type="number" class="form-control item-stock"
                        data-item-id="${item.id}"
                        value="${item.stock}" readonly>
                </td>
                <td>
                    <input type="number" class="form-control kanibal-stock"
                        data-item-id="${item.id}"
                        value="${item.kanibalStock}" readonly>
                </td>
                <td>
                    ${item.assetKanibalName}
                </td>
            </tr>`
                );
            });
        }

        updateSelectedItemsTable();
    })


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
</script>
<script>
    document.getElementById('formUpdate').addEventListener('submit', function(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);

        CKEDITOR.instances.editor.updateElement();

        selectedItems.forEach((item, index) => {
            formData.append(`selected_items[${index}][id]`, item.id);
            formData.append(`selected_items[${index}][stock]`, item.stock);
            formData.append(`selected_items[${index}][kanibal_stock]`, item.kanibalStock);
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
