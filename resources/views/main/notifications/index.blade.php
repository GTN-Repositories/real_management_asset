@extends('layouts.driver')

@section('title', 'Detail Notifikasi')

@push('css')
    <style>
        .massage {
            height: 50px;
            font-size: 18px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .badge-notif {
            width: 30px;
            height: 30px;
            border-radius: 100%;
        }
    </style>
@endpush

@section('content')
    <!-- Pricing Plans -->
    <section class="section-py first-section-pt">
        <div class="container">
            <div class="card my-4">
                <div class="d-flex justify-content-center align-items-center">
                    <h1 class="text-center fw-bold text-black m-3">All Notifications</h1>
                    <span
                        class="badge-notif bg-danger text-white text-center d-flex justify-content-center align-items-center">{{ count(\App\Helpers\Helper::notification()) }}</span>
                </div>
            </div>
            <div class="d-flex flex-column justify-content-center gap-4">
                <div class="d-flex gap-3 justify-content-end mt-4">
                    <input type="date" id="filter_date" class="form-control" style="max-width: 250px" />
                    <button type="button" class="btn btn-primary btn-md" id="filter-btn">Filter</button>
                    <button class="btn btn-warning btn-md" onclick="dashboard()">Kembali</button>
                </div>
                @foreach (\App\Helpers\Helper::notification() as $item)
                    <div class="card shadow" onclick="openModal('{{ $item->id }}')"
                        style="border-radius: 10px; cursor: pointer;">
                        <div class="row p-3">
                            <div class="col-12 col-md-2">
                                @if ($item->type == 'email')
                                    <img src="{{ asset('images/notif_email.png') }}" width="150" height="110"
                                        style="border-radius: 10px;" alt="">
                                @else
                                    <img src="{{ asset('images/notif_other.png') }}" width="150" height="110"
                                        style="border-radius: 10px;" alt="">
                                @endif
                            </div>
                            <div class="col-12 col-md-10 d-flex flex-column gap-2">
                                <h5 class="m-0 fw-bold">{{ $item->title }}</h5>
                                <p class="m-0 massage">
                                    @if ($item->type == 'email')
                                        Tekan untuk membuka detail email
                                    @else
                                        Pengingat: Pengisian Draft Sample Dashboard
                                        Hai [Nama Anda], ini adalah pengingat untuk mengisi draft sample dashboard. Apakah
                                        Anda sudah siap untuk mulai? (Ini Data Masih Dummy)
                                    @endif
                                </p>
                                <span class="m-0 fw-bold">{{ $item->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!--/ Pricing Plans -->
    <div class="modal fade" id="modal-ce" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple">
            <div class="modal-content p-1 p-md-5">
                <div class="modal-body" id="content-modal-ce">

                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        function dashboard(){
                window.location.href = "{{ route('dashboard') }}";
        }
        function openModal(id) {
            $.ajax({
                    url: "{{ route('notification.show', 'id') }}".replace('id', id),
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
        $(document).ready(function() {
            $('#filter-btn').click(function(e) {
                var filter_date = $('#filter_date').val();

            });
        });
    </script>
@endpush
