<!DOCTYPE html>
<html lang="en">
    <head>
        <title>@yield('title', decodeHTMLEntity($globalSetting->display_name))</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{$globalSetting->favicon ? $globalSetting->favicon =='assets/images/ebdaa-Logo.png' ? asset($globalSetting->favicon) : tenant_asset($globalSetting->favicon) : '' }}">

        <!-- css root variables -->
        @include('layouts.partials.root-css-vars')

        <!-- Plugins css -->
        <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        @yield('plugins_css')
        <!-- Sweet Alert-->
        <link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />


        <!-- App css -->
        <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/css/custom.css')}}" rel="stylesheet" type="text/css" />

        @yield('custom_css')

        @php
            if( Auth::guard('admin')->check() ){
                $authUser = Auth::guard('admin')->user();
            }
        @endphp

        <style>

         .select2-container--default .select2-results__option--highlighted[aria-selected]{
            background: var(--secondary-color) !important;
        }
        </style>
    </head>

    <body class="center-menu @yield('body-class')">
        <!-- Pre-loader -->
        <div id="page-loader">
            <div id="loader-status">
                <div class="spinner"></div>
                <p class="text-center">Loading...</p>
            </div>
        </div>
        <!-- End Preloader-->
        <!-- WRAPPER -->
            <!-- NAVBAR -->
            @include('layouts.partials.header')
            <!-- END NAVBAR -->
            <div id="content-section-wp">
                @if ($message = Session::get('exception'))
                <div class="alert alert-danger alert-block  mt-2">
                    <button type="button" class="btn-close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                </div>
                @endif

                <!-- MAIN CONTENT -->
                @yield('content')
                <!-- END MAIN CONTENT -->
            </div>

            <div class="clearfix"></div>

            @include('layouts.partials.footer')

            <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <!-- common-scritps  includes
            * jQuery JavaScript 3.5.0
            * slimscroll 3.1.8
            *bootstrap 4.5.3
            * wave.min.js 0.7.6
        -->


<div class="modal fade bs-example-modal-center" tabindex="-1" id="downlodReportModal" role="dialog" aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body">
                <div id="animationSandbox" class="p-2 text-center">
                    <div class="spinner-border text-success m-2" role="status"></div>
                    <p>Generating Your Report</p>
                </div>

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

        @include('layouts.common-scripts')


        @yield('plugins_js')
        <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

        <!-- page loader js -->
            <script nonce="{{ csp_nonce() }}">
                const PageLoader = {
                    pageLoader: () => {
                        return $('#page-loader')
                    },
                    hide: function() {
                        $('body').removeClass('overflow-y-hidden')

                        this.pageLoader().hide()
                    },
                    show: function() {
                        $('body').addClass('overflow-y-hidden')

                        this.pageLoader().show()
                    }
                }
               // configuring default options for datatable//
               if ($.fn.dataTable){
                    $.extend( $.fn.dataTable.defaults, {
                        "processing": true,
                        language: {
                            paginate: {
                                previous: "<i class='mdi mdi-chevron-left'>",
                                next: "<i class='mdi mdi-chevron-right'>"
                            },
                            "infoFiltered":"",
                            "processing": `
                            <div id="content-loading" class="p-2 d-flex align-items-center">
                                <div class="spinner"></div>
                                <p class="text-center m-0 px-2">Loading...</p>
                            </div>`
                        },
                        drawCallback: function () {
                            $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
                        }
                    } );
               }
            </script>
        @yield('custom_js')

        <!-- CUSTOM JS 2 CODE STARTS HERE -->
        @yield('custom_js_2')

        <!-- App js-->
        <script src="{{ asset('assets/js/app.min.js') }}"></script>

            <!-- App Primary color Classes -->
        <style nonce="{{ csp_nonce() }}">
            .navigation-menu>li>a, #topnav .has-submenu.active>a {
                color: var(--default-text-color);
            }
        </style>

        <script nonce="{{ csp_nonce() }}" >

        $(document).ready(function(){

            $(document).on('click', '.stop-session-check', function () {
                stopSessionCheck()
            })

            // hover color button
            function ColorLuminance(hex, lum) {

                // validate hex string
                hex = String(hex).replace(/[^0-9a-f]/gi, '');

                if (hex.length < 6) {
                    hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
                }

                lum = lum || 0;

                // convert to decimal and change luminosity
                var rgb = "#", c, i;

                for (i = 0; i < 3; i++) {
                    c = parseInt(hex.substr(i*2,2), 16);
                    c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
                    rgb += ("00"+c).substr(c.length);
                }

                return rgb;
            }

            var primary_bg_color_hover = ColorLuminance("{{ $globalSetting->primary_color }}", -0.1)
            var secondary_bg_color_hover = ColorLuminance("{{ $globalSetting->secondary_color }}", -0.1)

            document.documentElement.style.setProperty('--primary-color-hover', primary_bg_color_hover);
            document.documentElement.style.setProperty('--secondary-color-hover', secondary_bg_color_hover);

            // DUE TO INACTIVITY SESSION TIMEOUT
            var sessionTimeout = "{{ $globalSetting->session_timeout ?: 'never' }}"


            function checkSession() {
                $.post('{{ route('session.ajax.check') }}', { '_token' : '{!! csrf_token() !!}' }, function(res) {
                    if (res.success) {
                        stopSessionCheck();

                        // redirecting to pages lock screen


                        url_redirect({url: "{{ route('pages-lock-screen') }}",
                            method: "post",
                            data: {"_token":"{!! csrf_token() !!}", "email": res.user.email, "full_name": `${res.user.full_name}`, "logged_in_with_sso": res.user.is_sso_auth}
                        });
                    }
                });
            }


            let checkSessionInterval = setInterval(checkSession, 10000);

            function stopSessionCheck(){
                clearInterval(checkSessionInterval);
            }

            function url_redirect(options){
                var $form = $("<form />");

                $form.attr("action",options.url);
                $form.attr("method",options.method);

                for (var data in options.data)
                $form.append('<input type="hidden" name="'+data+'" value="'+options.data[data]+'" />');

                $("body").append($form);
                $form.submit();
            }


            // Handing ajax request error globally
            $( document ).ajaxError(function( event, request, settings ) {
                let jsonRes = request.responseJSON
                // Refreshing a page on Unauthenticated response\
                if(jsonRes.exception == "Unauthenticated."){
                    location.reload();
                }
            });



            // Handing ajax request error globally for license routes   
            $( document ).ajaxError(function( event, request, settings ) {
                let jsonRes = request.responseJSON
                // Refreshing a page on Unauthenticated response\
                if(jsonRes.exception == "licenseException."){
                    location.reload();
                }
            });



            // Datatbale error handling for unauthentication
            if ($.fn.dataTable){
                $.fn.dataTable.ext.errMode = function ( settings, helpPage, message ) {
                    const dtXHR = settings.jqXHR

                    let res = dtXHR.responseJSON

                    if(res.exception && res.exception == "Unauthenticated."){
                        location.reload();
                    }
                };
            }
        }); // END OF DOCUMENT READY

    const reportModal = (type,href = null) =>
        {

            let time;
            let exportLink;

            switch(type)
            {
                case "risk":
                    time = 7000;
                    exportLink = href;
                    document.location.href= exportLink;

                break;
                case "compliance":
                    time = 6000;
                    exportLink = href;
                    document.location.href= exportLink;
                    break;
                default:
                    time = 6000;
                    $("#exportFormDashboard").submit();
                break;

            }


            $("#downlodReportModal").modal('show');
            setTimeout(function(){
                $("#downlodReportModal").modal('hide');
            },time);
        };

        </script>

    </body>

</html>
