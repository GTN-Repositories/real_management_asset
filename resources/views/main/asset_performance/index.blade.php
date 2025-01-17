@extends('layouts.global')

@section('title', 'Report Summary')
@section('title_page', 'Report Summary')

@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
        <div class="d-flex justify-content-end gap-3 mb-4">
            <!-- Tombol Hapus Masal -->
            <button type="button" class="btn btn-danger btn-md" id="delete-btn" style="display: none !important;">
                <i class="fas fa-trash-alt me-2"></i> Hapus Masal
            </button>
            <!-- Tombol Tambah -->
            <button type="button" class="btn btn-primary btn-md" onclick="createData()">
                <i class="fas fa-plus me-2"></i> Ubah Target
            </button>
        </div>
        <!-- Product List Table -->
        <div class="card mb-4">
            <div class="card-datatable table-responsive">
                <table class="datatables table table-striped table-poppins " id="data-table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Asset</th>
                            <th>Performance Rate</th>
                            <th>Expenses</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Chart Expenses</h5>
            </div>
            <div class="card-body row">
                <div class="col-12 col-md-6">
                    <div id="chart" style="width: 100%; height: 300px;"></div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="row">
                        <div class="col-6 mb-3" style="border-left: 6px solid #000000;">
                            <h4 class="fw-bold mb-0">Fuel</h4>
                            <span>Fuel</span>
                        </div>
                        <div class="col-6 mb-3" style="border-left: 6px solid #000BE1;">
                            <h4 class="fw-bold mb-0">Taxes</h4>
                            <span>Taxes</span>
                        </div>
                        <div class="col-6 mb-3" style="border-left: 6px solid #FF0004;">
                            <h4 class="fw-bold mb-0">Maintanance</h4>
                            <span>Maintanance</span>
                        </div>
                        <div class="col-6 mb-3" style="border-left: 6px solid #00BD2C;">
                            <h4 class="fw-bold mb-0">Rent</h4>
                            <span>Rent</span>
                        </div>
                        <div class="col-6 mb-3" style="border-left: 6px solid #FABE29;">
                            <h4 class="fw-bold mb-0">Inserance</h4>
                            <span>Incurance</span>
                        </div>
                        <div class="col-6 mb-3" style="border-left: 6px solid #76CADA;">
                            <h4 class="fw-bold mb-0">Depreciate</h4>
                            <span>Depreciate</span>
                        </div>
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

        $(document).ready(function() {
            $.ajax({
                type: 'GET',
                url: "{{ route('report-asset-performance.chart') }}",
                dataType: 'json',
                success: function(data) {
                    var options = {
                        chart: {
                            type: 'pie',
                            width: 400,
                            height: 300,
                        },
                        series: data.series,
                        labels: data.labels,
                        colors: ['#000BE1', '#000000', '#FF0004'],
                        legend: {
                            show: true,
                            position: 'bottom',
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#chart"), options);
                    chart.render();
                }
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
                    url: "{{ route('report-asset-performance.data') }}",
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
                        data: 'asset',
                        name: 'asset'
                    },
                    {
                        data: 'PerformanceRate',
                        name: 'PerformanceRate'
                    },
                    {
                        data: 'Expenses',
                        name: 'Expenses'
                    },
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
                            url: "{{ route('category-item.destroy', ':id') }}".replace(':id', id),
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
                            url: "{{ route('category-item.destroyAll') }}",
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
                    url: "{{ route('report-asset-performance.create') }}",
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
                    url: "{{ route('report-asset-performance.edit', ':id') }}".replace(':id', id),
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
