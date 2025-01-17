<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Jadwal Inspeksi</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
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

    <div class="col-12" id="selectWerehouse">
        <label for="werehouse_id" class="form-label">Gudang</label>
        <select id="werehouse_id" class="form-select" name="werehouse_id" disabled>
            @if ($data->werehouse)
                <option value="{{ $data->werehouse_id }}" selected>
                    {{ $data->werehouse->name }}
                </option>
            @endif
        </select>
        <input type="hidden" name="asset_id" value="{{ $data->asset_id }}">
    </div>

    <div class="col-12 col-md-12" id="selectItem">
        <label for="item_id" class="form-label">Tambah Sparepart</label>
        <select id="item_id" class="form-select form-select-lg" name="item_id">
        </select>
    </div>

    <div class="col-12 mt-3" id="selectedItemsContainer">
        <label class="form-label">Item yang Dipilih:</label>
        <table class="table" id="selectedItemsTable">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Jumlah</th>
                    <th>Jenis Metode</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>
                            <input type="number" class="form-control item-stock" data-item-id="{{ $item->id }}"
                                value="{{ $item->stock_in_schedule > 0 ? $item->stock_in_schedule : $item->kanibal_stock_in_schedule }}">
                        </td>
                        <td>
                            <select class="form-select jenis-metode" data-item-id="{{ $item->id }}">
                                <option value="stock" {{ $item->stock_in_schedule > 0 ? 'selected' : '' }}>
                                    Pengurangan Stock</option>
                                <option value="kanibal" {{ $item->kanibal_stock_in_schedule > 0 ? 'selected' : '' }}>
                                    Kanibal Asset Lain</option>
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-item"
                                data-item-id="{{ $item->id }}">Hapus</button>
                        </td>
                    </tr>
                    <tr id="kanibal-row-{{ $item->id }}"
                        style="{{ $item->kanibal_stock_in_schedule > 0 ? '' : 'display: none;' }}">
                        <td colspan="5">
                            <div class="col-12" id="selectAssetKanibal-{{ $item->id }}">
                                <label for="asset_kanibal_id_{{ $item->id }}" class="form-label">Asset Yang
                                    Dipilih</label>
                                <select id="asset_kanibal_id_{{ $item->id }}"
                                    class="form-select asset-kanibal-select">
                                    @if($item->kanibal_stock_in_schedule > 0 && $item->assetKanibalId)
                                        <option value="{{ $item->assetKanibalId }}" selected>
                                            {{ $item->assetKanibalName }}
                                        </option>
                                    @endif
                                </select>
                            </div>
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
    $(document).ready(function() {
        $('#management_project_id').select2({
            dropdownParent: $('#managementRelation'),
            placeholder: 'Pilih projek',
            ajax: {
                url: "{{ route('management-project.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function(data) {
                    let apiResults = data.data.map(item => ({
                        text: item.name,
                        id: item.managementRelationId,
                    }));
                    return {
                        results: apiResults
                    };
                },
                cache: true
            }
        }).on('change', function() {
            var projectId = $(this).val();

            $('#asset_id').empty().trigger('change');
            if (projectId) {
                $.ajax({
                    url: "{{ route('management-project.by_project') }}",
                    dataType: 'json',
                    delay: 250,
                    data: {
                        projectId: projectId
                    },
                    success: function(data) {
                        if (data && typeof data === 'object' && Object.keys(data).length) {
                            var assetOptions = Object.entries(data).map(function([id,
                                name
                            ]) {
                                return {
                                    id: id,
                                    text: name
                                };
                            });

                            $('#asset_id').select2({
                                dropdownParent: $('#assetRelation'),
                                data: assetOptions,
                                allowClear: true
                            }).trigger('change');
                        } else {
                            $('#asset_id').select2({
                                dropdownParent: $('#assetRelation'),
                                data: [],
                                allowClear: true
                            }).trigger('change');
                        }
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
            }
        });

        $('#employee_id').select2({
            dropdownParent: $('#employeeId'),
            placeholder: 'Pilih karyawan',
            ajax: {
                url: "{{ route('employee.data') }}",
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
                    apiResults = data.data.map(function(item) {
                        return {
                            text: item.nameTitle,
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

        $('#item_id').select2({
            dropdownParent: $('#selectItem'),
            placeholder: 'Pilih Sparepart',
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
                    return {
                        results: data.data.map(item => ({
                            text: item.name,
                            id: item.id,
                            code: item.code,
                            available_stock: item.stock || 0
                        }))
                    };
                },
                cache: true
            }
        });

        $('#werehouse_id').select2({
            dropdownParent: $('#werehouseParent'),
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
                    return {
                        results: data.data.map(item => ({
                            text: item.name,
                            id: item.ids,
                        }))
                    };
                },
                cache: true
            }
        });
    });

    let selectedItems = [];
    $(document).ready(function() {
        $('#item_id').on('change', function() {
            const itemId = $(this).val();
            const selectedOption = $(this).select2('data')[0];
            const stockInput = $('#stock').val() || 1;

            if (itemId && !selectedItems.some(item => item.id === itemId)) {
                selectedItems.push({
                    id: itemId,
                    name: selectedOption.text,
                    code: selectedOption.code,
                    stock: stockInput,
                    availableStock: selectedOption.available_stock,
                    jenisMetode: 'stock',
                    assetKanibalId: null
                });
                updateSelectedItemsTable();
            }

            $('#stock').val('');
        });

        function updateSelectedItemsTable() {
            const tableBody = $('#selectedItemsTable tbody');

            selectedItems.forEach(function(item) {
                if (!tableBody.find(`tr[data-item-id="${item.id}"]`).length) {
                    tableBody.append(`
                    <tr data-item-id="${item.id}">
                        <td>${item.code}</td>
                        <td>${item.name}</td>
                        <td>
                            <input type="number"
                                   class="form-control item-stock"
                                   data-item-id="${item.id}"
                                   value="${item.stock}"
                            >
                        </td>
                        <td>
                            <select class="form-select jenis-metode" data-item-id="${item.id}">
                                <option value="stock" ${item.jenisMetode === 'stock' ? 'selected' : ''}>Pengurangan Stock</option>
                                <option value="kanibal" ${item.jenisMetode === 'kanibal' ? 'selected' : ''}>Kanibal Asset Lain</option>
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-item" data-item-id="${item.id}">Hapus</button>
                        </td>
                    </tr>
                    <tr id="kanibal-row-${item.id}" style="${item.jenisMetode === 'kanibal' ? '' : 'display: none;'}">
                        <td colspan="5">
                            <div class="col-12" id="selectAssetKanibal-${item.id}">
                                <label for="asset_kanibal_id_${item.id}" class="form-label">Asset Yang Dipilih</label>
                                <select id="asset_kanibal_id_${item.id}" class="form-select asset-kanibal-select" name="asset_kanibal_id"></select>
                            </div>
                        </td>
                    </tr>
                `);

                    const assetKanibalSelect = $(`#asset_kanibal_id_${item.id}`);
                    assetKanibalSelect.select2({
                        dropdownParent: $(`#selectAssetKanibal-${item.id}`),
                        placeholder: 'Pilih Asset',
                        ajax: {
                            url: "{{ route('asset.data') }}",
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    keyword: params.term,
                                    limit: 10
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data.data.map(asset => ({
                                        text: asset.nameWithNumber,
                                        id: asset.noDecryptId
                                    }))
                                };
                            },
                            cache: true
                        }
                    });
                }
            });

            $('.jenis-metode').off('change').on('change', function() {
                const itemId = $(this).data('item-id');
                const value = $(this).val();

                selectedItems = selectedItems.map(item =>
                    item.id === itemId ? {
                        ...item,
                        jenisMetode: value,
                        stock: value === 'stock' ? (item.stock || 1) : null,
                        kanibalStock: value === 'kanibal' ? (item.kanibalStock || 1) : null
                    } : item
                );

                if (value === 'kanibal') {
                    $(`#kanibal-row-${itemId}`).show();
                } else {
                    $(`#kanibal-row-${itemId}`).hide();
                }
            });

            $('.item-stock').off('change').on('change', function() {
                const itemId = $(this).data('item-id');
                const newStock = $(this).val();

                selectedItems = selectedItems.map(item =>
                    item.id === itemId ? {
                        ...item,
                        stock: item.jenisMetode === 'stock' ? newStock : null,
                        kanibalStock: item.jenisMetode === 'kanibal' ? newStock : null
                    } : item
                );
            });

            // Remove item
            $('.remove-item').off('click').on('click', function() {
                const itemId = $(this).data('item-id');
                selectedItems = selectedItems.filter(item => item.id !== itemId);
                updateSelectedItemsTable();
            });

            // Update asset kanibal selection
            $('.asset-kanibal-select').off('change').on('change', function() {
                const itemId = $(this).attr('id').split('_')[2];
                const selectedAssetId = $(this).val();

                selectedItems = selectedItems.map(item =>
                    item.id === itemId ? {
                        ...item,
                        assetKanibalId: selectedAssetId
                    } : item
                );
            });
        }

        $('#clearAllButton').on('click', function() {
            selectedItems = [];
            updateSelectedItemsTable();
        });
    });

    document.getElementById('formUpdate').addEventListener('submit', function(event) {
        event.preventDefault();

        for (let instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }

        const form = event.target;
        const formData = new FormData(form);

        // Data yang sudah ada (data yang di-foreach)
        @foreach ($items as $item)
            formData.append('selected_items[{{ $loop->index }}][id]', '{{ $item->id }}');
            formData.append('selected_items[{{ $loop->index }}][item_stock]',
                '{{ $item->stock_in_schedule }}');
            formData.append('selected_items[{{ $loop->index }}][kanibal_stock]',
                '{{ $item->kanibal_stock_in_schedule }}');
            formData.append('selected_items[{{ $loop->index }}][asset_kanibal_id]',
                '{{ $item->assetKanibalId }}');
        @endforeach

        // Data yang baru ditambahkan
        selectedItems.forEach((item, index) => {
            const adjustedIndex = index +
            {{ count($items) }}; // Menyesuaikan index agar tidak bertabrakan
            formData.append(`selected_items[${adjustedIndex}][id]`, item.id);
            const assetKanibalId = $(`#asset_kanibal_id_${item.id}`).val();
            formData.append(`selected_items[${adjustedIndex}][asset_kanibal_id]`, assetKanibalId);
            if (item.jenisMetode === 'stock') {
                formData.append(`selected_items[${adjustedIndex}][item_stock]`, item.stock);
            } else if (item.jenisMetode === 'kanibal') {
                formData.append(`selected_items[${adjustedIndex}][kanibal_stock]`, item.kanibalStock);
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
