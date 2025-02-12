<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Jadwal Work Order</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>

<form method="POST" class="row g-3" id="formUpdate" action="{{ route('maintenances.update', $maintenance->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="col-12 col-md-12">
        <label class="form-label">Judul Maintenance</label>
        <input type="text" name="name" id="name" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" value="{{ old('name', $maintenance->name) }}" required  />
            {{-- DISABLED --}}
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Tanggal</label>
        <input type="datetime-local" name="date" id="date" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Tanggal" value="{{ old('date', $maintenance->date) }}" required  />
            {{-- DISABLED --}}
    </div>

    <div class="col-12 col-md-6">
        <label for="exampleFormControlSelect1" class="form-label">Jenis Maintenance</label>
        <select class="form-select" id="exampleFormControlSelect1" name="type" aria-label="Select Type" >
            {{-- DISABLED --}}
            <option value="p2h" {{ $data->type == 'p2h' ? 'selected' : '' }}>P2H</option>
            <option value="pm" {{ $data->type == 'pm' ? 'selected' : '' }}>PM</option>
        </select>
    </div>

    <div class="col-12" id="selectAsset">
        <label for="asset_id" class="form-label">Plat Nomor</label>
        <select id="asset_id" class="form-select" name="asset_id" >
            {{-- DISABLED --}}
            @if ($data->asset)
                <option value="{{ encrypt($data->asset_id) }}" selected>
                    {{ $data->asset->license_plate . ' - ' . $data->asset->name . ' - ' . $data->asset->asset_number }}
                </option>
            @endif
        </select>
        <input type="hidden" name="asset_id" value="{{ $data->asset_id }}">
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Workshop</label>
        <input type="text" name="workshop" id="workshop" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Workshop" value="{{ old('workshop', $maintenance->workshop) }}" required
             />
             {{-- DISABLED --}}
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Mekanik</label>
        <input type="text" name="employee_id" id="employee_id" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Mekanik" value="{{ old('employee_id', $maintenance->employee_id) }}" required
             />
             {{-- DISABLED --}}
    </div>

    <div class="col-12 col-md-12">
        <label class="form-label" for="alias">Catatan</label>
        <div>
            {!! $data->note !!}
        </div>
    </div>

    <div class="col-12 col-md-12">
        <label for="statusMaintenance" class="form-label">Status</label>
        <select class="form-select select2" id="statusMaintenance" name="status" aria-label="Select Status">
            <option value="Active" {{ $data->status == 'Active' ? 'selected' : '' }}>Active</option>
            <option value="Inactive" {{ $data->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="UnderMaintenance" {{ $data->status == 'UnderMaintenance' ? 'selected' : '' }}>Under Maintenance</option>
            <option value="UnderRepair" {{ $data->status == 'UnderRepair' ? 'selected' : '' }}>Under Repair</option>
            <option value="Waiting" {{ $data->status == 'Waiting' ? 'selected' : '' }}>Waiting</option>
            <option value="Scrap" {{ $data->status == 'Scrap' ? 'selected' : '' }}>Scrap</option>
            <option value="RFU" {{ $data->status == 'RFU' ? 'selected' : '' }}>RFU</option>
        </select>
    </div>

    <div class="col-12 col-md-6" id="code_delay_form">
        <label class="form-label">Code Delay</label>
        <input type="text" name="code_delay" value="{{ $maintenance->code_delay }}" id="code_delay"
            class="form-control mb-3 mb-lg-0" placeholder="Masukan Code Delay" />
    </div>

    <div class="col-12 col-md-6" id="delay_reason_form">
        <label class="form-label">Delay Reason</label>
        <input type="text" name="delay_reason" value="{{ $maintenance->delay_reason }}" id="delay_reason"
            class="form-control mb-3 mb-lg-0" placeholder="Masukan Delay Reason" />
    </div>

    <div class="col-12 col-md-6" id="estimate_finish_form">
        <label class="form-label">DateTime Estimate Finish/RFU</label>
        <input type="date" value="{{ date('Y-m-d') }}" name="estimate_finish"
            value="{{ $maintenance->estimate_finish }}" id="estimate_finish" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan DateTime Estimate Finish/RFU" />
    </div>

    <div class="col-12 col-md-6" id="delay_hours_form">
        <label class="form-label">Delay (Strt-Bd) (hrs)</label>
        <input type="number" name="delay_hours" value="{{ $maintenance->delay_hours }}" id="delay_hours"
            class="form-control mb-3 mb-lg-0" placeholder="Masukan Delay (Strt-Bd) (hrs)" disabled/>
    </div>

    <div class="col-12 col-md-6" id="start_maintenace_form">
        <label class="form-label">DateTime Start Maintenance</label>
        <input type="datetime-local" name="start_maintenace" value="{{ $maintenance->start_maintenace }}"
            id="start_maintenace" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan DateTime Start Maintenance" />
    </div>

    <div class="col-12 col-md-6" id="end_maintenace_form">
        <label class="form-label">DateTime Stop Maintenance</label>
        <input type="datetime-local" name="end_maintenace" value="{{ $maintenance->end_maintenace }}"
            id="end_maintenace" class="form-control mb-3 mb-lg-0" placeholder="Masukan DateTime Stop Maintenance" />
    </div>

    <div class="col-12 col-md-6" id="deviasi_form">
        <label class="form-label">Deviasi</label>
        <input type="number" name="deviasi" value="{{ $maintenance->deviasi }}" id="deviasi"
            class="form-control mb-3 mb-lg-0" placeholder="Masukan Deviasi" disabled/>
    </div>

    <div class="col-12 col-md-6" id="finish_at_form">
        <label class="form-label">Finish / RFU</label>
        <input type="datetime-local" name="finish_at" value="{{ $maintenance->finish_at }}" id="finish_at"
            class="form-control mb-3 mb-lg-0" placeholder="Masukan Finish / RFU" />
    </div>

    <div class="col-12 col-md-6" id="hm_form">
        <label class="form-label">HM</label>
        <input type="number" name="hm" value="{{ $maintenance->hm }}" id="hm"
            class="form-control mb-3 mb-lg-0" placeholder="Masukan HM" />
    </div>

    <div class="col-12 col-md-6" id="km_form">
        <label class="form-label">KM</label>
        <input type="number" name="km" value="{{ $maintenance->km }}" id="km"
            class="form-control mb-3 mb-lg-0" placeholder="Masukan KM" />
    </div>

    <div class="col-12 col-md-6" id="location_form">
        <label class="form-label">Location</label>
        <input type="text" name="location" value="{{ $maintenance->location }}" id="location"
            class="form-control mb-3 mb-lg-0" placeholder="Masukan Location" />
    </div>

    <div class="col-12 col-md-12" id="detail_problem_form">
        <label class="form-label">Detail Problem</label>
        <textarea name="detail_problem" id="detail_problem" class="form-control mb-3 mb-lg-0" cols="30"
            rows="5">{{ $maintenance->detail_problem }}</textarea>
    </div>

    <div class="col-12 col-md-6" id="action_to_do_form">
        <label class="form-label">Action To Do</label>
        <input type="text" name="action_to_do" value="{{ $maintenance->action_to_do }}" id="action_to_do"
            class="form-control mb-3 mb-lg-0" placeholder="Masukan Action To Do" />
    </div>

    <div class="col-12 col-md-6" id="urgention_form">
        <label class="form-label">Jenis Kerusakan</label>
        <select name="urgention" id="urgention" class="form-select select2" required>
            <option value="" selected disabled>Pilih Jenis Kerusakan</option>
            <option value="Minor" {{ $maintenance->urgention == 'Minor' ? 'selected' : '' }}>Minor</option>
            <option value="Major" {{ $maintenance->urgention == 'Major' ? 'selected' : '' }}>Major</option>
        </select>
    </div>

    <div class="col-12 col-md-12" id="werehouseParent">
        <label for="werehouse_id" class="form-label">Gudang</label>
        <select id="werehouse_id" class="form-select form-select-lg" name="werehouse_id">
        </select>
    </div>

    <div class="col-12 mt-3" id="selectedItemsContainer">
        <label class="form-label">Item yang Dipilih:</label>
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Replacing From</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maintenanceSparepart as $key => $item)
                    <tr>
                        <td>{{ $item->item->code ?? null }}</td>
                        <td>{{ $item->item->name ?? null }}</td>
                        <td>
                            {{ $item->type }}
                        </td>
                        <td>
                            {{ $item->quantity }}
                        </td>
                        <td>
                            {{ ($item->asset_id == null) ? '-' :  'AST - '.$item->asset_id. ' - '. ($item->asset->name ?? null) . ' - '. ($item->asset->serial_number ?? '') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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

    <div class="col-12 col-md-12">
        <label class="form-label" for="alias">Komentar</label>
        <textarea name="comment" id="comment" cols="30" rows="10" class="form-control"
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
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="me-2">{{ $comment->user->name ?? 'anonymous' }}</span>
                            <span class="text-muted">
                                {{ $comment->time_note? \Carbon\Carbon::parse($comment->time_note)->locale('id')->translatedFormat('d F Y H:i'): '-' }}
                            </span>
                        </div>
                        <div class="mt-1">
                            {{ strip_tags($comment->comment) }}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

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

        // changeStatus('{{ $data->status }}');

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
                        >
                    </td>
                    <td>
                        <select class="form-select jenis-metode" data-item-id="${item.id}">
                            <option value="stock" ${item.jenisMetode === 'stock' ? 'selected' : ''}>Pengurangan Stock</option>
                            <option value="kanibal" ${item.jenisMetode === 'kanibal' ? 'selected' : ''}>Replacing From</option>
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

        var warehouse = '{{ $maintenanceSparepart[0]->warehouse->id ?? '' }}';
        var warehouse_name = '{{ $maintenanceSparepart[0]->warehouse->name ?? '' }}';

        if (warehouse) {
            var projectOption = new Option(warehouse_name, warehouse, true, true);
            $('#werehouse_id').append(projectOption).trigger('change');
        }
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

    $(document).on('change', '#statusMaintenance', function() {
        var status = $(this).val();

        // changeStatus(status);
    })

    // function changeStatus(status) {
    //     if (status == 'OnHold') {
    //         $('#code_delay_form').attr('hidden', false);
    //         // $('#delay_reason_form').attr('hidden', false);
    //         $('#estimate_finish_form').attr('hidden', false);
    //         $('#delay_hours_form').attr('hidden', false);
    //         $('#start_maintenace_form').attr('hidden', false);
    //         $('#end_maintenace_form').attr('hidden', false);
    //         $('#deviasi_form').attr('hidden', false);

    //         $('#finish_at_form').attr('hidden', true);
    //         $('#hm_form').attr('hidden', true);
    //         $('#km_form').attr('hidden', true);
    //         $('#location_form').attr('hidden', true);
    //         $('#detail_problem_form').attr('hidden', true);
    //         $('#action_to_do_form').attr('hidden', true);
    //         $('#urgention_form').attr('hidden', true);
    //     } else if (status == 'Finish') {
    //         $('#code_delay_form').attr('hidden', true);
    //         // $('#delay_reason_form').attr('hidden', true);
    //         $('#estimate_finish_form').attr('hidden', true);
    //         $('#delay_hours_form').attr('hidden', true);
    //         $('#start_maintenace_form').attr('hidden', true);
    //         $('#end_maintenace_form').attr('hidden', true);
    //         $('#deviasi_form').attr('hidden', true);

    //         $('#finish_at_form').attr('hidden', false);
    //         $('#hm_form').attr('hidden', false);
    //         $('#km_form').attr('hidden', false);
    //         $('#location_form').attr('hidden', false);
    //         $('#detail_problem_form').attr('hidden', false);
    //         $('#action_to_do_form').attr('hidden', false);
    //         $('#urgention_form').attr('hidden', false);
    //     } else {
    //         $('#code_delay_form').attr('hidden', true);
    //         // $('#delay_reason_form').attr('hidden', true);
    //         $('#estimate_finish_form').attr('hidden', true);
    //         $('#delay_hours_form').attr('hidden', true);
    //         $('#start_maintenace_form').attr('hidden', true);
    //         $('#end_maintenace_form').attr('hidden', true);
    //         $('#deviasi_form').attr('hidden', true);

    //         $('#finish_at_form').attr('hidden', true);
    //         $('#hm_form').attr('hidden', true);
    //         $('#km_form').attr('hidden', true);
    //         $('#location_form').attr('hidden', true);
    //         $('#detail_problem_form').attr('hidden', true);
    //         $('#action_to_do_form').attr('hidden', true);
    //         $('#urgention_form').attr('hidden', true);
    //     }
    // }
</script>
