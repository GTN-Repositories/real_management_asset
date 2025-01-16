@extends('layouts.global')

@section('title', 'Laporan Sparepart')
@section('title_page', 'Report / Sparepart Report')

@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
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
        </div>

        <div class="row g-3 text-center mb-4">
            <div class="col-md-4">
                <div class="card" style="height: 450px;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0 text-primary fw-bold">Vihacle</h5>
                    </div>
                    <div class="card-body d-flex justify-content-center">
                        <div id="asset-status-chart" class="chart-container"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card" style="height: 450px;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0 text-primary fw-bold">Overdue and Due Soon</h5>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="height: 100%">
                        <div class="d-flex gap-4">
                            <div class="d-flex flex-column align-items-center">
                                <h1 class="text-primary fw-bold" style="font-size: 30px;" id="overdue">
                                    Loading...</h1>
                                <h3 class="text-muted">Overdue</h3>
                            </div>
                            <div class="d-flex flex-column align-items-center">
                                <h1 class="text-muted fw-bold" style="font-size: 30px;" id="underMaintenanceSecondDay">
                                    Loading...
                                </h1>
                                <h3 class="text-muted">Due Soon</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card" style="height: 450px;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0 text-primary fw-bold">Percentage</h5>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="height: 100%">
                        <div class="d-flex gap-4">
                            <div class="d-flex flex-column align-items-center">
                                <h1 class="text-primary fw-bold" style="font-size: 30px;">
                                    <span id="percentageItemsYear">Loading...</span>%
                                </h1>
                                <h3 class="text-muted">This Year</h3>
                            </div>
                            <div class="d-flex flex-column align-items-center">
                                <h1 class="text-primary fw-bold" style="font-size: 30px;">
                                    <span id="percentageItemsWeek">Loading...</span>%
                                </h1>
                                <h3 class="text-muted">This Week</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6 col-lg-3" style="">
                <div class="card d-flex align-items-center justify-content-center" style="height: 100px; width: 50%; z-index: 20; border-radius: 20px; background-color: #D59A01;">
                    <h4 class="text-white card-title m-0 fw-bold">Scheduled</h4>
                </div>
                <div class="card d-flex align-items-end justify-content-center" style="height: 100px; margin-top: -100px; z-index: 10; border-radius: 20px; background-color: rgba(213, 153, 1, 0.35); text-align: end;">
                    <h5 class="text-black card-title p-1 p-lg-4 m-0 fw-bold" id="scheduled">Loading...</h5>
                </div>
            </div>            
            <div class="col-12 col-md-6 col-lg-3" style="">
                <div class="card d-flex align-items-center justify-content-center" style="height: 100px; width: 50%; z-index: 20; border-radius: 20px; background-color: #7F2DE8;">
                    <h4 class="text-white card-title m-0 fw-bold">In Progress</h4>
                </div>
                <div class="card d-flex align-items-end justify-content-center" style="height: 100px; margin-top: -100px; z-index: 10; border-radius: 20px; background-color: rgba(127, 45, 232, 0.35); text-align: end;">
                    <h5 class="text-white card-title p-1 p-lg-4 m-0 fw-bold" id="inProgress">Loading...</h5>
                </div>
            </div>            
            <div class="col-12 col-md-6 col-lg-3" style="">
                <div class="card d-flex align-items-center justify-content-center" style="height: 100px; width: 50%; z-index: 20; border-radius: 20px; background-color: #01A5DB;">
                    <h4 class="text-white card-title m-0 fw-bold">On Hold</h4>
                </div>
                <div class="card d-flex align-items-end justify-content-center" style="height: 100px; margin-top: -100px; z-index: 10; border-radius: 20px; background-color: rgba(1, 165, 219, 0.35); text-align: end;">
                    <h5 class="text-black card-title p-1 p-lg-4 m-0 fw-bold" id="onHold">Loading...</h5>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3" style="">
                <div class="card d-flex align-items-center justify-content-center" style="height: 100px; width: 50%; z-index: 20; border-radius: 20px; background-color: #3BBF56;">
                    <h4 class="text-white card-title m-0 fw-bold">Finished</h4>
                </div>
                <div class="card d-flex align-items-end justify-content-center" style="height: 100px; margin-top: -100px; z-index: 10; border-radius: 20px; background-color: rgba(59, 191, 86, 0.35); text-align: end;">
                    <h5 class="text-black card-title p-1 p-lg-4 m-0 fw-bold" id="finish">Loading...</h5>
                </div>
            </div>
        </div>

        <!-- Product List Table -->
        <div class="card my-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Project Item</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table table-striped table-poppins " id="data-table-project">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Nama Project</th>
                            <th>Item</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="card my-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Asset Item</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table table-striped table-poppins " id="data-table-asset">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Nama Asset</th>
                            <th>Item</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        {{-- chart stock category --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Item Stock by Category</h5>
            </div>
            <div class="card-body">
                <div id="stock-category-chart"></div>
            </div>
        </div>


        <!-- Product List Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Sparepart</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table table-striped table-poppins " id="data-table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Stock</th>
                            <th>Harga</th>
                            <th>Ukuran</th>
                            <th>Brand</th>
                            <th>Satuan</th>
                            <th>Kategori</th>
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
            init_table_asset();
            init_table_project();
            init_stock_category_chart();
            initAssetStatusChart();

            $('.dropdown-item').on('click', function(e) {
                e.preventDefault();
                $('.dropdown-item').removeClass('active');
                $(this).addClass('active');
                const filterType = $(this).text().trim();
                const filterBtn = $('.btn.btn-outline-primary.dropdown-toggle');
                filterBtn.text(filterType);

                reloadTableWithFilters(null, null, filterType);
                reloadSparepartChart(null, null, filterType);
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
                reloadSparepartChart(startDate, endDate);
            });

            $('#date-range-picker').on('cancel.daterangepicker', function() {
                $(this).val('');
                reloadTableWithFilters(); // Reload without date range
                reloadSparepartChart();
            });

            $.ajax({
                url: "{{ route('report-sparepart.maintenance-status') }}",
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#scheduled').text(data.scheduled);
                    $('#inProgress').text(data.inProgress);
                    $('#onHold').text(data.onHold);
                    $('#finish').text(data.finish);
                    $('#overdue').text(data.overdue);
                    $('#underMaintenanceSecondDay').text(data.underMaintenanceSecondDay);
                    $('#percentageItemsYear').text(data.percentageItemsYear);
                    $('#percentageItemsWeek').text(data.percentageItemsWeek);
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });

        $(document).on('input', '#searchData', function() {
            init_table($(this).val());
        });

        function reloadTableWithFilters(startDate = '', endDate = '', predefinedFilter = '') {
            $('#data-table').DataTable().destroy();
            init_table(startDate, endDate, predefinedFilter);
        }

        function reloadSparepartChart(startDate = '', endDate = '', predefinedFilter = '') {
            if (stockCategoryChart) stockCategoryChart.destroy();
            init_stock_category_chart(startDate, endDate, predefinedFilter);
        }


        function init_table(startDate = '', endDate = '', predefinedFilter = '', keyword = '') {
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('report-sparepart.data') }}",
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
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'stock',
                        name: 'stock'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'size',
                        name: 'size'
                    },
                    {
                        data: 'brand',
                        name: 'brand'
                    },
                    {
                        data: 'oum_id',
                        name: 'oum_id'
                    },
                    {
                        data: 'category_id',
                        name: 'category_id'
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
                    url: "{{ route('report-sparepart.project-item') }}",
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
                        data: 'item_id',
                        name: 'item_id'
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
                    url: "{{ route('report-sparepart.project-item') }}",
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
                        data: 'asset_id',
                        name: 'asset_id'
                    },
                    {
                        data: 'item_id',
                        name: 'item_id'
                    },
                ]
            });
        }

        function initAssetStatusChart() {
            $.ajax({
                url: "{{ route('report-sparepart.asset-status') }}",
                method: 'GET',
                success: function(response) {
                    var options = {
                        series: response.series,
                        chart: {
                            type: 'donut',
                            height: 350,
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    labels: {
                                        show: true,
                                        total: {
                                            show: true,
                                            label: 'Total',
                                            formatter: function(w) {
                                                return w.globals.seriesTotals.reduce((a, b) => a + b,
                                                    0);
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            text: 'Asset Status',
                            align: 'center'
                        },
                        colors: ['#FABE29', '#134B70'],
                        labels: ['Asset Maintenance', 'Asset Other', ]
                    };

                    var assetStatusChart = new ApexCharts(
                        document.querySelector("#asset-status-chart"),
                        options
                    );
                    assetStatusChart.render();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching asset status data:', error);
                    document.querySelector("#asset-status-chart").innerHTML =
                        '<div class="alert alert-danger">Failed to load asset status chart data. Please try again later.</div>';
                }
            });

        }

        let stockCategoryChart;

        function init_stock_category_chart(predefinedFilter = '', startDate = '', endDate = '') {
            $.ajax({
                url: "{{ route('report-sparepart.data-inspection') }}",
                method: 'GET',
                data: {
                    'start_date': startDate,
                    'end_date': endDate,
                    'predefinedFilter': predefinedFilter
                },
                success: function(response) {
                    var options = {
                        series: response.series, // Data for categories
                        chart: {
                            type: 'bar',
                            height: 350,
                            toolbar: {
                                show: true
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                dataLabels: {
                                    position: 'top',
                                },
                            },
                        },
                        dataLabels: {
                            enabled: true,
                        },
                        xaxis: {
                            categories: response.months, // Months data
                            title: {
                                text: 'Months'
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Stock Count'
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return value != null ? value + ' units' : '';
                                }
                            }
                        },
                        title: {
                            text: 'Monthly Stock by Category',
                            align: 'center'
                        },
                        legend: {
                            position: 'top'
                        },
                    };

                    var stockCategoryChart = new ApexCharts(
                        document.querySelector("#stock-category-chart"),
                        options
                    );
                    stockCategoryChart.render();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching stock category data:', error);
                    document.querySelector("#stock-category-chart").innerHTML =
                        '<div class="alert alert-danger">Failed to load stock category chart data. Please try again later.</div>';
                }
            });
        }
    </script>
@endpush
