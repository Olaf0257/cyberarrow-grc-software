@extends('layouts.pdf-report.layout')

@section('doc_head')
<style nonce="{{ csp_nonce() }}">
table.calendar-tasks-table tr{
    height: 10px;
}
</style>
@endsection

@section('report-heading', 'Compliance Report')

@section('content')
    
    <!-- Wrapper -->

<div class="container clearfix pt-5">
    <div class="float-start w-50 mt-3">
        <h3 class="text-center pb-2">Task Completion Percentage</h3>
        <canvas id="canvas"></canvas>
    </div>

    <div class="float-start w-50 mt-3">
        <h3 class="text-center pb-2">My task & approval monitor</h3>
        <ul class="list-group list-group-flush" style="font-size: 1.5rem">
            <li class="list-group-item ">
                <strong>All Upcomming</strong> 
                <span class="badge bg-primary rounded-pill float-end">
                {{ $myAllActiveTasks }}
                </span>
            </li>
            <li class="list-group-item">
                <strong>Due Today</strong> 
                <span class="badge bg-primary rounded-pill float-end">
                {{ $totalTaskDueToday }}
                </span>
            </li>
            <li class="list-group-item">
                <strong>Pass Due</strong> 
                <span class="badge bg-primary rounded-pill float-end">
                {{ $totalMyTaskPassDue }}
                </span>
            </li>
            <li class="list-group-item">
                <strong>Under Review</strong> 
                <span class="badge bg-primary rounded-pill float-end">
                {{ $totalUnderReviewMyTasks }}
                </span>
            </li>   
            <li class="list-group-item">
                <strong>Require my Approval</strong> 
                <span class="badge bg-primary rounded-pill float-end">
                {{ $totalNeedMyApprovalTasks }}
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

<script nonce="{{ csp_nonce() }}" type="text/javascript">
'use strict';


function drawGraphs() {

    var ctx = document.getElementById("canvas")


    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: 'doughnut',
        // The data for our dataset
        data: {
            labels: [
                "Completed",
                "remaining"
            ],
            datasets: [{
                backgroundColor: [
                    "{{ $globalSetting->secondary_color }}",
                ],
                data: [
                    "{{$myCompletedTasksPercent}}",
                    "{{100 - $myCompletedTasksPercent}}"
                ],
            }]
        },

        // Configuration options go here
        options: {
            cutoutPercentage: 70,
            legend: { 
                display: false
            },
            elements: {
                center: {
                    text: '{{$myCompletedTasksPercent}} %',
                }
            },
            plugins: {
                // Change options for ALL labels of THIS CHART
                labels: false,
                datalabels: false
            }
        }
    });
}

window.onload = function() {
    drawGraphs();
};
</script>
@endsection