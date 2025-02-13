<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Site</h3>
    <p class="text-muted">Edit Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="formEdit" action="{{ route('site.update', $data->id) }}" enctype="multipart/form-data">
    @csrf
    @method('put')
    
    <div class="col-12">
        <label class="form-label" for="code">Kode <span class="text-danger">*</span></label>
        <input type="text" id="code" name="code" value="{{ $data->code }}" class="form-control" placeholder="BSS TGR" />
    </div>
    <div class="col-12">
        <label class="form-label" for="name">Nama <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" value="{{ $data->name }}" class="form-control" placeholder="BSS TGR" />
    </div>
    <div class="col-12 mb-4" id="formSiteId">
        <label for="select2Basic" class="form-label">Site</label>
        <select id="select2Basic" class="select2 form-select form-select-lg" name="site_id" data-allow-clear="true">
            <option></option>
        </select>
    </div>
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        var siteData = @json($site);
        $('#select2Basic').select2({
            dropdownParent: $('#formSiteId'),
            placeholder: 'Pilih Site',
            data: siteData.map(function(site) {
                return {
                    id: site.id,
                    text: site.name + ' - ' + site.code,
                };
            })
        });
    });
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
            }else if(!data.status){
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