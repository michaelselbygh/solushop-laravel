<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

    <!-- BEGIN: Head-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="description" content="Manage Solushop Ghana">
        <meta name="author" content="Solushop Ghana Limited">
        <title>{{ config('app.name') }} - @yield('page-title')</title>
        <link rel="apple-touch-icon" href="{{ url('portal/images/ico/apple-icon-120.png') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ url('portal/images/ico/favicon-32.png') }}">
        <link href="{{ url('https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CQuicksand:300,400,500,700') }}" rel="stylesheet">

        <!-- BEGIN: Vendor CSS-->
        <link rel="stylesheet" type="text/css" href="{{ url('portal/vendors/css/vendors.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ url('portal/vendors/css/pickers/daterange/daterangepicker.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ url('portal/vendors/css/tables/datatable/datatables.min.css') }}">
        <!-- END: Vendor CSS-->

        <!-- BEGIN: Theme CSS-->
        <link rel="stylesheet" type="text/css" href="{{ url('portal/css/bootstrap.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ url('portal/css/bootstrap-extended.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ url('portal/css/colors.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ url('portal/css/components.css') }}">
        <!-- END: Theme CSS-->

        <!-- BEGIN: Page CSS-->
        <link rel="stylesheet" type="text/css" href="{{ url('portal/css/core/menu/menu-types/vertical-menu.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ url('portal/css/core/colors/palette-gradient.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ url('portal/vendors/css/cryptocoins/cryptocoins.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ url('portal/css/plugins/forms/wizard.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ url('portal/css/plugins/pickers/daterange/daterange.css') }}">
        <!-- END: Page CSS-->


        <script type="text/javascript" src="{{ url('portal/jquery/jquery-3.4.1.js') }}"></script>
        <style>
            .child{
                height: 100%;
                margin-right: -50px; /* maximum width of scrollbar */
                padding-right: 30px; /* maximum width of scrollbar */
                overflow-y: scroll;
            }
        </style>

    </head>
    <!-- END: Head-->

    <!-- BEGIN: Body-->
    <body class="vertical-layout vertical-menu 2-columns   fixed-navbar" data-open="click" data-menu="vertical-menu" data-col="2-columns">

        <!-- BEGIN: Header-->
        <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-dark navbar-shadow" style="height:  60px;">
            <div class="navbar-wrapper">
                <div class="navbar-header">
                    <ul class="nav navbar-nav flex-row">
                        <li class="nav-item mobile-menu d-md-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a></li>
                        <li class="nav-item" >
                            <a class="navbar-brand" href="{{ route('vendor.dashboard')}}">
                                <img class="brand-logo" alt="Solushop Icon" style="height:30px; width:auto" src="{{ url('portal/images/logo/icon.png') }}">
                                <h5 class="brand-text">Vendor</h5>
                            </a>
                        </li>
                        <li class="nav-item d-md-none"><a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i class="la la-ellipsis-v"></i></a></li>
                    </ul>
                </div>
                <div class="navbar-container content">
                    <div class="collapse navbar-collapse" id="navbar-mobile" style="height: 60px;">
                        <ul class="nav navbar-nav mr-auto float-left">
                            <li class="nav-item d-none d-md-block"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu"></i></a></li>
                        </ul>
                        <ul class="nav navbar-nav float-right">
                            <li class=" dropdown-user nav-item">
                                <a class=" nav-link dropdown-user-link">
                                    <span class="mr-1 user-name text-bold-700">{{ Auth::guard('vendor')->user()->name }}</span>
                                    <span class="avatar avatar-online"><img src="{{ url('portal/images/avi/default.jpg') }}" alt="avatar"><i></i></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <!-- END: Header-->


        <!-- BEGIN: Main Menu-->
        @include('portal.main.vendor.sidebar')
        <!-- END: Main Menu-->

        <!-- BEGIN: Content-->
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                </div>
                <div class="content-body">
                    @yield('content-body')
                </div>
            </div>
        </div>
        <!-- END: Content-->

        <div class="sidenav-overlay"></div>
        <div class="drag-target"></div>

        <!-- BEGIN: Footer-->
        <footer class="footer footer-static footer-light navbar-border navbar-shadow">
            <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2"><span class="float-md-left d-block d-md-inline-block">Copyright &copy; {{ date("Y") }} <a class="text-bold-800 grey darken-2" href="https://solushop.com.gh" target="_blank" style='color: #f68b1e'>Solushop Ghana Limited</a></span></span></p>
        </footer>
        <!-- END: Footer-->


        <!-- BEGIN: Vendor JS-->
        <script src="{{ url('portal/vendors/js/vendors.min.js') }}"></script>
        <!-- BEGIN Vendor JS-->

        <!-- BEGIN: Page Vendor JS-->
        <script src="{{ url('portal/vendors/js/extensions/jquery.steps.min.js') }}"></script>
        <script src="{{ url('portal/vendors/js/tables/datatable/datatables.min.js') }}" type="text/javascript"></script>
        <!-- END: Page Vendor JS-->

        <!-- BEGIN: Theme JS-->
        <script src="{{ url('portal/js/core/app-menu.js') }}"></script>
        <script src="{{ url('portal/js/core/app.js') }}"></script>
        <!-- END: Theme JS-->

        <!-- BEGIN: Page JS-->
        <script src="{{ url('portal/js/scripts/pages/dashboard-crypto.js') }}"></script>
        <script src="{{ url('portal/js/scripts/tables/datatables/datatable-basic.js') }}" type="text/javascript"></script>
        <!-- END: Page JS-->

    </body>
    <!-- END: Body-->

</html>