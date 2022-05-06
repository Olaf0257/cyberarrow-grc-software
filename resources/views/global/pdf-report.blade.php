@extends('layouts.pdf-report.layout')

@section('doc_head')
<style>

</style>

@endsection

@section('report-heading', 'Global Report')

@section('content')
<div class="container clearfix pt-5 mb-5">
    <div class="float-start w-50">
        <h3 class="text-center pb-2">Task Completion Percentage</h3>
        <canvas id="task-completion-percentage-chart"></canvas>
    </div>
    <div class="float-start w-50">
         <h3 class="text-center pb-2">Task Monitor</h3>
        <ul class="list-group list-group-flush" style="font-size: 1.5rem">
            <li class="list-group-item ">
                <strong>All Upcomming</strong>
                <span class="badge bg-primary rounded-pill float-end">
                {{ $allUpcomingTasks }}
                </span>
            </li>
            <li class="list-group-item">
                <strong>Due Today</strong>
                <span class="badge bg-primary rounded-pill float-end">
                {{ $allDueTodayTasks }}
                </span>
            </li>
            <li class="list-group-item">
                <strong>Pass Due</strong>
                <span class="badge bg-primary rounded-pill float-end">
                {{ $allPassDueTasks }}
                </span>
            </li>
            <li class="list-group-item"></li>
        </ul>
    </div>
</div>

<div class="container clearfix pt-5 mt-5">
    <div class="float-start w-50">
        <h3 class="text-center pb-2">Implementation Progress</h3>
        <canvas id="implementation-progress-chart"></canvas>
    </div>

    <div class="float-start w-50">
        <h3 class="text-center pb-2">Control Status</h3>
        <ul class="list-group list-group-flush" style="font-size: 1.5rem">
            <li class="list-group-item ">
                <strong>All Controls</strong>
                <span class="badge bg-primary rounded-pill float-end">
                {{ $allControls }}
                </span>
            </li>
            <li class="list-group-item">
                <strong>Not Applicable</strong>
                <span class="badge bg-primary rounded-pill float-end">
                {{ $notApplicableControls }}
                </span>
            </li>
            <li class="list-group-item">
                <strong>Implemented Controls</strong>
                <span class="badge bg-primary rounded-pill float-end">
                {{ $implementedControls }}
                </span>
            </li>
            <li class="list-group-item">
                <strong>Under Review</strong>
                <span class="badge bg-primary rounded-pill float-end">
                {{ $underReviewControls }}
                </span>
            </li>
            <li class="list-group-item">
                <strong>Not Implemented Controls</strong>
                <span class="badge bg-primary rounded-pill float-end">
                {{ $notImplementedControls }}
                </span>
            </li>
            <li class="list-group-item"></li>
        </ul>
    </div>
</div>

<!--Calendar tasks -->
<div class="container mt-5">
    <h3 class="text-center mt-5 mb-3">Task Calendar ( {{ date('F, Y')}} )</h3>
    <table class="table calendar-tasks-table">
        <tbody>
            @foreach($calendarTasks as $index => $tasks)
            <tr style="background: rgba(208,208,208,.3)">
                <td> <strong>{{ date_format(date_create($index), "j F, Y") }}</strong></td>
                <td><strong>{{ date_format(date_create($index), "l")}}</strong></td>
            </tr>
            @foreach($tasks as $task)
            <tr>
                <td>
                    <a href="{{ $task->url }}">
                    {{ $task->title }}
                    </a>
                </td>
                <td>
                    <span class="p-2 rounded" style="background:{{$task->backgroundColor}};color: {{$task->textColor}}">
                    {{$task->status }}
                    </span>
                </td>
            </tr>
            @endforeach
            @endforeach
            <!-- no records case -->
            @if($calendarTasks->count() == 0)
            <tr>
                <td colspan="2" class="text-center">
                    No tasks found
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection

@section('page_js')
<script>
function drawGraphs() {


    var allControls = "{{ $allControls }}"
    var implementedControls = "{{ $implementedControls }}"
    var underReviewControls = "{{ $underReviewControls }}"
    var notImplementedControls = "{{ $notImplementedControls  }}"
    var notApplicableControls = "{{ $notApplicableControls }}"
    var applicableControls =  (allControls - notApplicableControls)

    // Completed task percentage widget
    var completedTasksPercent = 0;

    if (implementedControls > 0 && applicableControls > 0) {
        completedTasksPercent = Math.round(implementedControls * 100 / applicableControls);
    }


    var ctx = document.getElementById("task-completion-percentage-chart")


    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: 'doughnut',
        // The data for our dataset
        data: {
            labels: [
                "Completed",
                "Remaining"
            ],
            datasets: [{
                label: "Din ledelsesstil",
                backgroundColor: [
                    "{{ $globalSetting->secondary_color }}"
                ],
                data: [
                    completedTasksPercent,
                    100 - completedTasksPercent
                ],
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
                datalabels: false,
            },
            centerText: completedTasksPercent+' %'
        }
    });

    // Implementaion progress chart starts here
    var ctx = document.getElementById("implementation-progress-chart")

    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: 'doughnut',
        // The data for our dataset
        data: {
            labels: allControls > 0 ? [
                "Implemented",
                "Under Review",
                "Not Implemented"
            ] : [],
            datasets: [{
                backgroundColor: allControls > 0 ? [
                    "#359f1d","#5bc0de","#cf1110"
                ] : ['#f5f6f8'],
                data: allControls > 0 ? [
                    implementedControls,
                    underReviewControls,
                    notImplementedControls
                ] : [100],
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
                datalabels: false,
            },
            centerText: allControls
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

window.onload = function() {
    drawGraphs();
};
</script>
@endsection
