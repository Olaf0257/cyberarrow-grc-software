@extends('layouts.pdf-report.layout')

@section('doc_head')
    <style>
        .badge.text-info{
            color: #38414a !important;
        }
    </style>

@endsection

@section('report-heading', 'Third Party Risk Project')

@section('content')
    <div class="p-5">
    </div>

    <div class="container clearfix">
        <div class="float-start" style="width: 40%;">
            <h3 class="text-center mb-3">Vendor Maturity</h3>
            <ul class="list-group list-group-flush" style="font-size: 1.5rem">

                <li class="list-group-item border-0 "><strong>Start Date: </strong> {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $project->launch_date, 'UTC')->setTimezone($project->timezone)->format('d-m-Y h:i A') }} {{$timezone}}</li>
                <li class="list-group-item border-0 "><strong>Due date: </strong>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $project->due_date, 'UTC')->setTimezone($project->timezone)->format('d-m-Y h:i A') }} {{$timezone}}</li>
                <li class="list-group-item border-0 "><strong>Vendor: </strong> <span
                        class="badge bg-primary">{{ $project->vendor->name}}</span>
                </li>
                <li class="list-group-item border-0 "><strong>Questionnaire: </strong><span
                        class="badge bg-primary">{{ $project->questionnaire->name}}</span>
                </li>
                <li class="list-group-item border-0 "><strong>Frequency: </strong>{{ $project->frequency }} </li>
            </ul>
        </div>
        <!-- risk on the basis of severity -->
        <div class="float-start" style="width: 60%;">
            <h3 class="text-center mb-3">Vendor Score</h3>
            <canvas id="score-canvas" style="margin: auto"></canvas>
        </div>
    </div>


    <div class="high-effect-risktable mt-5">
        <h3 class="text-center pt-5 mb-3">Assessment outcome</h3>
        <table class="table table-striped" aria-describedby="questions and answers table">
            <thead>
            <tr>
                <th scope="col">Question</th>
                <th scope="col">Answer</th>
            </tr>
            </thead>

            <tbody>
            @php
                $questions = $project->questionnaire->questions;
            @endphp

            @if(count($questions) > 0)
                @foreach($questions as $question)
                    <tr>
                        <td>
                            {{ $question->text }}
                        </td>
                        <td>
                            {{ $question->single_answer->answer ?? "Not answered yet." }}
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center">No task found</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
@endsection

@section('page_js')

    <script type="text/javascript">
        function drawGraphs() {
            var ctx = document.getElementById("score-canvas")

            var scoreChart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'doughnut',
                // The data for our dataset
                data: {
                    labels: [
                        "Completed",
                        "Remaining"
                    ],
                    datasets: [{
                        data: [
                            "{{ $project->score }}",
                            "{{ 100 - $project->score }}"
                        ],
                        backgroundColor: ["{{ $project->color }}"]
                    }]
                },

                // Configuration options go here
                options: {
                    cutoutPercentage: 70,
                    legend: {
                        display: true
                    },
                    plugins: {
                        // Change options for ALL labels of THIS CHART
                        labels: false,
                        datalabels: false
                    },
                    centerText: "Score: {!! $project->score !!}%"
                }
            });

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
        }

        window.onload = function () {
            drawGraphs();
        };
    </script>
@endsection
