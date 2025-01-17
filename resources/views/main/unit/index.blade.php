@extends('layouts.global')

@section('title', 'Asset')
@section('title_page', 'Master Data / Asset')

@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
        <div class="d-flex justify-content-end align-items-end gap-3 mb-4">
            <div id="categoryParent" style="max-width: 200px; width: 100%;">
                <label class="form-label" for="category">Kategori</label>
                <select name="category[]" id="category_id" class="select2 form-select" data-allow-clear="true" multiple
                    required>
                </select>
            </div>

            <div id="assets_locationParent" style="max-width: 200px; width: 100%;">
                <label class="form-label" for="assets_location">Lokasi Aset</label>
                <select name="assets_location[]" id="assets_location_id" class="form-select select2" multiple>
                </select>
            </div>

            <div id="picParent" style="max-width: 200px; width: 100%;">
                <label class="form-label" for="pic">Asset Manager</label>
                <select id="pic_id" name="manager[]" class="select2 form-select" data-allow-clear="true" multiple
                    required>
                </select>
            </div>

            <div id="statusParent" style="max-width: 200px; width: 100%;">
                <label class="form-label" for="status">Status</label>
                <select name="status[]" id="asset_status_id" class="select2 form-select" data-allow-clear="true" multiple>
                </select>
            </div>
            <!-- Tombol Hapus Masal -->
            @if (!auth()->user()->hasRole('Read only'))
                <button type="button" class="btn btn-danger btn-md" id="delete-btn" style="display: none !important;">
                    <i class="fas fa-trash-alt"></i> Hapus Masal
                </button>
            @endif
            <!-- Tombol Tambah -->
            @if (auth()->user()->hasPermissionTo('asset-import-excel'))
                @if (!auth()->user()->hasRole('Read only'))
                    <button type="button" class="btn btn-success btn-md d-flex align-items-center" onclick="importExcel()">
                        <i class="fas fa-file-excel me-2"></i> Import Excel
                    </button>
                @endif
            @endif
            @if (auth()->user()->hasPermissionTo('asset-export-excel'))
                @if (!auth()->user()->hasRole('Read only'))
                    <button onclick="exportExcel()" class="btn btn-success btn-md">
                        <i class="fa-solid fa-file-excel me-1"></i>Export Excel
                    </button>
                @endif
            @endif
            @if (auth()->user()->hasPermissionTo('asset-create'))
                @if (!auth()->user()->hasRole('Read only'))
                    <button type="button" class="btn btn-primary" onclick="createData()"><i
                            class="fas fa-plus me-1"></i>Tambah</button>
                @endif
            @endif
        </div>
        <!-- Product List Table -->
        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="datatables table table-striped table-poppins" id="data-table">
                    <thead class="border-top">
                        <tr>
                            <th>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="checkAll" />
                                </div>
                            </th>
                            <th>Gambar</th>
                            <th>ID</th>
                            <th>Kategori</th>
                            <th>Merek</th>
                            <th>Unit</th>
                            <th>Tipe</th>
                            <th>Nopol</th>
                            <th>Serial Number</th>
                            <th>Classification</th>
                            <th>No Rangka</th>
                            <th>No Mesin</th>
                            <th>NIK</th>
                            <th>Warna</th>
                            <th>Asset Manager</th>
                            <th>Assign to Project</th>
                            <th>Location</th>
                            <th>PIC</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="modal fade" id="createApp" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-simple modal-upgrade-plan">
                <div class="modal-content">
                    <div class="modal-body" id="content-modal-create">

                    </div>
                </div>
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
    <script>
        $(document).ready(function() {
            $('#category_id').select2({
                dropdownParent: $('#categoryParent'),
                placeholder: 'Pilih Kategori',
                ajax: {
                    url: "{{ route('asset.data') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            keyword: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.reduce((unique, item) => {
                                if (!unique.some((i) => i.text === item.category)) {
                                    unique.push({
                                        text: item.category,
                                        id: item.category
                                    });
                                }
                                return unique;
                            }, [])
                        };
                    },
                    cache: true
                }
            });

            $('#assets_location_id').select2({
                dropdownParent: $('#assets_locationParent'),
                placeholder: 'Pilih lokasi',
                ajax: {
                    url: "{{ route('asset.data') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            keyword: params.term,
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.reduce((unique, item) => {
                                if (!unique.some((i) => i.text === item.assets_location)) {
                                    unique.push({
                                        text: item.assets_location,
                                        id: item.assets_location
                                    });
                                }
                                return unique;
                            }, [])
                        };
                    },
                    cache: true
                }
            });

            $('#asset_status_id').select2({
                dropdownParent: $('#statusParent'),
                placeholder: 'Pilih status',
                ajax: {
                    url: "{{ route('asset.data') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            keyword: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.reduce((unique, item) => {
                                if (!unique.some((i) => i.text === item.status)) {
                                    unique.push({
                                        text: item.status,
                                        id: item.status
                                    });
                                }
                                return unique;
                            }, [])
                        };
                    },
                    cache: true
                }
            });


            $('#pic_id').select2({
                dropdownParent: $('#picParent'),
                placeholder: 'Pilih Manager',
                ajax: {
                    url: "{{ route('asset.data') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            keyword: params.term,
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.reduce((unique, item) => {
                                if (!unique.some((i) => i.text === item.manager)) {
                                    unique.push({
                                        text: item.manager,
                                        id: item.manager
                                    });
                                }
                                return unique;
                            }, [])
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            init_table();

            $('#category_id, #assets_location_id, #pic_id, #asset_status_id').on('change', function() {
                init_table();
            });

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
            var category = $('#category_id').val();
            var assets_location = $('#assets_location_id').val();
            var pic = $('#pic_id').val();
            var status = $('#asset_status_id').val();

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
                }],
                ajax: {
                    type: "GET",
                    url: "{{ route('asset.data') }}",
                    data: {
                        'keyword': keyword,
                        'category': category,
                        'assets_location': assets_location,
                        'pic': pic,
                        'status': status,
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'noDecryptId',
                        name: 'noDecryptId'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'license_plate',
                        name: 'license_plate'
                    },
                    {
                        data: 'serial_number',
                        name: 'serial_number'
                    },
                    {
                        data: 'classification',
                        name: 'classification'
                    },
                    {
                        data: 'chassis_number',
                        name: 'chassis_number'
                    },
                    {
                        data: 'machine_number',
                        name: 'machine_number'
                    },
                    {
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        data: 'color',
                        name: 'color'
                    },
                    {
                        data: 'owner',
                        name: 'owner'
                    },
                    {
                        data: 'management_project',
                        name: 'management_project'
                    },
                    {
                        data: 'assets_location',
                        name: 'assets_location'
                    },
                    {
                        data: 'pic',
                        name: 'pic'
                    },
                    {
                        data: 'status',
                        name: 'status'
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
                            url: "{{ route('asset.destroy', ':id') }}".replace(':id', id),
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
                            url: "{{ route('asset.destroyAll') }}",
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
                    url: "{{ route('asset.create') }}",
                    type: 'GET',
                })
                .done(function(data) {
                    $('#content-modal-create').html(data);

                    $("#createApp").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while creating the record.', 'error');
                });
        }

        function importExcel() {
            $.ajax({
                    url: "{{ route('asset.import.form') }}",
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
                    url: "{{ route('asset.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                })
                .done(function(data) {
                    $('#content-modal-create').html(data);

                    $("#createApp").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while editing the record.', 'error');
                });
        }

        function detailData(id) {

            $.ajax({
                    url: "{{ route('asset.show', ':id') }}".replace(':id', id),
                    type: 'GET',
                })
                .done(function(data) {
                    window.location.href = "{{ route('asset.show', ':id') }}".replace(':id', id);
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while editing the record.', 'error');
                });
        }

        function exportExcel() {
            const startDate = $('#date-range-picker').data('daterangepicker')?.startDate?.format('YYYY-MM-DD');
            const endDate = $('#date-range-picker').data('daterangepicker')?.endDate?.format('YYYY-MM-DD');
            const predefinedFilter = $('.dropdown-item.active').text().trim() || '';

            if (startDate && endDate) {
                $.ajax({
                    url: "{{ route('asset.export.excel') }}",
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
                        link.download = 'Export Asset.xlsx';
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
                    url: "{{ route('asset.export.excel') }}",
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
                        link.download = 'Export Asset.xlsx';
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
