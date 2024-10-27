@extends('layouts.global')

@section('title', 'Kendaraan / Unit')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> Fuel Consumption</h4>
        <div class="d-flex justify-content-end align-items-center mb-3">
            <button id="exportPdfBtn" class="btn btn-primary">
                <i class="fa-solid fa-file-pdf me-1"></i>Export PDF
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
                            <th>tanggal</th>
                            <th>nama project</th>
                            <th>nama aset</th>
                            <th>banyak penggunaan</th>
                            <th>harga/liter</th>
                            <th>total</th>
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
            init_chart(); // Initialize chart after document is ready
            $('#exportPdfBtn').on('click', exportPDF);
        });

        $(document).on('input', '#searchData', function() {
            init_table($(this).val());
        });

        function init_table(keyword = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                destroy: true, // Add this to make sure it reinitializes the table correctly.
                columnDefs: [{
                    target: 0,
                    visible: true,
                    searchable: false
                }],
                ajax: {
                    type: "GET",
                    url: "{{ route('report-fuel.data') }}",
                    data: {
                        'keyword': keyword
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'date',
                        name: 'date'
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
                        data: 'liter',
                        name: 'liter'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    }
                ]
            });
        }

        let fuelConsumptionChart;

        function init_chart() {
            $.ajax({
                url: "{{ route('report-fuel.chart') }}",
                method: 'GET',
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
                            background: '#ffffff' // Ensure white background for export
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
                        chartImage: imgURI
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response, status, xhr) {
                        var filename = "";
                        var disposition = xhr.getResponseHeader('Content-Disposition');
                        if (disposition && disposition.indexOf('attachment') !== -1) {
                            var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                            var matches = filenameRegex.exec(disposition);
                            if (matches !== null && matches[1]) filename = matches[1].replace(/['"]/g,
                                '');
                        }
                        var blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = filename || 'FuelConsumptionReport.pdf';
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
    </script>
@endpush
