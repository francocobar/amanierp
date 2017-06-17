<ul class="page-sidebar-menu  page-header-fixed page-sidebar-menu-hover-submenu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
    <li class="nav-item {{strpos(Route::currentRouteName(),'dashboard') !== false ? 'active open' : ''}} ">
        <a href="{{route('dashboard')}}" class="nav-link nav-toggle">
            <i class="icon-home"></i>
            <span class="title">Dashboard</span>
            <span class="arrow"></span>
        </a>
    </li>

    <li class="nav-item {{strpos(Route::currentRouteName(),'.salary') !== false ? 'active open' : ''}}">
        <a href="{{route('my.salary')}}">
            <i class="fa fa-credit-card"></i>
            <span class="title">Gaji Saya</span>
        </a>
    </li>

    @if(strtolower($role_user->slug) == 'cashier')
    <li class="nav-item {{strpos(Route::currentRouteName(),'.cashier') !== false ? 'active open' : ''}}">
        <a href="javascript:;" class="nav-link nav-toggle">
            <i class="icon-wallet"></i>
            <span class="title">Cashier</span>
            <span class="arrow"></span>
        </a>
        <ul class="sub-menu">
            <li class="nav-item ">
                <a href="{{route('get.cashier')}}" target="_blank" class="nav-link ">
                    <span class="title">Aplikasi Kasir</span>
                    <!--  -->
                </a>
            </li>
            <li class="nav-item ">
                <a href="{{route('search.invoice.cashier')}}" class="nav-link ">
                    <span class="title">Cari Invoice</span>
                    <!--  -->
                </a>
            </li>
        </ul>
    </li>
    @endif

    <li class="nav-item">
        <a href="{{route('logout')}}" class="nav-link nav-toggle">
            <i class="fa fa-sign-out"></i>
            <span class="title">Logout</span>
            <span class="arrow"></span>
        </a>
    </li>
</ul>
<!-- END SIDEBAR MENU -->
