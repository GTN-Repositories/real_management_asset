@extends('layouts.global')

@section('title', 'Menu')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="mb-4">Menu List</h4>

        <p class="mb-4">
            Each category (Basic, Professional, and Business) includes the four predefined roles shown below.
        </p>

        <!-- Menu Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title mb-0">Menu</h5>
                <div class="d-flex justify-content-end gap-2">
                    <!-- Tombol Hapus Masal -->
                    <button type="button" class="btn btn-danger btn-sm" id="delete-btn" style="display: none !important;">
                        <i class="fas fa-trash-alt"></i> Hapus Masal
                    </button>
                    <!-- Tombol Tambah -->
                    <button type="button" class="btn btn-primary btn-sm" onclick="createData()">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table border-top" id="data-table">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="checkAll" />
                                </div>
                            </th>
                            <th>Nama</th>
                            <th>Icon</th>
                            <th>Urutan</th>
                            <th>URL</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <!--/ Menu Table -->

        <!-- Modal -->
        <!-- Add Menu Modal -->
        <div class="modal fade" id="modalAddMenu" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-edit-user">
              <div class="modal-content p-3 p-md-5">
                <div class="modal-body" id="content-modal-add">
                </div>
              </div>
            </div>
          </div>
        <!--/ Add Menu Modal -->

        <!-- Edit Menu Modal -->
        <div class="modal fade" id="modalEditMenu" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-edit-user">
              <div class="modal-content p-3 p-md-5">
                <div class="modal-body" id="content-modal-edit">
                </div>
              </div>
            </div>
          </div>
        <!--/ Edit Menu Modal -->

        <!-- /Modal -->
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
                    url: "{{ route('menu.data') }}",
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
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'icon',
                        name: 'icon'
                    },
                    {
                        data: 'urutan',
                        name: 'urutan'
                    },
                    {
                        data: 'url',
                        name: 'url'
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
                        url: "{{ route('menu.destroy', ':id') }}".replace(':id', id),
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
                            url: "{{ route('menu.destroyAll') }}",
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
                    url: "{{ route('menu.create') }}",
                    type: 'GET',
                })
                .done(function(data) {
                    $('#content-modal-add').html(data);
                    $("#modalAddMenu").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while creating the record.', 'error');
                });
        }

        function editData(elemen) {
            var id = $(elemen).data('id');
            $.ajax({
                    url: "{{ route('menu.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                })
                .done(function(data) {
                    $('#content-modal-edit').html(data);

                    $("#modalEditMenu").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while editing the record.', 'error');
                });
        }
    </script>
@endpush