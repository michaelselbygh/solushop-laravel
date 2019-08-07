
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="description" content="Manage Solushop Ghana, your most trusted online store.">
        <meta name="author" content="Solushop Ghana Limited">
        <title> Login as @yield('entity') </title>
        <link rel="apple-touch-icon" href="{{ url("portal/images/ico/apple-icon-120.png") }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ url("portal/images/ico/favicon-32.png") }}">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CQuicksand:300,400,500,700" rel="stylesheet">

        <!-- BEGIN: Vendor CSS-->
        <link rel="stylesheet" type="text/css" href="{{ url("portal/vendors/css/vendors.min.css") }}">
        <link rel="stylesheet" type="text/css" href="{{ url("portal/vendors/css/forms/icheck/icheck.css") }}">
        <link rel="stylesheet" type="text/css" href="{{ url("portal/vendors/css/forms/icheck/custom.css") }}">
        <!-- END: Vendor CSS-->

        <!-- BEGIN: Theme CSS-->
        <link rel="stylesheet" type="text/css" href="{{ url("portal/css/bootstrap.css") }}">
        <link rel="stylesheet" type="text/css" href="{{ url("portal/css/bootstrap-extended.css") }}">
        <link rel="stylesheet" type="text/css" href="{{ url("portal/css/colors.css") }}">
        <link rel="stylesheet" type="text/css" href="{{ url("portal/css/components.css") }}">
        <!-- END: Theme CSS-->

        <!-- BEGIN: Page CSS-->
        <link rel="stylesheet" type="text/css" href="{{ url("portal/css/core/menu/menu-types/vertical-content-menu.css") }}">
        <link rel="stylesheet" type="text/css" href="{{ url("portal/css/core/colors/palette-gradient.css") }}">
        <link rel="stylesheet" type="text/css" href="{{ url("portal/css/pages/login-register.css") }}">
        <!-- END: Page CSS-->

        <style>
            input:focus{
                border-color: #f68c20 !important;
            }
        </style>

    </head>

    <body class="vertical-layout vertical-content-menu 1-column bg-full-screen-image blank-page" data-open="click" data-menu="vertical-content-menu" data-col="1-column">
        <!-- BEGIN: Content-->
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row mb-1">
                </div>
                <div class="content-body">
                    <section class="flexbox-container">
                        <div class="col-12 d-flex align-items-center justify-content-center">
                            <div class="col-sm-3" style="border-radius: 50px; text-align:center;">
                                <div class="card border-grey border-lighten-3 box-shadow-2 px-1 py-1" style="width:320px; display: inline-block">
                                    <div class="card-header border-0">
                                        <div class="card-title text-center">
                                            <img src="{{ url('portal/images/logo/logo.png') }}" style="width: 180px; height: auto; padding-top: 40px; padding-bottom: 20px;" alt="New Lucky Logo">
                                        </div>
                                        <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2"><span>Login as <b> @yield('entity') </b></span></h6><br>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body" style="padding-top: 0px; padding-bottom: 15px;">
                                            @yield('login')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <!-- END: Content-->


        <!-- BEGIN: Vendor JS-->
        <script src="{{ url("portal/vendors/js/vendors.min.js") }}"></script>
        <!-- BEGIN Vendor JS-->

        <!-- BEGIN: Page Vendor JS-->
        <script src="{{ url("portal/vendors/js/ui/headroom.min.js") }}"></script>
        <script src="{{ url("portal/vendors/js/forms/validation/jqBootstrapValidation.js") }}"></script>
        <script src="{{ url("portal/vendors/js/forms/icheck/icheck.min.js") }}"></script>
        <!-- END: Page Vendor JS-->

        <!-- BEGIN: Theme JS-->
        <script src="{{ url("portal/js/core/app-menu.js") }}"></script>
        <script src="{{ url("portal/js/core/app.js") }}"></script>
        <!-- END: Theme JS-->

        <!-- BEGIN: Page JS-->
        <script src="{{ url("portal/js/scripts/forms/form-login-register.js") }}"></script>
        <!-- END: Page JS-->

    </body>

</html>