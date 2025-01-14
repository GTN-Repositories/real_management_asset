@extends('layouts.global')

@section('title', 'Item')

@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
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
                        <td>{{ $data->code ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Nama Item</th>
                        <td>{{ $data->name ?? '-' }}</td>
                    </tr>
                    {{-- <tr>
                        <th>Ukuran</th>
                        <td>{{ $data->size ?? '-' }}</td>
                    </tr> --}}
                    <tr>
                        <th>Brand</th>
                        <td>{{ $data->brand ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{ $data->status ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Oum Tipe</th>
                        <td>{{ $data->oum->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Part</th>
                        <td>{{ $data->part ?? '-' }}</td>
                    </tr>
                    {{-- <tr>
                        <th>Warna</th>
                        <td>{{ $data->color ?? '-' }}</td>
                    </tr> --}}
                    <tr>
                        <th>Stock</th>
                        <td>{{ $data->stock ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Harga</th>
                        <td>{{ number_format($data->price, 0, ',', '.') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td>{{ $data->category->name ?? '-' }}</td>
                    </tr>
                </thead>
            </table>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Pemakaian Sparepart</h5>
            </div>

            <div class="card-datatable table-responsive">
                <table class="datatables table table-striped table-poppins " id="data-table-usage-stock">
                    <thead class="border-top">
                        <tr>
                            <th>No</th>
                            <th>ID Inspeksi</th>
                            <th>Asset</th>
                            <th>Jumlah Penggunaan</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">History Stock {{ $data->name ?? '-' }}</h5>
                <div class="d-flex justify-content-end gap-2">

                </div>
            </div>
            <table class="datatables table table-striped table-poppins " id="data-table">
                <thead class="border-top">
                    <tr>
                        <th>No</th>
                        <th>Stock</th>
                        <th>Metode</th>
                        <th>Harga</th>
                        <th>Gudang</th>
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
                            <td>{{ number_format($item->price) ?? '-' }}</td>
                            <td>{{ $item->warehouse->name ?? '-' }}</td>
                            <td>{{ $item->createdBy->name ?? '-' }}</td>
                            <td>{{ $item->status ?? '-' }}</td>
                            <td>{{ $item->approvedBy->name ?? '-' }}</td>
                            <td>{{ $item->created_at->format('d-m-Y H:i') ?? '-' }}</td>
                            @if (auth()->user()->hasPermissionTo('item-approve'))
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
                            @endif
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
    <script>
        $(document).ready(function() {
            init_table();
        });

        function init_table(keyword = '', startDate = '', endDate = '', predefinedFilter = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            var table = $('#data-table-usage-stock').DataTable({
                processing: true,
                serverSide: true,
                columnDefs: [{
                    target: 0,
                    visible: true,
                    searchable: false
                }, ],

                ajax: {
                    type: "GET",
                    url: "{{ route('item.dataUsagePart') }}",
                    data: {
                        keyword: keyword,
                        startDate: startDate,
                        endDate: endDate,
                        predefinedFilter: predefinedFilter,
                        item_id: '{{ $data->id }}'
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'inspection_id',
                        name: 'inspection_id'
                    },
                    {
                        data: 'asset',
                        name: 'asset'
                    },
                    {
                        data: 'usage',
                        name: 'usage'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ]
            });
        }
    </script>
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
