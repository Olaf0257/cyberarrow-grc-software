<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Plugins css -->
    <!-- <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
     -->

    <link href="{{ public_path('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .page-break {
            page-break-after: always !important;
        }

        body {
            background: #fff;
        }
    
        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .rounded-pill {
            border-radius: 50rem !important;
        }

        .bg-primary {
            background-color: rgba(102, 88, 221, 1) !important;
        }

        .bg-danger {
            background-color: rgba(241, 85, 108, 1) !important;
        }

        .bg-success {
            background-color: green !important;
        }

        .bg-warning {
            background-color: rgba(247, 184, 75, 1) !important;
        }

        h3 {
            font-size: 34px;
            font-weight: bold;
        }

        #report-title {
            padding-top: 25px;
            border-top: solid 6px #111;
            border-bottom: solid 6px #111;
            font-size: 70px;
            font-weight: bold;
            color: #111;
        }
        .container {
            width: 1240px;
        }

        table {
            font-size: 28px
        }

        table.report-version-table td {
            border: 2px solid #444;
            height: 55px;
            font-size: 22px;
            font-weight: bold;
            color: #444;
            vertical-align:bottom;
            padding: 0 0 0 7px;
        }
        .first-page h4 {
            color: #000;
            font-size: 30px;
            font-weight: bold;
        }

        /* custom css */
    </style>
    @yield('doc_head')
</head>
<body>
    <div class="container first-page">
        <div class="pt-5">

            @if(config('filesystems.default') == 'local')

                @if(env('TENANCY_ENABLED'))
                    <img src="{{ $globalSetting->company_logo =='assets/images/ebdaa-Logo.png' ? asset($globalSetting->company_logo): tenant_asset($globalSetting->company_logo) }}" alt="" class="mx-auto d-block" height="400">
                @else
                    <img src="{{ $globalSetting->company_logo =='assets/images/ebdaa-Logo.png' ? asset($globalSetting->company_logo):asset('/storage'.$globalSetting->company_logo) }}" alt="" class="mx-auto d-block" height="400">
                @endif
            @else
                <img src="{{ $globalSetting->company_logo =='assets/images/ebdaa-Logo.png' ? asset($globalSetting->company_logo): $globalSetting->company_logo }}" alt="" class="mx-auto d-block" height="400">
            @endif

            <!-- <img src="http://ebdaagrc.com/assets/images/ebdaa-Logo.png" alt="" class="mx-auto d-block" height="400"> -->
        </div>
        <div class="w-100 mt-5" >
            <h1 id="report-title" class="text-center" style="padding: 3rem 0 2rem">
                @yield('report-heading')
            </h1>
        </div>


        <div class="mt-5 w-100">
            <h4 class="text-center mt-1">Version 1.0</h4>
            <h4 class="text-center mt-3">Generated: {{ date('j F, Y') }}</h4>
            <table class="table mt-5 report-version-table">
                <tr>
                    <td class="w-50">
                    Version
                    </td> 
                    <td class="w-50">
                    1.0
                    </td>
                </tr>
                <tr>
                    <td class="w-50">
                    State
                    </td> 
                    <td class="w-50">
                    Final
                    </td>
                </tr>
                <tr>
                    <td class="w-50">
                    Creation Date
                    </td> 
                    <td class="w-50">
                    {{ date('j F, Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="w-50">
                    Classification
                    </td> 
                    <td class="w-50">
                    Confidential
                    </td>
                </tr>
            </table>
        </div>
    </div>
        <div style="page-break-after: always !important;">
        </div>
        @yield('content')

<!-- script applied to all pdf -->
    @php 
        $renderToBrowser = false;
    @endphp

    @if($renderToBrowser)
    <script src="{{ asset('assets/libs/chart-js/Chart.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/chart-js/chartjs-plugin-datalabels-v0.7.0.js') }}"></script>
    <script src="{{ asset('assets/libs/chart-js/chartjs-plugin-labels.js') }}"></script>
    @else
    <script src="{{ public_path('assets/libs/chart-js/Chart.bundle.min.js') }}"></script>
    <script src="{{ public_path('assets/libs/chart-js/chartjs-plugin-datalabels-v0.7.0.js') }}"></script>
    <script src="{{ public_path('assets/libs/chart-js/chartjs-plugin-labels.js') }}"></script>
    <!-- <script src="{{ public_path('assets/libs/chart-js/Chart.RadialGauge.umd.js') }}"></script> -->
    @endif
    <script>
    (function(setLineDash) {
        CanvasRenderingContext2D.prototype.setLineDash = function() {
            if(!arguments[0].length){
                arguments[0] = [1,0];
            }
            // Now, call the original method
            return setLineDash.apply(this, arguments);
        };
    })(CanvasRenderingContext2D.prototype.setLineDash);
    Function.prototype.bind = Function.prototype.bind || function (thisp) {
        var fn = this;
        return function () {
            return fn.apply(thisp, arguments);
        };
    };

    Chart.pluginService.register({
        beforeDraw: function(chart) {
            if (chart.config.options.elements.center) {
                var width = chart.chart.width,
                    height = chart.chart.height,
                    ctx = chart.chart.ctx;

                ctx.restore();
                var fontSize = chart.config.options.elements.center.fontSize || 2;
                ctx.font = fontSize + "em sans-serif";
                ctx.textBaseline = "middle";

                var text = chart.config.options.elements.center.text,
                    textX = Math.round((width - ctx.measureText(text).width) / 2),
                    textY = height / (chart.config.options.elements.center.textY || 2);

                ctx.fillText(text, textX, textY);
                ctx.save();
            }
        }
    });
    // chart js global config

    Chart.defaults.global.title.fontSize = 27;
    Chart.defaults.global.legend.labels.fontSize = 20
    </script>
    @yield('page_js')
</body>
</html>