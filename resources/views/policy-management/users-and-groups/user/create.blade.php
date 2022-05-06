@extends('layouts.layout')

@php $pageTitle = "Create User"; @endphp

@section('title', $pageTitle)

@section('plugins_css')
<link href="{{asset('assets/libs/multiselect/multi-select.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <!-- breadcrumbs -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">Policy Management</a></li>
                        <li class="breadcrumb-item"><a href="#">Create User</a></li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $pageTitle }}</h4>
            </div>
        </div>
    </div>
    <!-- end of breadcrumbs -->

    @include('includes.flash-messages')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('policy-management.users-and-groups.users.store') }}" method="post" id="validate-form" class="form-horizontal">
                    @csrf
                    <div class="row mb-3">
                        <label for="email" class="col-3 form-label">Email <span class="required text-danger">*</span></label>
                        <div class="col-9">
                            <input type="text" name="email" class="form-control" placeholder="Email Address" tabindex="3" value="{{old('email', $user->email)}}">
                            <div class="invalid-feedback d-block">
                                @if ($errors->has('email'))
                                {{ $errors->first('email') }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="firstname" class="col-3 form-label">First Name <span class="required text-danger">*</span></label>
                        <div class="col-9">
                            <input type="text" name="first_name" class="form-control" placeholder="First Name" tabindex="1" value="{{old('first_name', $user->first_name)}}">
                            <div class="invalid-feedback d-block">
                                @if ($errors->has('first_name'))
                                {{ $errors->first('first_name') }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="lastname" class="col-3 form-label">Last Name <span class="required text-danger">*</span></label>
                        <div class="col-9">
                            <input type="text" name="last_name" class="form-control" placeholder="Last Name" tabindex="2" value="{{old('last_name', $user->last_name)}}">
                            <div class="invalid-feedback d-block">
                                @if ($errors->has('last_name'))
                                {{ $errors->first('last_name') }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="groups" class="col-3 form-label">Select Group(s) to Add Users To
                        </label>
                        <div class="col-9">
                        <select name="groups[]" class="form-control select2-multiple" id="groups" multiple="multiple" data-placeholder="Click to select..." tabindex="5">
                        </select>
                        <div class="invalid-feedback d-block">
                            @if ($errors->has('groups'))
                            {{ $errors->first('groups') }}
                            @endif
                        </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mb-3">
                        <button type="submit" class="btn btn-primary waves-effect waves-light" tabindex="6">@if($user->id) Update @else Create @endif User</button>
                        <a href="">
                            <button type="button" class="ms-2 btn btn-danger waves-effect waves-light" tabindex="7">Back To List</button>
                        </a>
                    </div>
                </form>
            </div> <!-- end card box-->
        </div>
    </div>
</div>
@endsection

@section('plugins_js')
<script src="{{ asset('assets/libs/multiselect/jquery.multi-select.js') }}"></script>
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>
@endsection

@section('custom_js')
<script  nonce="{{ csp_nonce() }}">
$( document ).ready(function() {
    $(".select2-multiple").select2()
});
</script>
@endsection