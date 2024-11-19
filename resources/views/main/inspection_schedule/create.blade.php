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

    <div class="col-12" id="selectItem">
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
    $(document).ready(function() {
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
                            id: item.item_id
                        }))
                    };
                },
                cache: true
            }
        });

        loadSelectedItems();

        $('#item_id').on('change', function() {
            const itemId = $(this).val();

            if (itemId) {
                $.ajax({
                    url: "{{ route('add.item.session') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        item_id: itemId
                    },
                    success: function(response) {
                        loadSelectedItems();
                    },
                    error: function(xhr) {
                        console.error("Error adding item_id to session:", xhr.responseText);
                        alert("Failed to add item_id to session");
                    }
                });
            }
        });

        function loadSelectedItems() {
            $.ajax({
                url: "{{ route('get.selected.items') }}",
                type: "GET",
                success: function(items) {
                    $('#selectedItemsTable tbody').empty();

                    items.forEach(function(item) {
                        $('#selectedItemsTable tbody').append(`
                        <tr>
                            <td>${item.code}</td>
                            <td>${item.name}</td>
                            <td>${item.part}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-item" data-item-id="${item.id}">Hapus</button>
                            </td>
                        </tr>
                    `);
                    });

                    $('.remove-item').off('click').on('click', function() {
                        const itemId = $(this).data('item-id');
                        removeItemFromSession(itemId);
                    });
                }
            });
        }

        function removeItemFromSession(itemId) {
            $.ajax({
                url: "{{ route('remove.item.session') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    item_id: itemId
                },
                success: function(response) {
                    loadSelectedItems
                        ();
                },
                error: function(xhr) {
                    console.error("Error removing item_id from session:", xhr.responseText);
                    alert("Failed to remove item_id from session");
                }
            });
        }

        $('#clearAllButton').on('click', function() {
            $.ajax({
                url: "{{ route('clear.items.session') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    loadSelectedItems
                        ();
                },
                error: function(xhr) {
                    console.error("Error clearing all items from session:", xhr
                        .responseText);
                    alert("Failed to clear all items from session");
                }
            });
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
    });

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
