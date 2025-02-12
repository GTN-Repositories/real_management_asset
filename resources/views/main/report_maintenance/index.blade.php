@extends('layouts.global')

@section('title', 'Laporan Loadsheet')

@section('content')
<div class="mx-5 flex-grow-1 container-p-y">
    <div class="row g-3 text-center mb-4">
        <div class="col-md-4">
            <div class="card" style="height: 450px;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0 text-primary fw-bold">Vehicle</h5>
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
    <div class="row mb-4">
        {{-- <div class="col-12 col-md-4 col-lg-4 mb-3" style="">
            <div class="card d-flex align-items-center justify-content-center" style="height: 100px; width: 50%; z-index: 20; border-radius: 20px; background-color: #D59A01;">
                <h4 class="text-white card-title m-0 fw-bold">Active</h4>
            </div>
            <div class="card d-flex align-items-end justify-content-center" style="height: 100px; margin-top: -100px; z-index: 10; border-radius: 20px; background-color: rgba(213, 153, 1, 0.35); text-align: end;">
                <h5 class="text-black card-title p-1 p-lg-4 m-0 fw-bold" id="active">Loading...</h5>
            </div>
        </div>            
        <div class="col-12 col-md-4 col-lg-4 mb-3" style="">
            <div class="card d-flex align-items-center justify-content-center" style="height: 100px; width: 50%; z-index: 20; border-radius: 20px; background-color: #7F2DE8;">
                <h4 class="text-white card-title m-0 fw-bold">Inactive</h4>
            </div>
            <div class="card d-flex align-items-end justify-content-center" style="height: 100px; margin-top: -100px; z-index: 10; border-radius: 20px; background-color: rgba(127, 45, 232, 0.35); text-align: end;">
                <h5 class="text-white card-title p-1 p-lg-4 m-0 fw-bold" id="inactive">Loading...</h5>
            </div>
        </div>             --}}
        <div class="col-12 col-md-4 col-lg-4 mb-3" style="">
            <div class="card d-flex align-items-center justify-content-center" style="height: 100px; width: 50%; z-index: 20; border-radius: 20px; background-color: #01A5DB;">
                <h4 class="text-white card-title m-0 fw-bold">Under Maintenance</h4>
            </div>
            <div class="card d-flex align-items-end justify-content-center" style="height: 100px; margin-top: -100px; z-index: 10; border-radius: 20px; background-color: rgba(1, 165, 219, 0.35); text-align: end;">
                <h5 class="text-black card-title p-1 p-lg-4 m-0 fw-bold" id="underMaintenance">Loading...</h5>
            </div>
        </div>
        <div class="col-12 col-md-4 col-lg-3 mb-3" style="">
            <div class="card d-flex align-items-center justify-content-center" style="height: 100px; width: 50%; z-index: 20; border-radius: 20px; background-color: #3BBF56;">
                <h4 class="text-white card-title m-0 fw-bold">Under Repair</h4>
            </div>
            <div class="card d-flex align-items-end justify-content-center" style="height: 100px; margin-top: -100px; z-index: 10; border-radius: 20px; background-color: rgba(59, 191, 86, 0.35); text-align: end;">
                <h5 class="text-black card-title p-1 p-lg-4 m-0 fw-bold" id="underRepair">Loading...</h5>
            </div>
        </div>
        <div class="col-12 col-md-4 col-lg-3 mb-3" style="">
            <div class="card d-flex align-items-center justify-content-center" style="height: 100px; width: 50%; z-index: 20; border-radius: 20px; background-color: #3BBF56;">
                <h4 class="text-white card-title m-0 fw-bold">Waiting</h4>
            </div>
            <div class="card d-flex align-items-end justify-content-center" style="height: 100px; margin-top: -100px; z-index: 10; border-radius: 20px; background-color: rgba(59, 191, 86, 0.35); text-align: end;">
                <h5 class="text-black card-title p-1 p-lg-4 m-0 fw-bold" id="waiting">Loading...</h5>
            </div>
        </div>
        <div class="col-12 col-md-4 col-lg-4 mb-3" style="">
            <div class="card d-flex align-items-center justify-content-center" style="height: 100px; width: 50%; z-index: 20; border-radius: 20px; background-color: #3BBF56;">
                <h4 class="text-white card-title m-0 fw-bold">Scrap</h4>
            </div>
            <div class="card d-flex align-items-end justify-content-center" style="height: 100px; margin-top: -100px; z-index: 10; border-radius: 20px; background-color: rgba(59, 191, 86, 0.35); text-align: end;">
                <h5 class="text-black card-title p-1 p-lg-4 m-0 fw-bold" id="scrap">Loading...</h5>
            </div>
        </div>
        <div class="col-12 col-md-4 col-lg-4 mb-3" style="">
            <div class="card d-flex align-items-center justify-content-center" style="height: 100px; width: 50%; z-index: 20; border-radius: 20px; background-color: #3BBF56;">
                <h4 class="text-white card-title m-0 fw-bold">RFU</h4>
            </div>
            <div class="card d-flex align-items-end justify-content-center" style="height: 100px; margin-top: -100px; z-index: 10; border-radius: 20px; background-color: rgba(59, 191, 86, 0.35); text-align: end;">
                <h5 class="text-black card-title p-1 p-lg-4 m-0 fw-bold" id="rfu">Loading...</h5>
            </div>
        </div>
    </div>

    {{-- <div class="card mb-4">
        <div class="card-header flex-nowrap header-elements">
            <h5 class="card-title mb-0">Top Asset Maintenance Over Time</h5>
            <div class="card-header-elements ms-auto py-0 d-none d-sm-block">
            </div>
        </div>
        <div class="card-body pt-2">
            <canvas id="lineChart"></canvas>
        </div>
    </div> --}}

    <!-- Product List Table -->
    <div class="card mb-4">
        <div class="card-header flex-nowrap header-elements">
            <h5 class="card-title mb-0">Asset Maintenance History by Category</h5>
            <div class="card-header-elements ms-auto py-0 d-none d-sm-block">
                <button type="button" class="btn btn-primary btn-md" onclick="filterPeriodByDate()">
                    <i class="fas fa-solid fa-file-excel me-2"></i> Filter Period
                </button>
                <button type="button" class="btn btn-success btn-md" onclick="exportExcel()">
                    <i class="fas fa-solid fa-file-excel me-2"></i> Export Excel
                </button>
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables table table-striped table-bordered table-poppins" id="data-table-by-date">
                <thead class="border-top">
                    <tr>
                        <th rowspan="2" class="text-center align-middle">Asset ID</th>
                        <th colspan="{{ $daysInMonth }}" class="text-center">{{
                            \Carbon\Carbon::parse((int)$year.'-'.(int)$month.'-01')->format('F Y') }}</th>
                    </tr>
                    <tr>
                        @for ($i = 1; $i <= $daysInMonth; $i++)
                            <th class="text-center">{{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @php
                        $color = [
                            'Ringan' => '#00BD2C',
                            'Sedang' => '#FABE29',
                            'Berat' => '#FF0004',
                            'Aktif' => '#248FD6',
                            'RFU' => '#7F2DE8',
                            'Scrap' => '#666666',
                            'Uncertain' => '#FFFFFF'
                        ];
                    @endphp
                    @foreach ($dataByDate as $item)
                        <tr>
                            @php
                                $asset = \App\Models\Asset::find($item['asset_id']);
                            @endphp
                            <td>{{ 'AST - '.$item['asset_id']. ' - ' . ($asset->name ?? null) . ' - ' . ($asset->serial_number ?? '-') }}</td>
                            
                            @foreach ($item['data'] as $value)
                                <td>
                                    @if ($value == '[]')
                                        Uncertain
                                    @endif
                                    @foreach ($value as $detail)
                                        @php
                                            $status = isset($detail->status_after) && $detail->status_after != null ? $detail->status_after : 'Uncertain';
                                        @endphp
                                        <div class="p-3 m-1 text-center" style="background-color: {{ $color[$status] ?? '#FFFFFF' }}; color: {{ ($status == 'Uncertain') ? '#000000' : '#FFFFFF' }};" class="text-center">
                                            {{ $status }}
                                            <p style="font-size: 10px;">{{ $detail->created_at->format('H:i') ?? '-' }}</p>
                                        </div>
                                    @endforeach
                                </td>
                                {{-- <td style="background-color: {{ $color[$value] ?? '#FFFFFF' }}; color: {{ ($value == 'Uncertain') ? '#000000' : '#FFFFFF' }};" class="text-center">{{ $value }}</td> --}}
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Product List Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Asset Maintenance History Summary</h5>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables table table-striped table-poppins " id="data-table">
                <thead class="border-top">
                    <tr>
                        <th>#</th>
                        <th>Asset</th>
                        <th>Date</th>
                        <th>Last Problem</th>
                        <th>Total Problem</th>
                        <th>Last Duration</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

 {{-- MODAL FILTER --}}
 <div class="modal fade" id="modal-filter-by-date" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body" id="content-modal-filter-by-date">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>

                <div class="text-center mb-4">
                    <h3 class="role-title mb-2">Filter By Month</h3>
                    <p class="text-muted">Filter data by month</p>
                </div>
                
                <form action='{{ route('report-maintenance.index') }}' method='GET' enctype='multipart/form-data'>
                    <div class="row">
                        <div class="me-2 mb-2">
                            <label for="month" class="form-label">Bulan</label>
                            <select id="month" class="form-select select2" name="month">
                                <option value="">Pilih Bulan</option>
                                <option value="1" {{ (int)$month == 1 ? 'selected' : '' }}>Januari</option>
                                <option value="2" {{ (int)$month == 2 ? 'selected' : '' }}>Februari</option>
                                <option value="3" {{ (int)$month == 3 ? 'selected' : '' }}>Maret</option>
                                <option value="4" {{ (int)$month == 4 ? 'selected' : '' }}>April</option>
                                <option value="5" {{ (int)$month == 5 ? 'selected' : '' }}>Mei</option>
                                <option value="6" {{ (int)$month == 6 ? 'selected' : '' }}>Juni</option>
                                <option value="7" {{ (int)$month == 7 ? 'selected' : '' }}>Juli</option>
                                <option value="8" {{ (int)$month == 8 ? 'selected' : '' }}>Agustus</option>
                                <option value="9" {{ (int)$month == 9 ? 'selected' : '' }}>September</option>
                                <option value="10" {{ (int)$month == 10 ? 'selected' : '' }}>Oktober</option>
                                <option value="11" {{ (int)$month == 11 ? 'selected' : '' }}>November</option>
                                <option value="12" {{ (int)$month == 12 ? 'selected' : '' }}>Desember</option>
                            </select>
                        </div>
                        <div class="me-2 mb-2">
                            <label for="year" class="form-label">Tahun</label>
                            <select id="year" class="form-select select2" name="year">
                                <option value="">Pilih Tahun</option>
                                @for ($i = date('Y'); $i >= now()->subYear(10)->year; $i--)
                                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-file-excel me-1"></i> Submit
                            </button>

                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
            init_table();
            initAssetStatusChart();

            $('#data-table-by-date').DataTable({
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "order": [
                    [0, "asc"]
                ],
            });

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

            $.ajax({
                url: "{{ route('report-sparepart.maintenance-status') }}",
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#active').text(data.active);
                    $('#inactive').text(data.inactive);
                    $('#underMaintenance').text(data.underMaintenance);
                    $('#underRepair').text(data.underRepair);
                    $('#waiting').text(data.waiting);
                    $('#scrap').text(data.scrap);
                    $('#rfu').text(data.rfu);
                    
                    $('#scheduled').text(data.scheduled);
                    $('#inProgress').text(data.inProgress);
                    $('#onHold').text(data.onHold);
                    $('#finish').text(data.finish);
                    $('#overdue').text(data.overdue);
                    $('#underMaintenanceSecondDay').text(data.underMaintenanceSecondDay);
                    $('#percentageItemsYear').text(Math.round(data.percentageItemsYear, 2));
                    $('#percentageItemsWeek').text(Math.round(data.percentageItemsWeek, 2));
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });

        $(document).on('input', '#searchData', function() {
            init_table($(this).val());
        });

        function reloadTableWithFilters() {
            $('#data-table').DataTable().destroy();
            init_table();
        }

        function filterPeriodByDate() {
            console.log('asd');
            
            $("#modal-filter-by-date").modal("show");
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

        function exportExcel() {
            var table = $('#data-table-by-date').DataTable();
 
            // Ambil semua data dari DataTable
            var allData = table.rows({ search: 'applied' }).data().toArray();

            // Buat array untuk header
            var headers = [];
            $('#data-table-by-date thead tr').each(function() {
                var row = [];
                $(this).find('th').each(function() {
                    row.push($(this).text().trim());
                });
                headers.push(row);
            });

            // Gabungkan header dan data
            var exportData = [];
            headers.forEach(header => exportData.push(header)); // Header tabel
            allData.forEach(row => exportData.push(row)); // Data dari DataTable

            // Konversi data ke worksheet Excel
            var worksheet = XLSX.utils.aoa_to_sheet(exportData);
            var workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1');

            // Ekspor workbook ke file Excel
            XLSX.writeFile(workbook, 'Asset Maintenance History by Category {{ \Carbon\Carbon::parse((int)$year."-".(int)$month."-01")->format("F Y") }}.xlsx');
        }

        function init_table() {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var month = $('#month').val();
            var year = $('#year').val();

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
                    url: "{{ route('report-maintenance.data') }}",
                    data: {
                        'month': month,
                        'year': year,
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'asset',
                        name: 'asset'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'last_problem',
                        name: 'last_problem'
                    },
                    {
                        data: 'total_problem',
                        name: 'total_problem'
                    },
                    {
                        data: 'last_duration',
                        name: 'last_duration'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                ]
            });
        }
</script>

<script>
    // Ambil elemen canvas
    const ctx = document.getElementById('lineChart').getContext('2d');

    // Data dan konfigurasi chart
    var postForm = {
        '_token': '{{ csrf_token() }}',
    };
    $.ajax({
        url: '{{ route("report-maintenance.chart") }}', 
        type: 'GET', 
        data : postForm,
        dataType  : 'json',
    })
    .done(function(data) {
        getChart(data);
    })
    .fail(function() {
        alert('Load data failed.');
    });

    function getChart(dataset) {
        const data = {
          labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
          datasets: dataset,
        };
    
        const config = {
          type: 'line',
          data: data,
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'top'
              },
              title: {
                display: true,
                text: 'Top Asset Maintenance Over Time '+ '{{ date('Y') }}'
              }
            },
            scales: {
              x: {
                title: {
                  display: true,
                  text: 'Months'
                }
              },
              y: {
                title: {
                  display: true,
                  text: 'Values'
                }
              }
            }
          }
        };
    
        // Render chart
        new Chart(ctx, config);
    }
</script>

@endpush