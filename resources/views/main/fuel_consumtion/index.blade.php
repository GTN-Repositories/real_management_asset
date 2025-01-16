@extends('layouts.global')

@section('title', 'Fuel Consumtion')
@section('title_page', 'Tracking and Monitoring / Fuel Consumtion')

@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
        <div class="d-flex justify-content-end gap-3 mb-3">
            @if (!auth()->user()->hasRole('Read only'))
                <!-- Tombol Hapus Masal -->
                <button type="button" class="btn btn-danger btn-md" id="delete-btn" style="display: none !important;">
                    <i class="fas fa-trash-alt"></i> Hapus Masal
                </button>
                @if (auth()->user()->hasPermissionTo('fuel-import-excel'))
                    <button onclick="importExcel()" class="btn btn-success btn-md">
                        <i class="fa-solid fa-file-excel me-2"></i>Import Excel
                    </button>
                @endif
                @if (auth()->user()->hasPermissionTo('fuel-export-excel'))
                    <button onclick="exportExcel()" class="btn btn-success btn-md">
                        <i class="fa-solid fa-file-excel me-2"></i>Export Excel
                    </button>
                @endif
                @if (auth()->user()->hasPermissionTo('fuel-request'))
                    <button type="button" class="btn btn-warning btn-md" onclick="createDataRequest()">
                        <i class="fas fa-plus me-2"></i> Request Fuel
                    </button>
                @endif
                <!-- Tombol Tambah -->
                @if (auth()->user()->hasPermissionTo('fuel-create'))
                    <button type="button" class="btn btn-primary btn-md" onclick="createData()">
                        <i class="fas fa-plus me-2"></i> Tambah
                    </button>
                @endif
            @endif
        </div>
        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="datatables table table-striped table-poppins " id="data-table">
                    <thead class="border-top">
                        <tr>
                            <th>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="checkAll" />
                                </div>
                            </th>
                            <th>Nama Project</th>
                            <th>Nama Aset</th>
                            <th>Pengemudi</th>
                            <th>Tanggal</th>
                            <th>Banyak Penggunaan</th>
                            <th>HM</th>
                            {{-- <th>Loadsheet</th> --}}
                            {{-- <th>Harga/Liter</th> --}}
                            <th>Kategori</th>
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
        });

        $(document).on('input', '#searchData', function() {
            init_table($(this).val());
        })

        function init_table(keyword = '') {
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
                    url: "{{ route('fuel.data') }}",
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
                        data: 'management_project_id',
                        name: 'management_project_id'
                    },
                    {
                        data: 'asset_id',
                        name: 'asset_id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'liter',
                        name: 'liter'
                    },
                    {
                        data: 'hm',
                        name: 'hm'
                    },
                    // {
                    //     data: 'loadsheet',
                    //     name: 'loadsheet'
                    // },
                    // {
                    //     data: 'price',
                    //     name: 'price'
                    // },
                    {
                        data: 'category',
                        name: 'category'
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
                            url: "{{ route('fuel.destroy', ':id') }}".replace(':id', id),
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
                            url: "{{ route('fuel.destroyAll') }}",
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

        function createDataRequest() {
            $.ajax({
                    url: "{{ route('fuel-ipb.create') }}",
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

        function createData() {
            $.ajax({
                    url: "{{ route('fuel.create') }}",
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
                    url: "{{ route('fuel.edit', ':id') }}".replace(':id', id),
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

        function importExcel() {
            $.ajax({
                    url: "{{ route('fuel.import') }}",
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
                "{{ route('fuel.export-excel') }}?startDate=" + startDate + "&endDate=" + endDate;

            window.open(url);
        }
    </script>
@endpush
