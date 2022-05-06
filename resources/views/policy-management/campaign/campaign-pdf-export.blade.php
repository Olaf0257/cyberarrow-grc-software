@extends('layouts.pdf-report.layout')
@section('doc_head')
<style>

</style>

@endsection

@section('report-heading', 'Policy Management')

@section('content')
<div style="page-break-after: always !important;">
</div>
<div class="container clearfix py-4">
    <h3 class="mb-5 text-center">Result for {{ $campaign->name }}</h3>

    <ul class="list-group list-group-flush mt-2" style="font-size: 1.6rem">
        <li class="list-group-item border-0 "><strong>Start Date: </strong> {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $campaign->launch_date, 'UTC')->setTimezone($campaign->timezone)->format('d-m-Y h:i A') }} {{$timezone}}</li>
        <li class="list-group-item border-0 "><strong>Due date: </strong>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $campaign->due_date, 'UTC')->setTimezone($campaign->timezone)->format('d-m-Y h:i A') }} {{$timezone}}</li>
        <li class="list-group-item border-0 "><strong>Group(s): </strong>
            @foreach($campaign->groups as $group)
                <span class="badge bg-primary text-info">{{ $group->name}}</span>
            @endforeach
        </li>
        <li class="list-group-item border-0 "><strong>Policy(ies): </strong>
            @foreach($campaign->policies as $policy)
                <span class="badge bg-primary text-info">{{ $policy->display_name}}</span>
            @endforeach
        </li>
    </ul>
</div>

<div class="container clearfix my-5">
    <div class="float-start mt-3" style="width:33.33%">
        <h3 class="text-center pb-2">Email Sent</h3>
        <canvas id="email-sent-canvas"></canvas>
    </div>

    <div class="float-start mt-3" style="width:33.33%">
        <h3 class="text-center pb-2">Acknowledged</h3>
        <canvas id="policies-acknowledged-canvas"></canvas>
    </div>

    <div class="float-start mt-3" style="width:33.33%">
        <h3 class="text-center pb-2">Completion</h3>
        <canvas id="policies-completion-canvas"></canvas>
    </div>
</div>


<div class="high-effect-risktable mt-5">
    <h3 class="text-center pt-5 mb-3">Campaign Users</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Status</th>

            </tr>
        </thead>

        <tbody>
        @php
            $campaignUsers = $campaign->users;

        @endphp

        @if(count($campaignUsers) > 0)
            @foreach($campaignUsers as $campaignUser)
                <tr>
                    <td>
                    {{ $campaignUser->first_name }}
                    </td>
                    <td>
                    {{ $campaignUser->last_name }}
                    </td>
                    <td>
                    {{ $campaignUser->email }}
                    </td>
                    <td>
                    <span class="badge bg-primary text-info" style="background:{{$campaignUser->user_acknowledgement_status['color']}}">{{$campaignUser->user_acknowledgement_status['status']}}</span>
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
@php
    $totalEmailSentPercentage = ($emailSentSuccess && $totalEmailSent) ?  $emailSentSuccess * 100 / $totalEmailSent : 0;
@endphp
<script type="text/javascript">
function drawGraphs() {
    var ctx = document.getElementById("email-sent-canvas")


    var emailSentChart = new Chart(ctx, {
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
                    "{{ $totalEmailSentPercentage }}",
                    100 - "{{ $totalEmailSentPercentage }}"
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
                    text: "{{ $totalEmailSent }}"
                }
            },
            plugins: {
                // Change options for ALL labels of THIS CHART
                labels: false,
                datalabels: false
            }
        }
    });


    // Acknowledged policies chart
    var acknowledgedPoliciesChart = new Chart(document.getElementById("policies-acknowledged-canvas"), {
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
                    "{{ $completedAcknowledgementsPercentage }}",
                    "{{ 100 - $completedAcknowledgementsPercentage }}"
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
                    text: "{{ $completedAcknowledgements }}",
                }
            },
            plugins: {
                // Change options for ALL labels of THIS CHART
                labels: false,
                datalabels: false
            }
        }
    });

    // Acknowledgment completion chart
    var acknowledgmentCompletionChart = new Chart(document.getElementById("policies-completion-canvas"), {
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
                    "{{ $completedAcknowledgementsPercentage }}",
                    "{{ 100 - $completedAcknowledgementsPercentage }}"
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
                    text: "{{ $completedAcknowledgementsPercentage }} %",
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
