@extends('layouts.global')

@section('title', 'Process Purchase Order')
@section('title_page', 'Procurement / Process Purchase Order')


@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-3">
                    <div class="accordion-item border-0 active mb-0" id="fl-1">
                        <div id="fleet1" class="accordion-collapse collapse show" data-bs-parent="#fleet">
                            <div class="accordion-body pt-3 pb-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="mb-1">Request Process</h4>
                                </div>
                                <ul class="timeline ps-3 mt-4 mb-0">
                                    @php
                                    $statuses = [
                                        'Request Order' => 100,
                                        'RFQ' => 101,
                                        'Upload Invoice' => 102,
                                        'Memroses PO' => 103,
                                        'Mengirim Pesanan' => 104,
                                        'Penerimaan Barang' => 105,
                                        'Selesai' => 105,
                                    ];
                                    @endphp

                                    @foreach ($statuses as $label => $statusNumber)
                                    @php
                                    $textClass =
                                    $backlog->status == $statusNumber
                                    ? 'text-primary'
                                    : ($backlog->status > $statusNumber
                                    ? 'text-success'
                                    : 'text-secondary');
                                    $indicatorClass =
                                    $backlog->status == $statusNumber
                                    ? 'timeline-indicator-primary'
                                    : ($backlog->status > $statusNumber
                                    ? 'timeline-indicator-success'
                                    : 'timeline-indicator-secondary');
                                    @endphp
                                    <li class="timeline-item ms-1 ps-4 border-left-dashed">
                                        <span
                                            class="timeline-indicator-advanced {{ $indicatorClass }} border-0 shadow-none">
                                            <i class="ti ti-circle-check"></i>
                                        </span>
                                        <div class="timeline-event ps-0 pb-0">
                                            <div class="timeline-header">
                                                <h5 class="{{ $textClass }} text-uppercase mb-0">
                                                    {{ $label }}</h5>
                                            </div>
                                            <h6 class="mb-1">{{ $label }}</h6>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-9">
                    <div class="mb-4">
                        <div class="accordion mt-3" id="accordionExample">
                            <div class="card accordion-item active">
                                <h2 class="accordion-header" id="heading">
                                    <button type="button" class="accordion-button bg-white text-black"
                                        data-bs-toggle="collapse" data-bs-target="#accordionUnit" aria-expanded="false"
                                        aria-controls="accordion">
                                        Detail Request Process
                                    </button>
                                </h2>
                                <div id="accordionUnit" class="accordion-collapse collapse show" aria-labelledby="heading"
                                    data-bs-parent="#questionsAccordion">
                                    <div class="accordion-body row px-5 py-4">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th>Tgl. Permintaan</th>
                                                    <th>:</th>
                                                    <td>{{ $backlog->date ?? '-' }}</td>

                                                    <th>Gudang</th>
                                                    <th>:</th>
                                                    <td>{{ $backlog->warehouse?->name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tgl.</th>
                                                    <th>:</th>
                                                    <td>{{ $backlog->created_at ?? '-' }}</td>

                                                    <th>User</th>
                                                    <th>:</th>
                                                    <td>{{ $backlog->createdBy?->name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Kode RO</th>
                                                    <th>:</th>
                                                    <td>{{ $backlog->code ?? '-' }}</td>

                                                    <th>Total Harga</th>
                                                    <th>:</th>
                                                    <td>Rp {{ number_format($backlog->total_price ?? 0, 0, ',', '.') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Item</th>
                                                    <th>:</th>
                                                    <td>{{ $backlog->total_item ?? '-' }}</td>

                                                    <th>Status</th>
                                                    <th>:</th>
                                                    <td>-</td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- @if ($budgetStatus === 'Budget exceeded')
                    <div class="alert alert-danger">
                        Total backlog unit ini dalam 1 bulan terakhir telah melebihi budget maksimal!
                    </div>
                    @endif --}}

                    @if ($backlog->reason)
                        <div class="alert alert-warning text-start d-flex flex-column">
                            <h5 class="mb-1 text-warning">Backlog tidak lolos</h5>
                            <p class="m-0 text-warning">Alasan : {{ $backlog->reason }}</p>
                        </div>
                    @endif
                    
                    <div class="card table-responsive">
                        <div class="card-body">
                            <table class="datatables table table-striped table-poppins mb-3" id="data-table-item">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Code</th>
                                        <th>Item</th>
                                        <th>Harga</th>
                                        <th>Vendor</th>
                                        <th>Jumlah</th>
                                        <th>Total Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item as $data)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->item?->code ?? '-' }}</td>
                                        <td>{{ $data->item?->name ?? '-' }}</td>
                                        <td>Rp {{ number_format($data->price ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ $data->vendorComparation?->vendor?->name ?? '-' }}</td>
                                        <td>{{ $data->qty ?? '-' }}</td>
                                        <td>Rp {{ number_format($data->total_price ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                                @endforeach
                            </table>
                        </div>
                    </div>
                        <div class="text-end">
                            @if ($backlog->status == 100)
                            <a href="javascript:void(0)" type="button" class="btn btn-primary btn-md mt-3"
                                onclick="sendRo('{{ $backlog->id }}')">Ajukan RO</a>
                            @elseif ($backlog->status == 101)
                            <a href="javascript:void(0)" type="button" class="btn btn-primary btn-md mt-3"
                                onclick="sendRfq('{{ $backlog->id }}')">Submit</a>
                            @elseif ($backlog->status == 102)
                            <a href="javascript:void(0)" type="button" class="btn btn-primary btn-md mt-3 me-3"
                                onclick="sendUploadInvoice('{{ $backlog->id }}')">Submit</a>
                            @elseif ($backlog->status == 103)
                            <a href="javascript:void(0)" type="button" class="btn btn-danger btn-md mt-3 me-3"
                                onclick="processPo('{{ $backlog->id }}', 100)">Reject</a>
                            <a href="javascript:void(0)" type="button" class="btn btn-primary btn-md mt-3 me-3"
                                onclick="processPo('{{ $backlog->id }}', 104)">Approve</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-ce" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body" id="content-modal-ce">

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        $(document).ready(function() {
            init_table();

            $('#data-table-item').DataTable({
                "paging": false,
                "searching": true,
                "pageLength": -1,
                "order": [
                    [0, "asc"]
                ],
            });
        });

        function processPo(id, status) {
            // Confirmation
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this record!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, send it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('procurement.process-po.sendPo', ':id') }}".replace(':id', id),
                        type: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'status': status
                        },
                    })
                    .done(function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message
                        }).then(() => {
                            window.location.href = "{{ route('procurement.process-po.index') }}";
                        });
                    })
                    .fail(function() {
                        Swal.fire('Error!', 'An error occurred while sending the record.', 'error');
                    });
                }
            });
        }

        function deleteItem(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this record!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var postForm = {
                        '_token': '{{ csrf_token() }}',
                        '_method': 'DELETE',
                    };
                    $.ajax({
                            url: "{{ route('procurement.process-po.destroyItem', ':id') }}".replace(':id', id),
                            type: 'POST',
                            data: postForm,
                            dataType: 'json',
                        })
                        .done(function(data) {
                            Swal.fire('Deleted!', data['message'], 'success');
                            $('#data-table').DataTable().ajax.reload();
                        })
                        .fail(function() {
                            Swal.fire('Error!', 'An error occurred while deleting the record.', 'error');
                        });
                }
            });
        }

        function editItem(id) {
            $.ajax({
                    url: "{{ route('procurement.process-po.editItem', ':id') }}".replace(':id', id),
                    type: 'GET',
                })
                .done(function(data) {
                    $('#content-modal-ce').html(data);

                    $("#modal-ce").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while editing the record.', 'error');
                });
        }
    </script>
@endpush