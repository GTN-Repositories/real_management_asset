@extends('layouts.global')

@section('title', 'Laporan Loadsheet')

@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> Maintenance Report</h4>
        
        <!-- Product List Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Maintenance</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table" id="data-table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Management</th>
                            <th>Asset</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Soil Type</th>
                            <th>BPIT</th>
                            <th>Kilometer</th>
                            <th>Loadsheet</th>
                            <th>Per Load</th>
                            <th>Factor Lose</th>
                            <th>Cubication</th>
                            <th>Price</th>
                            <th>Billing Status</th>
                            <th>Remarks</th>
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
                        data: 'management_project_id',
                        name: 'management_project_id'
                    },
                    {
                        data: 'asset_id',
                        name: 'asset_id'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'soil_type_id',
                        name: 'soil_type_id'
                    },
                    {
                        data: 'bpit',
                        name: 'bpit'
                    },
                    {
                        data: 'kilometer',
                        name: 'kilometer'
                    },
                    {
                        data: 'loadsheet',
                        name: 'loadsheet'
                    },
                    {
                        data: 'perload',
                        name: 'perload'
                    },
                    {
                        data: 'lose_factor',
                        name: 'lose_factor'
                    },
                    {
                        data: 'cubication',
                        name: 'cubication'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'billing_status',
                        name: 'billing_status'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
                    },
                ]
            });
        }

        function exportExcel() {
            const startDate = $('#date-range-picker').data('daterangepicker')?.startDate?.format('YYYY-MM-DD');
            const endDate = $('#date-range-picker').data('daterangepicker')?.endDate?.format('YYYY-MM-DD');
            const predefinedFilter = $('.dropdown-item.active').text().trim() || '';

            if (startDate && endDate) {
                $.ajax({
                    url: "{{ route('report-loadsheet.export-excel') }}",
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                        startDate: startDate,
                        endDate: endDate,
                        predefinedFilter: predefinedFilter
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        const blob = new Blob([response], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = 'Loadsheet.xlsx';
                        link.click();
                    },
                    error: function() {
                        Swal.fire('Error!',
                            'An error occurred while exporting the report. Please try again later.',
                            'error');
                    }
                });
            } else {
                $.ajax({
                    url: "{{ route('report-loadsheet.export-excel') }}",
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                        startDate: moment().startOf('month').format('YYYY-MM-DD'),
                        endDate: moment().endOf('month').format('YYYY-MM-DD'),
                        predefinedFilter: predefinedFilter
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        const blob = new Blob([response], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = 'Loadsheet.xlsx';
                        link.click();
                    },
                    error: function() {
                        Swal.fire('Error!',
                            'An error occurred while exporting the report. Please try again later.',
                            'error');
                    }
                });
            }
        }
    </script>
@endpush
