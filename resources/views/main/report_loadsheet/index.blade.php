@extends('layouts.global')

@section('title', 'Laporan Loadsheet')
@section('title_page', 'Report / Laporan Loadsheet')

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
        }
    </style>
@endpush
@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
        <div class="col-lg-12 col-12 mb-4">
            <div class="card">
                <div class="card-header flex-nowrap header-elements">
                    <h5 class="card-title mb-0">Target vs Actual</h5>
                    <div class="card-header-elements ms-auto py-0 d-none d-sm-block">
                    </div>
                </div>
                <div class="card-body pt-2">
                    <canvas id="targetVsActual"></canvas>
                </div>
            </div>
        </div>
        <!-- /Scatter Chart -->
    
        <div class="col d-flex flex-column justify-content-between">
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

        <!-- Product List Table -->
        <div class="d-flex justify-content-end mb-4">
            @if (!auth()->user()->hasRole('Read only'))
            <button onclick="exportExcelByProject()" class="btn btn-success btn-md btn-asset">
                <i class="fa-solid fa-file-excel me-2"></i>Export Excel
            </button>
            @endif
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Project Loadsheet</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table table-striped table-poppins " id="data-table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Nama Project</th>
                            <th>Total Loadsheet</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <br>
        <div class="d-flex justify-content-end mt-3">
            @if (!auth()->user()->hasRole('Read only'))
            <button onclick="exportExcelByAsset()" class="btn btn-success btn-md btn-asset">
                <i class="fa-solid fa-file-excel me-2"></i>Export Excel
            </button>
            @endif
        </div>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Asset Loadsheet</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table table-striped table-poppins " id="data-table-asset">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>ID Asset</th>
                            <th>name</th>
                            <th>asset number</th>
                            <th>Total Loadsheet</th>
                            <th>liter</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="{{ asset('assets/vendor/libs/chartjs/chartjs.js')}}"></script>
<script src="{{ asset('assets/js/charts-chartjs.js?update=4')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>


<script type="text/javascript">
    $(document).ready(function() {
            init_table();
            init_table_asset();
            init_speedometer_chart();

            $('.dropdown-item').on('click', function(e) {
                e.preventDefault();
                $('.dropdown-item').removeClass('active');
                $(this).addClass('active');
                const filterType = $(this).text().trim();
                const filterBtn = $('.btn.btn-outline-primary.dropdown-toggle');
                filterBtn.text(filterType);

                reloadTableWithFilters(null, null, filterType);
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
            });

            $('#date-range-picker').on('cancel.daterangepicker', function() {
                $(this).val('');
                reloadTableWithFilters();
            });
        });

        $(document).on('input', '#searchData', function() {
            init_table($(this).val());
        });

        function reloadTableWithFilters(startDate = '', endDate = '', predefinedFilter = '') {
            $('#data-table').DataTable().destroy();
            init_table(startDate, endDate, predefinedFilter);
        }

        function init_table(keyword = '', startDate = '', endDate = '', predefinedFilter = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                columnDefs: [{
                    target: 0,
                    visible: true,
                    searchable: false
                }, ],

                ajax: {
                    type: "GET",
                    url: "{{ route('report-loadsheet.data') }}",
                    data: {
                        'keyword': keyword
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'project_name',
                        name: 'project_name'
                    },
                    {
                        data: 'total_loadsheet',
                        name: 'total_loadsheet'
                    },
                ]
            });
        }

        function init_table_asset(keyword = '', startDate = '', endDate = '', predefinedFilter = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            var table = $('#data-table-asset').DataTable({
                processing: true,
                serverSide: true,
                columnDefs: [{
                    target: 0,
                    visible: true,
                    searchable: false
                }, ],

                ajax: {
                    type: "GET",
                    url: "{{ route('report-loadsheet.dataAsset') }}",
                    data: {
                        'keyword': keyword
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'asset_number',
                        name: 'asset_number'
                    },
                    {
                        data: 'total_loadsheet',
                        name: 'total_loadsheet'
                    },
                    {
                        data: 'liter',
                        name: 'liter',
                    },
                ]
            });
        }

        function exportExcelByProject() {
            const startDate = $('#date-range-picker').data('daterangepicker')?.startDate?.format('YYYY-MM-DD');
            const endDate = $('#date-range-picker').data('daterangepicker')?.endDate?.format('YYYY-MM-DD');
            const predefinedFilter = $('.dropdown-item.active').text().trim() || '';

            var url = "{{ route('report-loadsheet.exportExcelByProject') }}?startDate=" + startDate + "&endDate=" + endDate;

            window.open(url);
        }

        function exportExcelByAsset() {
            const startDate = $('#date-range-picker').data('daterangepicker')?.startDate?.format('YYYY-MM-DD');
            const endDate = $('#date-range-picker').data('daterangepicker')?.endDate?.format('YYYY-MM-DD');
            const predefinedFilter = $('.dropdown-item.active').text().trim() || '';

            var url = "{{ route('report-loadsheet.exportExcelByAsset') }}?startDate=" + startDate + "&endDate=" + endDate;

            window.open(url);
        }

        let speedometerChart;

        function init_speedometer_chart(data) {
            const dateRange = $('#date-range-picker').val();
            let startDate = null;
            let endDate = null;
            const filterType = $('.dropdown-item.active').text().trim() || null;

            if (dateRange) {
                [startDate, endDate] = dateRange.split(' - ');
            }

            $.ajax({
                url: "{{ route('management-project.spedometer') }}",
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    filterType: filterType,
                },
                dataType: 'json',
                success: function(response) {
                    data = response.data;
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
                        series: [parseInt(percentage)],
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
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                    showErrorMessage('Failed to load chart data');
                }
            });
        }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('targetVsActual').getContext('2d');

        // Fetch data from Laravel endpoint
        fetch('/report-loadsheet/chart-project')
            .then(response => response.json())
            .then(data => {
                // Initialize chart
                new Chart(ctx, {
                    type: 'scatter',
                    data: {
                        datasets: data.datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 800
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    boxWidth: 10,
                                    boxHeight: 10,
                                    color: 'black'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#f8f9fa',
                                titleColor: '#212529',
                                bodyColor: '#495057',
                                borderWidth: 1,
                                borderColor: '#ced4da'
                            },
                            annotation: {
                                annotations: {
                                    targetLine: {
                                        type: 'line',
                                        yMin: 70, // Example target value
                                        yMax: 70,
                                        borderColor: 'green',
                                        borderWidth: 2,
                                        borderDash: [6, 6],
                                        label: {
                                            enabled: true,
                                            content: 'Target',
                                            position: 'end',
                                            backgroundColor: 'green',
                                            color: 'white',
                                            padding: 5
                                        }
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                type: 'time',
                                title: {
                                    display: true,
                                    text: 'Date'
                                },
                                time: {
                                    unit: 'day'
                                },
                                ticks: {
                                    autoSkip: true
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Hours'
                                },
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 10
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching scatter data:', error));
    });
</script>
@endpush