<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="index.html" class="app-brand-link">
            <img src="{{ asset('assets/img/logo_real.png') }}" width="80%" alt="">
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @can('view-dashboard')
            <li class="menu-item {{ request()->url() == url('/dashboard') ? 'active' : '' }}">
                <a href="/dashboard" class="menu-link">
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>
        @endcan

        @php
            $menus = \App\Helpers\Helper::getMenu();
        @endphp

        @foreach ($menus as $data)
            @php
                // Generate permission name from menu name
                $permissionName = 'view-' . Str::slug($data->name);
            @endphp

            @if ($data->parent_id == null && $data->children->isEmpty())
                @can($permissionName)
                    <li class="menu-item {{ request()->url() == url($data->route) ? 'active' : '' }}">
                        <a href="{{ $data->route }}" class="menu-link">
                            <div data-i18n="{{ $data->name }}">{{ $data->name }}</div>
                        </a>
                    </li>
                @endcan
            @elseif ($data->parent_id == null && $data->children->isNotEmpty())
                @php
                    $hasPermission = false;
                    foreach ($data->children as $child) {
                        $childPermission = 'view-' . Str::slug($child->name);
                        if (auth()->user()->can($childPermission)) {
                            $hasPermission = true;
                            break;
                        }
                    }
                @endphp

                @if ($hasPermission)
                    <li
                        class="menu-item {{ $data->children->contains(function ($child) {return request()->url() == url($child->route);})? 'active': '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <div data-i18n="{{ $data->name }}">{{ $data->name }}</div>
                        </a>
                        <ul class="menu-sub">
                            @foreach ($data->children as $child)
                                @php
                                    $childPermission = 'view-' . Str::slug($child->name);
                                @endphp
                                @can($childPermission)
                                    <li class="menu-item {{ request()->url() == url($child->route) ? 'active' : '' }}">
                                        <a href="{{ $child->route }}" class="menu-link">
                                            <div>{{ $child->name }}</div>
                                        </a>
                                    </li>
                                @endcan
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endif
        @endforeach
    </ul>
</aside>
