<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Edit Barang</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3" id="fromEdit" action="{{ route('employee.update', $data->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="col-12 col-md-6">
        <label class="form-label">Nama Karyawan</label>
        <input type="text" name="name" id="name" class="form-control mb-3 mb-lg-0"
            placeholder="Masukan Nama Karyawan" value="{{ $data->name }}" required />
    </div>

    <div class="col-12 col-md-6" id="jobRelation">
        <label class="form-label" for="job_title_id">Nama Jabatan<span class="text-danger">*</span></label>
        <select id="job_title_id" name="job_title_id" class="select2 form-select select2-primary"data-allow-clear="true"
            required>
        </select>
    </div>

    <div class="col-12 col-md-6" id="projectRelation">
        <label class="form-label" for="management_project_id">Nama Project<span class="text-danger">*</span></label>
        <select id="management_project_ids" name="management_project_id"
            class="select2 form-select select2-primary"data-allow-clear="true" required>
        </select>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

<script>
    var job_title_id = '{{ $data->job_title_id ?? '' }}';
    var job_name = '{{ $data->jobTitle->name ?? '' }}';
    var management_project_id = '{{ $data->management_project_id ?? '' }}';
    var project_name = '{{ $data->managementProject->name ?? '' }}';

    $(document).ready(function() {
        $('#job_title_id').select2({
            dropdownParent: $('#jobRelation'),
            placeholder: 'Pilih jabatan',
            ajax: {
                url: "{{ route('job-title.data') }}",
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
                    apiResults = data.data
                        .filter(function(item) {
                            return item.relationId !== null;
                        })
                        .map(function(item) {
                            return {
                                text: item.name,
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

        $('#management_project_ids').select2({
            dropdownParent: $('#projectRelation'),
            placeholder: 'Pilih project',
            ajax: {
                url: "{{ route('management-project.data') }}",
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
                    apiResults = data.data
                        .map(function(item) {
                            return {
                                text: item.name,
                                id: item.managementRelationId,
                            };
                        });

                    return {
                        results: apiResults
                    };
                },
                cache: true
            }
        });

        if (job_title_id) {
            var option = new Option(job_name, job_title_id, true, true);
            $('#job_title_id').append(option).trigger('change');
        }

        if (management_project_id) {
            var optionProject = new Option(project_name, management_project_id, true, true);
            $('#management_project_ids').append(optionProject).trigger('change');
        }
    });
</script>
<script>
    document.getElementById('fromEdit').addEventListener('submit', function(event) {
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
