@extends('layouts.global')

@section('title', 'Laporan Fuel Consumtion')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> Fuel Consumption</h4>
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
            <button id="exportPdfBtn" class="btn btn-primary" onclick="exportPDF()">
                <i class="fa-solid fa-file-pdf me-1"></i>Export PDF
            </button>
            <button onclick="exportExcel()" class="btn btn-success">
                <i class="fa-solid fa-file-excel me-1"></i>Export Excel
            </button>

        </div>
        <!-- Chart Container -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Fuel Consumption Over Time</h5>
            </div>
            <div class="card-body">
                <div id="fuel-consumption-chart"></div> <!-- Chart Div -->
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
                            <th>Management Project</th>
                            <th>Unit</th>
                            <th>Tanggal Awal</th>
                            <th>Tanggal Akhir</th>
                            <th>Total Hari</th>
                            <th>Pemakaian Solar</th>
                            <th>Total Loadsheet</th>
                            <th>Liter/Trip</th>
                            <th>Rata-rata/Hari</th>
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
            init_chart(); // Initialize chart on page load

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

        function reloadChartWithFilters(startDate = '', endDate = '', predefinedFilter = '') {
            if (fuelConsumptionChart) fuelConsumptionChart.destroy();
            init_chart(startDate, endDate, predefinedFilter);
        }


        function init_table(startDate = '', endDate = '', predefinedFilter = '') {
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('report-fuel.data') }}",
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
                        data: 'management_project_id',
                        name: 'management_project_id'
                    },
                    {
                        data: 'asset_id',
                        name: 'asset_id'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
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
                        data: 'loadsheet',
                        name: 'loadsheet'
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

        let fuelConsumptionChart;

        function init_chart(startDate = '', endDate = '', predefinedFilter = '') {
            $.ajax({
                url: "{{ route('report-fuel.chart') }}",
                method: 'GET',
                data: {
                    startDate: startDate,
                    endDate: endDate,
                    predefinedFilter: predefinedFilter
                },
                success: function(response) {
                    var options = {
                        series: [{
                            name: 'Fuel Consumption (liters)',
                            data: response.liters
                        }],
                        chart: {
                            height: 350,
                            type: 'line',
                            zoom: {
                                enabled: true
                            },
                            toolbar: {
                                show: true
                            },
                            background: '#ffffff'
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        title: {
                            text: 'Fuel Consumption Over Time',
                            align: 'left'
                        },
                        grid: {
                            row: {
                                colors: ['#f3f3f3', 'transparent'],
                                opacity: 0.5
                            }
                        },
                        xaxis: {
                            categories: response.dates,
                            title: {
                                text: 'Date'
                            },
                            labels: {
                                rotate: -45,
                                rotateAlways: true
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Liters'
                            },
                            labels: {
                                formatter: function(value) {
                                    return value.toFixed(1);
                                }
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return value.toFixed(1) + " liters";
                                }
                            }
                        },
                        markers: {
                            size: 5,
                            hover: {
                                size: 7
                            }
                        }
                    };

                    fuelConsumptionChart = new ApexCharts(document.querySelector("#fuel-consumption-chart"),
                        options);
                    fuelConsumptionChart.render();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching chart data:', error);
                    document.querySelector("#fuel-consumption-chart").innerHTML =
                        '<div class="alert alert-danger">Failed to load chart data. Please try again later.</div>';
                }
            });
        }

        function exportPDF() {
            const startDate = $('#date-range-picker').data('daterangepicker')?.startDate.format('YYYY-MM-DD') || '';
            const endDate = $('#date-range-picker').data('daterangepicker')?.endDate.format('YYYY-MM-DD') || '';
            const predefinedFilter = $('.dropdown-item.active').text().trim() || '';

            fuelConsumptionChart.dataURI().then(({
                imgURI
            }) => {
                Swal.fire({
                    title: 'Exporting PDF...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                $.ajax({
                    url: "{{ route('report-fuel.export-pdf') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        chartImage: imgURI,
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
            });
        }

        function exportExcel() {
            const startDate = $('#date-range-picker').data('daterangepicker')?.startDate.format('YYYY-MM-DD') || '';
            const endDate = $('#date-range-picker').data('daterangepicker')?.endDate.format('YYYY-MM-DD') || '';
            const predefinedFilter = $('.dropdown-item.active').text().trim() || '';

            const exportUrl = "{{ route('report-fuel.export-excel') }}" +
                `?startDate=${startDate}&endDate=${endDate}&predefinedFilter=${predefinedFilter}`;

            window.location.href = exportUrl;
        }
    </script>
    </script>
@endpush
