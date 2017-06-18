<ul class="page-sidebar-menu  page-header-fixed page-sidebar-menu-hover-submenu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
    <li class="nav-item start {{strpos(Route::currentRouteName(),'dashboard') !== false ? 'active open' : ''}} ">
        <a href="{{route('dashboard')}}" class="nav-link nav-toggle">
            <i class="icon-home"></i>
            <span class="title">Dashboard</span>
        </a>
    </li>

    <li class="nav-item {{strpos(Route::currentRouteName(),'.item') !== false ? 'active open' : ''}}">
        <a href="javascript:;" class="nav-link nav-toggle">
            <i class="fa fa-database"></i>
            <span class="title">Items</span>
            <span class="arrow"></span>
        </a>
        <ul class="sub-menu">
            <li class="nav-item ">
                <a href="{{route('get.items.produk',['page'=>1])}}" class="nav-link ">
                    <span class="title">Daftar Produk</span>
                </a>
            </li>
            <li class="nav-item ">
                <a href="{{route('get.items.jasa',['page'=>1])}}" class="nav-link ">
                    <span class="title">Daftar Jasa</span>
                </a>
            </li>
            <li class="nav-item ">
                <a href="{{route('get.items.sewa',['page'=>1])}}" class="nav-link ">
                    <span class="title">Daftar Sewa</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item {{strpos(Route::currentRouteName(),'.employee') !== false ? 'active open' : ''}}">
        <a href="javascript:;" class="nav-link nav-toggle">
            <i class="fa fa-child"></i>
            <span class="title">Karyawan</span>

            <span class="arrow"></span>
        </a>
        <ul class="sub-menu">
            <li class="nav-item ">
                <a href="{{route('add.employee')}}" class="nav-link ">
                    <span class="title">Tambah Karyawan</span>
                    <!--  -->
                </a>
            </li>
            <li class="nav-item ">
                <a href="{{route('get.employees',['page'=>1])}}"  class="nav-link ">
                    <span class="title">Daftar Karyawan</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item {{strpos(Route::currentRouteName(),'.member') !== false ? 'active open' : ''}}">
        <a href="javascript:;" class="nav-link nav-toggle">
            <i class="fa fa-users"></i>
            <span class="title">Member</span>
            <span class="arrow"></span>
        </a>
        <ul class="sub-menu">
            <li class="nav-item ">
                <a href="{{route('add.member')}}" class="nav-link ">
                    <span class="title">Tambah Member</span>
                    <!--  -->
                </a>
            </li>
            <li class="nav-item ">
                <a href="{{route('get.members',['page'=>1])}}"  class="nav-link ">
                    <span class="title">Daftar Member</span>
                </a>
            </li>
        </ul>
    </li>

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

    <li class="nav-item {{strpos(Route::currentRouteName(),'.sales') !== false ? 'active open' : ''}}">
        <a href="javascript:;" class="nav-link nav-toggle">
            <i class="icon-briefcase"></i>
            <span class="title">Laporan</span>
            <span class="arrow"></span>
        </a>

        <ul class="sub-menu">
            <li class="nav-item ">
                <a href="{{route('get.sales.report',['period'=>'1','spesific'=>'0', 'branch' => session('branch_id')])}}" class="nav-link ">
                    <span class="title">Lap. Penjualan Harian</span>
                    <!--  -->
                </a>
            </li>
            <li class="nav-item ">
                <a href="{{route('get.sales.report',['period'=>'2','spesific'=>'0', 'branch' => session('branch_id')])}}" class="nav-link ">
                    <span class="title">Lap. Penjualan Bulanan</span>
                    <!--  -->
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item {{strpos(Route::currentRouteName(),'.salary') !== false ? 'active open' : ''}}">
        <a href="{{route('my.salary')}}">
            <i class="fa fa-credit-card"></i>
            <span class="title">Gaji Saya</span>
        </a>
    </li>

    <li class="nav-item ">
        <a href="{{route('logout')}}">
            <i class="fa fa-sign-out"></i>
            <span class="title">Logout</span>
        </a>
    </li>
</ul>
<!-- END SIDEBAR MENU -->