<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Tambah Jadwal Maintenance</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>

<form method="POST" class="row g-3" id="formCreate" action="{{ route('inspection-schedule.store') }}"
    enctype="multipart/form-data">
    @csrf
    <div class="col-12 col-md-12">
        <label class="form-label">Judul Maintenance</label>
        <input type="text" name="name" id="name" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" value="{{ old('name') }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Tanggal</label>
        <input type="date" name="date" id="date" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" value="{{ old('date', date('Y-m-d')) }}" required />
    </div>

    <div class="col-12 col-md-6">
        <label for="exampleFormControlSelect1" class="form-label">Jenis Maintenance</label>
        <select class="form-select" id="exampleFormControlSelect1" name="type" aria-label="Select Type">
            <option value="p2h">P2H</option>
            <option value="pm">PM</option>
        </select>
    </div>

    <div class="col-12" id="selectAsset">
        <label for="asset_id" class="form-label">Plat Nomor</label>
        <select id="asset_id" class="form-select form-select-lg" name="asset_id">
        </select>
    </div>

    <div class="col-12 col-md-12">
        <label class="form-label" for="alias">Catatan</label>
        <textarea name="note" id="note" cols="30" rows="10" class="form-control"
            placeholder="Masukkan Deskripsi"></textarea>
    </div>

    <div class="col-12 col-md-12" id="selectItem">
        <label for="item_id" class="form-label">Sparepart</label>
        <select id="item_id" class="form-select form-select-lg" name="item_id">
        </select>
    </div>

    <div class="col-12 mt-3" id="selectedItemsContainer">
        <label class="form-label">Item yang Dipilih:</label>
        <table class="table" id="selectedItemsTable">
            <tbody></tbody>
        </table>
        <button id="clearAllButton" class="btn btn-warning btn-sm mt-2">Clear All</button>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Simpan</button>
    </div>
</form>

@include('components.select2_js')
<script>
    CKEDITOR.replace('note');
</script>
<script type="text/javascript">
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
                        'keyword': params.term,
                        'limit': 10,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.data.map(item => ({
                            text: item.nameWithNumber,
                            id: item.relationId
                        }))
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
                        'keyword': params.term,
                        'limit': 10,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.data.map(item => ({
                            text: item.name,
                            id: item.item_id,
                            code: item.code,
                            available_stock: item.stock || 0
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
            tableBody.empty();

            selectedItems.forEach(function(item) {
                tableBody.append(`
                <tr>
                    <td>${item.code}</td>
                    <td>${item.name}</td>
                    <td>
                        <input type="number"
                               class="form-control item-stock"
                               data-item-id="${item.id}"
                               value="${item.stock}"
                               min="1"
                               max="${item.availableStock}"
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
                                    id: asset.relationId
                                }))
                            };
                        },
                        cache: true
                    }
                });
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

    document.getElementById('formCreate').addEventListener('submit', function(event) {
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
