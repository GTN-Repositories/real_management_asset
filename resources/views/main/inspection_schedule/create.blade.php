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
        <textarea name="note" id="" cols="30" rows="10" class="form-control"
            placeholder="Masukkan Deskripsi"></textarea>
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
            <tbody></tbody>
        </table>
        <button id="clearAllButton" class="btn btn-warning btn-sm mt-2">Clear All</button>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Mulai Inspeksi</button>
        <button type="button" class="btn btn-label-secondary">Simpan</button>
    </div>
</form>

@include('components.select2_js')

<script type="text/javascript">
    let selectedItems = [];

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

        $('#item_id').on('change', function() {
            const itemId = $(this).val();
            const selectedOption = $(this).select2('data')[0];
            const stockInput = $('#stock').val() || 1;

            if (itemId && !selectedItems.some(item => item.id === itemId)) {
                selectedItems.push({
                    id: itemId,
                    name: selectedOption.text,
                    code: selectedOption.code,
                    part: selectedOption.part,
                    stock: stockInput,
                    availableStock: selectedOption.available_stock
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
                <td>${item.part}</td>
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
                    <button type="button" class="btn btn-danger btn-sm remove-item" data-item-id="${item.id}">Hapus</button>
                </td>
            </tr>
        `);
            });

            $('.item-stock').on('change', function() {
                const itemId = $(this).data('item-id');
                const newStock = $(this).val();

                selectedItems = selectedItems.map(item =>
                    item.id === itemId ? {
                        ...item,
                        stock: newStock
                    } : item
                );
            });

            $('.remove-item').on('click', function() {
                const itemId = $(this).data('item-id');
                selectedItems = selectedItems.filter(item => item.id !== itemId);
                updateSelectedItemsTable();
            });
        }

        $('#clearAllButton').on('click', function() {
            selectedItems = [];
            updateSelectedItemsTable();
        });
    });


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
    });

    document.getElementById('formCreate').addEventListener('submit', function(event) {
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
