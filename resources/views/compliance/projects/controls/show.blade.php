@extends('layouts.layout')

@php $pageTitle = "My Dashboard"; @endphp

@section('title', $pageTitle)

@section('plugins_css')
    @include('includes.assets-libs.datatable-css-libs')
    <link href="{{ asset('assets/libs/switchery/switchery.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/multiselect/multi-select.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/libs/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/libs/ladda/ladda-themeless.min.css') }}" rel="stylesheet" type="text/css">
    {{-- <link href="{{asset('assets/css/jquery.filer.css')}}" rel="stylesheet"> --}}
    <link href="{{asset('assets/css/jquery.filer-dragdropbox-theme.css')}}" rel="stylesheet">
@endsection

@section('content')
    <style nonce="{{ csp_nonce() }}">

        .comment-box {
            overflow-y: auto;
            max-height: 273px;
        }

        .comment-sec {
            background: #f7f7f7;
            padding: 8px;
            border-radius: 10px;
            margin: 0 3px;
        }

        .comment {
            background: #F8F9FB;
            padding: 8px;
            border-radius: 15px;
            margin-bottom: 28px;
        }

        .comment-body {
            margin-left: 53px;
            overflow: hidden;
        }

        .post-comment {
            margin-top: 20px;
        }

        .time {
            color: #aaa;
        }

        a {
            color: #6C757D;
        }


        .toast {
            max-width: 100%;
            margin-top: 19px;
            /* padding: 0; */
        }

        .submit-btn {
            margin-top: 14px;
        }

        .dataTables_scrollHeadInner {
            width: 100% !important;
            /* padding-right: 0 !important;
            box-sizing: content-box; */
        }

        /* removing dropdown icon from disabled select box */
        .select2-container--disabled .select2-selection__arrow b {
            display: none !important;
        }

        .status-pill {
            position: absolute;
            top: 10px;
            right: 5px;
        }

        /** removing arrow on datatable because sorting on js was not working */
        table.dataTable thead .sorting_asc:before,
        table.dataTable thead .sorting_asc:after {
            content: "";
        }


        input[type="text"]:disabled,
        td .input-group input[type="text"]:disabled > div, input:disabled + div > span.bg-none {
            background: #eee !important;
        }

        input.task-status-red {
            color: #40484e;
        }

        .dataTables_scroll {
            overflow: auto !important;
        }

        .dataTables_scrollHead {
            overflow: initial !important;
        }

        .dataTables_scrollBody {
            overflow: initial !important;
        }

        .name-table__overflow tr td:first-child {
            max-width: 65px;
            overflow: hidden;
            resize: horizontal;
            text-overflow: ellipsis;
        }

        textarea {
            resize: none;
        }

        #evidence-form-section {
            padding: 20px;
            border-radius: 6px;
            background: #f7f7f7;
        }

        #control-comments-wp, .uploaded-evidence-main {
            border: 2px solid #f7f7f7;
            border-radius: 6px;
        }

        textarea#comment {
            overflow: hidden !important;
        }

        .link-primary {
            color: #0d6efd;
        }

        .readmore {
            overflow: hidden;
        }

        .readmore-link {
            margin-left: 53px;
        }
    </style>
    <!-- breadcrumbs -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('compliance-dashboard') }}">Compliance</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('compliance-projects-view') }}">Projects</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('compliance-project-show', [$project->id]) }}">Controls</a>
                        </li>
                        <li class="breadcrumb-item active"><a href="javascript: void(0);">Details</a></li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $pageTitle }}</h4>
            </div>
        </div>
    </div>
    <!-- end of breadcrumbs -->

    @php
        $authUser = Auth::guard('admin')->user();
        $disabledColor = 'background: #eee;color: #444';
        $projectControlDeadline = $projectControl->deadline;
        $today = date("Y-m-d", strtotime( date("Y-m-d") ) );

        if( $authUser->hasAnyRole(['Global Admin', 'Compliance Administrator']) ) {
            if (!$projectControl->applicable) {
                $disabled = 'disabled';
                $allowUpdate = false;
            } else {
                if (!$projectControl->is_editable) {
                    $disabled = 'disabled';
                    $allowUpdate = false;
                } else {
                    $disabled = '';
                    $allowUpdate = true;
                }
            }
        } else {
            $disabled = "disabled";
            $allowUpdate = false;
        }

        $controlStatus = $projectControl->status;
        $taskStatusClass = '';

        if($controlStatus == "Not Implemented"){
            $taskStatusClass = 'task-status-red text-white';
        } elseif($controlStatus == "Under Review"){
            $taskStatusClass = 'task-status-blue';
        } elseif($controlStatus == "Implemented"){
            $taskStatusClass = 'task-status-green';
        } elseif($controlStatus == "Rejected"){
            $taskStatusClass = 'task-status-orange';
        }

    @endphp
    <div class="alert alert-success alert-block" id="alert-success-control" style="display: none;">
        <button type="button" class="btn-close" data-dismiss="alert">×</button>
        <strong> Control Detail is successfully updated</strong>
    </div>
    <div class="alert alert-success alert-block" id="alert-success-evidence" style="display: none;">
        <button type="button" class="btn-close" data-dismiss="alert">×</button>
        <strong> Evidence successfully uploaded</strong>
    </div>
    <div class="alert alert-danger alert-block" id="alert-danger-control" style="display: none;">
        <button type="button" class="btn-close" data-dismiss="alert">×</button>
        <div class="alert-error-messages"></div>
    </div>
    <div class="row"> <!-- row starts -->
        <div class="col-xl-12"> <!-- col starts -->
            <div class="card">
                <div class="card-body"> <!-- cardbox starts -->
                    <ul class="nav nav-tabs nav-bordered">
                        <li class="nav-item">
                            <a href="#details" data-toggle="tab" aria-expanded="false"
                            class="nav-link {{ $activeTabs == 'details' ? 'active' : '' }}">
                                Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tasks" data-toggle="tab" aria-expanded="true"
                            class="nav-link {{ $activeTabs == 'tasks' ? 'active' : '' }}">
                                Tasks
                            </a>
                        </li>

                        <!-- status -->
                        <div class="status-pill" id="control-status-badge">

                            @if($projectControl->status != 'Implemented')
                                <span class="badge me-2"
                                    style="background: #444">Deadline: {{ $projectControl->deadline}}</span>
                            @endif

                            @if($nextReviewDate)
                                @php($taskUnlockingDate = date('Y-m-d', strtotime('-14 days', strtotime($projectControlDeadline) ) ))
                                @if( ($today >= $taskUnlockingDate) && ($today <= $projectControlDeadline) && ($controlStatus != "Implemented"))
                                    <span class="badge me-2" style="background: #444">Review deadline approaching</span>
                                @endif
                            @endif
                            <span class="badge {{ $taskStatusClass }}">{{ $controlStatus }}</span>
                        </div>
                        <!-- status end-->
                    </ul>

                    <!-- outer tab content -->
                    <div class="tab-content">
                        <div class="tab-pane {{ $activeTabs == 'details' ? 'show active' : '' }}" id="details">
                            <!-- form -->
                            <form class="form-horizontal absolute-error-form"
                                action="{{ $allowUpdate ? route('compliance.project.controls.update', [$project->id, $projectControl->id]) : '' }}"
                                id="control-detail-form" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <label for="id" class="col-3 form-label col-form-label">Control ID </label>
                                    <div class="col-9">
                                        <input type="email" class="form-control" id="inputEmail3" placeholder="#1"
                                            value="{{ $projectControl->controlId }}" disabled
                                            style="{{ $disabledColor }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="name" class="col-3 form-label col-form-label">Name</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="name" placeholder="Enter name"
                                            value="{!! $projectControl->name !!}" disabled style="{{ $disabledColor }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="description" class="col-3 form-label col-form-label">Description</label>
                                    <div class="col-9">
                                        <textarea class="form-control overflow-auto" cols="50" rows="5" tabindex="3"
                                                id="description" disabled
                                                style="{{ $disabledColor }}">{!! strip_tags($projectControl->description) !!}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="control-status" class="col-3 form-label col-form-label">Status</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control {{ $taskStatusClass }}"
                                            value="{{ $projectControl->status }}" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="responsible" class="col-3 form-label col-form-label">Responsible<span
                                            class="required text-danger ms-1">*</span></label>
                                    <div class="col-9">
                                        <select name="responsible" id="responsible"
                                                class="form-control select2-multiple select2-picker"
                                                data-placeholder="Search Responsible..." tabindex="5" {{$disabled}}>
                                            <option value="">Search Responsible...</option>
                                            @foreach($contributors as $key => $value)
                                                <option value="{{$value}}"
                                                        @if($value == $projectControl->responsible || $value == old('responsible')) selected @endif {{$disabled}}>{{decodeHTMLEntity($key)}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback d-block">
                                            @if ($errors->has('responsible'))
                                                {{ $errors->first('responsible') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="approver" class="col-3 form-label col-form-label">Approver <span
                                            class="required text-danger ms-1">*</span></label>
                                    <div class="col-9">
                                        <select name="approver" id="approver"
                                                class="form-control select2-multiple select2-picker"
                                                data-placeholder="Search Approver..." tabindex="6" {{ $disabled }}>
                                            <option value="">Search Approver...</option>
                                            @foreach($contributors as $key => $value)
                                                <option value="{{$value}}"
                                                        @if($value == $projectControl->approver || $value == old('approver')) selected @endif {{$disabled}}>{{decodeHTMLEntity($key)}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback d-block">
                                            @if ($errors->has('approver'))
                                                {{ $errors->first('approver') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="control-deadline" class="col-3 form-label col-form-label">Deadline <span
                                            class="required text-danger ms-1">*</span></label>
                                    <div class="col-9 input-group">
                                        <input type="text" id="deadline" name="deadline"
                                            class="form-control basic-flatpicker border-end-0" tabindex="7"
                                            value="{{$projectControl->deadline != null ? $projectControl->deadline: date('Y-m-d')}}"
                                            {{ $disabled }}  style="{{ $disabled == 'disabled' ? $disabledColor : '' }}">
                                        <div class="border-start-0">
                                            <span class="input-group-text bg-none"><i class="mdi mdi-calendar-outline"></i></span>
                                        </div>
                                        <div class="invalid-feedback d-block">
                                            @if ($errors->has('deadline'))
                                                {{ $errors->first('deadline') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($nextReviewDate)
                                    <div class="row mb-3">
                                        <label for="next-review-date" class="col-3 form-label col-form-label">Next Review Date</label>
                                        <div class="col-9 input-group">
                                            <input type="text" id="next-review-date" class="form-control border-end-0"
                                                tabindex="7"
                                                value="{{ $nextReviewDate }}" disabled>
                                        </div>
                                    </div>
                                @endif

                                <div class="row mb-3">
                                    <label for="frequency" class="col-3 form-label col-form-label">Frequency <span
                                            class="required text-danger ms-1">*</span></label>
                                    <div class="col-9">
                                        <select class="form-control select2-picker" tabindex="8" id="frequency"
                                                name="frequency"
                                                {{$disabled}} style="{{ $disabled == 'disabled' ? $disabledColor : '' }}">
                                            <option value="">Select Frequency</option>
                                            @foreach($frequencies as $frequency)
                                                <option value="{{$frequency}}"
                                                        @if($frequency == $projectControl->frequency || $frequency == old('frequency')) selected @endif {{$disabled}}>
                                                    {{$frequency}}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback d-block">
                                            @if ($errors->has('frequency'))
                                                {{ $errors->first('frequency') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-xl-3 col-md-1 col-form-label"></label>
                                    <div class="col-xl-9 col-md-5 d-flex justify-content-end">
                                        <a href="{{route('compliance-project-show', [$project->id])}}" class="btn btn-danger">Back</a>
                                        @if( $allowUpdate )
                                            <button type="submit" class="btn btn-primary ms-1 ladda-button"
                                                    id="update-submit-btn"
                                                    data-style="expand-left">Update
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </form> <!-- form ends -->
                        </div>

                        <div class="tab-pane {{ $activeTabs == 'tasks' ? 'show active' : '' }}" id="tasks">
                            <div class="row">
                                <div class="col-xl-6">
                                    <!-- Evidence upload view is only viewable by responsible -->
                                    @if( $authUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Contributor']) && $authUser->id == $projectControl->responsible )
                                        @if($allowEvidencesUpload)
                                            <div id="evidence-form-section" class="pb-5 mb-3">
                                                <form
                                                    action="{{ route('compliance-project-control-evidences-upload', [$project->id, $projectControl->id]) }}"
                                                    method="POST" id="evidence-upload-form" enctype="multipart/form-data"
                                                    id="validate-form">
                                                    @csrf
                                                    <input type="hidden" name="project_control_id"
                                                        value="{{ $projectControl->id }}">
                                                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                                        @if($globalSetting->allow_document_upload)
                                                            <li class="nav-item">
                                                                <a class="nav-link btn-secondary text-white active"
                                                                id="pills-upload-document-tab" data-toggle="pill"
                                                                href="#pills-upload-document" role="tab"
                                                                aria-controls="pills-profile" aria-selected="false">
                                                                    Upload Document
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if($globalSetting->allow_document_link)
                                                            <li class="nav-item ms-2">
                                                                <a class="nav-link btn-secondary text-white {{ !$globalSetting->allow_document_upload ? 'active' : '' }}"
                                                                id="pills-create-link-tab" data-toggle="pill"
                                                                href="#pills-create-link" role="tab"
                                                                aria-controls="pills-home" aria-selected="true">
                                                                    Create Link
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li class="nav-item ms-2">
                                                            <a class="nav-link btn-secondary text-white"
                                                            id="pills-existing-control-tab" data-toggle="pill"
                                                            href="#pills-existing-control" role="tab"
                                                            aria-controls="pills-home" aria-selected="true">
                                                                Existing Control
                                                            </a>
                                                        </li>
                                                        <li class="nav-item ms-2">
                                                            <a class="nav-link btn-secondary text-white"
                                                            id="pills-text-input-tab" data-toggle="pill"
                                                            href="#pills-text-input" role="tab"
                                                            aria-controls="pills-home" aria-selected="true">
                                                                Text Input
                                                            </a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content" id="evidences-upload-tabContent">
                                                        @if($globalSetting->allow_document_upload)
                                                            <div class="tab-pane fade show active"
                                                                id="pills-upload-document" role="tabpanel"
                                                                aria-labelledby="pills-upload-document-tab">
                                                                <div class="row mb-3">
                                                                    <label class="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                                        for="name2">Name: <span
                                                                            class="required text-danger">*</span></label>
                                                                    <div class="col-xl-10 col-lg-10 col-md-10">
                                                                        <input type="text" name="name2" class="form-control"
                                                                            id="name2">
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3" id="evidence-section">
                                                                    <label class="col-xl-2 col-lg-2 col-md-2 col-form-label"
                                                                        for="evidences">Evidence: <span
                                                                            class="required text-danger">*</span></label>
                                                                    <div class="col-xl-10 col-lg-10 col-md-10">
                                                                        <input type="file" class="evidence-upload dropify"
                                                                            name="evidences"/>
                                                                        <div class="file-validation-limit mt-3">
                                                                            <div>
                                                                                <p><span
                                                                                        class="me-1">Accepted File Types: </span>.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.png,.jpeg,.gif,.pdf
                                                                                </p>
                                                                            </div>
                                                                            <p><span class="me-1">Maximum File Size: </span>15MB
                                                                            </p>
                                                                            <p><span
                                                                                    class="me-1">Maximum Character Length: </span>250
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--/.tab-pane-->
                                                        @endif
                                                        @if($globalSetting->allow_document_link)
                                                            <div
                                                                class="tab-pane fade {{ !$globalSetting->allow_document_upload ? 'show active' : '' }}"
                                                                id="pills-create-link" role="tabpanel"
                                                                aria-labelledby="pills-create-link-tab">
                                                                <div class="row mb-3">
                                                                    <label class="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                                        for="name">Name: <span
                                                                            class="required text-danger">*</span></label>
                                                                    <div class="col-xl-10 col-lg-10 col-md-10">
                                                                        <input type="text" name="name" class="form-control">
                                                                    </div>
                                                                </div>

                                                                <div class="row mb-3">
                                                                    <label
                                                                        class="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                                        for="link">Link: <span class="required text-danger">*</span></label>
                                                                    <div class="col-xl-10 col-lg-10 col-md-10">
                                                                        <input type="text" name="link" class="form-control"
                                                                            id="link">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--/.tab-pane-->
                                                        @endif
                                                        <div class="tab-pane fade" id="pills-existing-control"
                                                            role="tabpanel" aria-labelledby="pills-create-link-tab">
                                                            <div class="row mb-3">
                                                                <label class="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                                    for="name">Standards: <span
                                                                        class="required text-danger">*</span></label>
                                                                <div class="col-xl-10 col-lg-10 col-md-10">
                                                                    <select class="form-control select2-picker"
                                                                            name="tasks-linking-standards"
                                                                            id="tasks-linking-standards" disabled>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="row mb-3">
                                                                <label class="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                                    for="link">Projects: <span
                                                                        class="required text-danger">*</span></label>
                                                                <div class="col-xl-10 col-lg-10 col-md-10">
                                                                    <select class="form-control select2-picker"
                                                                            name="tasks-linking-projects"
                                                                            id="tasks-linking-projects" disabled>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="row mb-3">
                                                                <label class="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                                    for="link">Controls: <span
                                                                        class="required text-danger">*</span></label>
                                                                <div class="col-xl-10 col-lg-10 col-md-10">
                                                                    <input type="text" class="form-control"
                                                                        id="tasks-linking-controls" value="" disabled>
                                                                    <input type="text" name="linked_to_project_control_id"
                                                                        value="" readonly
                                                                        style="position: absolute;top: 0;z-index: -99;">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--/.tab-pane-->
                                                        <div class="tab-pane fade"
                                                            id="pills-text-input" role="tabpanel"
                                                            aria-labelledby="pills-text-input-tab">
                                                            <div class="row mb-3">
                                                                <label class="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                                    for="text_evidence_name">Name: <span
                                                                        class="required text-danger">*</span></label>
                                                                <div class="col-xl-10 col-lg-10 col-md-10">
                                                                    <input type="text" name="text_evidence_name"
                                                                        class="form-control"
                                                                        id="text_evidence_name">
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3" id="evidence-section">
                                                                <label class="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                                    for="text_evidence">Text: <span
                                                                        class="required text-danger">*</span></label>
                                                                <div class="col-xl-10 col-lg-10 col-md-10">
                                                                    <textarea name="text_evidence" id="text_evidence"
                                                                            class="form-control send-message"
                                                                            rows="3"
                                                                            placeholder="Write your evidence text here ..."
                                                                            autofocus></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--/.tab-pane-->
                                                    </div>
                                                    @if( $globalSetting->allow_document_link || $globalSetting->allow_document_upload )
                                                        <button type="submit"
                                                                class="btn btn-primary ladda-button float-end"
                                                                id="evidence-submit">Upload
                                                        </button>
                                                    @endif
                                                </form>
                                            </div><!--/.evidence-form-section-->
                                        @endif
                                    <!-- allow evidences upload check -->
                                    @endif

                                    <h4 class="pb-2 upload-text p-0">Uploaded Evidences for Control
                                        ID: {{ $projectControl->controlId }}</h4>

                                    <!-- dataTable starts here-->
                                    <div class="uploaded-evidence-main p-2">
                                        <table id="basic-datatable"
                                            class="table nowrap text-center table-bordered border-light low-padding w-100">
                                            <thead class="table-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Task Deadline</th>
                                                <th>Created On</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody class="tbody-light name-table__overflow">
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- dataTable ends here-->
                                </div> <!-- col-xl-6 ends -->

                                <!-- task right starts -->
                                <div class="col-xl-6" id="control-comments-wp">
                                    <h4 class="comment-text pb-2">Comments</h4>
                                    <div class="comment-box">
                                        @forelse($comments as $comment)
                                            <div class="comment-sec mb-2">
                                                <div class="comment-head d-flex align-items-center">
                                                    <span class="avatar"> {{ $comment->sender->avatar }} </span>
                                                    <h5 class="title m-2">{{ $comment->sender->id == $loggedInUser->id ? "You" : decodeHTMLEntity($comment->sender->full_name)}}</h5>
                                                    <small
                                                        class="fw-bold my-1 ms-auto time">{{ $comment->created_at->diffForHumans() }}</small>
                                                </div>
                                                <div class="comment-body">
                                                    {!! $comment->comment !!}
                                                </div>
                                            </div>
                                        @empty
                                            <p>No, comments available</p>
                                        @endforelse
                                    </div>

                                @if($authUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Contributor']))
                                    @if($authUser->id == $projectControl->responsible || $authUser->id == $projectControl->approver)
                                        <!-- post comment -->
                                            <div class="post-comment clearfix">
                                                <form id="control-comment-form"
                                                    action="{{ route( 'compliance.project-controls-comments', [$project->id, $projectControl->id] ) }}"
                                                    method="POST">
                                                    @csrf
                                                    <textarea name="comment" id="comment" class="form-control send-message"
                                                            rows="2" placeholder="Write a comment here ..."
                                                            autofocus></textarea>
                                                    <div class="invalid-feedback d-block">
                                                        @if ($errors->has('comment'))
                                                            {{ $errors->first('comment') }}
                                                        @endif
                                                    </div>
                                                    <button type="submit" class="float-end btn btn-primary my-2">Comment
                                                    </button>
                                                </form>
                                            </div>
                                    @endif
                                @endif
                                <!--justification-->
                                    <div id="justification-section">
                                        @if($controlStatus == 'Rejected')
                                            @if( !is_null($latestJustification) )
                                                <div class="toast show" role="alert" aria-live="assertive"
                                                    aria-atomic="true" data-toggle="toast">
                                                    <div class="toast-header">
                                                        <span
                                                            class="avatar"> {{ $latestJustification->creator->avatar  }} </span>
                                                        <strong class="me-auto m-2">
                                                            {{ $latestJustification->creator_id == $authUser->id ? "Me" : decodeHTMLEntity($latestJustification->creator->full_name)}}
                                                        </strong>
                                                        <small>{{ $latestJustification->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    <div class="toast-body readmore">
                                                        <strong>Status: Rejected</strong>
                                                        <p class="comment-box"> {!! $latestJustification->justification !!}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <!--end justification-->
                                </div>
                                <!-- task right ends -->
                            </div>
                            <!-- outer button submit starts -->
                            <div
                                class="d-flex justify-content-center justify-content-sm-center justify-content-md-end  mt-4"
                                id="evidence-submit-buttons-wp">
                                @if($authUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Contributor']) &&  $authUser->id == $projectControl->responsible)
                                    @if($projectControl->isEligibleForReview)
                                        <form
                                            action="{{ route('compliance.project-controls-review-submit', [$project->id, $projectControl->id]) }}"
                                            id="submit-for-review">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-primary" {{ $projectControl->isEligibleForReview ? "" : "disabled"}}>
                                                Submit for review
                                            </button>
                                        </form>
                                    @endif
                                @endif

                                @if($authUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Contributor']) &&  $authUser->id == $projectControl->approver && $projectControl->status == 'Under Review')
                                    <form
                                        action="{{ route('compliance.project-controls-review-approve', [$project->id, $projectControl->id]) }}"
                                        method="POST" id="approve-submit">
                                        @csrf
                                        <button type="submit" class="btn btn-primary ladda-button" id="approve-btn">
                                            Approve
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-primary mx-3" id="reject-btn" data-toggle="modal"
                                            data-target="#reject-justification-model">Reject
                                    </button>
                                @endif
                            </div>
                            <!-- outer button submit ends -->
                            <!-- modal for reject button -->
                            <div id="reject-justification-model" class="bs-example-modal-center modal fade" tabindex="-1"
                                role="dialog" aria-labelledby="myCenterModalLabel" aria-hidden="true"
                                style="display: none;">
                                <form id="reject-justification-form"
                                    action="{{ route('compliance.project-controls-review-reject', [$project->id, $projectControl->id]) }}"
                                    method="POST">
                                    @csrf
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header p-3">
                                                <h4 class="modal-title">Reject Evidence Confirmation</h4>
                                                <button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true">
                                                    ×
                                                </button>
                                            </div>
                                            <h4 class="ms-3">Justification Message</h4>
                                            <div class="modal-body p-3">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <textarea class="form-control" name="justification"
                                                                    id="justification_textarea"
                                                                    placeholder="Write justifcation message here"
                                                                    required></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer d-flex justify-content-center">
                                                <button type="submit" class="btn btn-primary mx-2">Submit</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"
                                                        aria-hidden="true">Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div><!-- /.modal -->
                            <!-- modal for reject button ends -->
                        </div><!--/.end of task section -->
                    </div><!--outer tab content ends -->

                </div> <!-- cardbox ends -->
            </div>
        </div> <!-- col ends -->
    </div> <!-- row ends -->

    @include('compliance.projects.components.link-to-implemented-controls-model')
    @include('compliance.projects.components.display-text-evidence')

@endsection

@section('plugins_js')
    @include('includes.assets-libs.datatable-js-libs')
    <script src="{{ asset('assets/libs/switchery/switchery.min.js') }}"></script>
    <script src="{{asset('assets/libs/multiselect/jquery.multi-select.js')}}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropify/dropify.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-fileuploads.init.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/ladda/spin.js') }}"></script>
    <script src="{{ asset('assets/libs/ladda/ladda.js') }}"></script>
    <script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('assets/js/readmore.min.js')}}"></script>
@endsection

@section('custom_js')
    <script nonce="{{ csp_nonce() }}">
        let evidences = @json($evidences);
        $(document).ready(function () {
            listenForHistoryBack();
            /* Adding control link evidence rule */
            $.validator.addMethod("control_link_evidence", function (value, element) {
                let isURL = /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z0-9\u00a1-\uffff][a-z0-9\u00a1-\uffff_-]{0,62})?[a-z0-9\u00a1-\uffff]\.)+(?:[a-z\u00a1-\uffff]{2,}\.?))(?::\d{2,5})?(?:[/?#]\S*)?$/i.test(value);

                let isNetworkShareFolderLink = /^(\\)(\\[\w\.\s\-_]+){2,}(\\?)$/.test(value)

                return this.optional(element) || isURL || isNetworkShareFolderLink;
            }, 'Please enter a valid URL or a valid shared/network folder.');

            $(".basic-flatpicker").flatpickr({
                dateFormat: "Y-m-d",
                minDate: "today",
            });

            $('.select2-picker').select2()

            $(document).on("change", ".evidence-upload", function () {
                $('.invalid-feedback').hide();
            })

            //make button name upload on file upload tab
            $(document).on("click", "#pills-tab", function () {
                const currentTabId = $(this).find('li a.active').attr('href');

                // When changing tab,  reset form to clean all inputs
                $('#evidence-upload-form')[0].reset();

                if (currentTabId != '#pills-upload-document') {

                    $('#evidence-submit').html('Save');
                } else {
                    $('#evidence-submit').html('Upload');
                }
            });

            // Make project grey out until select standard
            $(document).on('change', '.select-standard', function () {

                if ($(this).val() != "") {
                    $('.select-project').removeAttr('disabled', false)
                } else {
                    $('.select-project').attr('disabled', 'disabled');
                }

            })

            $(document).on('click', '.open-evidence-text-modal', function () {
                const selectedEvidenceId = $(this).data('evidenceId');
                const evidence = evidences.find(evidence => evidence.id === selectedEvidenceId)
                $('#text-evidence-modal .modal-title').html(evidence.name);
                $('#text-evidence-modal .evidence-text').html(evidence.text_evidence);
                $('#text-evidence-modal').modal();
            })

            // Existing controls linking features js
            $(document).on("click", "#pills-existing-control-tab", function () {
                // resetting input fields in tab
                $("#tasks-linking-standards").empty()
                $("#tasks-linking-projects").empty()
                $("#tasks-linking-controls").val("")
                $("input[name=linked_to_project_control_id]").val("")

                $("#linking-existing-controls-modal").modal()

                const linkingExistingControlsDatatable = $('#linking-existing-controls-datatable').DataTable({
                    serverSide: true,
                    searching: false,
                    ordering: false,
                    processing: true,
                    destroy: true,
                    pagingType: "simple_numbers",
                    ajax: {
                        "url": "{{ route('compliance.project-controls.get-all-implemented-controls', $projectControl->id ) }}",
                        "type": "GET",
                        "data": {
                            "standard_filter": function () {
                                return $('select[name="standard_filter"]').val()
                            },
                            "project_filter": function () {
                                return $('select[name="project_filter"]').val()
                            }
                        }
                    },
                    "drawCallback": function (settings) {
                        $(".dataTables_paginate > .pagination").addClass("pagination-rounded");

                        var elems = Array.prototype.slice.call(document.querySelectorAll('.project-control-switch'));

                        elems.forEach(function (html) {
                            var switchery = new Switchery(html);
                        });

                        $(elems).on("change", function () {
                            let standard = $(this).data('standard')
                            let project = $(this).data('project')
                            let controlName = $(this).data('control-name')
                            let controlId = $(this).data('control-id')

                            // standard select box
                            let standardSelect = $("#tasks-linking-standards")
                            standardSelect.empty()
                            standardSelect.append(`<option value="" selected>${standard}</option>`);

                            // populating project select
                            let projectSelect = $("#tasks-linking-projects")
                            projectSelect.empty()
                            projectSelect.append(`<option value="" selected>${project}</option>`);

                            // populating control select
                            let controlInputView = $("#tasks-linking-controls")
                            let controlInput = $("input[name=linked_to_project_control_id]")

                            controlInputView.val(controlName)
                            controlInput.val(controlId)

                            $('#linking-existing-controls-modal').modal('hide')
                        });
                    }
                });

                // Applying filters
                $("#linking-existing-controls-modal button[name='search']").on('click', function () {
                    linkingExistingControlsDatatable.ajax.reload()
                })

                // setting projects select boxes

                $(document).on('change', 'select[name="standard_filter"]', function () {
                    let selectedOption = this.options[this.selectedIndex];

                    $.ajax({
                        url: "{{ route('compliance.tasks.get-projects-by-standards') }}",
                        method: 'GET',
                        data: {standardId: selectedOption.value}
                    }).done(function (res) {

                        // resetting select  box options for projects select box
                        let targetSelectBox = $('select[name="project_filter"]');


                        targetSelectBox.empty();
                        targetSelectBox.append(`<option value="">Select Project</option>`);

                        if (res.length > 0) {
                            res.forEach(element => {
                                targetSelectBox.append(`<option value="${element.id}">${element.name}</option>`);
                            });
                        }
                    });
                });

            })

// remove Linked Control
            $(document).on('click', '#remove-linked-control', function (e) {
                e.preventDefault();
                let url = this.href

                $.ajax({
                    url: url, success: function (response) {
                        if (response.success) {
                            refreshEvidenceFormSection()
                        }
                    }
                });
            })


// Creating evidence upload form
// function validateEvidenceUploadForm(){
            $(document).on('click', '#evidence-upload-form button[type=submit]', function () {
                $("#evidence-upload-form").validate({
                    errorClass: 'invalid-feedback',
                    rules: {
                        name: {
                            required: true,
                            maxlength: 190
                        },
                        name2: {
                            required: true,
                            maxlength: 190
                        },
                        text_evidence_name: {
                            required: true,
                        },
                        text_evidence: {
                            required: true,
                        },
                        link: {
                            required: true,
                            // control_link_evidence: true,
                            maxlength: 500
                        },
                        'evidences': {
                            required: true
                        },
                        'tasks-linking-standards': {
                            required: true
                        },
                        'tasks-linking-projects': {
                            required: true
                        },
                        'linked_to_project_control_id': {
                            required: true
                        }
                    },
                    messages: {
                        name: {
                            required: 'The name field is required.',
                            maxlength: 'The name may not be greater than 190 characters.'
                        },
                        name2: {
                            required: 'The name field is required.',
                            maxlength: 'The name may not be greater than 190 characters.'
                        },
                        link: {
                            required: 'The link field is required.',
                            url: 'Please enter a valid link.',
                            maxlength: 'The link may not be greater than 500 characters.'
                        },
                        'evidences': {
                            required: 'The evidence field is required.'
                        },
                        'tasks-linking-standards': {
                            required: 'This field is required.'
                        },
                        'tasks-linking-projects': {
                            required: 'This field is required.'
                        },
                        'linked_to_project_control_id': {
                            required: 'This field is required.'
                        }
                    },
                    submitHandler: function (form, event) {
                        let loadingBtn = Ladda.create(document.querySelector('#evidence-submit'))

                        loadingBtn.start()
                        uploadEvidence(form, event);
                    }
                });
            });

// }

            // expandable textarea height
            ManageTextAreaHeight();

            initReadMoreComment();

            function initReadMoreComment() {
                $('.comment-body').readmore({
                    collapsedHeight: 60
                });

                $('.readmore').readmore({
                    collapsedHeight: 88
                });
            }

            $("#justification").on('keyup', function (e) {
                var justification = $("#justification").val();
                if (justification == '' || justification == 'undefined') {
                    $("#justification-err").show();
                } else {
                    $("#justification-err").hide();
                }
            });

            function initializeEvidencesDatatable() {
                // destroying datatable
                $('#basic-datatable').DataTable().clear().destroy();

                $("#basic-datatable").DataTable({
                    serverSide: true,
                    lengthChange: false,
                    searching: false,
                    ordering: false,
                    paging: false,
                    info: false,
                    scrollY: '110',
                    // sorting: false,
                    ajax: {
                        "url": "{{ route('compliance-project-control-evidences', [$project->id, $projectControl->id]) }}",
                        "type": "GET",
                    },
                    // "columnDefs": [
                    //     {
                    //         "render": function ( data, type, row ) {
                    //             return $.fn.dataTable.render.text().display(data, type, row);
                    //         },
                    //         "targets": [0]
                    //     }
                    // ],
                })
            }

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $($.fn.dataTable.tables(true)).css('width', '100%');
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                $('.dataTables_scrollBody').scrollTop($('.dataTables_scrollBody').height())
                // $('.comment-box').scrollTop($('.comment-box').height())
                $('.comment-box').scrollTop($('.comment-box')[0].scrollHeight);
            });

            // initializing evidence datatables
            initializeEvidencesDatatable()

            $.validator.addMethod("differentUser", function (value, element) {
                const responsibleVal = $('#responsible').val();
                const approverVal = $('#approver').val();
                let isDifferent = true;
                if (responsibleVal === approverVal) {
                    isDifferent = false
                }
                return this.optional(element) || isDifferent;
            }, "Select different user, please")

            /* Control detail form validation  */
            function validateControlDetailForm() {
                var controlDetailForm = $("#control-detail-form")

                controlDetailForm.validate({
                    errorClass: 'invalid-feedback',
                    rules: {
                        responsible: {
                            required: true,
                            differentUser: true
                        },
                        approver: {
                            required: true,
                            differentUser: true
                        },
                        deadline: {
                            required: true
                        },
                        frequency: {
                            required: true
                        }
                    },
                    messages: {
                        responsible: {
                            required: 'The responsible field is required.',
                            differentUser: "Responsible & Approver can't be same"
                        },
                        approver: {
                            required: 'The approver field is required.',
                            differentUser: "Responsible & Approver can't be same"
                        },
                        deadline: {
                            required: 'The deadline field is required.',
                        },
                        frequency: {
                            required: 'The frequency field is required.',
                        }
                    }
                });

                return controlDetailForm.valid()
            }

            $(document).on("submit", "#control-detail-form", function (e) {
                e.preventDefault();
                if (!validateControlDetailForm()) {
                    return
                }

                var serialize = $("#control-detail-form").serialize();

                let loadingBtn = Ladda.create(document.querySelector('#control-detail-form button[type=submit]'))

                loadingBtn.start()

                $.ajax({
                    url: $("#control-detail-form").attr('action'),
                    method: 'POST',
                    data: serialize,
                    success: function (response) {
                        // Ladda.stopAll()
                        if (response.exception) {
                            Swal.fire({
                                type: 'error',
                                text: response.exception
                            })
                        }

                        if (response == "success") {
                            $("#alert-success-control").show();

                            $('#tasks').load(document.URL + ' #tasks > *', function () {
                                // initializing evidence datatables
                                initializeEvidencesDatatable()

                                $(".dropify").dropify()

                                $('.select2-picker').select2()

                            })
                        }
                        // refresh tab contents
                        refreshTabContents();
                    },
                    error: function (error) {
                        if (error.responseJSON.errors) {
                            const alertMessagesElem = $('.alert-error-messages');
                            alertMessagesElem.empty();
                            const errors = error.responseJSON.errors;
                            if (errors.approver) {
                                errors.approver.forEach(function (error) {
                                    alertMessagesElem.append("<strong>" + error + "</strong><br>");
                                });
                            }
                            if (errors.responsible) {
                                errors.responsible.forEach(function (error) {
                                    alertMessagesElem.append("<strong>" + error + "</strong>");
                                });
                            }
                        }
                        $("#alert-danger-control").show();
                        refreshTabContents();
                    }
                });
            });


            function listenForHistoryBack(times = -1) {
                $('#history_back').click(function (e) {
                    window.history.back(times);
                });
            }

            function refreshTabContents() {
                $('#details').load(document.URL + ' #details > *', function () {
                    $('.select2-picker').select2()

                    $(".basic-flatpicker").flatpickr({
                        dateFormat: "Y-m-d",
                        minDate: "today"
                    });

                    refreshControlStatusBadges()
                })
            }

// Uploading Evidences
            function uploadEvidence(form, event) {
                event.preventDefault()

                // Find disabled inputs, and remove the "disabled" attribute
                var disabled = $(form).find('select:disabled').removeAttr('disabled');

                // serialize the form
                var serialized = new FormData(form);

                // re-disabled the set of inputs that you previously enabled
                disabled.attr('disabled', 'disabled');


                $.ajax({
                    processData: false,
                    contentType: false,
                    cache: false,
                    url: $("#evidence-upload-form").attr('action'),
                    method: 'POST',
                    data: serialized,
                    success: function (response) {
                        if (response.result.trim() == "success") {
                            $('#evidence-upload-form').load(document.URL + ' #evidence-upload-form > *', function () {
                                // validateEvidenceUploadForm()

                                $(".dropify").dropify()

                                $('.select2-picker').select2("destroy").select2()

                                Ladda.stopAll()

                                $('#basic-datatable').DataTable().ajax.reload();

                                // refresh submit button
                                $('#evidence-submit-buttons-wp').load(document.URL + ' #evidence-submit-buttons-wp > *')

                                if(response.evidence) {
                                    evidences.push(response.evidence);
                                }
                            });

                            $("#alert-success-evidence").show();
                        }
                    },
                    error: function (error) {
                        console.log('status', error);
                        if (error.status == 422) {
                            let validationErrors = error.responseJSON.errors;
                            if ('evidences' in validationErrors) {
                                validationErrors.evidences.forEach(function (msg) {
                                    $(document).find('#evidence-section .dropify-wrapper').after(`<label class="invalid-feedback d-block">${msg}</label>`)
                                })
                            }
                        }

                        /* Max Filesize limit on the server. */
                        if (error.status == 413) {
                            $(document).find('#evidence-section .dropify-wrapper').after(`<label class="invalid-feedback d-block">The Upload Max Filesize is {{ini_get("upload_max_filesize")}}B on the server. Please increase Upload Max Filesize limit on the server.</label>`)
                        }

                        Ladda.stopAll()
                    }
                });
            }

            // deleting evidences
            $(document).on('click', '.evidence-delete-link', function (event) {
                event.preventDefault()
                const evidenceDeleteLink = this.href

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: '#ff0000',
                    confirmButtonText: 'Yes, delete it!',
                    imageUrl: '{{asset('assets/images/warning.png')}}',
                    imageWidth: 120
                }).then((result) => {
                    if (result.value) {
                        $.get(evidenceDeleteLink)
                            .done(function (res) {
                                if (res.success) {
                                    $('#basic-datatable').DataTable().ajax.reload(null, false);

                                    // refresh submit button
                                    $('#evidence-submit-buttons-wp').load(document.URL + ' #evidence-submit-buttons-wp > *', function () {
                                        // reinitializing  select2
                                        $('.select2-picker').select2("destroy").select2()
                                    })

                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'Your file has been deleted.',
                                        confirmButtonColor: '#b2dd4c',
                                        imageUrl: '{{asset('assets/images/success.png')}}',
                                        imageWidth: 120
                                    })
                                }
                            });
                    }
                })
            });

            // Comment form Js

            // Creating Control Comment Validator
            $(document).on('click', '#control-comment-form button[type=submit]', function () {
                $("#control-comment-form").validate({
                    errorClass: 'invalid-feedback',
                    rules: {
                        comment: {
                            required: true
                        }
                    },
                    messages: {
                        comment: {
                            required: 'The comment field is required.'
                        }
                    },
                    submitHandler: function (form, event) {
                        event.preventDefault();

                        let loadingBtn = Ladda.create(event.target.querySelector('button[type=submit]'))

                        loadingBtn.start()

                        submitControlComment(form)
                    }
                });
            });

            // Comment Form Submit
            function submitControlComment(form) {
                var formData = new FormData(form);
                let formAction = form.action

                $.ajax({
                    processData: false,
                    contentType: false,
                    cache: false,
                    url: formAction,
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.trim() == "success") {
                            $('#control-comments-wp').load(document.URL + ' #control-comments-wp > *', function () {
                                Ladda.stopAll()
                                initReadMoreComment();
                                ManageTextAreaHeight();
                                $('.comment-box').scrollTop($('.comment-box')[0].scrollHeight);
                            })
                        }
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }

            function ManageTextAreaHeight() {
                // Text Area Auto Expandable
                var textarea = document.getElementById("comment");

                var heightLimit = 500;

                if (textarea) {
                    textarea.oninput = function () {
                        textarea.style.height = "";
                        textarea.style.height = Math.min(textarea.scrollHeight, heightLimit) + "px";
                    };
                }
            }

            var justification_textarea = document.getElementById("justification_textarea");

            var heightLimit = 500;

            justification_textarea.oninput = function () {
                justification_textarea.style.height = "";
                justification_textarea.style.height = Math.min(justification_textarea.scrollHeight, heightLimit) + "px";
            };

            // Submit Approval
            $(document).on('submit', "#approve-submit", function (event) {
                event.preventDefault()

                Swal.fire({
                    title: 'Approve Evidence Confirmation',
                    text: "Are you sure?",
                    showCancelButton: true,
                    confirmButtonColor: '#ff0000',
                    confirmButtonText: 'Approve',
                    imageUrl: "{{ asset('assets/images/warning.png') }}",
                    imageWidth: 120
                })
                    .then((result) => {
                        if (result.value) {
                            // STARTING PAGE LOADER
                            PageLoader.show()

                            let form = event.target
                            var formData = new FormData(form);
                            let formAction = event.target.action

                            $.post(formAction, {'_token': '{!! csrf_token() !!}'}, function (response) {
                                if (response.success) {
                                    refreshPageContentSection('tasks').then(function () {
                                        Swal.fire({
                                            title: "Success!",
                                            text: "The evidence was approved successfully.",
                                            confirmButtonColor: '#b2dd4c',
                                            imageUrl: '{{asset('assets/images/success.png')}}',
                                            imageWidth: 120
                                        })
                                    })
                                } else {
                                    refreshPageContentSection('tasks').then(function () {
                                        // displaying exception
                                        if (response.exception) {
                                            Swal.fire({
                                                type: 'error',
                                                text: response.exception
                                            })
                                        }
                                    })
                                }
                            })
                        }
                    })
            });

            // Submitted Evidences for review
            $(document).on('submit', "#submit-for-review", function (event) {
                event.preventDefault()

                Swal.fire({
                    title: 'Confirm submission?',
                    text: "Review your evidence before submitting.",
                    imageUrl: '{{asset('assets/images/warning.png')}}',
                    imageWidth: 120,
                    showCancelButton: true,
                    confirmButtonColor: '#ff0000',
                    confirmButtonText: 'Submit'
                }).then((result) => {
                    if (result.value) {
                        let form = event.target
                        let formData = new FormData(form);
                        let formAction = form.action

                        // STARTING PAGE LOADER
                        PageLoader.show()

                        $.ajax({
                            processData: false,
                            contentType: false,
                            cache: false,
                            url: formAction,
                            method: 'POST',
                            data: formData,
                            success: function (response) {
                                // hiding page loader
                                PageLoader.hide()

                                if (response.success) {
                                    Swal.fire({
                                        title: "Submitted!",
                                        text: "Your evidence was submitted successfully.",
                                        confirmButtonColor: '#b2dd4c',
                                        imageUrl: '{{asset('assets/images/success.png')}}',
                                        imageWidth: 120
                                    })

                                    refreshPageContentSection('tasks')
                                } else {
                                    // displaying exception
                                    if (response.exception) {
                                        Swal.fire({
                                            type: 'error',
                                            text: response.exception
                                        })

                                        Ladda.stopAll()
                                    }
                                }
                            },
                            error: function (error) {
                                // hiding page loader
                                PageLoader.hide()
                                console.log(error);
                            },
                            always: function () {
                                // hiding page loader
                                PageLoader.hide()
                            }
                        });
                    }
                })
            })

            //Reject and give Justification Form
            $(document).on("submit", "#reject-justification-form", function (event) {
                event.preventDefault()

                let form = event.target
                let formData = new FormData(form);
                let formAction = form.action
                // STARTING PAGE LOADER
                PageLoader.show()

                $('#reject-justification-model').modal('toggle');

                form.querySelector('textarea').value = ''

                $.ajax({
                    processData: false,
                    contentType: false,
                    cache: false,
                    url: formAction,
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            refreshPageContentSection('tasks')
                        } else {
                            refreshPageContentSection('tasks').then(function () {
                                // displaying exception
                                if (response.exception) {
                                    Swal.fire({
                                        type: 'error',
                                        text: response.exception
                                    })

                                }
                            })
                        }
                    },
                    error: function (error) {
                        refreshPageContentSection('tasks')
                    }
                });
            })

            // Refreshes Details tab
            function refreshDetailTabs() {
                $('#details').load(document.URL + ' #details > *', function () {
                    $('.select2-picker').select2("destroy").select2()

                    $(".basic-flatpicker").flatpickr({
                        dateFormat: "Y-m-d",
                        minDate: "today",
                    });
                })
            }

            // Refreshes control status badges
            function refreshControlStatusBadges() {
                $('#control-status-badge').load(document.URL + ' #control-status-badge > *');
                listenForHistoryBack(-2);
            }

            // refresh evidence-form-section
            function refreshEvidenceFormSection() {
                $('#evidence-form-section').load(document.URL + ' #evidence-form-section > *', function () {
                    $(".dropify").dropify()

                    $('.select2-picker').select2("destroy").select2()

                    // refresh submit button
                    $('#evidence-submit-buttons-wp').load(document.URL + ' #evidence-submit-buttons-wp > *')
                });
            }

            // Refresh content section of page
            function refreshPageContentSection(currentTab) {
                var d = new $.Deferred();

                $('#content-section-wp').load(document.URL + ' #content-section-wp > *', function () {
                    if (currentTab == 'tasks') {
                        // initializing evidence datatables
                        initializeEvidencesDatatable()
                        initReadMoreComment();
                        $(`a[href$='#${currentTab}']`).trigger('click')
                    }

                    // HINDING PAGE LOADER
                    PageLoader.hide()

                    d.resolve('some_value_compute_asynchronously');
                })

                return d.promise();
            }

        });// END OF DOCUMENT READY
    </script>
@endsection
