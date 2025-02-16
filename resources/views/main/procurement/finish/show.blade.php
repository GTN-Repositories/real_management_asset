@extends('layouts.global')

@section('title', 'Penerimaan Barang')
@section('title_page', 'Procurement / Penerimaan Barang')

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
                <div class="col-md-9 text-center">
                    <img src="{{ asset('images/finish_procurement.png') }}" alt="">

                    <div class="text-center mt-3">
                        <p>
                            Anda telah berhasil menyelesaikan tahap pengisian request order dalam proses procurement. <br> Silakan lanjut ke langkah berikutnya."
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection