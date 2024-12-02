@extends('layouts.global')

@section('title', 'Item')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> Detail Item {{ $data->name }}</h4>
        <div class="d-flex justify-content-end align-items-center mb-3 gap-3">
            <div>
                <a href="{{ route('item.index') }}" class="btn btn-primary">Kembali</a>
            </div>
        </div>
        <!-- Product List Table -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Detail Item {{ $data->name }}</h5>
                <div class="d-flex justify-content-end gap-2">

                </div>
            </div>
            <table class="datatables table mb-3" id="data-table">
                <thead class="border-top">
                    <tr>
                        <th>Code</th>
                        <td>{{ $data->code ?? null }}</td>
                    </tr>
                    <tr>
                        <th>Nama Item</th>
                        <td>{{ $data->name ?? null }}</td>
                    </tr>
                    <tr>
                        <th>Size</th>
                        <td>{{ $data->size ?? null }}</td>
                    </tr>
                    <tr>
                        <th>Brand</th>
                        <td>{{ $data->brand ?? null }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{ $data->status ?? null }}</td>
                    </tr>
                    <tr>
                        <th>Part</th>
                        <td>{{ $data->part ?? null }}</td>
                    </tr>
                    <tr>
                        <th>Color</th>
                        <td>{{ $data->color ?? null }}</td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td>{{ $data->category->name ?? null }}</td>
                    </tr>
                </thead>
            </table>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">History Stock {{ $data->name ?? null }}</h5>
                <div class="d-flex justify-content-end gap-2">

                </div>
            </div>
            <table class="datatables table" id="data-table">
                <thead class="border-top">
                    <tr>
                        <th>No</th>
                        <th>Stock</th>
                        <th>Metode</th>
                        <th>Dibuat Oleh</th>
                        <th>Status</th>
                        <th>Approve By</th>
                        <th>Dibuat Pada</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($itemStock as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->stock ?? '-' }}</td>
                            <td>{{ $item->metode ?? '-' }}</td>
                            <td>{{ $item->createdBy->name ?? '-' }}</td>
                            <td>{{ $item->status ?? '-' }}</td>
                            <td>{{ $item->approvedBy->name ?? '-' }}</td>
                            <td>{{ $item->created_at->format('d-m-Y H:i') ?? '-' }}</td>
                            <td>
                                @if ($item->status == 'approved')
                                    {{-- BUTTON REJECT --}}
                                    <button type="button" onclick="approveStock('{{ $item->id }}', 'rejected')"
                                        class="btn btn-sm btn-danger m-1"><i class="fas fa-close"></i></button>
                                @elseif($item->status == 'rejected')
                                    {{-- BUTTON APPROVE --}}
                                    <button type="button" onclick="approveStock('{{ $item->id }}', 'approved')"
                                        class="btn btn-sm btn-primary m-1"><i class="fas fa-check"></i></button>
                                @elseif($item->status == 'pending')
                                    {{-- BUTTON APPROVE --}}
                                    <button type="button" onclick="approveStock('{{ $item->id }}', 'approved')"
                                        class="btn btn-sm btn-primary m-1"><i class="fas fa-check"></i></button>

                                    {{-- BUTTON REJECT --}}
                                    <button type="button" onclick="approveStock('{{ $item->id }}', 'rejected')"
                                        class="btn btn-sm btn-danger m-1"><i class="fas fa-close"></i></button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No data</td>
                        </tr>
                    @endforelse
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
        function approveStock(id, status) {
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
                            url: "{{ route('item.approveStock', ':id') }}".replace(':id',
                                id),
                            type: 'POST',
                            data: postForm,
                            dataType: 'json',
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
