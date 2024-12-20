<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Kategori Barang</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formEdit" action="{{ route('asset-reminder.update', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="col-12 col-md-12">
        <label class="form-label" for="type">Jenis Reminder <span class="text-danger">*</span></label>
        <input type="text" id="type" name="type" class="form-control" placeholder="Masukkan Jenis Reminder"
            value="{{ $data->type }}" />
    </div>
    <div class="col-12 col-md-12">
        <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
        <input type="text" id="title" name="title" class="form-control" placeholder="Masukkan Title"
            value="{{ $data->title }}" />
    </div>
    <div class="col-12 col-md-12">
        <label class="form-label" for="body">Body <span class="text-danger">*</span></label>
        <input type="text" id="body" name="body" class="form-control" placeholder="Masukkan Body"
            value="{{ $data->body }}" />
    </div>
    <div class="col-12 col-md-12">
        <label class="form-label" for="send_to">Send To <span class="text-danger">*</span></label>
        <input type="text" id="send_to" name="send_to" class="form-control" placeholder="Masukkan Penerima"
            value="{{ $data->send_to }}" />
    </div>
    <div class="col-12 col-md-12" id="userId">
        <label for="employee_id" class="form-label">User<span class="text-danger">*</span></label>
        <div class="select2-primary">
            <div class="position-relative">
                <select id="user_id" name="user_id" class="select2 form-select" required>
                    <!-- Options will be populated dynamically -->
                </select>
            </div>
        </div>
    </div>
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#user_id').select2({
            dropdownParent: $('#userId'),
            placeholder: 'Pilih user',
            ajax: {
                url: "{{ route('user.data') }}",
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
                            text: item.name,
                            id: item.idRelationAll,
                        };
                    });

                    return {
                        results: apiResults
                    };
                },
                cache: true
            }
        });

        var assetId = '{{ $data->user_id ?? '' }}';
        var assetName = '{{ $data->user->name ?? '' }}';
        if (assetId) {
            var categoryOption = new Option(assetName, assetId, true, true);
            $('#user_id').append(categoryOption).trigger('change');
        }
    })
</script>
<script>
    document.getElementById('formEdit').addEventListener('submit', function(event) {
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

                        $('#data-table-reminder').DataTable().ajax.reload();
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
