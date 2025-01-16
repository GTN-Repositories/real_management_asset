@extends('layouts.global')

@section('title', 'Asset')
@section('title_page', 'Master Data / Asset / Detail')

@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
        <div class="nav-align-top nav-tabs-shadow mb-4">
            <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-justified-general" aria-controls="navs-justified-general" aria-selected="true">
                        General
                    </button>
                </li>
                {{-- <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-justified-project" aria-controls="navs-justified-project"
                        aria-selected="false">Project
                    </button>
                </li> --}}
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-justified-payment" aria-controls="navs-justified-payment"
                        aria-selected="false">Reminder
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-justified-history" aria-controls="navs-justified-history"
                        aria-selected="false">
                        History
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-justified-files" aria-controls="navs-justified-files" aria-selected="false">
                        Files
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-justified-appresiations" aria-controls="navs-justified-appresiations"
                        aria-selected="false">
                        Appresiations
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-justified-depresiation" aria-controls="navs-justified-depresiation"
                        aria-selected="false">
                        Depresiation
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-justified-reminder" aria-controls="navs-justified-reminder"
                        aria-selected="false">
                        Log History
                    </button>
                </li>
                {{-- <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-justified-notes" aria-controls="navs-justified-notes" aria-selected="false">
                        Note
                    </button>
                </li> --}}
                {{-- <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-justified-audits" aria-controls="navs-justified-audits" aria-selected="false">
                        Audits
                    </button>
                </li> --}}
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="navs-justified-general" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="text-primary">Asset Information</h4>
                        <a href="{{ asset('storage/qr_codes/' . $encryptedId . '.png') }}" class="btn btn-primary" download="qr_code_{{ $encryptedId }}.png"><i class="fas fa-download me-2"></i> Download QR Code</a>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <img src="{{ $asset->image ? asset('storage/' . $asset->image) : 'https://ca.shop.runningroom.com/media/catalog/product/placeholder/default/placeholder-image-square.jpg' }}"
                                alt="{{ $asset->name ?? '' }}" width="200" height="200" class="object-fit-cover">
                        </div>
                        <div class="col-md-6 d-flex justify-content-end">
                            <img src="{{ asset('storage/qr_codes/' . $encryptedId . '.png') }}" alt="QR Code"
                                width="200">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-primary">Asset Detail</h5>
                                    <hr>
                                    <table class="table table-striped">
                                        <tbody class="">
                                            <tr>
                                                <td>Asset Number</td>
                                                <td>{{ $asset->asset_number ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Asset Name</td>
                                                <td>{{ $asset->name ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Status</td>
                                                <td>{{ $asset->status ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Model Number</td>
                                                <td>{{ $asset->model_number ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Serial Number</td>
                                                <td>{{ $asset->serial_number ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Asset Category</td>
                                                <td>{{ $asset->category ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Purchase Cost</td>
                                                <td>{{ number_format($asset->cost, 0, ',', '.') ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Purchase Date</td>
                                                <td>{{ $asset->purchase_date ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Asset Location</td>
                                                <td>{{ $asset->assets_location ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Asset Manager</td>
                                                <td>{{ $asset->manager ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Warranty Period (Months)</td>
                                                <td>{{ $asset->warranty_period ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Asset Description</td>
                                                <td>{{ $asset->description ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Unit</td>
                                                <td>{{ $asset->unit ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>License Plate</td>
                                                <td>{{ $asset->license_plate ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Classification</td>
                                                <td>{{ $asset->classification ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Machine Number</td>
                                                <td>{{ $asset->machine_number ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Color</td>
                                                <td>{{ $asset->color ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Nik</td>
                                                <td>{{ $asset->nik ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Created at</td>
                                                <td>{{ $asset->created_at ?? '' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="text-primary">Depreciation Details</h5>
                                    <hr>
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <td>Depreciation (Months)</td>
                                                <td>{{ $asset->depreciation ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Depreciation Percentage (%)</td>
                                                <td>{{ $asset->depreciation_percentage ?? '' }}%</td>
                                            </tr>
                                            <tr>
                                                <td>Depreciation Method</td>
                                                <td>{{ $asset->depreciation_method ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Residual Value</td>
                                                <td>{{ $asset->residual_value ?? '' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="text-primary">Appreciation Details</h5>
                                    <hr>
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <td>Appreciation Rate (%)</td>
                                                <td>{{ $asset->appreciation_rate ?? '' }}%</td>
                                            </tr>
                                            <tr>
                                                <td>Appreciation Period (Months)</td>
                                                <td>{{ $asset->appreciation_period ?? '' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="text-primary">Supplier Details</h5>
                                    <hr>
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <td>Supplier Name</td>
                                                <td>{{ $asset->supplier_name ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Supplier Phone Number</td>
                                                <td>{{ $asset->supplier_phone_number ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Supplier Address</td>
                                                <td>{{ $asset->supplier_address ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Supplier PIC Name</td>
                                                <td>{{ $asset->supplier_pic_name ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Supplier PIC Phone Number</td>
                                                <td>{{ $asset->supplier_pic_phone ?? '' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-justified-project" role="tabpanel">
                    @forelse ($projects as $project)
                        <div class="row">
                            <div class="col-12">
                                @if ($loop->last)
                                    <h4>{{ $project->name }} (now)</h4>
                                @else
                                    <h4>{{ $project->name }}</h4>
                                @endif
                                <p>{{ $project->start_date . ' - ' . $project->end_date }}</p>
                            </div>
                        </div>
                    @empty
                        data ini belum pernah memiliki project
                    @endforelse
                </div>
                <div class="tab-pane fade" id="navs-justified-payment" role="tabpanel">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-md" onclick="createDataReminder()"> <i class="fas fa-plus me-1"></i> Tambah </button>
                    </div>
                    <table class="datatables table table-striped table-poppins " id="data-table-reminder">
                        <thead class="border-top">
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>type</th>
                                <th>title</th>
                                <th>body</th>
                                <th>sendto</th>
                                <th>user</th>
                                <th>aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="tab-pane fade" id="navs-justified-history" role="tabpanel">
                    <table class="datatables table table-striped table-poppins " id="data-table">
                        <thead class="border-top">
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>date</th>
                                <th>perubahan dari</th>
                                <th>perubahan menjadi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="tab-pane fade" id="navs-justified-files" role="tabpanel">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>File</th>
                                <th>Waktu Bayar Pajak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>gambar asset</td>
                                <td><img src="{{ asset('storage/' . $asset->image) }}" alt="gambar asset"
                                        width="50">
                                </td>
                                <td>never</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="createFile('asset')">
                                        <i class="fas fa-pencil me-2"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>gambar stnk</td>
                                <td><img src="{{ asset('storage/' . $asset->stnk) }}" alt="gambar stnk" width="50">
                                </td>
                                <td>{{ $asset->stnk_date }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="createFile('stnk')">
                                        <i class="fas fa-pencil me-2"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>gambar asuransi</td>
                                <td><img src="{{ asset('storage/' . $asset->asuransi) }}" alt="gambar asuransi"
                                        width="50"></td>
                                <td>{{ $asset->asuransi_date }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm"
                                        onclick="createFile('asuransi')">
                                        <i class="fas fa-pencil me-2"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>gambar pajak</td>
                                <td><img src="{{ asset('storage/' . $asset->file_tax) }}" alt="gambar asuransi"
                                        width="50"></td>
                                <td>{{ $asset->date_tax }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="createFile('tax')">
                                        <i class="fas fa-pencil me-2"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <form method="POST" class="row g-3 my-4" id="formAttachment"
                        action="{{ route('asset-attachment.store', $asset->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row col-12">
                            <div class="card card-body shadow-none dropzone" id="customDropzone"
                                style="background-color: #F8F8F8; border-radius: 4px; border: 2px dashed #000;">
                                <div class="text-center">
                                    <h3 class="fw-bold mb-1">Drop your files here!</h3>
                                    <span class="note needsclick">or click to upload</span>
                                </div>
                            </div>
                        </div>
                        <input type="file" class="form-control" id="attachment" name="attachment" style="display: none;"
                    multiple>

                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>

                    <table class="datatables table table-striped table-poppins " id="data-table-attachment">
                        <thead class="border-top">
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>gambar</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="tab-pane fade" id="navs-justified-appresiations" role="tabpanel">
                    <div id="appreciation-chart" style="height: 400px;"></div>
                    <div id="chart-error" class="alert alert-danger d-none"></div>
                    <div class="card">
                        <table class="table">
                            <tr class="fw-bold" style="background-color: #f1f1f1">
                                <th>Year</th>
                                <th>Monthly Appresiations</th>
                                <th>Total Appresiations</th>
                                <th>Book Value</th>
                            </tr>
                            <tr>
                                <td class="text-muted">2024/03/20</td>
                                <td class="text-success">$29.71</td>
                                <td class="text-success">$29.71</td>
                                <td class="text-black">$5,470.29</td>
                            </tr>
                            <tr>
                                <td class="text-muted">2024/03/20</td>
                                <td class="text-success">$29.71</td>
                                <td class="text-success">$29.71</td>
                                <td class="text-black">$5,470.29</td>
                            </tr>
                            <tr>
                                <td class="text-muted">2024/03/20</td>
                                <td class="text-success">$29.71</td>
                                <td class="text-success">$29.71</td>
                                <td class="text-black">$5,470.29</td>
                            </tr>
                            <tr>
                                <td class="text-muted">2024/03/20</td>
                                <td class="text-success">$29.71</td>
                                <td class="text-success">$29.71</td>
                                <td class="text-black">$5,470.29</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-justified-depresiation" role="tabpanel">
                    <div id="depreciation-chart"></div>
                    <div class="card">
                        <table class="table">
                            <tr class="fw-bold" style="background-color: #f1f1f1">
                                <th>Year</th>
                                <th>Depreciation Expense</th>
                                <th>Accumulated Depreciation</th>
                                <th>Book Value</th>
                            </tr>
                            <tr>
                                <td class="text-muted">2024/03/20</td>
                                <td class="text-danger">$29.71</td>
                                <td class="text-danger">$29.71</td>
                                <td class="text-black">$5,470.29</td>
                            </tr>
                            <tr>
                                <td class="text-muted">2024/03/20</td>
                                <td class="text-danger">$29.71</td>
                                <td class="text-danger">$29.71</td>
                                <td class="text-black">$5,470.29</td>
                            </tr>
                            <tr>
                                <td class="text-muted">2024/03/20</td>
                                <td class="text-danger">$29.71</td>
                                <td class="text-danger">$29.71</td>
                                <td class="text-black">$5,470.29</td>
                            </tr>
                            <tr>
                                <td class="text-muted">2024/03/20</td>
                                <td class="text-danger">$29.71</td>
                                <td class="text-danger">$29.71</td>
                                <td class="text-black">$5,470.29</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-justified-reminder" role="tabpanel">
                    <h5>Log Asset</h5>
                    <table class="datatables table table-striped table-poppins " id="data-table-log">
                        <thead class="border-top">
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>halaman</th>
                                <th>ip address</th>
                                <th>user agent</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                {{-- <div class="tab-pane fade" id="navs-justified-notes" role="tabpanel">
                    <form id="formNotes" action="{{ route('asset.note', $asset->id) }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="note">Note</label>
                            <textarea class="form-control" name="note" id="note" rows="3"></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary mt-3">Submit</button>
                        </div>
                    </form>
                    <table class="datatables table table-striped table-poppins " id="data-table-note">
                        <thead class="border-top">
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>note</th>
                                <th>dibuat pada</th>
                            </tr>
                        </thead>
                    </table>
                </div> --}}
                <div class="tab-pane fade" id="navs-justified-audits" role="tabpanel">
                    audits
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end align-items-center mb-3">
            <a href="{{ route('asset.index') ?? '' }}" class="btn btn-primary">Kembali</a>
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
        var asset_id = "{{ $asset->id }}";

        $(document).ready(function() {
            init_table();
            init_table_log();
            init_table_note();
            data_table_reminder();
            init_table_attachment();
            loadAppreciationChart();
            loadDepreciationChart();
        })

        document.getElementById('download-btn').addEventListener('mouseover', function() {
            const encryptedId = this.getAttribute('data-encrypted-id');
            this.href = "{{ url('asset/download') }}/" + encryptedId;
        });

        $(document).on('input', '#searchData', function() {
            init_table($(this).val());
        })

        function init_table(keyword = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            if ($.fn.DataTable.isDataTable('#data-table')) {
                $('#data-table').DataTable().clear().destroy();
            }

            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                columnDefs: [{
                    target: 0,
                    visible: true,
                    searchable: false
                }],
                ajax: {
                    type: "GET",
                    url: "{{ route('status-asset.data') }}",
                    data: {
                        'keyword': keyword,
                        'asset_id': asset_id,
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'status_before',
                        name: 'status_before'
                    },
                    {
                        data: 'status_after',
                        name: 'status_after'
                    },
                ]
            });
        }

        function data_table_reminder(keyword = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            if ($.fn.DataTable.isDataTable('#data-table-reminder')) {
                $('#data-table-reminder').DataTable().clear().destroy();
            }

            var table = $('#data-table-reminder').DataTable({
                processing: true,
                serverSide: true,
                columnDefs: [{
                    target: 0,
                    visible: true,
                    searchable: false
                }],
                ajax: {
                    type: "GET",
                    url: "{{ route('asset-reminder.data') }}",
                    data: {
                        'keyword': keyword,
                        'asset_id': asset_id,
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'body',
                        name: 'body'
                    },
                    {
                        data: 'send_to',
                        name: 'send_to'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        }

        function init_table_log(keyword = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            if ($.fn.DataTable.isDataTable('#data-table-log')) {
                $('#data-table-log').DataTable().clear().destroy();
            }

            var table = $('#data-table-log').DataTable({
                processing: true,
                serverSide: true,
                columnDefs: [{
                    target: 0,
                    visible: true,
                    searchable: false
                }],
                ajax: {
                    type: "GET",
                    url: "{{ route('log-activity.data') }}",
                    data: {
                        'keyword': keyword,
                        'asset_id': asset_id,
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'page',
                        name: 'page'
                    },
                    {
                        data: 'ip_address',
                        name: 'ip_address'
                    },
                    {
                        data: 'user_agent',
                        name: 'user_agent'
                    },
                ]
            });
        }

        function init_table_note(keyword = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            if ($.fn.DataTable.isDataTable('#data-table-note')) {
                $('#data-table-note').DataTable().clear().destroy();
            }

            var table = $('#data-table-note').DataTable({
                processing: true,
                serverSide: true,
                columnDefs: [{
                    target: 0,
                    visible: true,
                    searchable: false
                }],
                ajax: {
                    type: "GET",
                    url: "{{ route('asset-note.data') }}",
                    data: {
                        'keyword': keyword,
                        'asset_id': asset_id,
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'note',
                        name: 'note'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ]
            });
        }

        function init_table_attachment(keyword = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            if ($.fn.DataTable.isDataTable('#data-table-attachment')) {
                $('#data-table-attachment').DataTable().clear().destroy();
            }

            var table = $('#data-table-attachment').DataTable({
                processing: true,
                serverSide: true,
                columnDefs: [{
                    target: 0,
                    visible: true,
                    searchable: false
                }],
                ajax: {
                    type: "GET",
                    url: "{{ route('asset-attachment.data') }}",
                    data: {
                        'keyword': keyword,
                        'asset_id': asset_id,
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'attachment',
                        name: 'attachment'
                    },
                ]
            });
        }

        function createFile($kategori) {
            $.ajax({
                    url: "{{ route('asset.updateFiles') }}",
                    type: 'GET',
                    data: {
                        id: asset_id,
                        kategori: $kategori
                    }, // Send asset_id to fetch specific data
                })
                .done(function(data) {
                    $('#content-modal-ce').html(data);
                    $("#modal-ce").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while creating the record.', 'error');
                });
        }

        function loadAppreciationChart(assetId) {
            $.ajax({
                url: "{{ route('asset.appreciation-data') }}",
                method: 'GET',
                data: {
                    asset_id: assetId
                }, // Pass asset_id as a query parameter
                success: function(response) {
                    var seriesData = response.map(function(asset) {
                        return {
                            name: asset.label,
                            data: asset.data.map(point => ({
                                x: point.date,
                                y: point.value
                            }))
                        };
                    });

                    var options = {
                        series: seriesData,
                        chart: {
                            height: 350,
                            type: 'line',
                            zoom: {
                                enabled: true
                            },
                            toolbar: {
                                show: true
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        title: {
                            text: 'Asset Appreciation Over Time',
                            align: 'left'
                        },
                        xaxis: {
                            type: 'datetime',
                            title: {
                                text: 'Date'
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Value (Currency)'
                            },
                            labels: {
                                formatter: function(value) {
                                    return value.toFixed(2);
                                }
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return value.toFixed(2);
                                }
                            }
                        }
                    };

                    var appreciationChart = new ApexCharts(document.querySelector("#appreciation-chart"),
                        options);
                    appreciationChart.render();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching chart data:', error);
                    document.querySelector("#appreciation-chart").innerHTML =
                        '<div class="alert alert-danger">Failed to load chart data. Please try again later.</div>';
                }
            });
        }

        function loadDepreciationChart() {
            $.ajax({
                url: "{{ route('asset.depreciation-data') }}",
                method: 'GET',
                success: function(response) {
                    var seriesData = response.map(function(asset) {
                        return {
                            name: asset.label,
                            data: asset.data.map(point => ({
                                x: point.date,
                                y: point.value
                            }))
                        };
                    });

                    var options = {
                        series: seriesData,
                        chart: {
                            height: 350,
                            type: 'line',
                            zoom: {
                                enabled: true
                            },
                            toolbar: {
                                show: true
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        title: {
                            text: 'Asset Depreciation Over Time',
                            align: 'left'
                        },
                        xaxis: {
                            type: 'datetime',
                            title: {
                                text: 'Date'
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Value (Currency)'
                            },
                            labels: {
                                formatter: function(value) {
                                    return value.toFixed(2);
                                }
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return value.toFixed(2);
                                }
                            }
                        }
                    };

                    var depreciationChart = new ApexCharts(document.querySelector("#depreciation-chart"),
                        options);
                    depreciationChart.render();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching chart data:', error);
                    document.querySelector("#depreciation-chart").innerHTML =
                        '<div class="alert alert-danger">Failed to load chart data. Please try again later.</div>';
                }
            });
        }

        function deleteData(id) {
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
                            url: "{{ route('asset-reminder.destroy', ':id') }}".replace(':id', id),
                            type: 'POST',
                            data: postForm,
                            dataType: 'json',
                        })
                        .done(function(data) {
                            Swal.fire('Deleted!', data['message'], 'success');
                            $('#data-table-reminder').DataTable().ajax.reload();
                        })
                        .fail(function() {
                            Swal.fire('Error!', 'An error occurred while deleting the record.', 'error');
                        });
                }
            });
        }

        function createDataReminder() {
            $.ajax({
                    url: "{{ route('asset-reminder.create') }}",
                    type: 'GET',
                    data: {
                        asset_id: asset_id
                    }
                })
                .done(function(data) {
                    $('#content-modal-ce').html(data);

                    $("#modal-ce").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while creating the record.', 'error');
                });
        }

        function editData(id) {

            $.ajax({
                    url: "{{ route('asset-reminder.edit', ':id') }}".replace(':id', id),
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
    <script>
        document.getElementById('formNotes').addEventListener('submit', function(event) {
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
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message
                        }).then(() => {
                            $('#modal-ce').modal('hide');

                            $('#data-table-note').DataTable().ajax.reload();
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

    <script>
        document.getElementById('formAttachment').addEventListener('submit', function(event) {
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

                            $('#data-table-attachment').DataTable().ajax.reload();
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
    <script>
        $(document).ready(function() {
        const dropzone = document.getElementById("customDropzone");
        const fileInput = document.getElementById("attachment");

        dropzone.addEventListener("dragover", function(e) {
            e.preventDefault();
            dropzone.style.backgroundColor = "#D0D0D0";
        });

        // Event untuk menangani drag-leave
        dropzone.addEventListener("dragleave", function() {
            dropzone.style.backgroundColor = "#F8F8F8";
        });

        // Event untuk menangani drop file
        dropzone.addEventListener("drop", function(e) {
            e.preventDefault();
            dropzone.style.backgroundColor = "#F8F8F8";

            const files = e.dataTransfer.files;
            handleFiles(files);
        });

        // Event untuk klik
        dropzone.addEventListener("click", function() {
            fileInput.click();
        });

        // Event untuk perubahan pada input file
        fileInput.addEventListener("change", function() {
            handleFiles(fileInput.files);
        });

        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];

                console.log(file);

                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                };

                reader.readAsDataURL(file);
            }
        }
    });
    </script>
@endpush
