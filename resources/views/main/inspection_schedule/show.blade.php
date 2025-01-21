<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Inspeksi</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>

<form method="POST" class="row g-3" id="formUpdateInspection"
    action="{{ route('inspection-schedule.update', $data->id) }}" enctype="multipart/form-data">
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

    <div class="col-12 col-md-6" id="managementRelation">
        <label class="form-label" for="management_project_id">Nama Management Project<span
                class="text-danger">*</span></label>
        <select id="management_project_id" name="management_project_id"
            class="select2 form-select select2-primary"data-allow-clear="true" required>
        </select>
    </div>
    <div class="col-12 col-md-6" id="assetRelation">
        <label class="form-label" for="asset_id">Nama Asset<span class="text-danger">*</span></label>
        <select id="asset_id" name="asset_id" class="select2 form-select select2-primary"data-allow-clear="true"
            required>
        </select>
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
            {{-- @if ($data->werehouse)
                <option value="{{ $data->werehouse_id }}" selected>
                    {{ $data->werehouse->name }}
                </option>
            @endif --}}
        </select>
        <input type="hidden" name="asset_id" value="{{ $data->asset_id }}">
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
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Simpan</button>
    </div>
</form>

@include('components.select2_js')
<script type="text/javascript">
    let selectedItems = @json($items);

    $(document).ready(function() {
        // Inisialisasi Select2 untuk item

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
                                    text: nameWithNumber
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

        var management_project_id = '{{ $data->management_project_id }}';
        var management_project_name = '{{ $data->managementProject->name }}';

        if (management_project_id) {
            var projectOption = new Option(management_project_name, management_project_id, true, true);
            $('#management_project_id').append(projectOption).trigger('change');
        }

        var asset_id = '{{ $data->asset_id }}';
        var asset_name = '{{ $data->asset->name }}';
        var asset_number = '{{ $data->asset->license_plate }}';
        var asset_license_plate = '{{ Crypt::decrypt($data->asset->id) }}';

        if (asset_id) {
            var assetOption = new Option(`${asset_license_plate} - ${asset_name} - ${asset_number}`, asset_id,
                true, true);
            $('#asset_id').append(assetOption).trigger('change');
        }

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
                        }))
                    };
                },
                cache: true
            }
        });

        // Menambahkan item ke daftar
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
                    kanibalStock: null,
                    jenisMetode: 'stock',
                    assetKanibalId: null
                });
                updateSelectedItemsTable();
            }

            $('#stock').val('');
        });

        // Memperbarui tabel item yang dipilih
        function updateSelectedItemsTable() {
            const tableBody = $('#selectedItemsTable tbody');
            tableBody.empty();

            selectedItems.forEach(function(item) {
                tableBody.append(`
                <tr>
                    <td>${item.code}</td>
                    <td>${item.name}</td>
                    <td>
                        <input type="number" class="form-control item-stock" data-item-id="${item.id}" value="${item.stock_in_schedule || item.kanibal_stock_in_schedule || 1}">
                    </td>
                    <td>
                        <select class="form-select jenis-metode" data-item-id="${item.id}">
                            <option value="stock" ${item.stock_in_schedule > 0 ? 'selected' : ''}>Pengurangan Stock</option>
                            <option value="kanibal" ${item.kanibal_stock_in_schedule > 0 ? 'selected' : ''}>Kanibal Asset Lain</option>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-item" data-item-id="${item.id}">Hapus</button>
                    </td>
                </tr>
                <tr id="kanibal-row-${item.id}" style="${item.kanibal_stock_in_schedule > 0 ? '' : 'display: none;'}">
                    <td colspan="5">
                        <label for="kanibal_stock_${item.id}" class="form-label">Kanibal Stock</label>
                        <input type="number" class="form-control kanibal-stock" data-item-id="${item.id}" value="${item.kanibal_stock_in_schedule || 1}">
                        <select id="asset_kanibal_id_${item.id}" class="form-select asset-kanibal-select" name="asset_kanibal_id">
                            ${item.assetKanibalId ? `<option value="${item.assetKanibalId}" selected>${item.assetKanibalId}</option>` : ''}
                        </select>
                    </td>
                </tr>
                `);

                // Inisialisasi Select2 untuk asset kanibal
                const assetKanibalSelect = $(`#asset_kanibal_id_${item.id}`);
                assetKanibalSelect.select2({
                    dropdownParent: $(`#kanibal-row-${item.id}`),
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
            });

            // Event handler untuk perubahan metode (stock/kanibal)
            $('.jenis-metode').off('change').on('change', function() {
                const itemId = $(this).data('item-id');
                const value = $(this).val();

                selectedItems = selectedItems.map(item =>
                    item.id === itemId ? {
                        ...item,
                        jenisMetode: value,
                        stock: value === 'stock' ? item.stock : null,
                        kanibalStock: value === 'kanibal' ? item.kanibalStock : null
                    } : item
                );

                if (value === 'kanibal') {
                    $(`#kanibal-row-${itemId}`).show();
                } else {
                    $(`#kanibal-row-${itemId}`).hide();
                }
            });

            // Event handler untuk perubahan stok
            $('.item-stock').off('change').on('change', function() {
                const itemId = $(this).data('item-id');
                const newStock = $(this).val();

                selectedItems = selectedItems.map(item =>
                    item.id === itemId ? {
                        ...item,
                        stock: newStock
                    } : item
                );
            });

            // Event handler untuk perubahan kanibal stok
            $('.kanibal-stock').off('change').on('change', function() {
                const itemId = $(this).data('item-id');
                const newKanibalStock = $(this).val();

                selectedItems = selectedItems.map(item =>
                    item.id === itemId ? {
                        ...item,
                        kanibalStock: newKanibalStock
                    } : item
                );
            });

            // Event handler untuk penghapusan item
            $('.remove-item').off('click').on('click', function() {
                const itemId = $(this).data('item-id');
                selectedItems = selectedItems.filter(item => item.id !== itemId);
                updateSelectedItemsTable();
            });

            $('.asset-kanibal-select').off('change').on('change', function() {
                const itemId = $(this).attr('id').split('_')[2]; // Dapatkan ID item dari atribut elemen
                const selectedAssetId = $(this).val(); // Ambil nilai yang dipilih
                // Cari dan perbarui item di selectedItems
                selectedItems = selectedItems.map(item => {
                    console.log('item.id', item.id, 'itemId', itemId);

                    if (item.id === itemId) {
                        item.assetKanibalId = selectedAssetId || null;
                    }
                    return item;
                });
            });
        }

        // Menghapus semua item
        $('#clearAllButton').on('click', function() {
            selectedItems = [];
            updateSelectedItemsTable();
        });

        // Submit form dengan data yang diperbarui
        $('#formUpdateInspection').on('submit', function(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            selectedItems.forEach((item, index) => {
                console.log('item', item);

                // Pastikan data yang dikirim lengkap
                const stockInput = $(`.item-stock[data-item-id="${item.id}"]`).val();
                const kanibalStockInput = $(`.kanibal-stock[data-item-id="${item.id}"]`).val();
                const assetKanibalIdInput = $(`#asset_kanibal_id_${item.id}`).val();

                // Pengecekan apakah item_stock adalah kanibal atau tidak
                if (item.jenisMetode === 'kanibal' || item.kanibal_stock_in_schedule > 0) {
                    formData.append(`selected_items[${index}][id]`, item.id);
                    formData.append(`selected_items[${index}][name]`, item.name);
                    formData.append(`selected_items[${index}][code]`, item.code);
                    formData.append(`selected_items[${index}][kanibal_stock]`,
                        kanibalStockInput);
                    formData.append(`selected_items[${index}][asset_kanibal_id]`,
                        assetKanibalIdInput);
                } else {
                    formData.append(`selected_items[${index}][id]`, item.id);
                    formData.append(`selected_items[${index}][name]`, item.name);
                    formData.append(`selected_items[${index}][code]`, item.code);
                    formData.append(`selected_items[${index}][item_stock]`, stockInput);
                }
            });

            fetch(form.action, {
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

        updateSelectedItemsTable();
    });
</script>
