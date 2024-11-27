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
            placeholder="Masukan Nama Item" value="{{ old('name', $data->name) }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Tanggal</label>
        <input type="date" name="date" id="date" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Tanggal" value="{{ old('date', $data->date) }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label for="exampleFormControlSelect1" class="form-label">Jenis Maintenance</label>
        <select class="form-select" id="exampleFormControlSelect1" name="type" aria-label="Select Type">
            <option value="p2h" {{ $data->type == 'p2h' ? 'selected' : '' }}>P2H</option>
            <option value="pm" {{ $data->type == 'pm' ? 'selected' : '' }}>PM</option>
        </select>
    </div>

    <div class="col-12" id="selectAsset">
        <label for="asset_id" class="form-label">Plat Nomor</label>
        <select id="asset_id" class="form-select form-select-lg" name="asset_id">
            @if ($data->asset)
                <option value="{{ encrypt($data->asset_id) }}" selected>{{ $data->asset->name }}</option>
            @endif
        </select>
    </div>

    <div class="col-12 col-md-12">
        <label class="form-label" for="alias">Catatan</label>
        <textarea name="note" id="" cols="30" rows="10" class="form-control"
            placeholder="Masukkan Deskripsi">{{ old('note', $data->note) }}</textarea>
    </div>

    <div class="col-12 col-md-12" id="jenisMetode">
        <label for="jenis_metode" class="form-label">Jenis Penggunaan</label>
        <select id="jenis_metode" class="select2 form-select form-select-lg" name="jenis_metode">
            <option value="stock">Pengurangan Stock</option>
            <option value="kanibal">Kanibal Asset Lain</option>
        </select>
    </div>

    <div class="col-12" id="selectAssetKanibal" style="display: none">
        <label for="asset_kanibal_id" class="form-label">Asset Yang Dipilih</label>
        <select id="asset_kanibal_id" class="form-select form-select-lg" name="asset_kanibal_id">
        </select>
    </div>

    <div class="col-12 col-md-12" id="selectItem">
        <label for="item_id" class="form-label">Sparepart</label>
        <select id="item_id" class="form-select form-select-lg" name="item_id">
        </select>
    </div>

    <div class="col-12 mt-3" id="selectedItemsContainer">
        <label class="form-label">Item yang Dipilih:</label>
        <table class="table" id="selectedItemsTable">
            <tbody>
            </tbody>
        </table>
        <button id="clearAllButton" class="btn btn-warning btn-sm mt-2">Clear All</button>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"
            onclick="window.location.reload();">Cancel</button>
    </div>
</form>

@include('components.select2_js')

<script type="text/javascript">
    let selectedItems = [
        @php
            $existingItemStocks = json_decode($data->item_stock ?? '{}', true) ?? [];
        @endphp
        @foreach ($items as $item)
            {
                @php
                    $decryptId = Crypt::decrypt($item->id);
                @endphp
                id: "{{ $item->id }}",
                    name: "{{ $item->name }}",
                    code: "{{ $item->code }}",
                    part: "{{ $item->part }}",
                    stock: {{ (int) $existingItemStocks[$decryptId] }},
                    availableStock: {{ $item->stock }}
            },
        @endforeach
    ];

    $(document).ready(function() {
        $('#jenis_metode').on('change', function() {
            const value = $(this).val();

            if (value === 'stock') {
                $('#selectAssetKanibal').hide();
                $('#selectItem').show();
                $('#selectedItemsContainer').show();
            } else if (value === 'kanibal') {
                $('#selectAssetKanibal').show();
                $('#selectItem').hide();
                $('#selectedItemsContainer').hide();
            }
        });

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
                            text: item.name,
                            id: item.relationId,
                        }))
                    };
                },
                cache: true
            }
        });

        $('#asset_kanibal_id').select2({
            dropdownParent: $('#selectAssetKanibal'),
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
                    apiResults = data.data.reduce((unique, item) => {
                        if (!unique.some((i) => i.text === item.name)) {
                            unique.push({
                                text: item.name,
                                id: item.relationId,
                            });
                        }
                        return unique;
                    }, []);

                    return {
                        results: apiResults
                    };
                },
                cache: true
            }
        });

        $('#item_id').select2({
            dropdownParent: $('#selectItem'),
            placeholder: 'Pilih Sparepart',
            ajax: {
                url: "{{ route('item.data') }}",
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
                            text: item.name,
                            id: item.item_id,
                            code: item.code,
                            part: item.part,
                            available_stock: item.stock || 0
                        }))
                    };
                },
                cache: true
            }
        });

        function updateSelectedItemsTable() {
            const tableBody = $('#selectedItemsTable tbody');
            tableBody.empty();

            selectedItems.forEach(function(item) {
                tableBody.append(
                    `<tr>
                <td>${item.code}</td>
                <td>${item.name}</td>
                <td>${item.part}</td>
                <td>
                    <input type="number" class="form-control item-stock"
                        data-item-id="${item.id}"
                        value="${item.stock}">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item" data-item-id="${item.id}">Hapus</button>
                </td>
            </tr>`
                );
            });

            $('.item-stock').on('change', function() {
                const itemId = $(this).data('item-id');
                const newStock = $(this).val();

                selectedItems = selectedItems.map(item => {
                    if (item.id === itemId) {
                        item.stock = parseInt(newStock,
                        10);
                    }
                    return item;
                });
            });

            $('.remove-item').on('click', function() {
                const itemId = $(this).data('item-id');
                selectedItems = selectedItems.filter(item => item.id !== itemId);
                updateSelectedItemsTable();
            });
        }


        updateSelectedItemsTable();

        $('#item_id').on('change', function() {
            const itemId = $(this).val();
            const selectedOption = $(this).select2('data')[0];

            const existingItemIndex = selectedItems.findIndex(item => item.id === itemId);

            if (itemId && existingItemIndex === -1) {
                selectedItems.push({
                    id: itemId,
                    name: selectedOption.text,
                    code: selectedOption.code,
                    part: selectedOption.part,
                    stock: 1,
                    availableStock: selectedOption.available_stock
                });
                updateSelectedItemsTable();
            }
        });

        $('#clearAllButton').on('click', function() {
            selectedItems = [];
            updateSelectedItemsTable();
        });

        @if ($data->asset_kanibal_id)
            $('#asset_kanibal_id').append(new Option('{{ $data->assetKanibal->name }}',
                '{{ encrypt($data->asset_kanibal_id) }}', true, true)).trigger('change');
        @endif

        @if ($data->asset_id)
            $('#asset_id').append(new Option('{{ $data->asset->name }}',
                '{{ encrypt($data->asset_id) }}', true, true)).trigger('change');
        @endif
    });

    document.getElementById('formUpdate').addEventListener('submit', function(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);

        selectedItems.forEach((item, index) => {
            formData.append(`selected_items[${index}][id]`, item.id);
            formData.append(`selected_items[${index}][stock]`, item.stock);
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
