@extends('layouts.layout')

@php $pageTitle = "Campaign - Policy Management"; @endphp

@section('plugins_css')
<!-- vis-timeline -->
<link rel="stylesheet" href="{{asset('assets/libs/vis-timeline/vis-timeline-graph2d.min.css')}}">

@endsection

@section('custom_css')
<style type="text/css" nonce="{{ csp_nonce() }}">
    /* Chart.js */
    @-webkit-keyframes chartjs-render-animation{from{opacity:0.99}to{opacity:1}}@keyframes chartjs-render-animation{from{opacity:0.99}to{opacity:1}}.chartjs-render-monitor{-webkit-animation:chartjs-render-animation 0.001s;animation:chartjs-render-animation 0.001s;}

    #campaign-timeline .vis-timeline {
        border-left: none;
        border-right: none;
        border-top: none;
    }

    #campaign-timeline .timline-item-content {
        text-align: justify;
    }

    .custom-limit label{
        display: flex;
    }

    .custom-limit select{
        margin: 0 3px;
        width: auto;
    }

    .custom-limit span{
        margin: auto;
        font-weight: normal;
    }

    .table-hover #campaign-users-wp tr td.user-activities:hover{
        background: #fff;
    }

    .campaign-brief-details .campaign-info-list li strong {
        display: inline-block;
        width: 89px;
        font-weight: bolder;
    }

    .campaign-brief-details .campaign-info-list li .badge{
        color: #38414a!important;
    }
    .btn-primary.dropdown-toggle {
    }
        background-color: var(--secondary-color) !important;
        border-color: var(--secondary-color) !important;
    }

    .popover {
      display: none;
      position: absolute;
      top: -20px;
      left: 0px;
      background: yellow;
      z-index: 1;
    }

    .wrapper:hover .popover {
      display: block;
    }

    [class*=" dripicons-"]:before, [class^=dripicons-]:before{
        line-height: 1.4;
    }

    .node-icon i:before{
        line-height: inherit;
    }

    .campaign-brief-details-graph .card-body {
        box-shadow: none;
        webkit-box-shadow: none;
    }







</style>
<style nonce="{{ csp_nonce() }}">
    .user-activity-lists .list-group-item{
        border: none;
    }

    .user-activity-lists{
        position: relative;
    }

    .user-activity-lists:before{
        background-color: #28a745;
        bottom: 30px;
        content: "";
        left: 40px;
        position: absolute;
        top: 30px;
        width: 1px;
        z-index: 1;
    }

    .user-activity-node .node-icon{
        padding: 10px 12px;
        padding-bottom: 7px;
        border-radius: 50%;
        color: #fff;
        background: #28a745;
        width: 40px;
        height: 40px;
        display: inline-block;
        z-index: 2;
    }

    .user-activity-node .node-icon .node-icon-green{
        background: #28a745;
    }

    .user-activity-node .user-activity-node-title{
        font-weight: bold;
    }
</style>
@endsection

@section('content')

<!-- breadcrumbs -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('policy-management.campaigns') }}">Campaigns</a></li>
                    <li class="breadcrumb-item"><a href="#">Campaign details</a></li>
                </ol>
            </div>
            <h4 class="page-title">{{ $pageTitle }}</h4>
        </div>
    </div>
</div>
<!-- end of breadcrumbs -->

<!-- shows flash messages here -->
@include('includes.flash-messages')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body campaign-brief-details">
                <div class="campaign-brief-details-inner">
                    <div class="clearfix">
                        <div class="dropdown show float-end">
                            <button class="btn btn-primary theme-bg-secondary" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Export
                            </button>

                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item" href="{{ route('policy-management.campaigns.export-pdf', $campaign->id) }}">PDF</a>
                                <a class="dropdown-item" href="{{ route('policy-management.campaigns.export-csv', $campaign->id) }}">CSV</a>
                            </div>
                        </div>
                    </div>

                    <h3>Result for {{ decodeHTMLEntity($campaign->name) }}</h3>

                    <ul class="list-group campaign-info-list list-group-flush mt-2 campaign-card-date">
                        <li class="list-group-item border-0 ps-0">
                            <strong>Start Date: </strong>
                            <span class="text-muted">{{ $campaign->launch_date }}</span>
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            <strong>Due date: </strong>
                            <span class="text-muted">{{ $campaign->due_date }}</span>
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            <strong>Group(s): </strong>
                            @foreach($campaign->groups as $group)
                                <span class="badge bg-soft-info text-info">{{ $group->name}}</span>
                            @endforeach
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            <strong>Policy(ies): </strong>
                            @foreach($campaign->policies as $policy)
                                <span class="badge bg-soft-info text-info">{{ decodeHTMLEntity($policy->display_name)}}</span>
                            @endforeach
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            <strong>Auto-enroll: </strong>
                            <span class="text-muted">{{ Str::ucfirst($campaign->auto_enroll_users) }}</span>
                        </li>
                    </ul>
                </div>
                <!-- Campaign timeline -->
                <div class="row my-5">
                    <div class="col-12">
                        <h4 class="text-center mb-4">Campaign Timeline</h4>

                        <div id="campaign-timeline">
                            <!-- Timline zoom reset button -->
                            <button id="campaign-timeline__reset-zoom" class="btn btn-primary theme-bg-secondary" style="display:none;">Reset zoom</button>

                        </div>
                    </div>
                </div>

                <div class="row my-1">
                    <div id="visualization" class="w-100">
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 campaign-brief-details-graph offset-2 row">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title text-center mb-3">Email Sent</h4>
                                        <div id="email-sent-chart" width="100%" height="100%"></div>
                                    </div> <!-- end card-->
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title text-center mb-3">Acknowledged</h4>
                                        <div id="policies-acknowledged-chart" width="100%" height="100%"></div>
                                    </div> <!-- end card-->
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title text-center mb-3">Completion</h4>
                                        <div id="policies-completion-chart" width="100%" height="100%"></div>
                                    </div> <!-- end card-->
                                </div>
                            </div>
                        </div>                        
                    </div> <!-- end col-->
                </div>
                <!-- End row -->
            </div>
        </div>
    </div>
</div>


<div class="row">
    <!--dropzone-->
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body table-container">
                <div class="mb-3 clearfix">
                    <h3 class="mb-4">Details</h3>
                    <!-- chage length of data -->
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="custom-limit">
                                <label>
                                    <span>
                                        Show
                                    </span>
                                    <select name="user_list_length" class="form-select form-select-sm form-control form-control-sm">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <span>
                                        Entries
                                    </span>
                                </label>

                            </div>
                        </div>
                        <div class="col-lg-10">
                            <div class="float-end form-left-mobile">
                                <div class="ms-3 mb-3">
                                    <span>Search:</span>
                                    <input type="text" name="filter_by_user_name" class="form-control form-control-sm">
                                </div>
                            </div>
                            <a class="stop-session-check sendreminder" href="{{ route('policy-management.campaigns.send-users-reminder', $campaign->id) }}">
                                <button type="button" class="btn btn-sm btn-primary waves-effect waves-light float-end ">
                                    Send Reminder
                                </button>
                            </a>
                        </div>
                    </div>
                </div>

                <table class="table table-centered display table-hover w-100">
                    <thead>
                        <tr>
                            <th></th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody id="campaign-users-wp">

                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- end col -->
</div>
<!-- end row -->
@endsection

@section('plugins_js')
<script src="{{ asset('assets/libs/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- vis-timeline js -->
<script src="{{asset('assets/libs/vis-timeline/vis-timeline-graph2d.min.js')}}"></script>

<!-- Chart JS -->
<script src="{{ asset('assets/libs/chart-js/Chart.bundle.min.js') }}"></script>

<!-- apexcharts js -->
<script src="{{asset('assets/js/pages/apexcharts.min.js')}}"></script>
@endsection

@section('custom_js')
<script nonce="{{ csp_nonce() }}">
$(document).ready(function () {


    @php
        $totalEmailSentPercentage = ($emailSentSuccess && $totalEmailSent) ?  $emailSentSuccess * 100 / $totalEmailSent : 0;
    @endphp

    var options = {
        chart: {
            width: '100%',
            height: 150,
            type: "radialBar"
        },
        colors: ["rgb(178, 221, 76)"],
        series: ["{{ $totalEmailSentPercentage }}"],

        plotOptions: {
            radialBar: {
                hollow: {
                    margin: 10,
                    size: "60%"
                },

                dataLabels: {
                    name: {
                        show: false
                    },
                    value: {
                        show: true,
                        fontSize: '14px',
                        offsetX: 0,
                        offsetY: 0,
                        formatter: function (val) {
                            return "{{ $emailSentSuccess }}"
                        }
                    },
                }
            }
        },

        stroke: {
            lineCap: "round",
        },
        labels: [""]
    };

    var emailSentChart = new ApexCharts(document.querySelector("#email-sent-chart"), options);

    emailSentChart.render();


    /***
     * Policies acknowledged chart
     */
    @php
        $completedAcknowledgementPercentage = 0;

        if ($completedAcknowledgements && $totalAcknowledgements) {
            $completedAcknowledgementPercentage = ($completedAcknowledgements * 100) / $totalAcknowledgements;
        }
    @endphp
    var options = {
        chart: {
            width: '100%',
            height: 150,
            type: "radialBar"
        },
        colors: ["#f7b84b"],
        series: ["{{ round($completedAcknowledgementPercentage) }}"],

        plotOptions: {
            radialBar: {
                hollow: {
                    margin: 10,
                    size: "60%"
                },

                dataLabels: {
                    name: {
                        show: false
                    },
                    value: {
                        show: true,
                        fontSize: '14px',
                        offsetX: 0,
                        offsetY: 0,
                        formatter: function (val) {
                            return "{{ $completedAcknowledgements }}"
                        }
                    },
                }
            }
        },

        stroke: {
            lineCap: "round",
        },
        labels: [""]
    };

    var PoliciesAcknowledgedChart = new ApexCharts(document.querySelector("#policies-acknowledged-chart"), options);

    PoliciesAcknowledgedChart.render();



    /***
     * policies-completion-chart
     */
    var options = {
        chart: {
            width: '100%',
            height: 150,
            type: "radialBar"
        },
        colors: ["#28a745"],
        series: ["{{ round($completedAcknowledgementPercentage) }}"],

        plotOptions: {
            radialBar: {
                hollow: {
                    margin: 10,
                    size: "60%"
                },

                dataLabels: {
                    name: {
                        show: false
                    },
                    value: {
                        show: true,
                        fontSize: '14px',
                        offsetX: 0,
                        offsetY: 0,
                        formatter: function (val) {
                            return "{{ round($completedAcknowledgementPercentage) }} %"
                        }
                    },
                }
            }
        },

        stroke: {
            lineCap: "round",
        },
        labels: [""]
    };

    var policiesCompletionChart = new ApexCharts(document.querySelector("#policies-completion-chart"), options);

    policiesCompletionChart.render();

    // changing expan icon
    $(document).on('click', '.expandable-icon-wp', function () {
        $(this).find('.fas').toggleClass('fa-chevron-right fa-chevron-down');
    });

    // Campaign timeline script
    // DOM element where the Timeline will be attached
    var container = document.getElementById('campaign-timeline');
    var campaingTimelineData = [];

    <?php foreach ($campaignTimeline as $key => $val) {
    ?>
        campaingTimelineData.push(<?php echo $val; ?>);
    <?php
} ?>

    // Create a DataSet (allows two way data-binding)
    var items = new vis.DataSet(campaingTimelineData);

    // Configuration for the Timeline
    var options = {
        stack: false,
        maxMinorChars: 20,
        height: 280,
        tooltip: {
            delay: 1
        }
    };

    // Create a Timeline
    var counter = 0;

    var timeline = new vis.Timeline(container, items, options);

    //show zoom reset botton on zoom in and out
    timeline.on('rangechanged', function(s) {
        counter++
        if(counter > 1)
        {
            $("#campaign-timeline__reset-zoom").show();
        }
    });
     /* Handling Timeline zoom reset */
     $(document).on('click', '#campaign-timeline__reset-zoom', function() {
            timeline.fit()
            counter = 0;
            $(this).hide();
        })

    // Campaign timeline script end

    // Campaign users list scripts

    renderUsers()

    function renderUsers(renderUsersURL = null){

        if (!renderUsersURL) {
            renderUsersURL = "{{ route('policy-management.campaigns.render-users', $campaignId) }}";
        }
        $.get(renderUsersURL , {
            filter_by_user_name: function() {
                return $("input[name=filter_by_user_name]").val()
            },
            page_length: function(){
                return $("select[name=user_list_length]").val()
            }
        })
        .done(function(response) {
            console.log(response)
            if (response.success) {
                $("#campaign-users-wp").html(response.data)
            } else {
                $("#campaign-users-wp").html("")
            }
        })
        .fail(function() {
            alert( "error" );
        })
    }

    $(document).on('click', '.campaign-users-pagination a.page-link', function(event){
        event.preventDefault()

        let renderUsersURL = this.href

        renderUsers(renderUsersURL)
    })

    // Campaign searching
    $(document).on('keyup', 'input[name=filter_by_user_name]', function() {
        renderUsers()
    })

    // change length

    $(document).on('change', 'select[name="user_list_length"]', function(){
        renderUsers()
    })
    $(document).on("click", ".sendreminder", function(event) {
        event.preventDefault()

        swal({
                title: "Are you sure?",
                text: "Do you want to send user reminders?",
                showCancelButton: true,
                confirmButtonColor: '#ff0000',
                confirmButtonText: 'Yes, send it!',
                imageUrl: '{{ asset('assets/images/warning.png') }}',
                imageWidth: 120
            }).then(confirmed => {
                if(confirmed.value && confirmed.value == true){
                    window.location.href = this.href
                }
            });
    });
})
</script>
@endsection
