<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Tambah Pertanyaan</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formCreate" action="{{ route('form.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="col-12 col-md-6">
        <label for="exampleFormControlSelect1" class="form-label">Tipe Pertanyaan</label>
        <select class="form-select" id="exampleFormControlSelect1" name="type" aria-label="Select Tipe Pertanyaan">
            <option value="p2h">P2H</option>
            <option value="PM">PM</option>
        </select>
    </div>
    <div class="col-12 col-md-6 mb-4" id="formRelationId">
        <label for="select2Basic" class="form-label">Kategori Pertanyaan</label>
        <select id="select2Basic" class="select2 form-select form-select-lg" name="category_form_id" data-allow-clear="true">
            <option></option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label" for="question">Pertanyaan<span class="text-danger">*</span></label>
        <input type="text" id="question" name="question" class="form-control" placeholder="Masukan Pertanyaan" />
    </div>
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

<script>
    $(document).ready(function() {
    var relationData = @json($relation);
    $('#select2Basic').select2({
        dropdownParent: $('#formRelationId'),
        placeholder: 'Pilih kategori',
        data: relationData.map(function(relation) {
            return {
                id: relation.id,
                text: relation.name
            };
        }),
        tags: true,
        createTag: function(params) {
            var term = $.trim(params.term);

            if (term === '') {
                return null;
            }

            return {
                id: term,
                text: term,
                newOption: true
            };
        },
        insertTag: function(data, newTag) {
            data.push(newTag);
        }
    });
});
</script>
<script>
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

                        $('#data-table').DataTable().ajax.reload();
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
