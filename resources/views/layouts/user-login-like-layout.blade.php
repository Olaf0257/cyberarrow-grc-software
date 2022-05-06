<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>@yield('title',decodeHTMLEntity($globalSetting->display_name))</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="CyberArrow GRC" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ $globalSetting->favicon =='assets/images/ebdaa-Logo.png' ? asset($globalSetting->favicon): tenant_asset($globalSetting->favicon) }}">

        <!-- css root variables -->
        @include('layouts.partials.root-css-vars')

        <!-- App css -->
        <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/css/custom.css')}}" rel="stylesheet" type="text/css" />
        <style>
            .log-text {
                font-size: 30px;
            }

            .msg {
                color: red;
                font-weight: 600;
            }

            .form-control.border-error {
                border: 1px solid red!important;
            }

            .border-valid {
                border: 1px solid green;
            }

            @media (max-width: 768px) {
                .card-body img.logo-sm {
                    width: 95px;
                }

                .card-body p.log-text {
                    font-size: 20px;
                }

                footer .footer-alt {
                    font-size: 12px;
                }
            }

            body.authentication-bg-pattern {
                background-attachment: fixed;
            }

            .bg-pattern {
                background-size: 62rem !important;
                background-repeat: no-repeat;
            }
        </style>
        @yield('custom_css')

    </head>

    <body class="authentication-bg authentication-bg-pattern">

        <div class="account-pages mt-5 mb-5">
            <div class="container">
                @yield('content')
            </div>
            <!-- end container -->
        </div>
        <!-- end page -->


        <footer class="footer footer-alt">
        Copyright &copy; {{ date("Y") }}<a href="Javascript::void()" class="text-white-50"> EBDAA ITC CO LLC All rights reserved.</a>
        </footer>

        <script src="{{asset('assets/libs/jquery/jquery-3.5.0.min.js')}}"></script>
        <script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>

        <!-- Custom js -->
        @yield('custom_js')

        <!-- common-scritps  includes
            * jQuery JavaScript 3.5.0
            * slimscroll 3.1.8
            *bootstrap 4.5.3
            * wave.min.js 0.7.6
        -->
        @include('layouts.common-scripts')

        <!-- App js -->
        <script src="{{asset('assets/js/app.min.js')}}"></script>



    </body>
</html>
