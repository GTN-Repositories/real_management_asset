<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="text-center mb-4">
    <h3 class="mb-2">Request Petty Cash</h3>
    <p class="text-muted">Tambahkan Data Sesuai Dengan Informasi Yang Tersedia</p>
</div>
<form method="POST" class="row g-3 mb-3" id="formCreate" action="{{ route('management-project.requestPettyCash') }}"
    enctype="multipart/form-data">
    @csrf

    <div class="col-12 col-md-12" id="managementRelation">
        <label class="form-label" for="project_id">Nama Project<span
                class="text-danger">*</span></label>
        <select id="project_id" name="project_id"
            class="select2 form-select select2-primary"data-allow-clear="true" required>
        </select>
    </div>
    <div class="col-12 col-md-12">
        <label for="amount" class="form-label">Nilai</label>
        <input type="text" id="amount" name="amount" class="form-control" placeholder="Input Nilai">
    </div>
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
            aria-label="Close">Cancel</button>
    </div>
</form>

<div class="row">
    <div class="col-md-12">
        <table class="datatables table" id="data-table">
            <thead class="border-top">
                <tr>
                    <th>No</th>
                    <th>Nama Project</th>
                    <th>Nilai</th>
                    <th>Dibuat Oleh</th>
                    <th>Dibuat Pada</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($petty_cash as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->project->name ?? null }}</td>
                        <td>{{ number_format($item->amount) ?? null }}</td>
                        <td>{{ $item->createdBy->name ?? null }}</td>
                        <td>{{ $item->created_at->format('d-m-Y H:i') ?? null }}</td>
                        <td>
                            {{-- BUTTON APPROVE --}}
                            <button type="button" onclick="approvePettyCash('{{ $item->id }}', '2')"
                                class="btn btn-sm btn-primary m-1"><i class="fas fa-check"></i></button>
                            {{-- BUTTON REJECT --}}
                            <button type="button" onclick="approvePettyCash('{{ $item->id }}', '3')"
                                class="btn btn-sm btn-danger m-1"><i class="fas fa-close"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('components.select2_js')

<script>
    $(document).ready(function() {
        $('#project_id').select2({
            dropdownParent: $('#managementRelation'),
            placeholder: 'Pilih projek',
            ajax: {
                url: "{{ route('management-project.data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function(data) {
                    let apiResults = data.data.map(item => ({
                        text: item.name,
                        id: item.managementRelationId,
                    }));
                    return {
                        results: apiResults
                    };
                },
                cache: true
            }
        });
    });

    function approvePettyCash(id, status) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this record!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, approve it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                var postForm = {
                    '_token': '{{ csrf_token() }}',
                    '_method': 'PUT',
                    'status': status
                };
                $.ajax({
                    url: "{{ route('management-project.approvePettyCash', ':id') }}".replace(':id', id),
                    type: 'POST',
                    data : postForm,
                    dataType  : 'json',
                })
                .done(function(data) {
                    Swal.fire('Approved!', data['message'], 'success');
                    location.reload();
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while approving the record.', 'error');
                })
            }
        });
    }
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
