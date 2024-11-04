@extends('layouts.global')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">Dashboard</h4>

        <!-- Card Border Shadow -->
        <div class="row">
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-truck ti-md"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0" id="total-asset">Loading...</h4>
                        </div>
                        <p class="mb-1">Total Asset</p>
                        <p class="mb-0">
                            <span class="fw-medium me-1">+18.2%</span>
                            <small class="text-muted">than last week</small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-warning"><i
                                        class="ti ti-alert-triangle ti-md"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0" id="total-fuel">Loading...</h4>
                        </div>
                        <p class="mb-1">Fuel Consumption</p>
                        <p class="mb-0">
                            <span class="fw-medium me-1">-8.7%</span>
                            <small class="text-muted">than last week</small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-danger"><i
                                        class="ti ti-git-fork ti-md"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">27</h4>
                        </div>
                        <p class="mb-1">Productivity</p>
                        <p class="mb-0">
                            <span class="fw-medium me-1">+4.3%</span>
                            <small class="text-muted">than last week</small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-info"><i class="ti ti-clock ti-md"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">13</h4>
                        </div>
                        <p class="mb-1">Asset Value</p>
                        <p class="mb-0">
                            <span class="fw-medium me-1">-2.5%</span>
                            <small class="text-muted">than last week</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Status Donut Charts -->
        <div class="row">
            <!-- Operational Status -->
            <div class="col-md-6 col-lg-4 mb-4">
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
            <div class="col-md-6 col-lg-4 mb-4">
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
            <div class="col-md-6 col-lg-4 mb-4">
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
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('asset.data') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let totalCount = response.recordsTotal;
                    $('#total-asset').text(totalCount);
                },
                error: function(xhr, status, error) {
                    $('#total-asset').text('Error');
                    console.error('Error fetching data:', error);
                }
            });
            $.ajax({
                url: "{{ route('fuel.data') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let literDashboard = response.data.map(item => item.literDashboard);
                    let totalLiter = literDashboard.length ? literDashboard.reduce((total, num) =>
                        total + num) : 0;
                    $('#total-fuel').text(totalLiter ? totalLiter + ' liter' : 0 + ' liter');
                },
                error: function(xhr, status, error) {
                    $('#total-fuel').text('Error');
                    console.error('Error fetching data:', error);
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Fetch data and initialize charts
            fetchStatusData();

            // Configure download buttons
            setupDownloadButtons();
        });

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
                colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                series: [data.idle || 0, data.standby || 0, data.underMaintenance || 0, data.active || 0],
                labels: ['Idle', 'StandBy', 'Under Maintenance', 'Active']
            });
            operationalChart.render();

            // Maintenance Status Chart
            const maintenanceChart = new ApexCharts(document.querySelector("#maintenanceStatusChart"), {
                ...baseOptions,
                colors: ['#FF9F40', '#4BC0C0', '#9966FF', '#FF6384'],
                series: [data.onHold || 0, data.finish || 0, data.scheduled || 0, data.inProgress || 0],
                labels: ['On Hold', 'Finish', 'Scheduled', 'In Progress']
            });
            maintenanceChart.render();

            // Asset Status Chart
            const assetChart = new ApexCharts(document.querySelector("#assetStatusChart"), {
                ...baseOptions,
                colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
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
            // Add your error handling logic here
            console.error(message);
        }
    </script>
@endpush
