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

        .input-filter {
            max-width: 180px;
            width: 100%;
        }

        @media (max-width: 768px) {
            .input-filter {
                max-width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
        <!-- Card Border Shadow -->
        <div class="row">
            <div class="col-sm-6 col-lg-12 mb-4">
                <div class="d-flex flex-wrap justify-content-end align-items-end my-1 gap-3">
                    <div class="input-filter">
                        <label for="date-range-picker" class="form-label">filter dengan jangka waktu</label>
                        <input type="text" id="date-range-picker" class="form-control" placeholder="Select Date Range">
                    </div>
                    <div class="btn-group input-filter">
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
                        <h4 class="ms-1 mb-0 text-muted" id="total-productivity">On Progress</h4>
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
                        <h5 class="m-0">Equipment Status</h5>
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
            {{-- <div class="col mb-4">
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
            </div> --}}
        </div>

        <div class="row mt-4">
            <div class="col mb-4">
                <div class='card'>
                    <div class='card-header'>
                        <h5 class="m-0" style="font-weight: 900;">Equipment Status Count</h5>
                    </div>
                    <div class='card-body'>
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th><span class="fas fa-circle" style="color: green;"></span> Active</th>
                                    <td style="float: right;"><span class="badge bg-primary" style="background-color: green !important; color: white; border-radius: 35%;" id="asset-active">0</span></td>
                                </tr>
                                <tr>
                                    <th><span class="fas fa-circle" style="color: blue;"></span> Inactive</th>
                                    <td style="float: right;"><span class="badge bg-primary" style="background-color: blue !important; color: white; border-radius: 35%;" id="asset-inactive">0</span></td>
                                </tr>
                                <tr>
                                    <th><span class="fas fa-circle" style="color: red;"></span> Scrap</th>
                                    <td style="float: right;"><span class="badge bg-primary" style="background-color: red !important; color: white; border-radius: 35%;" id="asset-scrap">0</span></td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col mb-4">
                <div class='card'>
                    <div class='card-header'>
                        <h5 class="m-0" style="font-weight: 900;">Maintenance Status</h5>
                    </div>
                    <div class='card-body'>
                        <table class="datatables table table-striped table-poppins  border-top" id="data-table-maintenance-status">
                            <thead>
                                <tr>
                                    <td>Asset</td>
                                    <td>Status</td>
                                    <td class="text-center">Jumlah</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col d-flex flex-column justify-content-between">
                <div class="card" style="height: 300px;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0 text-primary fw-bold">Open Issue</h5>
                    </div>
                    <div class="card-body d-flex justify-content-center">
                        <div class="row gap-4">
                            <div class="col d-flex flex-column align-items-center">
                                <h1 class="text-primary fw-bold" style="font-size: 50px;" id="total-maintenance">
                                    0</h1>
                                <h3 class="text-muted">Open</h3>
                            </div>
                            <div class="col d-flex flex-column align-items-center">
                                <h1 class="text-muted fw-bold" style="font-size: 50px;" id="total-overdue">0
                                </h1>
                                <h3 class="text-muted">Overdue</h3>
                            </div>
                            <div class="col d-flex flex-column align-items-center">
                                <h1 class="text-muted fw-bold" style="font-size: 50px;" id="total-rfu">27
                                </h1>
                                <h3 class="text-muted">RFU</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col d-flex flex-column justify-content-between">
                {{-- <div class="col-12 col-md-12" id="managementProject">
                    <div class="select2-primary">
                        <div class="position-relative">
                            <select id="management_project_id" name="management_project_id" class="select2 form-select"
                                required>
                            </select>
                        </div>
                    </div>
                </div> --}}

                <div class="card background-card" style="height: 300px;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="m-0 text-white">Speedometer</h4>
                    </div>
                    <div class="card-body row">
                        <div class="col-6" id="speedometerChart"></div>
                        <div class="col-6 d-flex flex-column">
                            <div class="mb-2">
                                <h5 class="text-white fw-bold mb-0">Project Value</h5>
                                <h2 class="text-white fw-bold" id="max-value-speedometer"></h2>
                            </div>
                            <div class="mb-3">
                                <h5 class="text-white fw-bold mb-0">Actual Sales</h5>
                                <h2 class="text-white fw-bold" id="current-value-speedometer"></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Total Asset By Kategori</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary" id="group-asset-by-category-download">
                                <i class="ti ti-download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="groupAssetByCategoryChart"></div>
                    </div>
                </div>
            </div>
            <div class="col">
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
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>ADT 40 Ton</td>
                                        <td>24</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Bulldozer</td>
                                        <td>14</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Breaker</td>
                                        <td>7</td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Asphalt Truck</td>
                                        <td>2</td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>Backhoeloader</td>
                                        <td>2</td>
                                    </tr>
                                </tbody>
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
            init_table_maintenance_status();
            init_table_grouped_by_category();

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
                fetchProductivity();
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
                fetchProductivity();
                fetchLoadsheetData(startDate, endDate, null);
            });

            $('#date-range-picker').on('cancel.daterangepicker', function() {
                $(this).val('');
                updateSpeedometerWithDateRange(null, null, null);
                fetchFuelData(null, null, null); // Fetch default data
                fetchProductivity();
                fetchLoadsheetData(null, null, null);
            });

            fetchFuelData();
            fetchProductivity();
            fetchLoadsheetData();
            init_table_category_asset();
            updateSpeedometerWithDateRange();
        });

        function fetchFuelData(startDate = null, endDate = null, filterType = null) {
            $.ajax({
                url: "{{ route('fuel.sumFuelConsumption') }}",
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    filterType: filterType,
                },
                dataType: 'json',
                success: function(response) {
                    let totalLiter = response.data;
                    $('#total-fuel').text(totalLiter ? totalLiter + ' liter' : '0 liter');
                },
                error: function(xhr, status, error) {
                    $('#total-fuel').text('Error');
                    console.error('Error fetching fuel data:', error);
                }
            });
        }

        function fetchProductivity(startDate = null, endDate = null, filterType = null) {
            $.ajax({
                url: "{{ route('loadsheet.productivityByHours') }}",
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    filterType: filterType,
                },
                dataType: 'json',
                success: function(response) {
                    let total = response.data;
                    $('#total-productivity').text(total ? total + ' Hours' : '0 Hours');
                },
                error: function(xhr, status, error) {
                    $('#total-productivity').text('Error');
                    console.error('Error fetching loadsheet data:', error);
                }
            });
        }

        function fetchLoadsheetData(startDate = null, endDate = null, filterType = null) {
            $.ajax({
                url: "{{ route('loadsheet.sumTotalLoadsheet') }}",
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    filterType: filterType,
                },
                dataType: 'json',
                success: function(response) {
                    let totalLoadsheet = response.data;
                    $('#total-loadsheet').text(totalLoadsheet ? totalLoadsheet + ' Loadsheet' : '0 Loadsheet');
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
                    filterType: filterType,
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
            const maxValue = data.maxValue;
            const currentValue = Math.min(data.totalPrice, maxValue) || 0;
            const percentage = Math.ceil((parseInt(data.totalPrice) / parseInt(data.maxValue)) * 100);

            document.getElementById('max-value-speedometer').innerText = maxValue;
            document.getElementById('current-value-speedometer').innerText = data.totalPrice;

            const options = {
                chart: {
                    type: 'radialBar',
                    height: 200,
                    colors: ['#426B80'],
                    sparkline: {
                        enabled: true
                    },
                    animations: {
                        enabled: true
                    },
                },
                series: [percentage],
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
                                    return val.toFixed(2);
                                }
                            },
                            total: {
                                show: true,
                                label: '',
                                color: '#FFFFFF',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
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
            };

            const speedometerChart = new ApexCharts(document.querySelector("#speedometerChart"), options);
            speedometerChart.render();
        }

        function fetchStatusData() {
            $.ajax({
                url: "{{ route('asset.statusData') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    initializeCharts(response);
                    
                    $('#asset-active').text(response.active || 0);
                    $('#asset-inactive').text(response.inactive || 0);
                    $('#asset-scrap').text(response.scrap || 0);
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
                colors: ['#FFAC82', '#000BE1', '#FABE29'],
                series: [data.active || 0, data.inactive || 0, data.scrap || 0],
                labels: ['Active', 'Inactive', 'Scrap']
            });
            operationalChart.render();

            // Maintenance Status Chart
            const maintenanceChart = new ApexCharts(document.querySelector("#maintenanceStatusChart"), {
                ...baseOptions,
                colors: ['#FFAC82', '#000BE1', '#FABE29'],
                series: [data.underMaintenance || 0, data.underRepair || 0, data.waiting || 0],
                labels: ['Under Maintenance', 'Under Repair', 'Waiting'],
            });
            maintenanceChart.render();

            // Asset Status Chart
            // const assetChart = new ApexCharts(document.querySelector("#assetStatusChart"), {
            //     ...baseOptions,
            //     colors: ['#FFAC82', '#000BE1', '#FABE29', '#134B70'],
            //     series: [data.damaged || 0, data.fair || 0, data.needsRepair || 0, data.good || 0],
            //     labels: ['Damaged', 'Fair', 'Needs Repair', 'Good']
            // });
            // assetChart.render();

            // Store chart instances for download functionality
            window.chartInstances = {
                operational: operationalChart,
                maintenance: maintenanceChart,
                // asset: assetChart
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

            document.getElementById('group-asset-by-category-download').addEventListener('click', function() {
                if (window.chartInstances?.asset) {
                    window.chartInstances.asset.exportToSVG();
                }
            });
        }

        function showErrorMessage(message) {
            console.error(message);
        }

        function init_table_category_asset(keyword = '') {
            var postForm = {
                'keyword': keyword,
            };
            $.ajax({
                url: '{{ route("asset.getDataGroupedByCategory") }}', 
                type: 'GET', 
                data : postForm,
                dataType  : 'json',
            })
            .done(function(data) {
                initializeChartsCategoryAsset(data);
            })
            .fail(function() {
                alert('Load data failed.');
            });
        }

        function initializeChartsCategoryAsset(data) {
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

            const labels = data.map(item => item.category); // Ambil semua kategori
            const series = data.map(item => item.total_asset); // Ambil semua total_asset
            const randomColors = data.map(() => getRandomColor());

            // Operational Status Chart
            const operationalChart = new ApexCharts(document.querySelector("#groupAssetByCategoryChart"), {
                ...baseOptions,
                colors: randomColors,
                series: series,
                labels: labels
            });
            operationalChart.render();

            // Store chart instances for download functionality
            window.chartInstances = {
                operational: operationalChart,
            };
        }

        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        function init_table_maintenance_status() {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            var table = $('#data-table-maintenance-status').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,

                ajax: {
                    type: "GET",
                    url: "{{ route('maintenances.maintenanceStatus') }}",
                },
                columns: [
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
                ]
            });
        }

        function init_table_grouped_by_category() {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            var table = $('#data-table-by-category-asset').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,

                ajax: {
                    type: "GET",
                    url: "{{ route('asset.dataGroupedByCategory') }}",
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
