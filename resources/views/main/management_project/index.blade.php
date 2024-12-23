@extends('layouts.global')

@section('title', 'Management Project')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> Management Project</h4>
        <div class="d-flex justify-content-end align-items-end mb-3 gap-3">
            <div>
                <label for="date-range-picker" class="form-label">filter dengan jangka waktu</label>
                <input type="text" id="date-range-picker" class="form-control" placeholder="Select Date Range">
            </div>
        </div>
        <!-- Product List Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Management Project</h5>
                <div class="d-flex justify-content-end gap-2">
                    <!-- Tombol Hapus Masal -->
                    <button type="button" class="btn btn-danger btn-sm" id="delete-btn" style="display: none !important;">
                        <i class="fas fa-trash-alt"></i> Hapus Masal
                    </button>
                    @if (auth()->user()->hasPermissionTo('management-project-import-excel'))
                        <button onclick="importExcel()" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-file-excel me-1"></i>Import Excel
                        </button>
                    @endif
                    @if (auth()->user()->hasPermissionTo('management-project-export-excel'))
                        <button onclick="exportExcel()" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-file-excel me-1"></i>Export Excel
                        </button>
                    @endif
                    <!-- Tombol Tambah -->
                    @if (auth()->user()->hasPermissionTo('management-project-create'))
                        <button type="button" class="btn btn-primary btn-sm" onclick="createData()">
                            <i class="fas fa-plus"></i> Tambah
                        </button>
                    @endif
                    @if (auth()->user()->hasPermissionTo('management-project-request'))
                        <button type="button" class="btn btn-warning btn-sm" onclick="requestPettyCash()">
                            <i class="fas fa-dollar"></i> Request Petty Cash
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table" id="data-table">
                    <thead class="border-top">
                        <tr>
                            <th>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="checkAll" />
                                </div>
                            </th>
                            <th>ID</th>
                            <th>nama project</th>
                            <th>nama asset</th>
                            <th>tanggal awal</th>
                            <th>tanggal akhir</th>
                            <th>Nilai Project</th>
                            <th>metode</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="modal fade" id="modal-ce" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body" id="content-modal-ce">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            init_table();

            $('#checkAll').on('click', function() {
                $('tbody input[type="checkbox"]').prop('checked', $(this).prop('checked'));

                if ($('tbody input[type="checkbox"]:checked').length > 0) {
                    $('#delete-btn').attr('style', 'display: inline-block !important;');
                } else {
                    $('#delete-btn').attr('style', 'display: none !important;');
                }
            });

            $('tbody').on('click', 'input[type="checkbox"]', function() {
                if ($('tbody input[type="checkbox"]:checked').length > 0) {
                    $('#delete-btn').attr('style', 'display: inline-block !important;');
                } else {
                    $('#delete-btn').attr('style', 'display: none !important;');
                }
            });

            $('#delete-btn').on('click', function() {
                var elem = $('tbody input[type="checkbox"]:checked');
                var ids = [];
                elem.map(function() {
                    ids.push($(this).val());
                });

                bulkDelete(ids);
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

        function reloadTableWithFilters(startDate = '', endDate = '', predefinedFilter = '') {
            $('#data-table').DataTable().destroy();
            init_table(startDate, endDate, predefinedFilter);
        }

        $(document).on('input', '#searchData', function() {
            init_table($(this).val());
        })

        function init_table(startDate = '', endDate = '', predefinedFilter = '', keyword = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('management-project.data') }}",
                    data: {
                        'keyword': keyword,
                        'startDate': startDate,
                        'endDate': endDate,
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'format_id',
                        name: 'format_id'
                    },
                    {
                        data: 'name',
                        name: 'name'
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
                        data: 'value_project',
                        name: 'value_project'
                    },
                    {
                        data: 'calculation_method',
                        name: 'calculation_method'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        }

        function deleteData(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this record!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var postForm = {
                        '_token': '{{ csrf_token() }}',
                        '_method': 'DELETE',
                    };
                    $.ajax({
                            url: "{{ route('management-project.destroy', ':id') }}".replace(':id', id),
                            type: 'POST',
                            data: postForm,
                            dataType: 'json',
                        })
                        .done(function(data) {
                            Swal.fire('Deleted!', data['message'], 'success');
                            $('#data-table').DataTable().ajax.reload();
                        })
                        .fail(function() {
                            Swal.fire('Error!', 'An error occurred while deleting the record.', 'error');
                        });
                }
            });
        }

        function bulkDelete(ids) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this record!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var postForm = {
                        '_token': '{{ csrf_token() }}',
                        '_method': 'DELETE',
                        'ids': ids
                    };
                    $.ajax({
                            url: "{{ route('management-project.destroyAll') }}",
                            type: 'POST',
                            data: postForm,
                            dataType: 'json',
                        })
                        .done(function(data) {
                            Swal.fire('Deleted!', data['message'], 'success');
                            $('#data-table').DataTable().ajax.reload();
                        })
                        .fail(function() {
                            Swal.fire('Error!', 'An error occurred while deleting the record.', 'error');
                        });
                }
            });
        }

        function createData() {
            $.ajax({
                    url: "{{ route('management-project.create') }}",
                    type: 'GET',
                })
                .done(function(data) {
                    $('#content-modal-ce').html(data);

                    $("#modal-ce").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while creating the record.', 'error');
                });
        }

        function editData(id) {

            $.ajax({
                    url: "{{ route('management-project.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                })
                .done(function(data) {
                    $('#content-modal-ce').html(data);

                    $("#modal-ce").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while editing the record.', 'error');
                });
        }

        function requestPettyCash() {
            $.ajax({
                    url: "{{ route('management-project.todoRequestPettyCash') }}",
                    type: 'GET',
                })
                .done(function(data) {
                    $('#content-modal-ce').html(data);

                    $("#modal-ce").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while creating the record.', 'error');
                });
        }

        function detailData(id) {
            $.ajax({
                    url: "{{ route('management-project.show', ':id') }}".replace(':id', id),
                    type: 'GET',
                })
                .done(function(data) {
                    window.location.href = "{{ route('management-project.show', ':id') }}".replace(':id', id);
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while editing the record.', 'error');
                });
        }

        function importExcel() {
            $.ajax({
                    url: "{{ route('management-project.import.form') }}",
                    type: 'GET',
                })
                .done(function(data) {
                    $('#content-modal-ce').html(data);

                    $("#modal-ce").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while creating the record.', 'error');
                });
        }

        function exportExcel() {
            const startDate = $('#date-range-picker').data('daterangepicker')?.startDate?.format('YYYY-MM-DD');
            const endDate = $('#date-range-picker').data('daterangepicker')?.endDate?.format('YYYY-MM-DD');
            const predefinedFilter = $('.dropdown-item.active').text().trim() || '';

            var url =
                "{{ route('management-project.export.excel') }}?startDate=" + startDate + "&endDate=" + endDate;

            window.open(url);
        }
    </script>
@endpush
