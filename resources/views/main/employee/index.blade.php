@extends('layouts.global')

@section('title', 'Karyawan')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Data Karyawan</h4>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6" id="job_titleParent">
                        <label class="form-label" for="job_title">Jabatan</label>
                        <select name="job_title[]" id="job_title" class="select2 form-select" data-allow-clear="true"
                            multiple required>
                        </select>
                    </div>
                    <div class="col-12 col-md-6" id="management_projectParent">
                        <label class="form-label" for="management_project_title">Management Project</label>
                        <select name="management_project_title[]" id="management_project_title" class="select2 form-select"
                            data-allow-clear="true" multiple required>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product List Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Karyawan</h5>
                <div class="d-flex justify-content-end gap-2">
                    <!-- Tombol Hapus Masal -->
                    <button type="button" class="btn btn-danger btn-sm" id="delete-btn" style="display: none !important;">
                        <i class="fas fa-trash-alt"></i> Hapus Masal
                    </button>
                    <!-- Tombol Tambah -->
                    @if (auth()->user()->hasPermissionTo('employee-create'))
                        <button type="button" class="btn btn-primary btn-sm" onclick="createData()">
                            <i class="fas fa-plus"></i> Tambah
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
                            <th>Nama Karyawan</th>
                            <th>Jabatan</th>
                            <th>Project</th>
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
            $('#job_title').select2({
                dropdownParent: $('#job_titleParent'),
                placeholder: 'Pilih Kategori',
                ajax: {
                    url: "{{ route('job-title.data') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            'search[value]': params.term,
                            start: 0,
                            length: 10
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.relationId,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#management_project_title').select2({
                dropdownParent: $('#management_projectParent'),
                placeholder: 'Pilih Project',
                ajax: {
                    url: "{{ route('management-project.data') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            'search[value]': params.term,
                            start: 0,
                            length: 10
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.managementRelationId,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#job_title').on('change', function() {
                filter();
            });
            $('#management_project_title').on('change', function() {
                filter();
            });

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
            filter();
        })

        function filter() {
            var keyword = $('#searchData').val();
            var job_title_id = $('#job_title').val();
            var management_project_id = $('#management_project_title').val();

            init_table(keyword, job_title_id, management_project_id);
        }

        function init_table(keyword = '', job_title_id = [], management_project_id = []) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            if ($.fn.DataTable.isDataTable('#data-table')) {
                $('#data-table').DataTable().clear().destroy();
            }

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
                    url: "{{ route('employee.data') }}",
                    data: {
                        'keyword': keyword,
                        'job_title_id': job_title_id,
                        'management_project_id': management_project_id,
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
                        data: 'job_title',
                        name: 'job_title'
                    },
                    {
                        data: 'management_project_id',
                        name: 'management_project_id'
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
                            url: "{{ route('employee.destroy', ':id') }}".replace(':id', id),
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
                            url: "{{ route('employee.destroyAll') }}",
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
                    url: "{{ route('employee.create') }}",
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
                    url: "{{ route('employee.edit', ':id') }}".replace(':id', id),
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
    </script>
@endpush
