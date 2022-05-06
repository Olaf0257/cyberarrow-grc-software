@extends('layouts.pdf-report.layout')

@section('doc_head')
    <style>
        .list-group-flush > .list-group-item:last-child {
            border-bottom-width: 1px !important;
        }
    </style>

@endsection

@section('report-heading', 'Third Party Risks')

@section('content')
    <!-- Risks on the basis of severity and  closed status charts -->
    <div class="p-5">
    </div>

    <div class="container clearfix">
            <div class="float-start" style="width: 40%;">
                    <h3 class="text-center mb-3">Vendor Maturity</h3>
                    <ul class="list-group list-group-flush" style="font-size: 1.5rem">
                        @foreach($levels as $level)
                            <li class="list-group-item">
                                <strong>{{ $level['name'] }}</strong>
                                <span class="badge bg-primary rounded-pill float-end">
                            {{ $level['count'] }}
                            </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
         <div class="float-start" style="width: 60%;">
            <h3 class="text-center mb-3">Vendors on the basis of maturity</h3>
            <canvas id="risk-by-severity-chart"></canvas>
        </div>
    </div>

    <div class="p-5">
    </div>

    <div class="container clearfix">
        <div class="float-start" style="width: 40%;"></div>
        <div class="float-end" style="width: 60%;">
            <h3 class="text-center mb-3">Vendor risk questionnaire progress</h3>
            <canvas id="question_progress_chart"></canvas>
        </div>
       
    </div>


    <div style="page-break-after: always !important;">
    </div>

    <!-- TOP RISK TABLE STARTS HERE -->
    <div class="high-effect-risktable">
        <h3 class="text-center pt-5">Top Risks</h3>
        <table class="table table-striped" aria-describedby="table of top vendors">
            <thead>
            <tr>
                <th scope="col">Vendor Name</th>
                <th scope="col">Score</th>
                <th scope="col">Maturity</th>
                <th scope="col">Status</th>
                <th scope="col">Contact Name</th>
            </tr>
            </thead>

            <tbody>
            @foreach($top_vendors as $vendor)
                <!-- risk status -->
                @php
                    if($vendor->status == "active"){
                        $status = '<span class="badge bg-success badge-pill">Active</span>';
                    } else {
                    $status = '<span class="badge bg-danger badge-pill">Disabled</span>';
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $vendor->name }}</td>
                    <td class="text-center">{!! $vendor->score !!}</td>
                    <td class="text-center">{{ "Level " . $vendor->level }}</td>
                    <td class="text-center">{!! $status !!}</td>
                    <td class="text-center">{{ $vendor->contact_name }}</td>
                </tr>
            @endforeach
            @if(count($top_vendors) == 0)
                <tr>
                    <td colspan="7" class="text-center">No vendors found</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

@endsection


@section('page_js')

    <script type="text/javascript">
        'use strict';

        function drawGraphs() {
            /* Risk on the basis of severity */
            var totalCount = @json($levels).reduce(function(acc, val) { return acc + val['count']; }, 0);
            var progressCount = @json($projects_progress_pdf).reduce(function(acc, val) { return acc + val['count']; }, 0);

            var riskBySeverityChart = new Chart(document.getElementById("risk-by-severity-chart"), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data:  totalCount > 0 ? @json((array_map(function($level) {
                        return $level['count'];
                    }, $levels)) ) : [100],
                        backgroundColor: totalCount > 0 ? @json((array_map(function($level) {
                        return $level['color'];
                    }, $levels)) ) : ['#f5f6f8'],
                    }],
                    labels: totalCount > 0 ? @json((array_map(function($level) {
                        return $level['name'];
                    }, $levels)) ) : [],
                },
                options: {
                    cutoutPercentage: 70,
                    title : {
                        display: false
                    },
                    legend: {
                        display: true
                    },
                    plugins: {
                        // Change options for ALL labels of THIS CHART
                        labels: totalCount > 0 ? {
                            render: function (args) {
                                return args.value;
                            },
                            fontColor: '#000',
                            fontSize: 24,
                        } : false,
                        datalabels: false,
                    },
                    centerText: "Total : "+ totalCount
                }
            });

            var questionProgressChart = new Chart(document.getElementById("question_progress_chart"), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data:  progressCount > 0 ? @json((array_map(function($level) {
                        return $level['count'];
                    }, $projects_progress_pdf)) ) : [100],
                        backgroundColor: progressCount > 0 ? @json((array_map(function($level) {
                        return $level['color'];
                    }, $projects_progress_pdf)) ) : ['#f5f6f8'],
                    }],
                    labels: progressCount > 0 ? @json((array_map(function($level) {
                        return $level['name'];
                    }, $projects_progress_pdf)) ) : [],
                },
                options: {
                    cutoutPercentage: 70,
                    title : {
                        display: false
                    },
                    legend: {
                        display: true
                    },
                    plugins: {
                        // Change options for ALL labels of THIS CHART
                        labels: progressCount > 0 ? {
                            render: function (args) {
                                return args.value;
                            },
                            fontColor: '#000',
                            fontSize: 24,
                        } : false,
                        datalabels: false,
                    },
                    centerText: "Total : "+ progressCount
                }
            });

            // for center text
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
        };
        

        window.onload = function () {
            drawGraphs();   
        }
    </script>
@endsection
