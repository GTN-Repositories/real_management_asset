@extends('layouts.global')

@section('title', 'Dashboard')
@section('title_page', 'Dashboard')

@push('css')
    <style>
        .background-card {
            background-image: url("{{ asset('images/backgorund_spedometer.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
@endpush

@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
        <!-- Card Border Shadow -->
        <div class="row">
            <div class="col-sm-6 col-lg-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center pb-1">
                            <div class="d-flex justify-content-center align-items-end my-1 gap-3">
                                <div>
                                    <label for="date-range-picker" class="form-label">filter dengan jangka waktu</label>
                                    <input type="text" id="date-range-picker" class="form-control"
                                        placeholder="Select Date Range">
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary dropdown-toggle waves-effect"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        filter tanggal
                                    </button>
                                    <ul class="dropdown-menu" style="">
                                        <li><a class="dropdown-item" id="hari ini" href="javascript:void(0);">hari ini</a>
                                        </li>
                                        <li><a class="dropdown-item" id="minggu ini" href="javascript:void(0);">minggu
                                                ini</a></li>
                                        <li><a class="dropdown-item" id="bulan ini" href="javascript:void(0);">bulan ini</a>
                                        </li>
                                        <li><a class="dropdown-item" id="bulan kemarin" href="javascript:void(0);">bulan
                                                kemarin</a></li>
                                        <li><a class="dropdown-item" id="tahun ini" href="javascript:void(0);">tahun ini</a>
                                        </li>
                                        <li><a class="dropdown-item" id="tahun kemarin" href="javascript:void(0);">tahun
                                                kemarin</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-2">
                                <img src="{{ asset('images/truck.png') }}" alt="">
                            </div>
                            <strong class="mb-0 text-primary">Total Asset</strong>
                        </div>
                        <h4 class="ms-1 mb-0 text-muted" id="total-asset">Loading...</h4>
                    </div>
                </div>
            </div>
            <div class="col mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-1">
                                <img src="{{ asset('images/fuel.png') }}" alt="">
                            </div>
                            <strong class="mb-0 text-primary">Fuel Consumption</strong>
                        </div>
                        <h4 class="ms-1 mb-0 text-muted" id="total-fuel">Loading...</h4>
                    </div>
                </div>
            </div>
            <div class="col mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-2">
                                <img src="{{ asset('images/productivity.png') }}" alt="">
                            </div>
                            <strong class="mb-0 text-primary">Productivity</strong>
                        </div>
                        <h4 class="ms-1 mb-0 text-muted">On Progress</h4>
                    </div>
                </div>
            </div>
            <div class="col mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-2">
                                <img src="{{ asset('images/asset_value.png') }}" alt="">
                            </div>
                            <strong class="mb-0 text-primary">Asset Value</strong>
                        </div>
                        <h4 class="ms-1 mb-0 text-muted" id="asset-value">Loading...</h4>
                    </div>
                </div>
            </div>
            <div class="col mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-2">
                                <img src="{{ asset('images/loadsheet.png') }}" alt="">
                            </div>
                            <strong class="mb-0 text-primary">Total Loadsheet</strong>
                        </div>
                        <h4 class="ms-1 mb-0 text-muted" id="total-loadsheet">Loading...</h4>
                    </div>
                </div>
            </div>
        </div>
        <!-- Status Donut Charts -->
        <div class="row">
            <!-- Operational Status -->
            <div class="col mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Operational Status</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary" id="operational-download">
                                <i class="ti ti-download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="operationalStatusChart"></div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Status -->
            <div class="col mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Maintenance Status</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary" id="maintenance-download">
                                <i class="ti ti-download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="maintenanceStatusChart"></div>
                    </div>
                </div>
            </div>

            <!-- Asset Status -->
            <div class="col mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Asset Status</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary" id="asset-download">
                                <i class="ti ti-download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="assetStatusChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col d-flex flex-column justify-content-between">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0 text-primary fw-bold">Open Issue</h5>
                    </div>
                    <div class="card-body d-flex justify-content-center">
                        <div class="row gap-4">
                            <div class="col d-flex flex-column align-items-center">
                                <h1 class="text-primary fw-bold" style="font-size: 50px;" id="total-maintenance">
                                    Loading...</h1>
                                <h3 class="text-muted">Open</h3>
                            </div>
                            <div class="col d-flex flex-column align-items-center">
                                <h1 class="text-muted fw-bold" style="font-size: 50px;" id="total-overdue">Loading...
                                </h1>
                                <h3 class="text-muted">Overdue</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col d-flex flex-column justify-content-between">
                <div class="col-12 col-md-12" id="managementProject">
                    <div class="select2-primary">
                        <div class="position-relative">
                            <select id="management_project_id" name="management_project_id" class="select2 form-select"
                                required>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card background-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="m-0 text-white">Speedometer</h4>
                    </div>
                    <div class="card-body">
                        <div id="speedometerChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="m-0 text-primary fw-bold">Total Asset By Kategori</h5>
                        <div class="card-datatable table-responsive">
                            <table class="datatables table border-top" id="data-table-by-category-asset">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kategori</th>
                                        <th>Total Aset</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            fetchStatusData();

            init_speedometer_chart();

            setupDownloadButtons();
        });

        $(document).ready(function() {
            $.ajax({
                url: "{{ route('asset.data') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let totalCount = response.recordsTotal;
                    $('#total-asset').text(totalCount + ' Unit');
                },
                error: function(xhr, status, error) {
                    $('#total-asset').text('Error');
                    console.error('Error fetching data:', error);
                }
            });
            $.ajax({
                url: "{{ route('asset.data') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let cost = response.data.map(item => item.cost);
                    let totalCost = cost.length ? cost.reduce((total, num) =>
                        total + num) : 0;
                    $('#asset-value').text(totalCost ? totalCost : 0);
                },
                error: function(xhr, status, error) {
                    $('#asset-value').text('Error');
                    console.error('Error fetching data:', error);
                }
            });
            $.ajax({
                url: "{{ route('asset.data') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let status = response.data.filter(item => item.status === 'UnderMaintenance').map(
                        item => 1);
                    let totalMaintenance = status.length ? status.reduce((total, num) =>
                        total + num) : 0;
                    $('#total-maintenance').text(totalMaintenance ? totalMaintenance : 0);
                },
                error: function(xhr, status, error) {
                    $('#total-maintenance').text('Error');
                    console.error('Error fetching data:', error);
                }
            });
            $.ajax({
                url: "{{ route('asset.data') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let status = response.data.filter(item => item.status === 'Overdue').map(item => 1);
                    let totalOverdue = status.length ? status.reduce((total, num) =>
                        total + num) : 0;
                    $('#total-overdue').text(totalOverdue ? totalOverdue : 0);
                },
                error: function(xhr, status, error) {
                    $('#total-overdue').text('Error');
                    console.error('Error fetching data:', error);
                }
            });
            $('#management_project_id').select2({
                dropdownParent: $('#managementProject'),
                placeholder: 'Pilih management project',
                ajax: {
                    url: "{{ route('management-project.data') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            keyword: params.term
                        };
                    },
                    processResults: function(data) {
                        apiResults = data.data.map(function(item) {
                            return {
                                text: item.name,
                                id: item.managementRelationId,
                            };
                        });

                        return {
                            results: apiResults
                        };
                    },
                    limit: 10,
                    cache: true
                }
            });

            $('.dropdown-item').on('click', function(e) {
                e.preventDefault();
                $('.dropdown-item').removeClass('active');
                $(this).addClass('active');
                const filterType = $(this).text().trim();
                const filterBtn = $('.btn.btn-outline-primary.dropdown-toggle');
                filterBtn.text(filterType);
                updateSpeedometerWithDateRange(null, null, filterType);
                fetchFuelData(null, null, filterType);
                fetchLoadsheetData(null, null, filterType);
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
                updateSpeedometerWithDateRange(startDate, endDate, null);
                fetchFuelData(startDate, endDate, null);
                fetchLoadsheetData(startDate, endDate, null);
            });

            $('#date-range-picker').on('cancel.daterangepicker', function() {
                $(this).val('');
                updateSpeedometerWithDateRange(null, null, null);
                fetchFuelData(null, null, null); // Fetch default data
                fetchLoadsheetData(null, null, null);
            });

            fetchFuelData();
            fetchLoadsheetData();
            init_table_category_asset();
        });

        function fetchFuelData(startDate = null, endDate = null, filterType = null) {
            $.ajax({
                url: "{{ route('fuel.data') }}",
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    filterType: filterType,
                },
                dataType: 'json',
                success: function(response) {
                    let literDashboard = response.data.map(item => item.literDashboard);
                    let totalLiter = literDashboard.length ?
                        literDashboard.reduce((total, num) => total + num) :
                        0;
                    $('#total-fuel').text(totalLiter ? totalLiter + ' liter' : '0 liter');
                },
                error: function(xhr, status, error) {
                    $('#total-fuel').text('Error');
                    console.error('Error fetching fuel data:', error);
                }
            });
        }

        // Fetch Loadsheet Data
        function fetchLoadsheetData(startDate = null, endDate = null, filterType = null) {
            $.ajax({
                url: "{{ route('loadsheet.data') }}",
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    filterType: filterType,
                },
                dataType: 'json',
                success: function(response) {
                    let loadsheetDashboard = response.data.map(item => item.loadsheetDashboard);
                    let totalLoadsheet = loadsheetDashboard.length ?
                        loadsheetDashboard.reduce((total, num) => total + num) :
                        0;
                    $('#total-loadsheet').text(totalLoadsheet ? totalLoadsheet + ' Kubik' : '0 Kubik');
                },
                error: function(xhr, status, error) {
                    $('#total-loadsheet').text('Error');
                    console.error('Error fetching loadsheet data:', error);
                }
            });
        }

        $('#management_project_id').on('change', function() {
            const managementProjectId = $(this).val();
            const dateRange = $('#date-range-picker').val();
            let startDate = null;
            let endDate = null;
            const filterType = $('.dropdown-item.active').text().trim() || null;

            if (dateRange) {
                [startDate, endDate] = dateRange.split(' - ');
            }

            updateSpeedometerWithDateRange(startDate, endDate, filterType, managementProjectId);
        });

        function updateSpeedometerWithDateRange(startDate, endDate, filterType, managementProjectId = null) {
            const managementProjectIdValue = managementProjectId || $('#management_project_id').val();

            $.ajax({
                url: "{{ route('management-project.spedometer') }}",
                type: 'GET',
                data: {
                    management_project_id: managementProjectIdValue,
                    start_date: startDate,
                    end_date: endDate,
                    filterType: filterType, // Ensure filterType is sent
                },
                dataType: 'json',
                success: function(response) {
                    reloadSpeedometer(response.data);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                    showErrorMessage('Failed to load chart data');
                }
            });
        }

        function reloadSpeedometer(data) {
            if (speedometerChart) speedometerChart.destroy();
            init_speedometer_chart(data);
        }

        let speedometerChart;

        function init_speedometer_chart(data) {
            const options = {
                chart: {
                    type: 'radialBar',
                    height: 200,
                    colors: ['#426B80'],
                    sparkline: {
                        enabled: true
                    },
                    animations: {
                        enabled: false
                    },
                },
                series: [parseFloat(data.performance)],
                labels: ['Performance'],
                plotOptions: {
                    radialBar: {
                        startAngle: -135,
                        endAngle: 135,
                        hollow: {
                            size: '60%'
                        },
                        track: {
                            background: '#FFFFFF',
                            strokeWidth: '97%',
                            margin: 5
                        },
                        dataLabels: {
                            name: {
                                fontSize: '22px',
                                color: '#426B80'
                            },
                            value: {
                                fontSize: '36px',
                                color: '#FFFFFF',
                                formatter: function(val) {
                                    return val.toFixed(2) + '%';
                                }
                            },
                            total: {
                                show: true,
                                label: 'Actual Sales',
                                color: '#FFFFFF',
                                formatter: function(w) {
                                    return data.totalPrice;
                                }
                            }
                        }
                    }
                },
                fill: {
                    colors: ['#20E647']
                },
                stroke: {
                    lineCap: 'round'
                },
                title: {
                    text: `Project Value`,
                    align: 'left',
                    weight: 'bold',
                    offsetX: -10,
                    style: {
                        color: '#FFFFFF',
                        fontSize: '16px'
                    }
                },
                subtitle: {
                    text: `${data.maxValue}`,
                    align: 'left',
                    weight: 'bold',
                    offsetX: -10,
                    style: {
                        color: '#FFFFFF',
                        fontSize: '16px'
                    }
                }
            };


            speedometerChart = new ApexCharts(document.querySelector("#speedometerChart"),
                options);
            speedometerChart.render();
        }


        function fetchStatusData() {
            $.ajax({
                url: "{{ route('asset.statusData') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    initializeCharts(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching status data:', error);
                    showErrorMessage('Failed to load chart data');
                }
            });
        }

        function initializeCharts(data) {
            // Base options for all donut charts
            const baseOptions = {
                chart: {
                    type: 'donut',
                    height: 350,
                    toolbar: {
                        show: true
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '22px',
                                    fontFamily: 'Helvetica, Arial, sans-serif',
                                    color: undefined,
                                    offsetY: -10
                                },
                                value: {
                                    show: true,
                                    fontSize: '16px',
                                    fontFamily: 'Helvetica, Arial, sans-serif',
                                    color: undefined,
                                    offsetY: 16,
                                    formatter: function(val) {
                                        return val
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    color: '#373d3f',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        return Math.round(val) + '%'
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    floating: false,
                    fontSize: '14px',
                    offsetY: 7
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                tooltip: {
                    enabled: true,
                    y: {
                        formatter: function(val) {
                            return val + " units"
                        }
                    }
                }
            };

            // Operational Status Chart
            const operationalChart = new ApexCharts(document.querySelector("#operationalStatusChart"), {
                ...baseOptions,
                colors: ['#FFAC82', '#000BE1', '#FABE29', '#134B70'],
                series: [data.idle || 0, data.standby || 0, data.underMaintenance || 0, data.active || 0],
                labels: ['Idle', 'StandBy', 'Maintenance', 'Active']
            });
            operationalChart.render();

            // Maintenance Status Chart
            const maintenanceChart = new ApexCharts(document.querySelector("#maintenanceStatusChart"), {
                ...baseOptions,
                colors: ['#FFAC82', '#000BE1', '#FABE29', '#134B70'],
                series: [data.onHold || 0, data.finish || 0, data.scheduled || 0, data.inProgress || 0],
                labels: ['Hold', 'Finish', 'Scheduled', 'Progress'],
            });
            maintenanceChart.render();

            // Asset Status Chart
            const assetChart = new ApexCharts(document.querySelector("#assetStatusChart"), {
                ...baseOptions,
                colors: ['#FFAC82', '#000BE1', '#FABE29', '#134B70'],
                series: [data.damaged || 0, data.fair || 0, data.needsRepair || 0, data.good || 0],
                labels: ['Damaged', 'Fair', 'Needs Repair', 'Good']
            });
            assetChart.render();

            // Store chart instances for download functionality
            window.chartInstances = {
                operational: operationalChart,
                maintenance: maintenanceChart,
                asset: assetChart
            };
        }

        function setupDownloadButtons() {
            document.getElementById('operational-download').addEventListener('click', function() {
                if (window.chartInstances?.operational) {
                    window.chartInstances.operational.exportToSVG();
                }
            });

            document.getElementById('maintenance-download').addEventListener('click', function() {
                if (window.chartInstances?.maintenance) {
                    window.chartInstances.maintenance.exportToSVG();
                }
            });

            document.getElementById('asset-download').addEventListener('click', function() {
                if (window.chartInstances?.asset) {
                    window.chartInstances.asset.exportToSVG();
                }
            });
        }

        function showErrorMessage(message) {
            console.error(message);
        }

        function init_table_category_asset(keyword = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            var table = $('#data-table-by-category-asset').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                columnDefs: [{
                    target: 0,
                    visible: true,
                    searchable: false
                }, ],

                ajax: {
                    type: "GET",
                    url: "{{ route('asset.getDataGroupedByCategory') }}",
                    data: {
                        'keyword': keyword
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'total_asset',
                        name: 'total_asset'
                    },
                ]
            });
        }
    </script>
@endpush
