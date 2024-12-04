@extends('layouts.global')

@section('title', 'Laporan Sparepart')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> Sparepart Report</h4>
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

        {{-- chart manpower --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Manpower Data Over Time</h5>
            </div>
            <div class="card-body">
                <div id="hours-chart"></div> <!-- Chart Div -->
            </div>
        </div>


        <!-- Product List Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Fuel Consumption</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table" id="data-table">
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
            init_hours_chart();

            $('.dropdown-item').on('click', function(e) {
                e.preventDefault();
                $('.dropdown-item').removeClass('active');
                $(this).addClass('active');
                const filterType = $(this).text().trim();
                const filterBtn = $('.btn.btn-outline-primary.dropdown-toggle');
                filterBtn.text(filterType);

                reloadTableWithFilters(null, null, filterType);
                reloadHoursChartWithFilters(null, null, filterType);
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
                reloadHoursChartWithFilters(startDate, endDate);
            });

            $('#date-range-picker').on('cancel.daterangepicker', function() {
                $(this).val('');
                reloadTableWithFilters(); // Reload without date range
                reloadHoursChartWithFilters();
            });
        });

        $(document).on('input', '#searchData', function() {
            init_table($(this).val());
        });

        function reloadTableWithFilters(startDate = '', endDate = '', predefinedFilter = '') {
            $('#data-table').DataTable().destroy();
            init_table(startDate, endDate, predefinedFilter);
        }

        function reloadHoursChartWithFilters(startDate = '', endDate = '', predefinedFilter = '') {
            if (hoursChart) hoursChart.destroy();
            init_hours_chart(startDate, endDate, predefinedFilter);
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

        let hoursChart;

        function init_hours_chart(startDate = '', endDate = '', predefinedFilter = '') {
            $.ajax({
                url: "{{ route('report-fuel.hours-data') }}",
                method: 'GET',
                data: {
                    startDate: startDate,
                    endDate: endDate,
                    predefinedFilter: predefinedFilter
                },
                success: function(response) {
                    var options = {
                        series: [{
                            name: 'Hours',
                            data: response.hours
                        }],
                        chart: {
                            type: 'bar',
                            height: 350,
                            toolbar: {
                                show: true
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false // Set to false for vertical bar chart
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(value) {
                                return value != null ? value.toFixed(1) + ' hrs' : '';
                            }
                        },
                        xaxis: {
                            categories: response.dates,
                            title: {
                                text: 'Dates'
                            },
                            labels: {
                                rotate: -45,
                                rotateAlways: true
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Hours'
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return value != null ? value.toFixed(1) + ' hrs' : '';
                                }
                            }
                        },
                        title: {
                            text: 'Manpower Over Time',
                            align: 'left'
                        },
                        grid: {
                            row: {
                                colors: ['#f3f3f3', 'transparent'],
                                opacity: 0.5
                            }
                        }
                    };

                    hoursChart = new ApexCharts(document.querySelector("#hours-chart"),
                        options);
                    hoursChart.render();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching hours data:', error);
                    document.querySelector("#hours-chart").innerHTML =
                        '<div class="alert alert-danger">Failed to load hours chart data. Please try again later.</div>';
                }
            });
        }
    </script>
@endpush
