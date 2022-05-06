@extends('layouts.layout')

@php $pageTitle = "Create Project"; @endphp

@section('title', $pageTitle)

@section('content')

<!-- breadcrumbs -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('compliance-dashboard') }}">Compliance</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('compliance-projects-view') }}">Projects</a></li>
                    <li class="breadcrumb-item active"><a href="javascript: void(0);">{{ $project->id ? 'Update' : 'Create' }}</a></li>
                </ol>
            </div>
            <h4 class="page-title">{{ $project->id ? 'Update' : 'Create' }} Project</h4>
        </div>
    </div>
</div>
<!-- end of breadcrumbs -->

<!-- shows flash messages here -->
@include('includes.flash-messages')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <form class="absolute-error-form" id="project-form" action="{{ $project->id ? route('compliance-projects-update', $project->id) : route('compliance-projects-store') }}" method="post">
                    @csrf
                    <div id="progressbarwizard">
                        <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-3">
                            <li class="nav-item">
                                <a href="#account-2" data-toggle="tab" id="first-tab" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-information-outline me-1"></i>
                                    <span class="d-none d-sm-inline">Project Details</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#finish-2" data-toggle="tab" id="finish" class="nav-link rounded-0 pt-2 pb-2 disabled">
                                    <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>
                                    <span class="d-none d-sm-inline">Finish</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content b-0 mb-0">

                            <div id="bar" class="progress mb-3" style="height: 7px;">
                                <div class="bar progress-bar progress-bar-striped progress-bar-animated secondary-bg-color"></div>
                            </div>

                            <div class="tab-pane" id="account-2">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row mb-3">
                                            <label class="col-md-3 form-label" for="name">Project Name <span class="required text-danger">*</span></label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="project-name" name="name" placeholder="Project Name" tabindex="1"
                                                       value="{{old('name', decodeHTMLEntity($project->name))}}">
                                                <div class="invalid-feedback d-block">
                                                    @if ($errors->has('name'))
                                                    {{ $errors->first('name') }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-md-3 form-label" for="description"> Description <span class="required text-danger">*</span></label>
                                            <div class="col-md-9">
                                                <textarea name="description" id="description" class="form-control" cols="30" rows="5" placeholder="Description" tabindex="2">{{ old('description', decodeHTMLEntity($project->description)) }}</textarea>
                                                <div class="invalid-feedback d-block">
                                                    @if ($errors->has('description'))
                                                    {{ $errors->first('description') }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 form-label col-form-label" for="standard">Standard <span class="required text-danger">*</span></label>
                                            <div class="col-md-9">
                                                <select name="standard_id" id="standard" class="form-control" tabindex="3" @if(isset($assignedControls) && $assignedControls > 0) disabled ="true" @endif>
                                                    <option value="">Choose Standard</option>
                                                    @foreach($standards as $key => $standard)
                                                    <option value="{{$standard->id}}" @if( old('standard', $project->standard_id ? $project->standard_id : '') == $standard->id) ) selected @endif>
                                                            {{ $standard->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback d-block">
                                                    @if ($errors->has('standard_id'))
                                                    {{ $errors->first('standard_id') }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="list-inline mb-0 wizard">
                                            <li class="next list-inline-item float-end">
                                                <a href="javascript: void(0);" id="next" class="btn btn-primary" tabindex="4">Next</a>
                                            </li>
                                        </ul>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->
                            </div>

                            <div class="tab-pane" id="finish-2">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="text-center">
                                            <h2 class="mt-0"><i class="mdi mdi-check-all"></i></h2>
                                            <h2 class="mt-0 mb-3">Thank you !</h2>
                                            <button type="submit" class="btn btn-primary">{{ $project->id ? 'Update' : 'Launch' }} Project</button>
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->
                            </div>
                        </div> <!-- tab-content -->
                    </div> <!-- end #progressbarwizard-->
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('plugins_js')
<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>
@endsection

@section('custom_js')
<!-- Init js-->
<script src="{{asset('assets/js/pages/form-wizard.init.js')}}"></script>

<script nonce="{{ csp_nonce() }}">
    // Create form

    $( document ).ready(function() {
        $('#project-form').on('submit', function () {
        $(this).find(':input').prop('disabled', false);
        });

        var projectForm = $("#project-form");

        projectForm.validate({
            errorClass: 'invalid-feedback',
            rules: {
                name: {
                    required: true,
                    maxlength: 190,
                    remote: {
                        url: "{{ route('compliance.projects.check-project-name-taken', $project->id) }}",
                        type: "get",
                        data: {
                            name: function() {
                                return $( "#project-name" ).val();
                            }
                        }
                    }
                },
                description: {
                    required: true,
                },
                standard_id: {
                    required: true,
                }
            },
            messages: {
                name: {
                    required: 'The Project Name field is required',
                    maxlength: 'The Project Name may not be greater than 190 characters',
                    remote: 'The Project Name already taken'
                },
                description: {
                    required: 'The Description field is required',
                },
                standard_id: {
                    required: 'The Standard field is required',
                }
            },
            submitHandler: function(form) {
                form.submit();

                $(form).find('button[type=submit]').attr('disabled', true)
            }
        });


        $( "#project-form #next" ).on('click', function() {
            // $( ".invalid-feedback" ).remove();

            validateProjectCreateForm()
        });


        function validateProjectCreateForm(){
            $isValid = projectForm.valid();

            if($isValid){
                $('#project-form #finish').removeClass('disabled');

                $('#project-form #prev, #project-form #first-tab').click(function() {
                    $('#project-form #finish').addClass('disabled');
                })
            }
        }
    });




</script>
@endsection


