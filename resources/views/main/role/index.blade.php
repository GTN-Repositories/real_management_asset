@extends('layouts.global')

@section('title', 'Roles')
@section('title_page', 'Setting / Roles')

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

        <div class="d-flex flex-wrap justify-content-end align-items-end gap-3 mb-4">
            <!-- Tombol Hapus Masal -->
            @if (!auth()->user()->hasRole('Read only'))
                <button type="button" class="btn btn-danger btn-md btn-del-all" id="delete-btn" style="display: none !important;">
                    <i class="fas fa-trash-alt me-2"></i> Hapus Masal
                </button>
                <!-- Tombol Tambah -->
                @if (auth()->user()->hasPermissionTo('role-create'))
                    <button type="button" class="btn btn-primary btn-md btn-add" onclick="createData()">
                        <i class="fas fa-plus me-2"></i> Tambah
                    </button>
                @endif
            @endif
        </div>
        <!-- Role cards -->
        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="datatables table table-striped table-poppins  border-top" id="data-table">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="checkAll" />
                                </div>
                            </th>
                            <th>Name</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <!--/ Role cards -->

        <!-- / Add Role Modal -->
        <div class="modal fade" id="modalAddRole" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body" id="content-modal-add">
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Permission Modal -->
        <div class="modal fade" id="modalEditRole" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body" id="content-modal-edit">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
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
                destroy: true,
                columnDefs: [{
                    target: 0,
                    visible: true,
                    searchable: false
                }, ],

                ajax: {
                    type: "GET",
                    url: "{{ route('role.data') }}",
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'created',
                        name: 'created'
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


        function deleteData(element) {
            var id = $(element).data('id');

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
                    $.ajax({
                        url: "{{ route('role.destroy', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: {
                            '_token': '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.status) {
                                Swal.fire('Deleted!', data.message, 'success');
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                            $('#data-table').DataTable().ajax.reload();
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred while deleting the record.',
                                'error');
                        }
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
                            url: "{{ route('role.destroyAll') }}",
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
                    url: "{{ route('role.create') }}",
                    type: 'GET',
                })
                .done(function(data) {
                    $('#content-modal-add').html(data);
                    $("#modalAddRole").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while creating the record.', 'error');
                });
        }

        function editData(elemen) {
            var id = $(elemen).data('id');
            $.ajax({
                    url: "{{ route('role.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                })
                .done(function(data) {
                    $('#content-modal-edit').html(data);

                    $("#modalEditRole").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while editing the record.', 'error');
                });
        }
    </script>
@endpush
