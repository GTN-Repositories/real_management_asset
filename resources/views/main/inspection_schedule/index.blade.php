@extends('layouts.global')

@section('title', 'Maintenance')
@section('title_page', 'Tracking and Monitoring / Inspection Schedule')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-calendar.css') }}" />

    <style>
        .cke_notifications_area{
            display: none !important;
        }
    </style>
@endpush

@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
        <div class="d-flex justify-content-end mb-3 gap-2">
            <!-- Tombol Hapus Masal -->
            {{-- <button type="button" class="btn btn-danger btn-sm" id="delete-btn"
                style="display: none !important;">
                <i class="fas fa-trash-alt"></i> Hapus Masal
            </button> --}}
            @if (auth()->user()->hasPermissionTo('inspection-schedule-create'))
                <button type="button" class="btn btn-primary btn-md" onclick="createData()">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            @endif
        </div>
        <div class="card my-3 mb-3">
            <div class="card-datatable table-responsive">
                <table class="datatables table" id="data-table">
                    <thead class="border-top">
                        <tr>
                            <th>
                                #
                            </th>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Type</th>
                            <th>Catatan</th>
                            <th>Management Project</th>
                            <th>Asset</th>
                            <th>Tanggal</th>
                            <th>Gudang</th>
                            <th>Item</th>
                            <th>Stok</th>
                            <th>Stok Kanibal</th>
                            <th>Asset Kanibal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="card app-calendar-wrapper">
            <div class="row g-0">
                <!-- Calendar Sidebar -->
                <div class="col app-calendar-sidebar" id="app-calendar-sidebar">
                    <div class="border-bottom p-4 my-sm-0 mb-3">
                        <div class="d-grid">
                            @if (auth()->user()->hasPermissionTo('inspection-schedule-create-maintenance'))
                                <button class="btn btn-primary btn-toggle-sidebar" onclick="createDataMaintenance()">
                                    <i class="ti ti-plus me-1"></i>
                                    <span class="align-middle">Tambah Maintenance</span>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="p-3">
                        <!-- inline calendar (flatpicker) -->
                        <div class="inline-calendar"></div>

                        <hr class="container-m-nx mb-4 mt-3" />

                        <!-- Filter -->
                        <div class="mb-3 ms-3">
                            <small class="text-small text-muted text-uppercase align-middle">Filter</small>
                        </div>

                        <div class="form-check mb-2 ms-3">
                            <input class="form-check-input select-all" type="checkbox" id="selectAll" data-value="all"
                                checked />
                            <label class="form-check-label" for="selectAll">View All</label>
                        </div>

                        <div class="app-calendar-events-filter ms-3">
                            <div class="form-check form-check-info">
                                <input class="form-check-input input-filter" type="checkbox" id="select-p2h"
                                    data-value="p2h" checked />
                                <label class="form-check-label" for="select-p2h">P2H</label>
                            </div>
                            <div class="form-check form-check-danger mb-2">
                                <input class="form-check-input input-filter" type="checkbox" id="select-pmcheck"
                                    data-value="pmcheck" checked />
                                <label class="form-check-label" for="select-pmcheck">PM Check</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Calendar Sidebar -->

                <!-- Calendar & Modal -->
                <div class="col app-calendar-content">
                    <div class="card shadow-none border-0">
                        <div class="card-body pb-0">
                            <!-- FullCalendar -->
                            <div id="calendar"></div>
                        </div>
                    </div>
                    <div class="app-overlay"></div>
                    <!-- FullCalendar Offcanvas -->
                    <div class="offcanvas offcanvas-end event-sidebar" tabindex="-1" id="addEventSidebar"
                        aria-labelledby="addEventSidebarLabel">
                        <div class="offcanvas-header my-1">
                            <h5 class="offcanvas-title" id="addEventSidebarLabel">Add Event</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body pt-0">
                            <form class="event-form pt-0" id="eventForm" onsubmit="return false">
                                <div class="mb-3">
                                    <label class="form-label" for="eventTitle">Judul Inspeksi</label>
                                    <input type="text" class="form-control" id="eventTitle" name="eventTitle"
                                        placeholder="Event Title" />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="eventLabel">Jenis Inspeksi</label>
                                    <select class="select2 select-event-label form-select" id="eventLabel"
                                        name="eventLabel">
                                        <option data-label="primary" value="P2H" selected>P2H</option>
                                        <option data-label="success" value="pmcheck">PM CHECK</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="eventDescription">Description</label>
                                    <textarea class="form-control" name="eventDescription" id="eventDescription"></textarea>
                                </div>
                                <div class="mb-3 d-flex justify-content-sm-between justify-content-start my-4">
                                    <div>
                                        <button type="submit"
                                            class="btn btn-primary btn-add-event me-sm-3 me-1">Add</button>
                                        <button type="reset" class="btn btn-label-secondary btn-cancel me-sm-0 me-1"
                                            data-bs-dismiss="offcanvas">
                                            Cancel
                                        </button>
                                    </div>
                                    <div><button class="btn btn-label-danger btn-delete-event d-none">Delete</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /Calendar & Modal -->
            </div>
        </div>

        <div class="modal fade" id="modal-ce" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-simple">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body" id="content-modal-ce">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>

    <!-- Page JS -->
    {{-- <script src="{{ asset('assets/js/app-calendar-events.js') }}"></script> --}}
    <script src="{{ asset('assets/js/app-calendar.js?update=13') }}"></script>
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
                    url: "{{ route('inspection-schedule.data') }}",
                    data: {
                        'keyword': keyword,
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
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
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'note',
                        name: 'note'
                    },
                    {
                        data: 'managementProject',
                        name: 'managementProject',
                    },
                    {
                        data: 'asset_id',
                        name: 'asset_id',
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'werehouse_id',
                        name: 'werehouse_id'
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'item_stock',
                        name: 'item_stock'
                    },
                    {
                        data: 'kanibal_stock',
                        name: 'kanibal_stock'
                    },
                    {
                        data: 'asset_kanibal_name',
                        name: 'asset_kanibal_name'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ]
            });
        }

        function createData() {
            $.ajax({
                    url: "{{ route('inspection-schedule.create') }}",
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
                    url: "{{ route('inspection-schedule.edit', ':id') }}".replace(':id', id),
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

        function createDataMaintenance() {
            $.ajax({
                    url: "{{ route('maintenances.create') }}",
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

        function editDataMaintenance(id) {
            $.ajax({
                    url: "{{ route('maintenances.edit', ':id') }}".replace(':id', id),
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
                            url: "{{ route('inspection-schedule.destroy', ':id') }}".replace(':id', id),
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
                            url: "{{ route('inspection-schedule.destroyAll') }}",
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
    </script>
    <script>
        'use strict';

        let date = new Date();
        let nextDay = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);
        // prettier-ignore
        let nextMonth = date.getMonth() === 11 ? new Date(date.getFullYear() + 1, 0, 1) : new Date(date.getFullYear(), date
            .getMonth() + 1, 1);
        // prettier-ignore
        let prevMonth = date.getMonth() === 11 ? new Date(date.getFullYear() - 1, 0, 1) : new Date(date.getFullYear(), date
            .getMonth() - 1, 1);

        window.events = @json($data);
        console.log(window.events);

        window.events = window.events.map(event => ({

            id: event.id,
            title: event.name,
            start: new Date(event.start),
            end: new Date(event.end),
            allDay: false,
            extendedProps: {
                calendar: event.type
            }
        }));
    </script>
@endpush
