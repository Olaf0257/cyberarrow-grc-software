@extends('layouts.layout')

@php($pageTitle = "EBDAA GRC")

@section('title', $pageTitle)

@section('plugins_css')
<link href="{{ asset('assets/libs/c3/c3.min.css') }}" rel="stylesheet" type="text/css" />
<!-- incliding fullcalendar libs -->
@include('includes.assets-libs.fullcalendar-io-css-libs')

<link rel="stylesheet" href="{{ asset('assets/css/full-calendar-tooltip-customize.css') }}">

<!-- multi select -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/multiple-select/multiple-select.css') }}">

@endsection

@section('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/modules/global-dashboard/style.css') }}">
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

<div class="row">
    <div class="col-xl-12">
        <div class="overview-div mt-3 d-flex mb-3">
            <div class="overview-div-text">
                <h4 class="overview-text">Current Overview: <span class="overview-break-text">All projects</span></h4>
            </div>

            <div class="select-dropdown ms-auto">
                <form action="{{ route('global.dashboard.generate-report') }}" method="Post" id="exportFormDashboard">
                    @csrf
                    <div class="row">
                        <div class="projects-filter-wp mb-3">
                            <select id="department-filter" name="department_ids[]" data-width="200" multiple="multiple" class="multiple-select" style="visibility: hidden; height: 20px">
                                @if($organization)
                                <option value="0">&nbsp;{{ decodeHTMLEntity($organization->name) }}</option>
                                @endif
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}">&nbsp;{{decodeHTMLEntity($department->name)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="projects-filter-wp ms-2 mb-3">
                            <select id="projects-filter" name="project_ids[]" data-width="200" multiple="multiple" class="multiple-select" style="visibility: hidden; height: 20px">
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}">&nbsp;{{decodeHTMLEntity($project->name)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <a href="#" class="btn btn-primary all-projects-btn mx-2 dashboard-btn">All Projects</a>
                        <button class="btn btn-primary global-export_btn dashboard-btn">
                            Export to PDF
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div id="content-loading" class="p-2 align-items-center" style="display:none;">
    <div class="spinner"></div>
    <p class="text-center m-0 px-2">Loading...</p>
</div>


<!-- task status -->
<div class="task-box loader-overlay">
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6">
           <div class="card">
                <div class="task-completion card-body" id="tasks-completion-percentage-widget">
                    <div class="title">
                        <h4 class="head-title">Task Completion Percentage</h4>
                    </div>
                    <div class="widget-chart text-center py-3 invisible" id="task-completion-widget" dir="ltr">
                        <input class="dial" id="tasks-completion-percentage" data-width="150" data-height="170" data-linecap=round data-fgColor="{{ $globalSetting->secondary_color }}"  value="00" data-skin="tron" data-readOnly=true data-thickness=".20"/>
                    </div>
                </div>
           </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card">
                <div class="task-monitor card-body" id="tasks-monitor-widget">
                    <div class="title">
                        <h4 class="head-title">Task Monitor</h4>
                    </div>
                    <div class="row task d-flex justify-content-around py-4"> <!-- task starts -->
                            <!-- box 1 -->
                            <div class="upcoming">
                                <i class="fas fa-tasks fa-2x"><span class="mx-2 text-muted" id="all-upcomming-tasks">0</span></i>
                                <hr>
                                <p class="text-muted">All <span class="task-break-text">Upcoming</span> </p>
                                <hr>
                                <a href="{{ route('global.tasks.all-active') }}" class="btn btn-primary width-xs btn-rounded go-btn upcoming-go-btn">Go</a>
                            </div>

                            <!-- box 2 -->
                            <div class="due-today">
                                <i class="fas fa-info-circle fa-2x"><span class="mx-2 text-muted" id="due-today-tasks">0</span></i>
                                <hr>
                                <p class="text-muted">Due <span class="task-break-tet">Today</span></p>
                                <hr>
                                <a href="{{ route('global.tasks.due-today') }}" class="btn btn-primary width-xs btn-rounded go-btn">Go</a>

                            </div>

                            <!-- box 3 -->
                            <div class="past-due">
                                <i class="fas fa-times-circle fa-2x"><span class="mx-2 text-muted" id="past-due-tasks">0</span></i>
                                <hr>
                                <p class="text-muted">Past <span class="task-break-txt">Due</span></p>
                                <hr>
                                <a href="{{ route('global.tasks.pass-today') }}" class="btn btn-primary width-xs btn-rounded go-btn">Go</a>

                            </div>
                        </div> <!-- task ends -->
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card">
                <div class="control-stat card-body"  id="controls-stats-widget">
                    <div class="title">
                        <h4 class="head-title">Control Status</h4>
                    </div>

                    <div class="control-items">
                        <div class="total">
                            <i data-feather="box"></i>
                            <span class="control-text">All Controls</span>
                            <span class="float-end control-num" id="all-controls">0</span>
                        </div>

                        <div class="total py-3">
                            <i data-feather="delete"></i>
                            <span class="control-text">Not Applicable</span>
                            <span class="float-end control-num" id="not-applicable-controls">0</span>
                        </div>

                        <div class="total">
                            <i data-feather="flag"></i>
                            <span class="control-text">Implemented Controls</span>
                            <span class="float-end control-num" id="implemented-controls">0</span>
                        </div>

                        <div class="total py-3">
                            <i data-feather="star"></i>
                            <span class="control-text">Under Review</span>
                            <span class="float-end control-num" id="under-review-controls">0</span>
                        </div>

                        <div class="total">
                            <i data-feather="x-square"></i>
                            <span class="control-text">Not Implemented Controls</span>
                            <span class="float-end control-num" id="not-implemented-controls">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card">
                <div class="implementation card-body">
                    <div class="title">
                        <h4 class="head-title">Implementation Progress</h4>
                    </div>
                    <div class="chart-box">
                        <div id="implementation-progress-chart" dir="ltr"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- task status ends-->

<!-- calendar here -->
<div class="calendar-div loader-overlay">
    <div class="row">
        <div class="col-xl-12">
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
                    <div id="task-calendar"></div>
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
        </div>
    </div>
</div>


<!-- calendar here ends-->
@endsection

@section('plugins_js')
    <!-- feather -->
    <script src="{{ asset('assets/feather/js/feather.min.js') }}"></script>
    <script src="{{asset('assets/libs/jquery-knob/jquery.knob.min.js')}}"></script>

    <!-- incliding fullcalendar libs -->
    @include('includes.assets-libs.fullcalendar-io-js-libs')

    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/libs/tooltip-js/tooltip.min.js') }}"></script>

    <!--C3 Chart-->
    <script src="{{ asset('assets/libs/d3/d3.min.js') }}"></script>
    <script src="{{ asset('assets/libs/c3/c3.min.js') }}"></script>

    <!-- multi select -->
    <script src="{{ asset('assets/libs/multiple-select/multiple-select.js') }}"></script>

@endsection

@section("custom_js")
    <script nonce="{{ csp_nonce() }}">
    $( document ).ready(function() {
        var day_event_page=1;
        var current_event_date=null;
        /* variables definations */
        var selectedProjects = [];
        var selectedDepartments = [];

        /* TASK MONITOR >> HANDLE REDIRECT TO TASKS PAGE  */
        $(document).on('click', '#tasks-monitor-widget .go-btn', function(event){
            event.preventDefault()

            let intendedRedirectURL = this.href;

            /* Redirecting to intended URL */
            window.location.href = `${intendedRedirectURL}?selected_projects=${selectedProjects}&selected_departments=${selectedDepartments}`
        })


        /* Initialization of multipleselect in project and department filter*/
        var projectFilter  = $('#projects-filter')
        var departmentFilter  = $('#department-filter')

        projectFilter.multipleSelect({
            filter: true,
            selectAllDelimiter: ['', ''],
        });

        //department filter
        departmentFilter.multipleSelect({
            filter: true,
            selectAllDelimiter: ['', ''],
        });

        // selection all
        projectFilter.multipleSelect('checkAll')

        // selection all
        departmentFilter.multipleSelect('checkAll')

        /* Selects all project */
        $(".all-projects-btn").on('click', function(){
            projectFilter.multipleSelect('checkAll')
        })




        /* Wrapping multiple select to show overlay*/
        $( ".ms-search,.ms-drop ul" ).each(function(index) {
            $(this).wrapAll( '<div class="hide-lists loader-overlay"></div>' )
        })


        /* Dashboard data render or refresh*/
        const renderDashboardData = async () =>  {
            try {
                $('#content-loading').show();
                $(".loader-overlay").css({"opacity":0.3, "z-index": -1,"pointer-events":"none"});

                selectedProjects = projectFilter.val()
                selectedDepartments = departmentFilter.val()

                let res = await $.get("{{ route('global.dashboard.get-data') }}",{
                    'project_ids' : selectedProjects,
                    'departments' : selectedDepartments
                });


                if(res.success){
                    let data = res.data

                    let allControls = data.allControls
                    let implementedControls = data.implementedControls
                    let underReviewControls = data.underReviewControls
                    let notImplementedControls = data.notImplementedControls
                    let notApplicableControls = data.notApplicableControls
                    let applicableControls =  (allControls - notApplicableControls)

                    // Completed task percentage widget
                    let completedTasksPercent = 0;

                    if (implementedControls > 0 && applicableControls > 0) {
                        completedTasksPercent = Math.round(implementedControls * 100 / applicableControls);
                    }


                    $("#tasks-completion-percentage-widget #tasks-completion-percentage").val(`${completedTasksPercent}%`)
                    $("input.dial").trigger('change');

                    // task monitor widget
                    $("#tasks-monitor-widget #all-upcomming-tasks").html(data.allUpcomingTasks)
                    $("#tasks-monitor-widget #due-today-tasks").html(data.allDueTodayTasks)
                    $("#tasks-monitor-widget #past-due-tasks").html(data.allPassDueTasks)




                    // control stats widget
                    $("#controls-stats-widget #all-controls").html(allControls)
                    $("#controls-stats-widget #not-applicable-controls").html(notApplicableControls)
                    $("#controls-stats-widget #implemented-controls").html(implementedControls)
                    $("#controls-stats-widget #under-review-controls").html(underReviewControls)
                    $("#controls-stats-widget #not-implemented-controls").html(notImplementedControls)

                    // Implementation Progresss Chart Widget
                    let implementedControlsPercentage = ((allControls > 0) ?(implementedControls/allControls) * 100 :  0)
                    let underReviewControlsPercentage = ((allControls > 0) ?(underReviewControls/allControls) * 100 :  0)
                    let notImplementedControlsPercentage = ((allControls > 0) ?(notImplementedControls/allControls) * 100 :  0)

                    ImplementationProgresssChart.load({
                        columns: [
                            ['Implemented', implementedControlsPercentage],
                            ['Under Review', underReviewControlsPercentage],
                            ['Not Implemented', notImplementedControlsPercentage]
                        ]
                    });

                    $('#content-loading').hide();
                    $(".loader-overlay").css({"z-index":1, "opacity":1,"pointer-events":"auto"});
                }

            } catch (error) {

            }
        }

        $("#popover_close").on('click', function(){
            document.getElementById('custom_popover').style.display='none';
        })

        /* Dashboard data render or refresh*/
        const renderCalendar = async () =>  {
            try {
                $('#content-loading').show();
                $(".loader-overlay").css({"opacity":0.3, "z-index": -1,"pointer-events":"none"});

                selectedProjects = projectFilter.val()
                selectedDepartments = departmentFilter.val()
                // current month calendar
                var cdate = calendar.getDate();
                var month_int = cdate.getMonth() + 1;
                var current_month_date= cdate.getFullYear()+'-'+month_int+'-01';
                let res = await $.get("{{ route('global.dashboard.get-caledar-data') }}",{
                    'project_ids' : selectedProjects,
                    'departments' : selectedDepartments,
                    'current_date_month' :current_month_date
                });
                if(res.success){
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

        // onload opopulation calendar data
        $("#projects-filter").trigger("change")
        $("#department-filter").trigger("change")

        // Knob plugin
        $(".dial").knob({
            'format' : function (value) {
                return value + '%';
            }
        });

        $("#task-completion-widget").removeClass("invisible")

        //FullCalendar Initialisation
        var calendarEl = document.getElementById('task-calendar');

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
                var top= (rect.top + window.scrollY)/2 - 65;
                // var tooopp = cellInfo.dayEl.offsetTop + (cellInfo.dayEl.offsetParent && getTop(cellInfo.dayEl.offsetParent));
                var screen_width= window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                console.log(screen_width);
                if(screen_width<=766){
                    top =top/2;
                }
                var popop_width=rect.width+25;
                if(popop_width<100){
                    popop_width=100;
                }
                document.getElementById('custom_popover').style.top=top+'px';
                document.getElementById('custom_popover').style.left=left;
                document.getElementById('custom_popover').style.width=popop_width+'px';
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
        });

        calendar.render();

        //feather
        feather.replace();

        //c3 chart
        var ImplementationProgresssChart = c3.generate({
            bindto: '#implementation-progress-chart',
            size: {
                height: 220,
                width: 240
            },
            data: {
            columns: [
            ],
            type : 'pie'
            },
            color:{
                pattern:["#359f1d","#5bc0de","#cf1110"]
            },
            pie:{
                label:{show:!1}
            },
            legend: {
                show: true
            }
        });
        
        const getPaginatedEvent = async (date,page) =>  {
            var month= date.getMonth() + 1;
            var current_month_date=date.getFullYear() + '-' + month + '-01';
            var date=date.getFullYear() + '-' + month + '-' + date.getDate();

             // Date format for header
            const dateOptions = { timeZone: 'GMT', month: 'long', day: 'numeric', year: 'numeric' };
            const dateFormatter = new Intl.DateTimeFormat('en-US', dateOptions);
            const dateAsFormattedString = dateFormatter.format(Date.parse(date));
            $('#popover_current_date').text(dateAsFormattedString);

            selectedProjects = projectFilter.val()
            selectedDepartments = departmentFilter.val()
            document.getElementById('pagination-loader').style.display='block';

            let res = await $.get("{{ route('global.dashboard.get-caledar-data') }}",{
                'project_ids' : selectedProjects,
                'departments' : selectedDepartments,
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

        /* TO re-render dashboard data on project and department filter change */
        $(document).on('change', '#projects-filter, #department-filter', function(){
            renderDashboardData()
            renderCalendar();
        })

        // fetch event and render calendar on previous, next and today button click
        $('.fc-prev-button, .fc-next-button , .fc-today-button').click(function(){
            document.getElementById('custom_popover').style.display='none';
            // calendar.prev();
            renderCalendar();
        });

        renderDashboardData()
        renderCalendar();
    });
    // END OF DOCUMENT READY
    $(".global-export_btn").click(function(event){
        event.preventDefault();
        reportModal('global');
    });

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
