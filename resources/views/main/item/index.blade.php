@extends('layouts.global')

@section('title', 'Barang')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory /</span> Barang</h4>

    <!-- Product List Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Barang</h5>
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
            <table class="datatables table" id="data-table">
                <thead class="border-top">
                    <tr>
                        <th>
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="checkAll" />
                            </div>
                        </th>
                        <th>Part Number</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Kode Barang</th>
                        <th>Status</th>
                        <th>Ukuran</th>
                        <th>Merek</th>
                        <th>Warna</th>
                        <th>Dibuat Pada</th>
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
                }else{
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
                processing:true,
                serverSide:true,
                columnDefs: [
                    {
                        target: 0,
                        visible: true,
                        searchable: false
                    },
                ],

                ajax: {type: "GET", url: "{{ route('item.data') }}", data:{'keyword':keyword}},
                columns: [
                    {data: 'id', name: 'id', orderable: false, searchable: false},
                    {data: 'part',name: 'part'},
                    {data: 'image',name: 'image'},
                    {data: 'name',name: 'name'},
                    {data: 'category',name: 'category'},
                    {data: 'code',name: 'code'},
                    {data: 'status',name: 'status'},
                    {data: 'size',name: 'size'},
                    {data: 'brand',name: 'brand'},
                    {data: 'color',name: 'color'},
                    {data: 'created_at',name: 'created_at'},
                    {data: 'action',name: 'action',orderable: false,searchable: false}
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
                        url: "{{ route('item.destroy', ':id') }}".replace(':id', id),
                        type: 'POST',
                        data : postForm,
                        dataType  : 'json',
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
                        url: "{{ route('item.destroyAll') }}",
                        type: 'POST',
                        data : postForm,
                        dataType  : 'json',
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
                url: "{{ route('item.create') }}",
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
                url: "{{ route('item.edit', ':id') }}".replace(':id', id),
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
