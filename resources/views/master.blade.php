<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{isset($title) ? $title.' - ' : ''}}Amanie</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- <link href="{{ URL::asset('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css" /> -->
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{{ URL::asset('assets/global/css/plugins.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="{{ URL::asset('assets/layouts/layout2/css/layout.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/layouts/layout2/css/themes/blue.min.css') }}" rel="stylesheet" type="text/css" id="style_color" />
        <link href="{{ URL::asset('assets/layouts/layout2/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->

        @yield('optional_css')

        <link href="{{ URL::asset('css/custom.css') }}" rel="stylesheet" type="text/css" />

    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid">
        <!-- BEGIN HEADER -->
        <div class="page-header navbar navbar-fixed-top">
            <!-- BEGIN HEADER INNER -->
            <div class="page-header-inner ">
                <!-- BEGIN LOGO -->

                <!-- END LOGO -->
                <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
                <!-- END RESPONSIVE MENU TOGGLER -->
                <!-- BEGIN PAGE ACTIONS -->
                <!-- DOC: Remove "hide" class to enable the page header actions -->
                <!-- END PAGE ACTIONS -->
                <!-- BEGIN PAGE TOP -->
                <div class="page-top">
                    <img src="{{ URL::asset('assets/layouts/layout2/img/logo-amanie.jpg') }}" alt="logo" class="logo-default" />

                    <?php $role_user = UserService::getRoleByUser(); ?>
                    <div class="top-menu">
                        <ul class="nav navbar-nav pull-right">
                            <!-- BEGIN NOTIFICATION DROPDOWN -->
                            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                            @if(strtolower($role_user->slug) == 'superadmin')
                            <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                                <?php
                                    $rejected = StockService::getRejectedStockConfirmation(1, false);
                                    $rejected_count = $rejected->count();
                                    $pending = StockService::getPendingStockConfirmation(1);
                                    $pending_count = $pending->count();

                                    $total = $rejected_count + $pending_count;
                                ?>
                                @if($total>0)
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <i class="icon-bell"></i>
                                    <span class="badge badge-default"> {{$total}} </span>
                                </a>
                                <ul class="dropdown-menu">
                                    @if($pending_count>0)
                                    <li class="external">
                                        <h3>
                                            <span class="bold">{{$pending_count}} konfirmasi pending</span></h3>
                                        <a href="{{route('get.item.pending.confirmations')}}">Lihat Semua</a>
                                    </li>
                                    @endif
                                    @if($rejected_count>0)
                                    <li class="external">
                                        <h3>
                                            <span class="bold">{{$rejected_count}} konfirmasi rejected</span></h3>
                                        <a href="{{route('get.item.rejected.confirmations',['unseen'=>1])}}">Lihat Semua</a>
                                    </li>
                                    @endif
                                </ul>
                                @endif
                            </li>
                            @elseif(strtolower($role_user->slug) == 'manager')
                            <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                                <?php
                                    $pending = StockService::getPendingStockConfirmation(1);
                                    $pending_count = $pending->count();
                                ?>
                                @if($pending_count>0)
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <i class="icon-bell"></i>
                                    <span class="badge badge-default"> {{$pending_count}} </span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="external">
                                        <h3>
                                            <span class="bold">{{$pending_count}} konfirmasi pending</span></h3>
                                        <a href="{{route('get.item.pending.confirmations')}}">Lihat Semua</a>
                                    </li>
                                </ul>
                                @endif
                            </li>
                            @endif

                            @if(session('user_login'))
                            <?php $user_login = session('user_login') ?>
                            <li class="dropdown dropdown-user">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <span class="username username-hide-on-mobile" style="padding-right: 13px;">
                                    <span class="fa fa-user"></span>{{$user_login['full_name'].' ('.$user_login['branch'].')'}}</span>
                                </a>
                            </li>
                            @endif
                            <!-- END NOTIFICATION DROPDOWN -->
                            <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                            <!-- END QUICK SIDEBAR TOGGLER -->
                        </ul>
                    </div>
                    <!-- END TOP NAVIGATION MENU -->
                </div>
                <!-- END PAGE TOP -->
            </div>
            <!-- END HEADER INNER -->
        </div>
        <!-- END HEADER -->
        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"> </div>
        <!-- END HEADER & CONTENT DIVIDER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- BEGIN SIDEBAR -->
            <div class="page-sidebar-wrapper">
                <!-- END SIDEBAR -->
                <div class="page-sidebar navbar-collapse collapse">
                    @if($role_user!=null)
                        @if(strtolower($role_user->slug) == 'superadmin')
                            @include('menu.samenu')
                        @elseif(strtolower($role_user->slug) == 'manager')
                            @include('menu.managermenu')
                        @elseif(strtolower($role_user->slug) == 'staff' || strtolower($role_user->slug) == 'cashier')
                            @include('menu.staffmenu')
                        @endif
                    @else

                    @endif
                </div>
                <!-- END SIDEBAR -->
            </div>
            <!-- END SIDEBAR -->
            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <!-- BEGIN CONTENT BODY -->
                <div class="page-content">
                    @yield('content')
                </div>
                <!-- END CONTENT BODY -->
            </div>
            <!-- END CONTENT -->

        </div>
        <!-- END CONTAINER -->
        <!-- BEGIN FOOTER -->
        <div class="page-footer">
            <div class="page-footer-inner"> &copy; Amanie
            </div>
            <div class="scroll-to-top">
                <i class="icon-arrow-up"></i>
            </div>
        </div>
        <!-- END FOOTER -->
        <!--[if lt IE 9]>
        <![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="{{ URL::asset('js/jquery.js') }} " type="text/javascript"></script>
        <script src="{{ URL::asset('js/jquery-ui.js') }} " type="text/javascript"></script>
        <script src="{{ URL::asset('js/amanie.js') }} " type="text/javascript"></script>
        <!-- <script src="{{ URL::asset('assets/global/plugins/moment.min.js') }} " type="text/javascript"></script> -->
        <script src="{{ URL::asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }} " type="text/javascript"></script>
        <!--
        <script src="{{ URL::asset('assets/global/plugins/js.cookie.min.js') }} " type="text/javascript"></script>
        -->
        <script src="{{ URL::asset('assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }} " type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }} " type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/plugins/jquery.blockui.min.js') }} " type="text/javascript"></script>
        <!-- <script src="{{ URL::asset('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }} " type="text/javascript"></script> -->
        <!-- END CORE PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="{{ URL::asset('assets/global/scripts/app.min.js') }} " type="text/javascript"></script>
        <script src="{{ URL::asset('js/app.min.js') }} " type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="{{ URL::asset('assets/global/plugins/bootbox/bootbox.min.js') }} " type="text/javascript"></script>
        <script src="{{ URL::asset('js/jquery.maskMoney.min.js') }}" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
        @yield('optional_js')
    </body>
</html>
