@extends('layouts.global')

@section('title', 'Laporan Fuel Consumtion')
@section('title_page', 'Report / Fuel Consumtion')

@push('css')
    <style>
        .input-filter {
            max-width: 180px;
            width: fit-content;
        }

        .btn-asset {
            width: 100%;
            max-width: 200px;
        }

        .card-w {
            width: 49%;
        }

        @media (max-width: 768px) {
            .input-filter {
                max-width: 100%;
                width: 100%;
            }

            .btn-asset {
                max-width: 100%;
            }

            .card-w {
                width: 100%;
            }
        }
    </style>
@endpush
@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
        <div class="d-flex flex-wrap justify-content-end align-items-end gap-3 mb-4">
            <div class="btn-group input-filter">
                <button type="button" class="btn btn-outline-primary dropdown-toggle waves-effect" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    filter tanggal
                </button>
                <ul class="dropdown-menu input-filter">
                    <li><a class="dropdown-item" id="hari ini" href="javascript:void(0);">hari ini</a></li>
                    <li><a class="dropdown-item" id="minggu ini" href="javascript:void(0);">minggu ini</a></li>
                    <li><a class="dropdown-item" id="bulan ini" href="javascript:void(0);">bulan ini</a></li>
                    <li><a class="dropdown-item" id="bulan kemarin" href="javascript:void(0);">bulan kemarin</a></li>
                    <li><a class="dropdown-item" id="tahun ini" href="javascript:void(0);">tahun ini</a></li>
                    <li><a class="dropdown-item" id="tahun kemarin" href="javascript:void(0);">tahun kemarin</a></li>
                </ul>
            </div>
            <div class="input-filter">
                <label for="date-range-picker" class="form-label">filter dengan jangka waktu</label>
                <input type="text" id="date-range-picker" class="form-control" placeholder="Select Date Range">
            </div>
            {{-- @if (auth()->user()->hasPermissionTo('report-fuel-export-pdf'))
                <button id="exportPdfBtn" class="btn btn-primary" onclick="exportPDF()">
                    <i class="fa-solid fa-file-pdf me-1"></i>Export PDF
                </button>
            @endif --}}
            @if (!auth()->user()->hasRole('Read only'))
                @if (auth()->user()->hasPermissionTo('report-fuel-export-excel'))
                    <button onclick="exportExcel()" class="btn btn-success btn-asset">
                        <i class="fa-solid fa-file-excel me-2"></i>Export Excel
                    </button>
                @endif
                @if (auth()->user()->hasPermissionTo('report-fuel-export-excel-month'))
                    {{-- <button onclick="exportExcelMonth()" class="btn btn-success">
                        <i class="fa-solid fa-file-excel me-1"></i>Excel Fuel Monthly
                    </button> --}}

                    <button onclick="exportExcelMonthModal()" class="btn btn-success btn-asset">
                        <i class="fa-solid fa-file-excel me-2"></i>Excel Fuel Monthly
                    </button>
                @endif
            @endif
        </div>
        <!-- Chart Container -->
        {{-- <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Fuel Consumption Over Time</h5>
            </div>
            <div class="card-body">
                <div id="fuel-consumption-chart"></div> <!-- Chart Div -->
            </div>
        </div> --}}

        {{-- chart expense --}}
        <div id="dashboard-container">
            <div id="scorecard-section" class="scorecard-container">
                <!-- Scorecard data will be inserted here dynamically -->
            </div>
            <div class="card my-3">
                <div class="card-body">
                    <h6>Fuel Consumption Over Time</h6>
                    <div id="liters-chart-section" class="chart-container">
                        <!-- Liters chart will be rendered here dynamically -->
                    </div>
                </div>
            </div>
            <div class="card my-3">
                <div class="card-body">
                    <h6>Fuel Price Over Time</h6>
                    <div id="price-chart-section" class="chart-container">
                        <!-- Price chart will be rendered here dynamically -->
                    </div>
                </div>
            </div>
        </div>
        <div class="card my-3 p-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Grouping by project</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table table-striped table-poppins " id="data-table-project">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Management Project</th>
                            <th>Total Liter</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="card my-3 p-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Grouping by asset</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table table-striped table-poppins " id="data-table-asset">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Asset</th>
                            <th>Total Liter</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Fuel Consumption</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table table-striped table-poppins " id="data-table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Management Project</th>
                            <th>Unit</th>
                            <th>Tanggal</th>
                            <th>Total Hari</th>
                            <th>Pemakaian Solar</th>
                            <th>Liter/Trip</th>
                            <th>Rata-rata/Hari</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL FILTER --}}
    <div class="modal fade" id="modal-export-excel-month" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body" id="content-modal-export-excel-month">
                    <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>

                    <div class="text-center mb-4">
                        <h3 class="role-title mb-2">Excel Fuel Monthly</h3>
                        <p class="text-muted">Export data to excel</p>
                    </div>

                    <div class="row">
                        <div class="me-2 mb-2">
                            <label for="month" class="form-label">Bulan</label>
                            <select id="month" class="form-select select2">
                                <option value="">Pilih Bulan</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="me-2 mb-2">
                            <label for="year" class="form-label">Tahun</label>
                            <select id="year" class="form-select select2">
                                <option value="">Pilih Tahun</option>
                                @for ($i = date('Y'); $i >= 1985; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button onclick="exportExcelMonth()" class="btn btn-success">
                                <i class="fa-solid fa-file-excel me-1"></i> Export
                            </button>

                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            init_table();
            init_table_project();
            init_table_asset();
            init_expanse_chart();

            $('.dropdown-item').on('click', function(e) {
                e.preventDefault();
                $('.dropdown-item').removeClass('active');
                $(this).addClass('active');
                const filterType = $(this).text().trim();
                const filterBtn = $('.btn.btn-outline-primary.dropdown-toggle');
                filterBtn.text(filterType);

                reloadTableWithFilters(filterType);
                reloadExpanseChartWithFilters(null, null, filterType);
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
                reloadExpanseChartWithFilters(startDate, endDate);
                init_table_project(startDate, endDate);
            });

            $('#date-range-picker').on('cancel.daterangepicker', function() {
                $(this).val('');
                reloadTableWithFilters();
                reloadExpanseChartWithFilters();
            });
        });

        $(document).on('input', '#searchData', function() {
            init_table($(this).val());
        });

        function reloadTableWithFilters(startDate = '', endDate = '', predefinedFilter = '') {
            $('#data-table').DataTable().destroy();
            init_table(startDate, endDate, predefinedFilter);
        }

        function reloadExpanseChartWithFilters(startDate = '', endDate = '', predefinedFilter = '') {
            if (litersChartSection) litersChartSection.destroy();
            if (priceChartSection) priceChartSection.destroy();
            init_expanse_chart(startDate, endDate, predefinedFilter);
            init_table_project(startDate, endDate, predefinedFilter);
        }

        let litersChartSection;
        let priceChartSection;

        function init_expanse_chart(startDate = '', endDate = '', predefinedFilter = '') {
            $.ajax({
                url: "{{ route('report-fuel.expanse-fuel') }}",
                method: 'GET',
                data: {
                    startDate: startDate,
                    endDate: endDate,
                    predefinedFilter: predefinedFilter
                },
                success: function(response) {
                    // Render the scorecard
                    const scorecard = `
    <div class="row justify-content-center mt-3">
        <div class="col-12 col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-2">
                            <img src="{{ asset('images/truck.png') }}" alt="">
                        </div>
                        <strong class="mb-0 text-primary">Avg Per Day</strong>
                    </div>
                    <h4 class="ms-1 mb-0 text-muted">${response.avgPerDay ? response.avgPerDay.toFixed(2) : '0'} Liters
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-2">
                            <img src="{{ asset('images/fuel.png') }}" alt="">
                        </div>
                        <strong class="mb-0 text-primary">Avg Per Trip</strong>
                    </div>
                    <h4 class="ms-1 mb-0 text-muted">${response.avgPerTrip ? response.avgPerTrip.toFixed(2) : '0'}
                        Liters</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-2">
                            <img src="{{ asset('images/productivity.png') }}" alt="">
                        </div>
                        <strong class="mb-0 text-primary">Avg Per Liter</strong>
                    </div>
                    <h4 class="ms-1 mb-0 text-muted">${response.avgPerLiter ?
                        response.avgPerLiter.toLocaleString('id-ID', { style:
                        'currency', currency: 'IDR' }) : 'Rp0'}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-2">
                            <img src="{{ asset('images/asset_value.png') }}" alt="">
                        </div>
                        <strong class="mb-0 text-primary">Total Fuel Cost</strong>
                    </div>
                    <h4 class="ms-1 mb-0 text-muted">${response.totalFuelCost ?
                        response.totalFuelCost.toLocaleString('id-ID', { style:
                        'currency', currency: 'IDR' }) : 'Rp0'}</h4>
                </div>
            </div>
        </div>
    </div>
                    `;

                    document.querySelector("#scorecard-section").innerHTML = scorecard;

                    // Handle empty data for liters chart
                    if (Array.isArray(response.litersData) && response.litersData.length === 0) {
                        document.querySelector("#liters-chart-section").innerHTML =
                            `<div class="alert alert-info">Data untuk konsumsi bahan bakar kosong.</div>`;
                    } else {
                        // Remove any existing alert if there is data
                        document.querySelector("#liters-chart-section").innerHTML = "";

                        // Prepare chart options for liters
                        var litersChartOptions = {
                            series: [{
                                name: 'Liters',
                                data: response.litersData
                            }],
                            chart: {
                                type: 'line',
                                height: 350,
                                toolbar: {
                                    show: true
                                }
                            },
                            xaxis: {
                                categories: response.dates
                            },
                            title: {
                                text: 'Fuel Consumption Over Time',
                                align: 'left'
                            }
                        };

                        // Render the liters chart
                        litersChartSection = new ApexCharts(document.querySelector("#liters-chart-section"),
                            litersChartOptions);
                        litersChartSection.render();
                    }

                    // Handle empty data for price chart
                    if (Array.isArray(response.priceData) && response.priceData.length === 0) {
                        document.querySelector("#price-chart-section").innerHTML =
                            `<div class="alert alert-info">Data untuk harga bahan bakar kosong.</div>`;
                    } else {
                        // Remove any existing alert if there is data
                        document.querySelector("#price-chart-section").innerHTML = "";

                        // Prepare chart options for price
                        var priceChartOptions = {
                            series: [{
                                name: 'Price',
                                data: response.priceData.map(item => item.total_price)
                            }],
                            chart: {
                                type: 'line',
                                height: 350,
                                toolbar: {
                                    show: true
                                }
                            },
                            xaxis: {
                                categories: response.priceData.map(item => item.date)
                            },
                            title: {
                                text: 'Fuel Price Over Time',
                                align: 'left'
                            }
                        };

                        // Render the price chart
                        priceChartSection = new ApexCharts(document.querySelector("#price-chart-section"),
                            priceChartOptions);
                        priceChartSection.render();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching expanse data:', error);
                    document.querySelector("#liters-chart-section").innerHTML =
                        '<div class="alert alert-danger">Failed to load expanse chart data. Please try again later.</div>';
                    document.querySelector("#price-chart-section").innerHTML =
                        '<div class="alert alert-danger">Failed to load price chart data. Please try again later.</div>';
                }
            });
        }


        function init_table(startDate = '', endDate = '', predefinedFilter = '', keyword = '') {
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('report-fuel.data') }}",
                    data: {
                        'keyword': keyword,
                        'startDate': startDate,
                        'endDate': endDate,
                        'predefinedFilter': predefinedFilter
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'management_project_id',
                        name: 'management_project_id'
                    },
                    {
                        data: 'asset_id',
                        name: 'asset_id'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'day_total',
                        name: 'day_total'
                    },
                    {
                        data: 'liter',
                        name: 'liter'
                    },
                    {
                        data: 'liter_trip',
                        name: 'liter_trip'
                    },
                    {
                        data: 'avarage_day',
                        name: 'avarage_day'
                    },
                ]
            });
        }

        function init_table_project(startDate = '', endDate = '', predefinedFilter = '', keyword = '') {
            $('#data-table-project').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('report-fuel.get-by-project') }}",
                    data: {
                        'keyword': keyword,
                        'startDate': startDate,
                        'endDate': endDate,
                        'predefinedFilter': predefinedFilter
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'total_liter',
                        name: 'total_liter'
                    },
                ]
            });
        }

        function init_table_asset(startDate = '', endDate = '', predefinedFilter = '', keyword = '') {
            $('#data-table-asset').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('report-fuel.get-by-asset') }}",
                    data: {
                        'keyword': keyword,
                        'startDate': startDate,
                        'endDate': endDate,
                        'predefinedFilter': predefinedFilter
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'total_liter',
                        name: 'total_liter'
                    },
                ]
            });
        }

        function exportPDF() {
            const startDate = $('#date-range-picker').data('daterangepicker')?.startDate.format('YYYY-MM-DD') || '';
            const endDate = $('#date-range-picker').data('daterangepicker')?.endDate.format('YYYY-MM-DD') || '';
            const predefinedFilter = $('.dropdown-item.active').text().trim() || '';
            $.ajax({
                url: "{{ route('report-fuel.export-pdf') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    startDate: startDate,
                    endDate: endDate,
                    predefinedFilter: predefinedFilter
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response) {
                    const blob = new Blob([response], {
                        type: 'application/pdf'
                    });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'FuelConsumptionReport.pdf';
                    link.click();
                    Swal.close();
                },
                error: function() {
                    Swal.fire('Error!',
                        'An error occurred while exporting the report. Please try again later.',
                        'error');
                }
            });
        }

        function exportExcel() {
            const startDate = $('#date-range-picker').data('daterangepicker')?.startDate?.format('YYYY-MM-DD');
            const endDate = $('#date-range-picker').data('daterangepicker')?.endDate?.format('YYYY-MM-DD');
            const predefinedFilter = $('.dropdown-item.active').text().trim() || '';

            if (startDate && endDate) {
                $.ajax({
                    url: "{{ route('report-fuel.export-excel') }}",
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                        startDate: startDate,
                        endDate: endDate,
                        predefinedFilter: predefinedFilter
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        const blob = new Blob([response], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = 'FuelConsumptionReport.xlsx';
                        link.click();
                    },
                    error: function() {
                        Swal.fire('Error!',
                            'An error occurred while exporting the report. Please try again later.',
                            'error');
                    }
                });
            } else {
                $.ajax({
                    url: "{{ route('report-fuel.export-excel') }}",
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                        startDate: moment().startOf('month').format('YYYY-MM-DD'),
                        endDate: moment().endOf('month').format('YYYY-MM-DD'),
                        predefinedFilter: predefinedFilter
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        const blob = new Blob([response], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = 'FuelConsumptionReport.xlsx';
                        link.click();
                    },
                    error: function() {
                        Swal.fire('Error!',
                            'An error occurred while exporting the report. Please try again later.',
                            'error');
                    }
                });
            }
        }

        function exportExcelMonthModal() {
            $("#modal-export-excel-month").modal("show");
        }

        function exportExcelMonth() {
            const month = $('#month').val();
            const year = $('#year').val();

            if (!month || !year) {
                Swal.fire('Error!', 'Please select month and year.', 'error');
                return;
            }

            $.ajax({
                url: "{{ route('report-fuel.export-excel-month') }}",
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}',
                    month: month,
                    year: year,
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response) {
                    const blob = new Blob([response], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'FuelConsumptionReport.xlsx';
                    link.click();
                },
                error: function() {
                    Swal.fire('Error!',
                        'An error occurred while exporting the report. Please try again later.',
                        'error');
                }
            });
        }
    </script>
@endpush
