@extends('layouts.layout')

@php $pageTitle = "Risk Dashboard"; @endphp

@section('title', $pageTitle)


@section('plugins_css')
@endsection


@section('custom_css')
<link href="{{asset('assets/css/modules/risk-management.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .risksby-category,
        .risksby-status {
            background: #fff;
            border-radius: 5px;
            border: 1px solid var(--secondary-color);
        }

        .risk__dashboard h5 {
            margin: 0 !important;
            padding: 10px 0;
            color: #555;
            background: var(--secondary-color);
        }


        .bg-green {
            background: #bce293;
            border: 1px solid #92d050;
        }

        .low-risk svg {
            color: #92d050;
        }

        .bg-orange {
            background: #ff8;
            border: 1px solid #ee0;
        }

        .mode-risk svg {
            color: #ee0;
        }

        .bg-yellow {
            background: #fd7;
            border: 1px solid #ffc000;
        }

        .high-risk svg {
            color: #ffc000;
        }

        .bg-red {
            background: #f77;
            border: 1px solid #FF0000;
        }

        .extreme-risk svg {
            color: #FF0000;
        }

        .risk__num,
        .risk__stat {
            text-align: center;
            top: 21%;
            left: -3%;
            position: relative;
        }

        .current__vulnerability {
            border: 1px solid #ddd;
        }



        .vulnerability-box {
            cursor: pointer;
            padding: 5px;
            border-radius: 8px;
            background: #fff;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        }

        .vulnerability__icon {
            height: 65px;
            width: 65px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }


        .vulnerability__stat-text .risk__stat {
            color: #aaa;
            letter-spacing: 1px;
        }

        table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > td:first-child:before, table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > th:first-child:before {
            top: 12px;
            left: 4px;
            height: 14px;
            width: 14px;
            display: block;
            position: absolute;
            color: white;
            border: 2px solid white;
            border-radius: 14px;
            box-shadow: 0 0 3px #444;
            box-sizing: content-box;
            text-align: center;
            text-indent: 0 !important;
            font-family: 'Courier New', Courier, monospace;
            line-height: 14px;
            content: '+';
            background-color: #b2dd4c !important;
        }

        .donut-pie-chart {
            min-height: 352px;
        }

        /*******************
            Apexchart
         *******************/

        .apexcharts-menu-icon,
        .apexcharts-legend-marker,
        .apexcharts-legend-text,
        .apexcharts-text.apexcharts-xaxis-label {
            display: none !important;
        }


        #risks-by-severity-chart  tspan, #risks-by-severity-chart:hover {
            font-weight: 100;
            font-family: 'Arial';
            font-size: 1.8px !important;
        }

        #risks-closed-status-chart {
            margin-top: -10px;
        }


    </style>

@include('includes.assets-libs.datatable-css-libs')

@endsection

@section('content')

    <!-- breadcrumbs -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('risks.dashboard.generate-pdf-report') }}" class="btn btn-primary risk-export_btn width-md">Export to PDF</a>
                </div>
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>
    </div>
    <!-- end of breadcrumbs -->

    <!-- current vulnerability -->

    <div class="row">
        <div class="col-xl-12">
            <div class="risk-stat-div pb-1">
                <h4 class="risk-stat-text">Summary - Current Risks</h4>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($riskCountWithinRiskLevels as $riskCountWithinRiskLevel)
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="widget-rounded-circle card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-primary vulnerability__icon" style="background: {{$riskCountWithinRiskLevel['color']}};">
                                <i data-feather="alert-triangle"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1">{{$riskCountWithinRiskLevel['risk_count'] }}</h3>
                                <p class="text-muted mb-1 text-truncate">{{ decodeHTMLEntity($riskCountWithinRiskLevel['name'])}}</p>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div> <!-- end widget-rounded-circle-->
            </div>
        </div> <!-- end col-->
        @endforeach
    </div>
    <!-- end row-->

    <!-- current vulnerability ends -->

    <!-- pie charts -->
    <div class="row">
        <div class="col-xl-12">
            <!-- pie charts -->
            <div class="pie-charts">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="donut-pie-chart card-body">
                                <h4 class="header-title">Risks on the basis of severity</h4>
                                <div id="risks-by-severity-chart" style="height: 260px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="radial-pie-chart card-body">
                                <h4 class="header-title">Risks on the basis of closed status</h4>
                                <div id="risks-closed-status-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- risks-by-category starts -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="risks-by-category mb-2 card-body">
                    <div class="risk-category-div">
                        <h4 class="risk-category-text">Risks By Category</h4>
                        <div id="riskbycategory-chart" class="apexcharts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- risks-by-category  ends-->

    <!-- top risks table -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="top-risk pb-1">
                        <h4 class="top-risk-text">Top Risks</h4>
                    </div>
                    <div class="high-effect-risktable">
                        <table id="top-ten-risks-table" class="table display table-hover nowrap table-borderless w-100">
                            <thead class="table-light">
                            <tr>
                                <th>Risk ID</th>
                                <th>Risk Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Treatment Option</th>
                                <th>Likelihood</th>
                                <th>Impact</th>
                                <th>Inherent Risk Score</th>
                                <th>Residual Risk Score</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('plugins_js')
    <!-- feather icon -->
    <script src="{{asset('assets/feather/js/feather.min.js')}}"></script>
    <script src="{{ asset('assets/libs/underscore-js/underscore-umd-min.js') }}"></script>
    <!-- morris js -->
    <script src="{{asset('assets/libs/morris-js/morris.min.js')}}"></script>
    <script src="{{asset('assets/libs/raphael/raphael.min.js')}}"></script>

    <!-- apexcharts js -->
    <script src="{{asset('assets/js/pages/apexcharts.min.js')}}"></script>

    @include('includes.assets-libs.datatable-js-libs')


@endsection


@section('custom_js')
    <script nonce="{{ csp_nonce() }}">
        $(document).ready( function () {

            // Export
            $(".risk-export_btn").click(function(e)
            {   e.preventDefault();
                reportModal('risk',this.href);
            });


            $('#top-ten-risks-table').DataTable({
                serverSide: true,
                searching: false,
                responsive: true,
                ajax: {
                    "url": "{{ route('risks.dashboard.get-top-risks') }}",
                    "type": "GET"
                },
                columnDefs: [
                    { responsivePriority: 0, targets: 0 }, // Risk ID
                    { responsivePriority: 1, targets: 1 }, // Risk Title
                    { responsivePriority: 9, targets: 2 }, // Category
                    { responsivePriority: 5, targets: 3 }, // Status
                    { responsivePriority: 8, targets: 4 }, // Treatment Option
                    { responsivePriority: 7, targets: 5 }, // Likelihood
                    { responsivePriority: 6, targets: 6 }, // Impact
                    { responsivePriority: 4, targets: 7, className: "text-center" }, // Inherent Risk Score
                    { responsivePriority: 3, targets: 8, className: "text-center" }, // Residual Risk Score
                    { responsivePriority: 2, targets: -1, "orderable": false }
                ],
                order: [[8, "desc"]],
                "columnDefs": [
                {
                    "render": function ( data, type, row ) {
                        return $.fn.dataTable.render.text().display(data, type, row);
                    },
                    "targets": [1]
                }
            ],

            });

        }); // END OF DOCUMENT READY METHOD


        //feather icons
        feather.replace()


        //Donut piechart
        Morris.Donut({
            element: 'risks-by-severity-chart',
            colors: @json($riskLevelColors),
            data: @json((array_map(function($riskCountWithinRiskLevel) {

                return array(
                    'label' => decodeHTMLEntity($riskCountWithinRiskLevel['name']),
                    'value' => $riskCountWithinRiskLevel['risk_count']
                );
            }, $riskCountWithinRiskLevels)) )

        });

        //Risks on the basis of risks-closed-status-chart
        var options = {
            series: @json($closedRiskCountOfDifferentLevels),
            chart: {
                height: 288,
                type: 'radialBar',
            },
            plotOptions: {
                radialBar: {
                    dataLabels: {
                        name: {
                            fontSize: '6px',
                            fontFamily: 'Arial'
                        },
                        value: {
                            fontSize: '32px',
                            fontWeight: 400,
                            fontFamily: 'Arial',
                            formatter: function (val) {
                                return val
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total Closed',
                            fontSize: '24px',
                            fontFamily: 'Arial',
                            fontWeight: 500,
                            formatter: function (w) {
                                // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                                return @json( $closedRiskCountOfDifferentLevels ).reduce(function(total, num) {return total + num})
                            }
                        }
                    }
                }
            },
            labels: @json($riskLevelsList),
            colors: @json($riskLevelColors)
        };

        var chart = new ApexCharts(document.querySelector("#risks-closed-status-chart"), options);
        chart.render();

        // Apexchart stacked - risk by category
        var options = {
            series: @json($riskCountWithinRiskLevelForCategories),
            chart: {
                type: 'bar',
                stacked: true
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                },
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            xaxis: {
                categories: @json($riskRegisterCategoriesList),
                labels: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            yaxis: {
                title: {
                    text: undefined
                },
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            fill: {
                opacity: 1
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                offsetX: 40
            },
            colors: @json($riskLevelColors),
            dataLabels: {
                style: {
                    colors: ['#38414a']
                }
            }
        };

        // CODE TO ASSIGN HEIGHT STARTS HERE
        var categoriesLength = options.xaxis.categories.length

        function calculateChartHeight(categoriesLength) {
            // 33 increment for  100 start form 2nd bar
            let incrementRate = 32
            let chartHeight = 100

            if(categoriesLength > 1){
                for (var i = 1; i < categoriesLength; i++) {
                    chartHeight = chartHeight+ incrementRate
                }
            }

            return chartHeight
        }


        options.chart.height = (categoriesLength > 0) ? calculateChartHeight(categoriesLength) : 0

        // CODE TO ASSIGN HEIGHT ENDS HERE
        var chart = new ApexCharts(document.querySelector("#riskbycategory-chart"), options);
        chart.render();
    </script>

@endsection
