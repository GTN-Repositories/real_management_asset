<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Tambah Jadwal Maintenance</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
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
            placeholder="Masukan Nama Item" value="{{ old('date', $data->date) }}" required />
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

    <div class="col-12" id="selectItem">
        <label for="item_id" class="form-label">Sparepart</label>
        <select id="item_id" class="form-select form-select-lg" name="item_id">
        </select>
    </div>

    <div class="col-12 mt-3" id="selectedItemsContainer">
        <label class="form-label">Item yang Dipilih:</label>
        <table class="table" id="selectedItemsTable">
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->part }}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-item"
                                data-item-id="{{ encrypt($item->id) }}">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button id="clearAllButton" class="btn btn-warning btn-sm mt-2">Clear All</button>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        var asset_id = "{{ $data->asset_id }}";
        var assetName = "{{ $data->asset->name ?? '' }}";

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

        if (asset_id) {
            var option = new Option(assetName, asset_id, true, true);
            $('#asset_id').append(option).trigger('change');
        }

        $('.btn-label-secondary').on('click', function() {
            $.ajax({
                url: "{{ route('clear.items.session') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $("#modal-ce").modal("hide");
                },
                error: function(xhr) {
                    console.error("Error clearing session:", xhr.responseText);
                }
            });
        });
    });

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
                    loadSelectedItems();
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
                    loadSelectedItems();
                }
            });
        });

        $('.remove-item').on('click', function() {
            const itemId = $(this).data('item-id');
            removeItemFromSession(itemId);
        });
    });

    document.getElementById('formUpdate').addEventListener('submit', function(event) {
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
