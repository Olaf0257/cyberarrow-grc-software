@extends('layouts.layout')

@php
if($standard->id) {
    $pageTitle = "Edit Template";
} else {
    $pageTitle = "Create Template";
}
@endphp

@section('title', $pageTitle)

@section('content')

<!-- breadcrumbs -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Administration</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('compliance-template-view') }}">Compliance Template</a></li>
                    <li class="breadcrumb-item active"><a href="javascript: void(0);">Create</a></li>
                </ol>
            </div>
            <h4 class="page-title">{{ $pageTitle }}</h4>
        </div>
    </div>
</div>
<!-- end of breadcrumbs -->

@include('includes.flash-messages')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h5 class="sub-header">Fields with <span class="text-danger">*</span> are required.</h5>
                @php
                 $actionUrl = '';

                    if ($standard->id){
                        $actionUrl = route('compliance-template-update', $standard->id);
                    } else {
                        $actionUrl = route('compliance-template-store');
                    }
                    $url = $_SERVER['REQUEST_URI'];
                   $explodeurl = explode('/',$url);

                $standardID = 0;

                if(isset($explodeurl[6]))
                {
                    $standardID =$explodeurl[6];
                }
                @endphp
                <form class="absolute-error-form" action="{{ $actionUrl}}" method="post" id="validate-form">
                    @csrf
                    <div class="tab-pane">
                        <div class="row">
                            <div class="col-12">
                                <div class="row mb-3">
                                    <label class="col-md-3 form-label col-form-label" for="name">Name <span class="text-danger">*</span></label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="name" placeholder="Name" tabindex="1"
                                               value="{{old('name', decodeHTMLEntity($standard->name))}}">
                                        <div class="invalid-feedback d-block">
                                            @if ($errors->has('name'))
                                            {{ $errors->first('name') }}
                                            @endif
                                        </div>

                                    </div>
                                </div>
                                <input type="hidden" value="{{$standardID}}" name="dublicateStandard">
                                <div class="row mb-3">
                                    <label class="col-md-3 form-label col-form-label" for="version"> Version <span class="text-danger">*</span></label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="version" placeholder="Version" tabindex="2"
                                               value="{{old('version', decodeHTMLEntity($standard->version))}}">
                                        <div class="invalid-feedback d-block">
                                            @if ($errors->has('version'))
                                            {{ $errors->first('version') }}
                                            @endif
                                        </div>

                                    </div>
                                </div>

                                <ul class="list-inline mb-0 wizard">
                                    <li class="next list-inline-item float-end">
                                    <a href="{{route('compliance-template-view')}}"><button type="button" class="btn btn-danger back-btn" tabindex="4">Back To List</button></a>
                                        <button type="submit" class="btn btn-primary" tabindex="3">@if($standard->id) Update @else Create @endif</button>
                                    </li>
                                </ul>
                            </div> <!-- end col -->
                        </div> <!-- end row -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script nonce="{{ csp_nonce() }}">
   $("#validate-form").validate({
     errorClass: 'invalid-feedback',
     rules: {
         name: {
             required: true,
             maxlength: 190
         },
         version: {
             required: true,
             maxlength: 190
         }
     },
     messages: {
         name: {
             required: 'The Name field is required',
             maxlength: 'The Name field may not be greater than 190 characters'
         },
         version: {
             required: 'The Version field is required',
             maxlength: 'The Version field may not be greater than 190 characters'
         }
     },
     submitHandler: function(form) {

         $(form).find('button[type=submit]').prop('disabled', true)

         form.submit();
     }
    });
    </script>
@endsection
