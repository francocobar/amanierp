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

    <li class="nav-item">
        <a href="{{route('logout')}}" class="nav-link nav-toggle">
            <i class="fa fa-sign-out"></i>
            <span class="title">Logout</span>
            <span class="arrow"></span>
        </a>
    </li>
</ul>
<!-- END SIDEBAR MENU -->
