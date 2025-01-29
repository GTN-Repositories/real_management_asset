<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Jadwal Maintenance</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>

<form method="POST" class="row g-3" id="formUpdate" action="{{ route('maintenances.update', $maintenance->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="col-12 col-md-12">
        <label class="form-label">Judul Maintenance</label>
        <input type="text" name="name" id="name" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" value="{{ old('name', $maintenance->name) }}" required disabled />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Tanggal</label>
        <input type="date" name="date" id="date" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Tanggal" value="{{ old('date', $maintenance->date) }}" required disabled />
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
        <select id="asset_id" class="form-select" name="asset_id" disabled>
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
            disabled />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Mekanik</label>
        <input type="text" name="employee_id" id="employee_id" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Mekanik" value="{{ old('employee_id', $maintenance->employee_id) }}" required
            disabled />
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
            class="form-control mb-3 mb-lg-0" placeholder="Masukan Delay (Strt-Bd) (hrs)" />
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
        <input type="datetime-local" name="deviasi" value="{{ $maintenance->deviasi }}" id="deviasi"
            class="form-control mb-3 mb-lg-0" placeholder="Masukan Deviasi" />
    </div>

    <div class="col-12 col-md-6" id="finish_at_form">
        <label class="form-label">Finish / RFU</label>
        <input type="datetime-local" name="finish_at" value="{{ $maintenance->finish_at }}" id="finish_at"
            class="form-control mb-3 mb-lg-0" placeholder="Masukan Finish / RFU" />
    </div>

    <div class="col-12 col-md-6" id="hm_form">
        <label class="form-label">HM</label>
        <input type="text" name="hm" value="{{ $maintenance->hm }}" id="hm"
            class="form-control mb-3 mb-lg-0" placeholder="Masukan HM" />
    </div>

    <div class="col-12 col-md-6" id="km_form">
        <label class="form-label">KM</label>
        <input type="text" name="km" value="{{ $maintenance->km }}" id="km"
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
        <select name="urgention" id="urgention" class="form-select select2">
            <option value="Major">Major</option>
            <option value="Minor">Minor</option>
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

        changeStatus('{{ $data->status }}');
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

    function changeStatus(status) {
        if (status == 'OnHold') {
            $('#code_delay_form').attr('hidden', false);
            // $('#delay_reason_form').attr('hidden', false);
            $('#estimate_finish_form').attr('hidden', false);
            $('#delay_hours_form').attr('hidden', false);
            $('#start_maintenace_form').attr('hidden', false);
            $('#end_maintenace_form').attr('hidden', false);
            $('#deviasi_form').attr('hidden', false);

            $('#finish_at_form').attr('hidden', true);
            $('#hm_form').attr('hidden', true);
            $('#km_form').attr('hidden', true);
            $('#location_form').attr('hidden', true);
            $('#detail_problem_form').attr('hidden', true);
            $('#action_to_do_form').attr('hidden', true);
            $('#urgention_form').attr('hidden', true);
        } else if (status == 'Finish') {
            $('#code_delay_form').attr('hidden', true);
            // $('#delay_reason_form').attr('hidden', true);
            $('#estimate_finish_form').attr('hidden', true);
            $('#delay_hours_form').attr('hidden', true);
            $('#start_maintenace_form').attr('hidden', true);
            $('#end_maintenace_form').attr('hidden', true);
            $('#deviasi_form').attr('hidden', true);

            $('#finish_at_form').attr('hidden', false);
            $('#hm_form').attr('hidden', false);
            $('#km_form').attr('hidden', false);
            $('#location_form').attr('hidden', false);
            $('#detail_problem_form').attr('hidden', false);
            $('#action_to_do_form').attr('hidden', false);
            $('#urgention_form').attr('hidden', false);
        } else {
            $('#code_delay_form').attr('hidden', true);
            // $('#delay_reason_form').attr('hidden', true);
            $('#estimate_finish_form').attr('hidden', true);
            $('#delay_hours_form').attr('hidden', true);
            $('#start_maintenace_form').attr('hidden', true);
            $('#end_maintenace_form').attr('hidden', true);
            $('#deviasi_form').attr('hidden', true);

            $('#finish_at_form').attr('hidden', true);
            $('#hm_form').attr('hidden', true);
            $('#km_form').attr('hidden', true);
            $('#location_form').attr('hidden', true);
            $('#detail_problem_form').attr('hidden', true);
            $('#action_to_do_form').attr('hidden', true);
            $('#urgention_form').attr('hidden', true);
        }
    }
</script>
