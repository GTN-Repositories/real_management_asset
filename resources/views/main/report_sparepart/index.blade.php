@extends('layouts.global')

@section('title', 'Laporan Sparepart')
@section('title_page', 'Report / Sparepart Report')

@push('css')
    <style>
        .input-filter {
            max-width: 180px;
            width: 100%;
        }

        .btn-asset {
            width: 100%;
            max-width: 160px;
        }

        .btn-add {
            width: 100%;
            max-width: 130px;
        }

        .btn-req {
            width: fit-content;
            max-width: 210px;
        }

        .btn-del-all {
            width: 100%;
            max-width: 180px;
        }

        .row-rgb {
            gap: 0px;
        }

        @media (max-width: 768px) {
            .input-filter {
                max-width: 100%;
            }

            .btn-asset {
                max-width: 100%;
            }

            .btn-add {
                max-width: 100%;
            }
            .btn-req {
                max-width: 100%;
                width: 100%;
            }
            .btn-del-all {
                max-width: 100%;
            }

            .row-rgb {
                gap: 15px;
            }
        }
    </style>
@endpush
@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
        <div class="d-flex flex-wrap justify-content-end align-items-end mb-3 gap-3">
            <div class="btn-group input-filter">
                <button type="button" class="btn btn-outline-primary dropdown-toggle waves-effect" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    filter tanggal
                </button>
                <ul class="dropdown-menu input-filter" style="">
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
