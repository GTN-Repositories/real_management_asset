@extends('layouts.global')

@section('title', 'Laporan Loadsheet')

@section('content')
<div class="mx-5 flex-grow-1 container-p-y">
    <div class="card mb-4">
        <div class="card-header flex-nowrap header-elements">
            <h5 class="card-title mb-0">Top Asset Maintenance Over Time</h5>
            <div class="card-header-elements ms-auto py-0 d-none d-sm-block">
            </div>
        </div>
        <div class="card-body pt-2">
            <canvas id="lineChart"></canvas>
        </div>
    </div>

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
            <table class="datatables table table-striped table-poppins" id="data-table-by-date">
                <thead class="border-top">
                    <tr>
                        <th rowspan="2" class="text-center align-middle">Asset ID</th>
                        <th colspan="{{ $daysInMonth }}" class="text-center">{{
                            \Carbon\Carbon::parse($month)->format('F') }}</th>
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
                            <td>{{ 'AST - '.$item['asset_id'] }}</td>
                            @foreach ($item['data'] as $value)
                                <td style="background-color: {{ $color[$value] ?? '#FFFFFF' }}; color: {{ ($value == 'Uncertain') ? '#000000' : '#FFFFFF' }};" class="text-center">{{ $value }}</td>
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

                <div class="row">
                    <div class="me-2 mb-2">
                        <label for="month" class="form-label">Bulan</label>
                        <select id="month" class="form-select select2">
                            <option value="">Pilih Bulan</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    <div class="me-2 mb-2">
                        <label for="year" class="form-label">Tahun</label>
                        <select id="year" class="form-select select2">
                            <option value="">Pilih Tahun</option>
                            @for ($i = date('Y'); $i >= 1985; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
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
            XLSX.writeFile(workbook, 'data-export.xlsx');
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