@extends('layouts.global')

@section('title', 'Loadsheet')
@section('title_page', 'Tracking and Monitoring / Loadsheet')

@push('css')
    <style>
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

        <!-- Product List Table -->
        <div class="d-flex flex-wrap justify-content-end align-items-end gap-3 mb-4">
            <!-- Tombol Hapus Masal -->
            @if (!auth()->user()->hasRole('Read only'))
                <button type="button" class="btn btn-danger btn-md btn-del-all" id="delete-btn" style="display: none !important;">
                    <i class="fas fa-trash-alt me-2"></i> Hapus Masal
                </button>
                @if (auth()->user()->hasPermissionTo('loadsheet-import-excel'))
                    <button onclick="importExcel()" class="btn btn-success btn-md btn-asset">
                        <i class="fa-solid fa-file-excel me-2"></i>Import Excel
                    </button>
                @endif
                @if (auth()->user()->hasPermissionTo('loadsheet-export-excel'))
                    <button onclick="exportExcel()" class="btn btn-success btn-md btn-asset">
                        <i class="fa-solid fa-file-excel me-2"></i>Export Excel
                    </button>
                @endif
                <!-- Tombol Tambah -->
                @if (auth()->user()->hasPermissionTo('loadsheet-create'))
                    <button type="button" class="btn btn-primary btn-md btn-add" onclick="createData()">
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
                            <th>Management</th>
                            <th>Asset</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Soil Type</th>
                            <th>Loading Area</th>
                            <th>Kilometer</th>
                            <th>Load/Ritase</th>
                            <th>Tonase/Kubikasi</th>
                            @if (session('selected_project_id'))
                                @if (\App\Helpers\Helper::projectSelected()->calculation_method == 'Kubic')
                                    <th>Lose Factor</th>
                                @elseif (\App\Helpers\Helper::projectSelected()->calculation_method == 'Tonase')
                                    <th>RF</th>
                                @endif
                            @else
                                <th>Lose Factor/RF</th>
                            @endif
                            <th>Cubication</th>
                            <th>Price</th>
                            <th>Billing Status</th>
                            <th>Remarks</th>
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
                    url: "{{ route('loadsheet.data') }}",
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
                            url: "{{ route('loadsheet.destroy', ':id') }}".replace(':id', id),
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
                            url: "{{ route('loadsheet.destroyAll') }}",
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
                    url: "{{ route('loadsheet.create') }}",
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
                    url: "{{ route('loadsheet.edit', ':id') }}".replace(':id', id),
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
                    url: "{{ route('loadsheet.import') }}",
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
                "{{ route('loadsheet.export-excel') }}?startDate=" + startDate + "&endDate=" + endDate;

            window.open(url);
        }
    </script>
@endpush
