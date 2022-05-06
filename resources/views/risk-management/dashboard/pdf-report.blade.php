@extends('layouts.pdf-report.layout')

@section('doc_head')
    <style>
        table thead {
            font-size: 22px;
        }
    </style>

@endsection

@section('report-heading', 'Risk Management')

@section('content')
    <!-- Risks on the basis of severity and  closed status charts -->
    <div class="p-5">
    </div>

    <div class="container clearfix">
        <div class="float-start" style="width: 40%;">
            <h3 class="text-center mb-3">Current Vulnerabilities Summary</h3>
            <ul class="list-group list-group-flush" style="font-size: 1.5rem">
                @foreach($riskCountWithinRiskLevels as $riskCountWithinRiskLevel)
                    <li class="list-group-item">
                        <strong>{{ $riskCountWithinRiskLevel['name'] }}</strong>
                        <span class="badge bg-primary rounded-pill float-end">
                    {{ $riskCountWithinRiskLevel['risk_count'] }}
                    </span>
                    </li>
                @endforeach
                <li class="list-group-item"></li>
            </ul>
        </div>
        <!-- risk on the basis of severity -->
        <div class="float-start" style="width: 60%;">
            <h3 class="text-center mb-3">Risks on the basis of severity</h3>
            <canvas id="risk-by-severity-chart"></canvas>
        </div>
    </div>

    <div class="p-5">
    </div>

    <div class="container mt-5">
        <div class="float-start" style="width: 40%;"></div>
        <div class="float-end" style="width: 60%;">
        <h3 class="text-center mb-3">Risks on the basis of closed status</h3>
        <canvas id="risk-by-closed-status-chart"></canvas>
        </div>
    </div>

    <div style="page-break-after: always !important;">
    </div>

    <!-- Risk by Category -->
    <div class="container" style="padding-top: 55px">
        <h3 class="text-center mb-3">Risks By Category</h3>
        <canvas id="risk-by-categories"></canvas>
    </div>

    <div style="page-break-after: always !important;">
    </div>
    <!-- TOP RISK TABLE STARTS HERE -->

    <div class="high-effect-risktable">
        <h3 class="text-center pt-5">Top Risks</h3>
        <table class="table table-striped">
            <caption>Table of Top Risks</caption>
            <thead>
            <tr>
                <th scope="col">Risk ID</th>
                <th scope="col">Risk Title</th>
                <th scope="col">Category</th>
                <th scope="col">Status</th>
                <th scope="col">Treatment Option</th>
                <th scope="col">Likelihood</th>
                <th scope="col">Impact</th>
                <th scope="col">Inherent Risk Score</th>
                <th scope="col">Residual Risk Score</th>
            </tr>
            </thead>

            <tbody>
            @php
                $riskStatusClass = [
                    'Mitigate' => 'bg-danger',
                    'Accept' => 'bg-success',
                    'Closed' => 'bg-warning'
                ];
            @endphp
            @foreach($topTenRisks as $topTenRisk)
                <!-- risk status -->
                @php
                    $status = '<span class="badge bg-danger rounded-pill">Open</span>';
                    $mappedControl = $topTenRisk->controls()->first();

                    if($mappedControl){
                        if(($topTenRisk->treatment_options == 'Mitigate') && ($mappedControl->status == 'Implemented')){
                            $status = '<span class="badge bg-success rounded-pill">Closed</span>';
                        }
                    }
                    if($topTenRisk->treatment_options == 'Accept' || $topTenRisk->treatment_options == 'Closed'){
                        $status = '<span class="badge bg-success rounded-pill">Closed</span>';
                    }
                @endphp
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td><a href="{{ route('risks.register.risks-show', $topTenRisk->id ) }}"
                           class="name__link">{{ $topTenRisk->name }}</a></td>
                    <td>{{ $topTenRisk->category->name }}</td>
                    <td>{!! $status !!}</td>
                    <td>
                        <span class="badge {{$riskStatusClass[$topTenRisk->treatment_options]}} rounded-pill">{{ $topTenRisk->treatment_options }}</span>
                    </td>
                    <td>{{ $topTenRisk->riskMatrixLikelihood ?  $topTenRisk->riskMatrixLikelihood->name : '' }}</td>
                    <td>{{ $topTenRisk->riskMatrixImpact ? $topTenRisk->riskMatrixImpact->name : '' }}</td>
                    <td>{{ $topTenRisk->inherent_score }}</td>
                    <td>{{ $topTenRisk->residual_score }}</td>
                </tr>
            @endforeach
            @if(count($topTenRisks) == 0)
                <tr>
                    <td colspan="7" class="text-center">No task found</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
@endsection


@section('page_js')

    <!-- apexcharts js -->
    <script type="text/javascript">
        'use strict';

        function drawGraphs() {
            
            /* Risk on the basis of severity */
            var totalSeverityRiskCount = @json($riskCountWithinRiskLevels).reduce(function(acc, val) { return acc + val['risk_count']; }, 0);
            
            var riskBySeverityChart = new Chart(document.getElementById("risk-by-severity-chart"), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: totalSeverityRiskCount > 0 ? @json((array_map(function($riskCountWithinRiskLevel) {
                        return $riskCountWithinRiskLevel['risk_count'];
                    }, $riskCountWithinRiskLevels)) ) : [100],
                        backgroundColor: totalSeverityRiskCount > 0 ? @json($riskLevelColors) : ['#f5f6f8']
                    }],
                    labels: totalSeverityRiskCount > 0 ? @json($riskLevelsList) : []
                },
                options: {
                    cutoutPercentage: 70,
                    title: {
                        display: false
                    },
                    legend: {
                        display: true
                    },
                    plugins: {
                        // Change options for ALL labels of THIS CHART
                        labels: totalSeverityRiskCount > 0 ? {
                            render: function (args) {
                                return args.value;
                            },
                            fontColor: '#000',
                            fontSize: 24,
                        } : false,
                        datalabels: false,
                    },
                    centerText: "Total Risk: "+ totalSeverityRiskCount
                }
            });

//Risks on the basis of risks-closed-status-chart
            var totalClosedRisks = @json($closedRiskCountOfDifferentLevels).reduce(function(acc, val) { return acc + val; }, 0);

            var options = {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: totalClosedRisks > 0 ? @json((array_map(function($closedRiskCount) {
                        return $closedRiskCount;
                    }, $closedRiskCountOfDifferentLevels))) : [100],
                        backgroundColor: totalClosedRisks > 0 ? @json($riskLevelColors) : ['#f5f6f8']
                    }],
                    labels: totalClosedRisks > 0 ? @json($riskLevelsList) : []
                },
                options: {
                    cutoutPercentage: 70,
                    title: {
                        display: false
                    },
                    legend: {
                        display: true
                    },
                    plugins: {
                        // Change options for ALL labels of THIS CHART
                        labels: totalClosedRisks > 0 ? {
                            render: function (args) {
                                return args.value;
                            },
                            fontColor: '#000',
                            fontSize: 24,
                        } : false,
                        datalabels: false,
                    },
                    centerText: "Total Risk: "+ totalClosedRisks
                }
            };

            new Chart(document.querySelector("#risk-by-closed-status-chart"), options);

            Chart.pluginService.register({
                beforeDraw: function (chart) {
                    if (chart.options.centerText) {
                        var width = chart.chart.width,
                            height = chart.chart.height,
                            ctx = chart.chart.ctx;

                        ctx.restore();
                        var fontSize = (height / 200).toFixed(2); // was: 114
                        ctx.font = fontSize + "em sans-serif";
                        ctx.textBaseline = "middle";

                        var text = chart.options.centerText, // "75%",
                            textX = Math.round((width - ctx.measureText(text).width) / 2),
                            textY = height / 2 - (chart.titleBlock.height - 15);

                        ctx.fillText(text, textX, textY);
                        ctx.save();
                    }
                }
            });

            /* Risk by categories chart script starts here */
            var barOptions_stacked = {
                title: {
                    display: false
                },
                tooltips: {
                    enabled: false
                },
                hover: {
                    animationDuration: 0
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            display: false
                        },
                        scaleLabel: {
                            display: false
                        },
                        gridLines: {
                            display: false
                        },
                        stacked: true
                    }],
                    yAxes: [{
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            fontFamily: "'Open Sans Bold', sans-serif",
                            fontSize: 20,
                            // display: false
                        },
                        stacked: true,
                        barThickness: 30
                    }]
                },
                legend: {
                    display: true
                },
                plugins: {
                    // Change options for ALL labels of THIS CHART
                    datalabels: {
                        color: "#444",
                        align: "center",
                        font: {
                            size: '20'
                        },
                        // formatter: function(value, context) {
                        //     return context.chart.data.labels[context.dataIndex];
                        // },
                        display: function (context) {
                            return context.dataset.data[context.dataIndex] !== 0; // or >= 1 or ...
                        }
                    }
                },
                pointLabelFontFamily: "Quadon Extra Bold",
                scaleFontFamily: "Quadon Extra Bold",
            };

            var ctx = document.getElementById("risk-by-categories");

            var myChart = new Chart(ctx, {
                // type: 'bar',
                type: 'horizontalBar',
                data: {
                    labels:  @json($riskRegisterCategoriesList),
                    datasets:@json((array_map(function($riskCountWithinRiskLevelForCategory) {
                    $riskCountWithinRiskLevelForCategory['label'] = $riskCountWithinRiskLevelForCategory['name'];
                    $riskCountWithinRiskLevelForCategory['backgroundColor'] = $riskCountWithinRiskLevelForCategory['color'];

                    return $riskCountWithinRiskLevelForCategory;
                }, $riskCountWithinRiskLevelForCategories))
            )
                },

                options: barOptions_stacked,
            });

        }

        window.onload = function () {
            drawGraphs();
        };

    </script>
@endsection
