@extends('layouts.global')

@section('title', 'Edit Profile')

@section('content')
    <div class="mx-5 flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">User Profile /</span> Profile</h4>
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="user-profile-header-banner">
                        <img src="../../assets/img/pages/profile-banner.png" alt="Banner image" class="rounded-top" />
                    </div>
                    <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                            <img src="../../assets/img/avatars/14.png" alt="user image"
                                class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" />
                        </div>
                        <div class="flex-grow-1 mt-3 mt-sm-5">
                            <div
                                class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                                <div class="user-profile-info">
                                    <h4>{{ auth()->user()->name }}</h4>
                                    <ul
                                        class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                        <li class="list-inline-item d-flex gap-1">
                                            <i class="ti ti-color-swatch"></i> {{ auth()->user()->roles->first()->name }}
                                        </li>
                                        <li class="list-inline-item d-flex gap-1"><i class="ti ti-mail"></i>
                                            {{ auth()->user()->email }}
                                        </li>
                                        <li class="list-inline-item d-flex gap-1">
                                            <i class="ti ti-calendar"></i> Bergabung
                                            {{ \Carbon\Carbon::parse(auth()->user()->created_at)->locale('id_ID')->isoFormat('MMMM Y') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Header -->

        {{-- <div class="card mb-3">
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div> --}}
        <div class="card mb-3">
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>
        {{-- <div class="card mb-3">
            <div class="card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div> --}}
    </div>
@endsection
