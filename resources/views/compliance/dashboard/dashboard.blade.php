@extends('layouts.layout')

@php($pageTitle = "EBDAA GRC")

@section('title', $pageTitle)

@section('plugins_css')
<!-- incliding fullcalendar libs -->
@include('includes.assets-libs.fullcalendar-io-css-libs')
<link rel="stylesheet" href="{{ asset('assets/css/full-calendar-tooltip-customize.css') }}">

@endsection

@section('custom_css')
<style nonce="{{ csp_nonce() }}">
     .card-body {
        padding: 0;
    }

    .title {
        padding: 12px 10px;
    }

    .title h4 {
        text-align: center;
        color: #fff;
    }

    .title,
    .second .title,
    .third .title {
        background: var(--primary-color);
    }

    .task, .approval {
        padding: 10px 8px;
    }

    .second i,
    .third i {
        color: var(--secondary-color);

    }

    .second i, .second h5,
    .third i, .third h5{
        display: flex;
        justify-content: center;
    }

    hr {
        display: block;
        height: 1px;
        border: 0;
        border-top: 1px solid #ccc;
        margin: 1em 0;
        padding: 0;
    }

    .dial {
        color: var(--secondary-color) !important;
        border: none;
        font: bold 24px Arial;
        text-align: center;
    }

    .card-body ul.rect-div {
        display: flex;
        justify-content: flex-end;
        margin-top: -42px;
    }

    .card-body ul li.rect {
        border: none;
    }

    .card-body ul li.rect .badge {
        padding: 1px 6px;
        height: 9px;
        width: 5px;
        border-radius: 0;

    }

    .card-body {
        padding: 1.1rem;
    }

    @media (min-width: 320px) and (max-width: 460px)  {


        .card-body ul.rect-div {
            display: flex;
            justify-content: center;
            margin-top: 0;
        }

        .card-body ul li span.status-color {
            display: flex;
            justify-content: center;
            width: 5px;
            border-radius: 0;
        }

        .card-body ul li span.status-text {
           font-size: 10px;
           font-weight: 600;
           display: block;
           white-space: normal;
        }

        ul li.rect {
            padding: 2px 8px;
        }

        .card-body h5.head-text {
            display: none;
        }



        .fc-toolbar.fc-header-toolbar {
            display: flex;
            flex-direction: column;
        }

    }

    @media (min-width: 320px) and (max-width: 375px) {
        .second h5, .third h5 {
            font-size: 13px;
        }

        .boxx .go-btn {
            min-width: 80px;
        }
    }

    @media (min-width: 461px) and (max-width: 608px) {
        .card ul li span.status-text {
            display: block;
        }

        .card h5.head-text {
            display: none;
        }

        .card-body ul.rect-div {
            display: flex;
            justify-content: center;
            margin-top: 0;
        }
    }
     .second .box .justify-content-between {
        justify-content: space-evenly !important;
     }

     @media screen (max-width: 280px) {
        .second .box .justify-content-between .third-box {
            margin-top: 20px;
        }
        .box .task > div{
            margin: 0 12px;
        }
     }

     @media (max-width:390px) {
        .second .box .justify-content-between {
            flex-wrap: wrap;
        }

        .third .box .justify-content-around {
            flex-direction: column;
            text-align: center;
        }
        .third .box .justify-content-around .second-box {
            margin-top: 40px;
        }
        .third .box .justify-content-around .second-box .ms-3 {
            margin-left: 0px !important;
        }
        .card-body ul.rect-div {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            margin: 10px 0px 10px 42px;
        }
        .card-body .head-text {
            text-align:center;
        }
        .card-body .fc-unthemed .fc-header-toolbar {
            display: flex;
            flex-direction: column;
        }

     }
</style>

<style>
    #paginate_event_container{
        height: 179px;
        overflow: overlay;
    }
    .tooltip {
        z-index: 100000000;
    }
    #pagination-loader{
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
</style>
@endsection

@section('content')

<!-- breadcrumbs -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="{{ route('compliance.dashboard.export-to-pdf') }}" class="btn btn-primary compliance-export_btn width-md">Export to PDF</a>
            </div>
            <h4 class="page-title">My Dashboard</h4>
        </div>
    </div>
</div>
<!-- end of breadcrumbs -->

<div class="row">
    <div class="col-xl-4">
    <!-- first card -->
        <div class="card">
            <div class="card-body">
                <div class="box"> <!-- box starts -->
                    <div class="title">
                        <h4 class="header-title">Task Completion Percentage</h4>
                    </div>
                    <div class="widget-chart text-center py-3 invisible" id="task-completion-widget" dir="ltr">
                        <input class="dial" data-compliance="knob" data-width="120" data-height="120" data-linecap=round data-fgColor="{{ $globalSetting->secondary_color }}"  value="{{ $myCompletedTasksPercent }}" data-skin="tron" data-readOnly=true data-thickness=".20"/>
                    </div>
                </div> <!-- box ends -->
            </div> <!-- first card ends -->
        </div>

        <!-- second card -->
        <div class="card">
            <div class="card-body second">
                <div class="box"> <!-- box starts -->
                    <div class="title">
                        <h4 class="header-title">My Task Monitor</h4>
                    </div>
                    <div class="task d-flex justify-content-between"> <!-- task starts -->
                            <!-- box 1 -->
                            <div class="boxx">
                                <i class="fas fa-tasks fa-2x"><span class="mx-2 text-muted">{{ $myAllActiveTasks }}</span></i>
                                <hr>
                                <h5 class="text-muted">All Upcoming</h5>
                                <hr>
                            <a href="{{ route('compliance.tasks.all-active') }}">
                                    <button class="btn btn-primary width-sm btn-rounded go-btn">Go</button>
                            </a>
                            </div>

                            <!-- box 2 -->
                            <div class="boxx">
                                <i class="fas fa-info-circle fa-2x"><span class="mx-2 text-muted">{{ $totalTaskDueToday }}</span></i>
                                <hr>
                                <h5 class="text-muted">Due Today</h5>
                                <hr>
                                <a href="{{ route('compliance.tasks.due-today') }}">
                                    <button class="btn btn-primary width-sm btn-rounded go-btn">Go</button>
                                </a>
                            </div>

                            <!-- box 3 -->
                            <div class="boxx third-box">
                                <i class="fas fa-times-circle fa-2x"><span class="mx-2 text-muted">{{ $totalMyTaskPassDue }}</span></i>
                                <hr>
                                <h5 class="text-muted">Past Due</h5>
                                <hr>
                                <a href="{{ route('compliance.tasks.pass-today') }}">
                                    <button class="btn btn-primary width-sm btn-rounded go-btn">Go</button>
                                </a>
                            </div>
                        </div> <!-- task ends -->
                </div> <!-- box ends -->
            </div> <!-- second card ends -->
        </div>


        <!-- third card -->
        <div class="card">
            <div class="card-body third">
                <div class="box"> <!-- box starts -->
                    <div class="title">
                        <h4 class="header-title ">Approvals</h4>
                    </div>

                    <div class="approval d-flex justify-content-around py-4"> <!-- task starts -->
                            <!-- box 1 -->
                            <div class="boxx">
                                <i class="fas fa-clock fa-2x"><span class="mx-2 text-muted">{{ $totalUnderReviewMyTasks }}</span></i>
                                <hr>
                                <h5 class="text-muted">Under Review</h5>
                                <hr>
                                <a href="{{ route('compliance.tasks.under-review') }}">
                                    <button class="btn btn-primary width-sm btn-rounded go-btn">Go</button>
                                </a>
                            </div>

                            <!-- box 2 -->
                            <div class="boxx second-box">
                                <i class="fas fa-thumbs-up fa-2x"><span class="mx-2 text-muted">{{ $totalNeedMyApprovalTasks }}</span></i>
                                <hr>
                                <h5 class="text-muted">Require My Approval</h5>
                                <hr>
                                <a href="{{ route('compliance.tasks.need-my-approval') }}">
                                    <button class="btn btn-primary width-sm btn-rounded ms-3 go-btn">Go</button>
                                </a>
                            </div>
                        </div> <!-- task ends -->
                </div> <!-- box ends -->
            </div> <!-- third card ends -->
        </div>
    </div> <!-- col-xl-4 ends -->

    <div id="content-loading" class="p-2 align-items-center" style="display:none;">
            <div class="spinner"></div>
            <p class="text-center m-0 px-2">Loading...</p>
    </div>
    <!-- col-xl-8 starts -->
    <div class="col-xl-8 loader-overlay">
        <div class="card">
            <div class="task-calendar-main card-body border border-top-0 border-start-0 border-end-0 ">
                <h5 class="head-text"><i class="fe-calendar"></i>&nbsp;Task Calendar</h5>

                <ul class="list-group list-group-horizontal  rect-div">
                    <li class="list-group-item  rect">
                        <span class="badge status-color" style="background: #414141">&nbsp;</span>
                        <span class="status-text">Upcoming</span>
                    </li>

                    <li class="list-group-item  rect">
                        <span class="badge status-color" style="background: #5bc0de">&nbsp;</span>
                        <span class="status-text">Under Review</span>
                    </li>

                    <li class="list-group-item  rect">
                        <span class="badge status-color" style="background: #cf1110">&nbsp;</span>
                        <span class="status-text">Late</span>
                    </li>

                    <li class="list-group-item  rect">
                        <span class="badge status-color" style="background: #359f1d">&nbsp;</span>
                        <span class="status-text">Implemented</span>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
            <!-- custom popover for calendar events -->
            <div class="fc-popover fc-more-popover" id="custom_popover" style="background: white; display:none; border: 1px solid gainsboro;">
                    <div class="fc-header fc-widget-header">
                        <span class="fc-title" id="popover_current_date"></span><span class="fc-close fc-icon fc-icon-x" id="popover_close"></span>
                    </div>
                    <div class="fc-body fc-widget-content">
                        <div class="fc-event-container" id="paginate_event_container">

                        </div>
                        <div class="row" style="height: 20px;">
                            <img src="{{asset('assets/images/event_loading.gif')}}" id="pagination-loader" style="display:none" alt="" height="20px">
                        </div>
                    </div>
                </div>
            <!-- custom popover for calendar events ends -->
        </div>
    </div> <!-- end col -->
    <!-- col-xl-8 ends -->

</div> <!-- row ends -->

@endsection

@section('plugins_js')
<!-- incliding fullcalendar libs -->
@include('includes.assets-libs.fullcalendar-io-js-libs')
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/libs/tooltip-js/tooltip.min.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-knob/jquery.knob.min.js') }}"></script>
@endsection

@section("custom_js")
    <script nonce="{{ csp_nonce() }}">
    $( document ).ready(function() {

        var day_event_page=1;
        var current_event_date=null;

         // Knob plugin
         $(".dial").knob({
                'format' : function (value) {
                    return value + '%';
                }
            });

            $("#task-completion-widget").removeClass("invisible");

            $("#popover_close").on('click', function(){
                document.getElementById('custom_popover').style.display='none';
            })

            /* Calendar data render or refresh*/
            const renderCalendar = async () =>  {
                try {
                    $('#content-loading').show();
                    $(".loader-overlay").css({"opacity":0.3, "z-index": -1,"pointer-events":"none"});

                    // current month calendar
                    var cdate = calendar.getDate();
                    var month_int = cdate.getMonth() + 1;
                    var current_month_date= cdate.getFullYear()+'-'+month_int+'-01';
                    console.log('--------------',current_month_date)
                    let res = await $.get("{{ route('compliance-dashboard-calendar') }}",{
                        'current_date_month' :current_month_date
                    });
                    if(res.success){
                        console.log('----------------------------',res.data)
                        let data = res.data
                        // Task calendar
                        let calendarTasks = data.calendarTasks

                        let oldEvents = calendar.getEvents()

                        // CLEARING EVENTS
                        for(index in  oldEvents){
                            let oldEvent = oldEvents[index]

                            oldEvent.remove()
                        }
                        // ADDING TASK
                        for(index in calendarTasks){
                            let calendarTask = JSON.parse(calendarTasks[index])

                            calendar.addEvent(calendarTask)
                        }
                        $('.fc-more').html('Show More');
                        $('#content-loading').hide();
                        $(".loader-overlay").css({"z-index":1, "opacity":1,"pointer-events":"auto"});
                    }

                } catch (error) {

                }
            }

            //FullCalendar Initialisation
            var calendarEl = document.getElementById('calendar');
            var calendarTasks = new Array();
            var calendarTaskEvents = [];
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: [ 'dayGrid' ],
                height: 675,
                eventLimit : true,
                views: {
                    agenda: {
                        eventLimit: 4
                    },
                    dayGrid: {
                        eventLimit: 4
                    },
                    day:{
                        eventLimit: 4
                    }
                },
                eventLimitText: "Show",
                eventLimitClick: function(cellInfo, jsEvent, view) {
                    var rect=cellInfo.dayEl.getBoundingClientRect();
                    // var left = rect.left + window.scrollX - 162;
                    var left = getLeftMargin(cellInfo.date.getDay());
                    var top= (rect.top + window.scrollY)/2 +72;
                    // var tooopp = cellInfo.dayEl.offsetTop + (cellInfo.dayEl.offsetParent && getTop(cellInfo.dayEl.offsetParent));
                    var screen_width= window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                    if(screen_width<=1200){
                        top =top/2;
                    }
                    var popop_width=rect.width+25;
                    if(popop_width<100){
                        popop_width=100;
                    }
                    document.getElementById('custom_popover').style.top=top+'px';
                    document.getElementById('custom_popover').style.left=left;
                    document.getElementById('custom_popover').style.width=popop_width+25+'px';
                    document.getElementById('custom_popover').style.display='block';
                    if(current_event_date!=cellInfo.date){
                        day_event_page=1;
                        $('#paginate_event_container').empty();
                    }
                    current_event_date=cellInfo.date;
                    getPaginatedEvent(current_event_date,day_event_page);
                },
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek'
                },
                eventRender: function(info) {
                    var tooltip = new Tooltip(info.el, {
                        title: info.event._def.title,
                        placement: 'top',
                        trigger: 'hover',
                        container: 'body'
                    });
                },
                events: calendarTaskEvents
            });

            calendar.render();

            const getPaginatedEvent = async (date,page) =>  {
                var month= date.getMonth() + 1;
                var current_month_date=date.getFullYear() + '-' + month + '-01';
                var date=date.getFullYear() + '-' + month + '-' + date.getDate();

                // Date format for header
                const dateOptions = { timeZone: 'GMT', month: 'long', day: 'numeric', year: 'numeric' };
                const dateFormatter = new Intl.DateTimeFormat('en-US', dateOptions);
                const dateAsFormattedString = dateFormatter.format(Date.parse(date));
                $('#popover_current_date').text(dateAsFormattedString);

                document.getElementById('pagination-loader').style.display='block';

                let res = await $.get("{{ route('compliance-dashboard-calendar') }}",{
                    'current_date_month' :current_month_date,
                    'date' : date,
                    'page' : page
                });

                if(res.success){
                document.getElementById('pagination-loader').style.display='none';
                    for(index in res.data.calendarTasks){
                        var event=JSON.parse(res.data.calendarTasks[index]);
                        var event_html=`<a class="fc-day-grid-event fc-h-event fc-event fc-start fc-end" id="${event.control_id}_event" href="${event.url}" style="background-color:${event.backgroundColor};color:${event.textColor}">
                                <div class="fc-content">
                                    <span class="fc-title">${event.title}</span>
                                </div>
                                </a>`;

                        $('#paginate_event_container').append(event_html);
                        var element=$(`#${event.control_id}_event`);
                        var tooltip = new Tooltip(element, {
                            title: event.title,
                            placement: 'top',
                            trigger: 'hover',
                            container: 'body'
                        });
                    }
                }
            }

             // check if to call paginate event api
            $('#paginate_event_container').on('scroll', function(e){
                var elem = $('#paginate_event_container');
                // logic if scroll to bottom
                if (elem[0].scrollHeight - elem.scrollTop() <= elem.outerHeight()) {
                    day_event_page++;
                    getPaginatedEvent(current_event_date,day_event_page);
                }
            });

             // fetch event and render calendar on previous, next and today button click
            $('.fc-prev-button, .fc-next-button , .fc-today-button').click(function(){
                document.getElementById('custom_popover').style.display='none';
                // calendar.prev();
                renderCalendar();
            });

        $(".compliance-export_btn").click(function(e)
        {
            e.preventDefault();
            reportModal('compliance',this.href);
        });

        renderCalendar();
    }); // END OF DOCUMENT READY

    // for show more text change
    window.addEventListener('resize', function(event) {
        setTimeout(ClearShowMore, 300);
    }, true);

    function ClearShowMore(){
        $('.fc-more').html('Show More')
    }
    function getLeftMargin(day){
        switch (day) {
            case 0:
                left = "1%";
                break;
            case 1:
                left = "15%";
                break;
            case 2:
                left = "29%";
                break;
            case 3:
                left = "43%";
                break;
            case 4:
                left = "57%";
                break;
            case 5:
                left = "71%";
                break;
            case 6:
                left = "85%";
            }
        return left;
    }
    </script>
@endsection
