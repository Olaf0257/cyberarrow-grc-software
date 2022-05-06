@extends('layouts.layout')

@php
    if($control->id) {
        $pageTitle = "Edit Control";
    } else {
        $pageTitle = "Create Control";
    }
@endphp

@section('title', $pageTitle)

@section('content')

@section('plugins_css')
    <link href="{{ asset('assets/libs/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('custom_css')

    <style>
        .manual__import-left span {
            color: red;
        }

        .csv {
            margin-top: -35px;
        }

        .edit-control-btn a:nth-child(2) {
            margin-left: 4px;
        }

        /*******************
            RESPONSIVE
        ********************/
        @media (max-width: 575px) {
            .table-right h4 {
                padding-top: 10px;
            }

            .upload__btn {
                margin-bottom: 5px;
                min-width: 145px;
            }
        }
    </style>

@endsection


<!-- breadcrumbs -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Administration</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('compliance-template-view') }}">Compliance
                            Template</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('compliance-template-view-controls', $control->standard->id) }}">Controls</a>
                    </li>
                    <li class="breadcrumb-item active"><a href="javascript: void(0);">Edit</a></li>
                </ol>
            </div>
            <h4 class="page-title">{{  $pageTitle }}</h4>
        </div>
    </div>
</div>
<!-- end of breadcrumbs -->
@include('includes.flash-messages')
<section id="table">
    <div class="row bg-white py-3 px-2">
        <div class="{{ $control->id ? 'col-xl-12' : 'col-xl-6' }}">
            <div class="table-left">
                <h4>{{ $control->id ? "Update" : "Create a"}} New Control</h4>
                <h5 class="mb-3 sub-header">Fields with <span class="text-danger">*</span> are required.</h5>

                <form
                    action="{{ $control->id ? route('compliance-template-update-controls', [$control->standard->id, $control->id])  : route('compliance-template-store-controls', $control->standard->id) }}"
                    method="post" id="manual-control-upload">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label"> Name <span>*</span></label>
                        <input type="text" class="form-control" id="name"
                               value="{{ old('name', decodeHTMLEntity($control->name)) }}" name="name"
                               aria-describedby="emailHelp" placeholder="Enter name">
                        <div class="invalid-feedback d-block">
                            @if ($errors->has('name') && !Session::get('csv_upload_errors'))
                                {{ $errors->first('name') }}
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label"> Description <span>*</span></label>
                        <textarea class="form-control" name="description" id="description" rows="5" cols="10"
                                  placeholder="Enter description...">{{ old('description', decodeHTMLEntity($control->description)) }}</textarea>
                        <div class="invalid-feedback d-block">
                            @if ($errors->has('description') && !Session::get('csv_upload_errors'))
                                {{ $errors->first('description') }}
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="primary_id" class="form-label"> Primary ID <span>*</span></label>
                        <input type="text" id="primary_id" name="primary_id"
                               value="{{ old('primary_id', decodeHTMLEntity($control->primary_id)) }}"
                               class="form-control" placeholder="Enter primary ID">
                        <div class="invalid-feedback d-block">
                            @if ($errors->has('primary_id') && !Session::get('csv_upload_errors'))
                                {{ $errors->first('primary_id') }}
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="sub_id" class="form-label"> Sub ID <span>*</span></label>
                        <input type="text" id="sub_id" name="sub_id"
                               value="{{ old('sub_id', decodeHTMLEntity($control->sub_id)) }}" class="form-control"
                               placeholder="Enter sub ID">
                        <div class="invalid-feedback d-block">
                            @if ($errors->has('sub_id') && !Session::get('csv_upload_errors'))
                                {{ $errors->first('sub_id') }}
                            @endif
                        </div>
                    </div>


                    <div class="mb-3 id-sep">
                        <label for="id_separator" class="id_sep-label"> ID Separator </label>
                        <select class="form-select my-1 col-md-12" id="id_separator" name="id_separator">
                            @foreach($idSeparators as $index => $idSeparator)
                                <option value="{{ $index }}"
                                        @if( old('id_separator', $control->id_separator) == $index)
                                        selected
                                    @endif
                                >
                                    {{ $idSeparator }}
                                </option>
                            @endforeach
                        </select>

                        <div class="invalid-feedback d-block">
                            @if ($errors->has('id_separator'))
                                {{ $errors->first('id_separator') }}
                            @endif
                        </div>
                    </div>


                    <div class="{{ $control->id ? 'd-flex justify-content-end edit-control-btn' : '' }}">
                        <button type="submit"
                                class="btn btn-primary">{{ $control->id ? "Update Control" : "Create Control" }}</button>
                        <a href="{{ route('compliance-template-view-controls', [$standard->id]) }}">
                            <button type="button" class="btn btn-danger">Back to List</button>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- rigt row begins-->
        <div class="{{ $control->id ? 'd-none' : 'col-xl-6' }}">
            @if($errors->any()  && Session::get('csv_upload_errors'))
                <div class="alert alert-danger">
                    <button type="button" class="btn-close" data-dismiss="alert">×</button>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            <form action="{{ route('compliance-template-upload-csv-store-controls', [$standard->id]) }}" method="post"
                  enctype="multipart/form-data">
                @csrf
                <div class="table-right">
                    <h4>Upload Control CSV</h4>
                    <h5 class="mb-5"> Upload a CSV file to create new controls</h5>

                    <div class="csv">
                        <input type="file" class="btn btn-csv-upload my-2 dropify" name="csv_upload" data-height="150">
                        <div class="invalid-feedback d-block">
                            @if ($errors->has('csv_upload'))
                                {{ $errors->first('csv_upload') }}
                            @endif
                        </div>
                    </div>


                    <div class="mb-3 pt-2">
                        <label for="id_separator" class="form-label"> ID Separator </label>
                        <select class="form-select-next my-1 col-md-12 form-control" name="id_separator">

                            @foreach($idSeparators as $index => $idSeparator)
                                <option value="{{ $index }}">{{ $idSeparator }}</option>
                            @endforeach

                        </select>
                        <div class="invalid-feedback d-block">
                            @if ($errors->has('id_separator'))
                                {{ $errors->first('id_separator') }}
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="upload__btn btn btn-primary">
                        Upload Controls
                    </button>

                    <a href="{{ route('compliance-template-download-template-controls', [$standard->id]) }}"
                       class="btn sample__dwn-btn btn-primary"> Download Sample</a>

                    <div class="cv-info">
                        <h5 class="text-uppercase text-white">the csv file should have the following header line:</h5>
                        <p>primary_id, sub_id, name, description</p>
                        <p>Field size limits for the CSV are: </p>
                        <ul>
                            <li>primary_id: 191 character limit</li>
                            <li>sub_id: 191 character limit</li>
                            <li>name: 191 character limit</li>
                            <li>description: 50,000 character limit</li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
        <!-- rigt row ends-->
    </div>
</section>
@endsection

@section('plugins_js')
    <script src="{{ asset('assets/libs/dropify/dropify.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-fileuploads.init.js') }}"></script>
    <script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
@endsection


@section('custom_js')
    <script nonce="{{ csp_nonce() }}">
        $("#manual-control-upload").validate({
            errorClass: 'invalid-feedback',
            rules: {
                name: {
                    required: true,
                    maxlength: 191
                },
                description: {
                    required: true,
                },
                primary_id: {
                    required: true,
                    maxlength: 191
                },
                sub_id: {
                    required: true,
                    maxlength: 191
                }
            },
            messages: {
                name: {
                    required: 'The Name field is required',
                    maxlength: 'The name may not be greater than 191 characters.'
                },
                description: {
                    required: 'The description field is required.',
                }
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
    </script>
@endsection
