@extends('layouts.global')

@section('title', 'Laporan Fuel Consumtion')

@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> Manpower</h4>

        {{-- chart manpower --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Manpower Data Over Time</h5>
            </div>
            <div class="card-body">
                <div id="hours-chart"></div> <!-- Chart Div -->
            </div>
        </div>

        <div class="card my-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Grouping by project</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table table-striped table-poppins " id="data-table-project">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Management Project</th>
                            <th>Total Hours</th>
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
            init_hours_chart(); // Initialize chart on page load
            init_table_project(); // Initialize chart on page load

            $('.dropdown-item').on('click', function(e) {
                e.preventDefault();
                $('.dropdown-item').removeClass('active');
                $(this).addClass('active');
                const filterType = $(this).text().trim();
                const filterBtn = $('.btn.btn-outline-primary.dropdown-toggle');
                filterBtn.text(filterType);

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
                reloadExpanseChartWithFilters(startDate, endDate);
            });

            $('#date-range-picker').on('cancel.daterangepicker', function() {
                $(this).val('');
                reloadHoursChartWithFilters(); // Reload chart without date range
            });
        });

        $(document).on('input', '#searchData', function() {
            init_table($(this).val());
        });

        function reloadHoursChartWithFilters(startDate = '', endDate = '', predefinedFilter = '') {
            if (hoursChart) hoursChart.destroy();
            init_hours_chart(startDate, endDate, predefinedFilter);
        }

        let hoursChart;

        function init_table_project(startDate = '', endDate = '', predefinedFilter = '', keyword = '') {
            $('#data-table-project').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('report-manpower.getDataProjectHours') }}",
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
                        data: 'total_hours',
                        name: 'total_hours'
                    },
                ]
            });
        }

        function init_hours_chart(startDate = '', endDate = '', predefinedFilter = '') {
            $.ajax({
                url: "{{ route('report-manpower.hours-data') }}",
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
                            categories: response.months,
                            title: {
                                text: 'Months'
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
                            text: 'Manpower Over Time (Monthly)',
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
