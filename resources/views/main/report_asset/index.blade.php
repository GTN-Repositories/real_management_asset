@extends('layouts.global')

@section('title', 'Laporan asset Project')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> Assets Report</h4>
        <div class="d-flex justify-content-end align-items-end mb-3 gap-3">
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary dropdown-toggle waves-effect" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    filter tanggal
                </button>
                <ul class="dropdown-menu" style="">
                    <li><a class="dropdown-item" id="hari ini" href="javascript:void(0);">hari ini</a></li>
                    <li><a class="dropdown-item" id="minggu ini" href="javascript:void(0);">minggu ini</a></li>
                    <li><a class="dropdown-item" id="bulan ini" href="javascript:void(0);">bulan ini</a></li>
                    <li><a class="dropdown-item" id="bulan kemarin" href="javascript:void(0);">bulan kemarin</a></li>
                    <li><a class="dropdown-item" id="tahun ini" href="javascript:void(0);">tahun ini</a></li>
                    <li><a class="dropdown-item" id="tahun kemarin" href="javascript:void(0);">tahun kemarin</a></li>
                </ul>
            </div>
            <div>
                <label for="date-range-picker" class="form-label">filter dengan jangka waktu</label>
                <input type="text" id="date-range-picker" class="form-control" placeholder="Select Date Range">
            </div>
            {{-- <button id="exportPdfBtn" class="btn btn-primary" onclick="exportPDF()">
                <i class="fa-solid fa-file-pdf me-1"></i>Export PDF
            </button> --}}
            <button onclick="exportExcel()" class="btn btn-success">
                <i class="fa-solid fa-file-excel me-1"></i>Export Excel
            </button>

        </div>
        <!-- Chart Container -->
        {{-- <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">asset Project</h5>
            </div>
            <div class="card-body">
                <div id="fuel-consumption-chart"></div> <!-- Chart Div -->
            </div>
        </div> --}}

        <!-- Product List Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Assets</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table" id="data-table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>gambar aset</th>
                            <th>nama aset</th>
                            <th>nomor seri</th>
                            <th>nomor model</th>
                            <th>manajer aset</th>
                            <th>lokasi aset</th>
                            <th>kategori aset</th>
                            <th>biaya pembelian</th>
                            <th>tanggal pembelian</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            init_table();
            
            // Event listeners for filters
            $('.dropdown-item').on('click', function(e) {
                e.preventDefault();
                $('.dropdown-item').removeClass('active'); // Hapus class active dari item lain
                $(this).addClass('active'); // Tambah class active ke item yang dipilih

                const filterType = $(this).text().trim();
                const filterBtn = $('.btn.btn-outline-primary.dropdown-toggle');
                filterBtn.text(filterType);
                reloadTableWithFilters(null, null, filterType);
                reloadChartWithFilters(null, null, filterType);
            });

            $('#date-range-picker').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#date-range-picker').on('apply.daterangepicker', function(ev, picker) {
                const startDate = picker.startDate.format('YYYY-MM-DD');
                const endDate = picker.endDate.format('YYYY-MM-DD');
                $(this).val(startDate + ' - ' + endDate);
                reloadTableWithFilters(startDate, endDate);
                reloadChartWithFilters(startDate, endDate);
            });

            $('#date-range-picker').on('cancel.daterangepicker', function() {
                $(this).val('');
                reloadTableWithFilters(); // Reload without date range
                reloadChartWithFilters(); // Reload chart without date range
            });
        });

        $(document).on('input', '#searchData', function() {
            init_table($(this).val());
        });

        function reloadTableWithFilters(startDate = '', endDate = '', predefinedFilter = '') {
            $('#data-table').DataTable().destroy();
            init_table(startDate, endDate, predefinedFilter);
        }

        function init_table(startDate = '', endDate = '', predefinedFilter = '') {
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('report-asset.data') }}",
                    data: {
                        startDate: startDate,
                        endDate: endDate,
                        predefinedFilter: predefinedFilter
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'serial_number',
                        name: 'serial_number'
                    },
                    {
                        data: 'model_number',
                        name: 'model_number'
                    },
                    {
                        data: 'manager',
                        name: 'manager'
                    },
                    {
                        data: 'assets_location',
                        name: 'assets_location'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'cost',
                        name: 'cost'
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date'
                    },
                ]
            });
        }

        function exportExcel() {
            const startDate = $('#date-range-picker').data('daterangepicker')?.startDate.format('YYYY-MM-DD') || '';
            const endDate = $('#date-range-picker').data('daterangepicker')?.endDate.format('YYYY-MM-DD') || '';
            const predefinedFilter = $('.dropdown-item.active').text().trim() || '';

            const exportUrl = "{{ route('report-asset.export-excel') }}" +
                `?startDate=${startDate}&endDate=${endDate}&predefinedFilter=${predefinedFilter}`;

            window.location.href = exportUrl;
        }
    </script>
@endpush
