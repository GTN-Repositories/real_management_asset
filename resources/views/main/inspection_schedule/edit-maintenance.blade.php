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
            placeholder="Masukan Tanggal" value="{{ old('date', $data->date) }}" required disabled />
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

    <div class="col-12 col-md-12">
        <label class="form-label" for="alias">Catatan</label>
        <div>
            {!! $data->note !!}
        </div>
    </div>

    <div class="col-12 col-md-12">
        <label for="exampleFormControlSelect1" class="form-label">Status</label>
        <select class="form-select select2" id="exampleFormControlSelect1" name="status" aria-label="Select Status">
            <option value="Scheduled" {{ $data->status == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
            <option value="InProgress" {{ $data->status == 'InProgress' ? 'selected' : '' }}>In Progress</option>
            <option value="OnHold" {{ $data->status == 'OnHold' ? 'selected' : '' }}>On Hold</option>
            <option value="Finish" {{ $data->status == 'Finish' ? 'selected' : '' }}>Finish</option>
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Tanggal Breakdown</label>
        <input type="datetime-local" value="{{ date('Y-m-d') }}" name="date" id="date" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Hm</label>
        <input type="text" name="hm" id="hm" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Km</label>
        <input type="text" name="km" id="km" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Detail Problem</label>
        <input type="text" name="detail_problem" id="detail_problem" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Action To Do</label>
        <input type="text" name="action_to_do" id="action_to_do" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Urgensi</label>
        <select name="urgention" id="urgention" class="form-select" id="urgention">
            <option value="Major">Major</option>
            <option value="Minor">Minor</option>
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Tanggal Pengingat</label>
        <input type="date" value="{{ date('Y-m-d') }}" name="date_reminder" id="date_reminder" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Item" />
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
</script>
