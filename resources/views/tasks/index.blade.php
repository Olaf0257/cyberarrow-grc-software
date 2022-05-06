
@extends('layouts.layout')

@section('plugins_css')
    @include('includes.assets-libs.datatable-css-libs')
<link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/multiselect/multi-select.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/icon.css')}}" rel="stylesheet">
<!-- multi select -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/multiple-select/multiple-select.css') }}">
@endsection

@section('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/modules/tasks/style.css') }}">
@endsection

@section('content')
<!-- page title here -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="javascript: void(0);">
                            Compliance
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{route('compliance-dashboard')}}">
                            Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="javascript: void(0);">
                            Task Monitor
                        </a>
                    </li>
                </ol>
            </div>
            <h4 class="page-title">@if($urlSegmentTwo == 'global') Global Task Monitor @else My Task Monitor @endif </h4>
        </div>
    </div>
</div>
<!-- page title ends here -->

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body w-100">
                <div class="col-12">
                    <form action="{{ route('compliance.tasks.export-data') }}" id="export-data-form" method="post">
                        @csrf
                        <div class="filter-row d-flex flex-column flex-sm-row justify-content-between my-2 p-2 rounded">
                            <div class="filter-row__wrap d-flex flex-wrap">
                                <div class="m-1">
                                    <select name="selected_departments[]" class="filter-input" id="departments-filter" multiple>
                                        @if($organization)
                                        <option value="0" {{ in_array(0, $selectedDepartments) ? 'selected' : '' }}>{{ decodeHTMLEntity($organization->name)}}</option>
                                        @endif
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ in_array($department->id, $selectedDepartments) ? 'selected' : '' }}>{{ decodeHTMLEntity($department->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="all-projects m-1">
                                    <select name="selected_projects[]" class="filter-input" id="projects-filter" multiple>
                                        @foreach($complianceProjects as $complianceProject)
                                            <option value="{{ $complianceProject->id }}" {{ in_array($complianceProject->id, $selectedProjects) ? 'selected' : '' }}>{{ decodeHTMLEntity($complianceProject->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="m-1 all-standards"><input class="form-control filter-input" name="standard_name" type="text" placeholder="All Standards"></div>
                                <div class="m-1 all-tasks"><input class="form-control filter-input" name="control_name" type="text" placeholder="All Tasks"></div>
                                <div class="all-users m-1">
                                    <select class="form-control select2-field filter-input" name="responsible_user" {{ $responsibleView ? "disabled" : ''}}>
                                        <option value="">All Users</option>
                                        @foreach($taskContributors as $key => $value)
                                            <option value="{{ $value }}" {{ $responsibleView ? ($authUser->id == $value ? 'selected' : '' ) : ''}}> {{ decodeHTMLEntity($key)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="all-approvers m-1">
                                    <select class="form-control select2-field filter-input" name="approver_user" {{ $approverView ? "disabled" : ''}}>
                                        <option value="">All Approvers</option>
                                        @foreach($taskContributors as $key => $value)
                                            <option value="{{ $value }}" {{ $approverView ? ($authUser->id == $value ? 'selected' : '' ) : ''}}> {{ decodeHTMLEntity($key)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="completion-date m-1">
                                    <div class="input-group">
                                        <input name="completion_date" type="text" class="basic-datepicker form-control ps-1 pe-1 border-end-0 flatpickr-input filter-input" placeholder="Completion Date">
                                        <div class="border-start-0">
                                        <span class="input-group-text bg-none"><i class="mdi mdi-calendar-outline"></i></span></div>
                                    </div>
                                </div>
                                <div class="due-date m-1">
                                    <div class="input-group">
                                        <input name="due_date" type="text" class="basic-datepicker form-control ps-1 pe-1 border-end-0 flatpickr-input filter-input" placeholder="Due Date">
                                        <div class="border-start-0">
                                            <span class="input-group-text bg-none"><i class="mdi mdi-calendar-outline"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="all-status m-1">
                                    <select class="form-control select2-field filter-input" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="active" {{ $currentPage == 'all-active' ? 'selected' : '' }}>Upcoming</option>
                                        <option value="pass_due" {{ $currentPage == 'pass-due' ? 'selected' : '' }}>Past Due</option>
                                        <option value="due_today" {{ $currentPage == 'due-today' ? 'selected' : '' }}>Due Today</option>
                                    </select>
                                </div>

                                <div class="all-stages m-1">
                                    <select class="form-control select2-field filter-input" name="approval_status">
                                        <option value="">All Stages</option>
                                        <option value="Under Review" {{ ( $currentPage == 'under-review' || $currentPage == 'need-my-approval') ? 'selected' : '' }}>Under Review</option>
                                        <option value="Implemented">Implemented</option>
                                        <option value="Not Implemented">Not Implemented</option>
                                        <option value="Rejected">Rejected</option>
                                    </select>
                                </div>
                            </div><!--/.filter-row__wrap-->
                            <div class="m-1 text-center text-sm-auto task-button-wrapper">
                                <button class="btn btn-primary" type="button" id="search"> Search </button>

                                <button id="export-data-btn" class="btn btn-primary" type="submit"> Export </button>
                            </div>
                        </div><!--/.filter-row-->
                    </form>
                </div>
                <div class="col-12">
                    <!-- table -->
                    <table id="tasks-datatable" class="display table table-bordered border-light w-100" >
                        <thead class="table-light">
                            <tr class="tasks-header">
                                <th>Project</th>
                                <th>Standard</th>
                                <th>Control</th>
                                <th>Control Description</th>
                                <th>Type</th>
                                <th>Assigned</th>
                                <th>Approver</th>
                                <th>Completion date </th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Approval Stage </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="tbody-light">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('custom_js')
    @include('includes.assets-libs.datatable-js-libs')
<script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/multiselect/jquery.multi-select.js') }}"></script>
<!-- multi select -->
<script src="{{ asset('assets/libs/multiple-select/multiple-select.js') }}"></script>
<script nonce="{{ csp_nonce() }}">
    $(document).ready(function() {
        var projectFilter  = $('#projects-filter')
        var departmentFilter = $('#departments-filter')

        /* Initialization of multi-select filters*/
        projectFilter.multipleSelect({
            filter: true,
            selectAllDelimiter: ['', ''],
        });

        departmentFilter.multipleSelect({
            filter: true,
            selectAllDelimiter: ['', ''],
        });




        // initializing datatable
        const taskDatatable = $("#tasks-datatable").DataTable({
            serverSide: true,
            processing: true,
            searching: false,
            stateSave: true,
            lengthChange: true,
            pagingType: "simple_numbers",
            // scrollX: true,
            ordering: false,
            responsive: true,
            "autoWidth": false,
            columns: [
                { responsivePriority: 0, targets:0 },
                { responsivePriority: 9, targets:1 },
                { responsivePriority: 2, targets:2 },
                { responsivePriority: 11, targets:3 },
                { responsivePriority: 10, targets:4 },
                { responsivePriority: 3, targets:5 },
                { responsivePriority: 4, targets:6 },
                { responsivePriority: 8, targets:7 },
                { responsivePriority: 5, targets:8 },
                { responsivePriority: 7, targets:9 },
                { responsivePriority: 6, targets:10 },
                { responsivePriority: 1, targets:11 }
            ],
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next: "<i class='mdi mdi-chevron-right'>"
                },
                "processing": `
                <div id="content-loading" class="p-2 d-flex align-items-center">
                    <div class="spinner"></div>
                    <p class="text-center m-0 px-2">Loading...</p>
                </div>`
            },
            ajax: {
                url: "{{ $taskListURL }}",
                type: "GET",
                data: {
                    selected_departments: function () {
                        return $(document).find('select[name="selected_departments[]"]').val()
                    },
                    selected_projects: function() {
                        return $(document).find('select[name="selected_projects[]"]').val()
                    },
                    control_name: function() {
                        return $(document).find('input[name="control_name"]').val()
                    },
                    standard_name: function() {
                        return $(document).find('input[name="standard_name"]').val()
                    },
                    responsible_user: function () {
                        return $(document).find('select[name="responsible_user"]').val()
                    },
                    approver_user: function() {
                        return $(document).find('select[name="approver_user"]').val()
                    },
                    completion_date: function () {
                        return $(document).find('input[name="completion_date"]').val()
                    },
                    due_date: function () {
                        return $(document).find('input[name="due_date"]').val()
                    },
                    approval_status: function() {
                        return $(document).find('select[name="approval_status"]').val()
                    },
                    status: function () {
                        return $(document).find('select[name="status"]').val()
                    }
                }
            },
            drawCallback: function (settings) {
                let dataCount = settings.aoData.length

                if (dataCount > 0) {
                    $("#tasks-datatable thead tr th").css('padding', ".80rem")
                } else {
                    $("#tasks-datatable thead tr th").css('padding', ".45rem")
                }

                $(".basic-datepicker").flatpickr({
                    dateFormat:"Y-m-d"
                });
                $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
            },

        })

        // initializing select2
        $(".select2-field").select2()

        //handling filter
        // datatable reload
        $("#search").on('click', function(){
            taskDatatable.ajax.reload(function(){
            }, false)
        });

        taskDatatable.on('responsive-resize', function ( e, datatable, columns ) {
            for (i=0; i<columns.length; i++){
                let requiredIndex = i + 1
                if (columns[i]){
                    $("#tasks-datatable > thead tr:first-child > th:nth-child(" + requiredIndex + ")").show()
                }
                else {
                    $("#tasks-datatable > thead tr:first-child > th:nth-child(" + requiredIndex + ")").hide()
                }
            }
        });
    }); // END OF DOCUMENT READY
</script>

@endsection
