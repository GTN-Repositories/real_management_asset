<style>
    .gradient-navbar {
        background: linear-gradient(to right, #134B70, #201E43);
    }

    .title {
        font-weight: 600;
        font-size: 20px;
        color: #FFFFFF;
        margin: 0px;
        width: 1000px;
    }
    @media (max-width: 768px){
        .title{
            font-size: 12px;
            width: 100px;
        }
    }

</style>
<nav class="layout-navbar navbar navbar-expand-xl navbar-detached align-items-center gradient-navbar"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm text-white"></i>
        </a>
    </div>

    <div class="navbar-nav align-items-left">
        <h1 class="title">@yield('title_page')</h1>
    </div>
    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            @if (auth()->user()->roles[0]->name === 'superAdmin')
                <!-- Quick links  -->
                <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" aria-expanded="false">
                        <img src="{{ asset('images/app.png') }}" width="32" alt="" class="h-auto rounded-circle">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0">
                        <div class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h5 class="text-body mb-0 me-auto">Shortcuts</h5>
                                <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body"
                                    data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Add shortcuts"
                                    data-bs-original-title="Add shortcuts"><i class="ti ti-sm ti-apps"></i></a>
                            </div>
                        </div>
                        <div class="dropdown-shortcuts-list scrollable-container ps">
                            @php
                                $menus = \App\Helpers\Helper::getMenu();
                            @endphp
                            @foreach ($menus as $data)
                                @if ($data->parent_id == null && $data->children->isNotEmpty())
                                    <div class="row row-bordered overflow-visible g-0">
                                        <div class="dropdown-shortcuts-item col-6">
                                            <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                                <i class="ti ti-brand-tabler fs-4"></i>
                                            </span>
                                            <a href="{{ $data->route }}">
                                                {{ $data->name }}
                                            </a>
                                        </div>
                                        @foreach ($data->children as $child)
                                            <div class="dropdown-shortcuts-item col-6">
                                                <a href="{{ $child->route }}">
                                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                                        <i class="ti ti-brand-tabler fs-4"></i>
                                                    </span>
                                                    {{ $child->name }}
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                        </div>
                        <div class="ps__rail-y" style="top: 0px; right: 0px;">
                            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                        </div>
                    </div>
                </li>
                <!-- Quick links -->
            @endif

            <!-- Notification -->
            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside" aria-expanded="false">
                    <img src="{{ asset('images/bell.png') }}" width="32" alt=""
                            class="h-auto rounded-circle">
                    <span class="badge bg-danger rounded-pill badge-notifications">{{ count(\App\Helpers\Helper::notification()) }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0">
                    <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h5 class="text-body mb-0 me-auto">Notification</h5>
                            <a href="javascript:void(0)" class="dropdown-notifications-all text-body"
                                data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Mark all as read"
                                data-bs-original-title="Mark all as read"><i class="ti ti-mail-opened fs-4"></i></a>
                        </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container ps">
                        <ul class="list-group list-group-flush">
                            @foreach (\App\Helpers\Helper::notification() as $item)
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <span class="avatar-initial rounded-circle bg-label-warning"><i
                                                        class="ti ti-alert-triangle"></i></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $item->title }}</h6>
                                            <p class="mb-0">{!! $item->body !!}</p>
                                            <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                        </div>
                        <div class="ps__rail-y" style="top: 0px; right: 0px;">
                            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                        </div>
                    </li>
                    <li class="dropdown-menu-footer border-top">
                        <a href="javascript:void(0);" onclick="detailNotifikasi()"
                            class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                            View all notifications
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ Notification -->

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ asset('assets/img/icon_user_white.jpg') }}" alt=""
                            class="h-auto rounded-circle">
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="pages-account-settings-account.html">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('assets/img/icon_user.png') }}" alt=""
                                            class="h-auto rounded-circle">
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-medium d-block">
                                        @auth
                                            {{ auth()->user()->name }}
                                        @endauth
                                    </span>
                                    <small class="text-muted">
                                        @auth
                                            {{ auth()->user()->roles->pluck('name')->implode(', ') }}
                                        @endauth
                                    </small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="ti ti-user-check me-2 ti-sm"></i>
                            <span class="align-middle">My Profile</span>
                        </a>
                    </li>
                    <li>
                        <div class="d-flex align-items-center ms-4">
                            <i class="ti ti-switch-3 me-2 ti-sm"></i>
                            <a href="{{ route('select-project.index') }}">Pilih Project Lain</a>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display: none;">
                            @csrf
                        </form>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="logout()">
                            <i class="ti ti-logout me-2 ti-sm"></i>
                            <span class="align-middle">Log Out</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>

    <!-- Search Small Screens -->
    <div class="navbar-search-wrapper search-input-wrapper d-none">
        <span class="twitter-typeahead" style="position: relative; display: inline-block;"><input type="text"
                class="form-control search-input container-xxl border-0 tt-input" placeholder="Search..."
                aria-label="Search..." autocomplete="off" spellcheck="false" dir="auto"
                style="position: relative; vertical-align: top;">
            <pre aria-hidden="true"
                style="position: absolute; visibility: hidden; white-space: pre; font-family: &quot;Public Sans&quot;, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Oxygen, Ubuntu, Cantarell, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; font-size: 15px; font-style: normal; font-variant: normal; font-weight: 400; word-spacing: 0px; letter-spacing: 0px; text-indent: 0px; text-rendering: auto; text-transform: none;"></pre>
            <div class="tt-menu navbar-search-suggestion ps"
                style="position: absolute; top: 100%; left: 0px; z-index: 100; display: none;">
                <div class="tt-dataset tt-dataset-pages"></div>
                <div class="tt-dataset tt-dataset-files"></div>
                <div class="tt-dataset tt-dataset-members"></div>
                <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                    <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                </div>
                <div class="ps__rail-y" style="top: 0px; right: 0px;">
                    <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                </div>
            </div>
        </span>
        <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
    </div>
</nav>

<script>
    function logout() {
        Swal.fire({
            title: "Are you sure?",
            text: "You will be logged out from your account!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, logout!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logoutForm').submit();
            }
        });
    }
    function detailNotifikasi() {
        window.location.href = "{{ route('notification.index') }}";
    }
</script>
