@extends('layouts.global')

@section('title', 'Management Project')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> Detail Project {{ $data->name }}</h4>

    <!-- Product List Table -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="card-title mb-0">Detail Project {{ $data->name }}</h5>
            <div class="d-flex justify-content-end gap-2">
                
            </div>
        </div>
        <table class="datatables table mb-3" id="data-table">
            <thead class="border-top">
                <tr>
                    <th>Nama Project</th>
                    <td>{{ $data->name }}</td>
                </tr>
                <tr>
                    <th>Nama Asset</th>
                    <td>{{ $data->asset ?? null }}</td>
                </tr>
                <tr>
                    <th>Tanggal Awal</th>
                    <td>{{ $data->start_date ?? null }}</td>
                </tr>
                <tr>
                    <th>Tanggal Akhir</th>
                    <td>{{ $data->end_date ?? null }}</td>
                </tr>
                <tr>
                    <th>Petty Cash</th>
                    <td>{{ number_format($data->petty_cash) ?? null }}</td>
                </tr>
                <tr>
                    <th>Metode</th>
                    <td>{{ $data->calculation_method ?? null }}</td>
                </tr>
            </thead>
        </table>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">History Petty Cash {{ $data->name }}</h5>
            <div class="d-flex justify-content-end gap-2">
                
            </div>
        </div>
        <table class="datatables table" id="data-table">
            <thead class="border-top">
                <tr>
                    <th>No</th>
                    <th>Nama Project</th>
                    <th>Nilai</th>
                    <th>Dibuat Oleh</th>
                    <th>Status</th>
                    <td>Approve By</td>
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
                        <td>{{ \App\Helpers\Helper::statusPettyCash($item->status) ?? null }} </td>
                        <td>{{ $item->approvedBy->name ?? null }}</td>
                        <td>{{ $item->created_at->format('d-m-Y H:i') ?? null }}</td>
                        <td>
                            @if ($item->status == 2)
                                {{-- BUTTON REJECT --}}
                                <button type="button" onclick="approvePettyCash('{{ $item->id }}', '3')"
                                    class="btn btn-sm btn-danger m-1"><i class="fas fa-close"></i></button>
                            @elseif($item->status == 3)
                                {{-- BUTTON APPROVE --}}
                                <button type="button" onclick="approvePettyCash('{{ $item->id }}', '2')"
                                    class="btn btn-sm btn-primary m-1"><i class="fas fa-check"></i></button>
                            @elseif($item->status == 1)
                                {{-- BUTTON APPROVE --}}
                                <button type="button" onclick="approvePettyCash('{{ $item->id }}', '2')"
                                    class="btn btn-sm btn-primary m-1"><i class="fas fa-check"></i></button>

                                {{-- BUTTON REJECT --}}
                                <button type="button" onclick="approvePettyCash('{{ $item->id }}', '3')"
                                    class="btn btn-sm btn-danger m-1"><i class="fas fa-close"></i></button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="modal-ce" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body" id="content-modal-ce">

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
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
@endpush
