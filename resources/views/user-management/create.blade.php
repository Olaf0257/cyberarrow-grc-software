@extends('layouts.layout')

@php
if($admin->id) {
$pageTitle = "Edit User";
} else {
$pageTitle = "Create User";
}
@endphp

@section('title', $pageTitle)

@section('plugins_css')
<link href="{{asset('assets/libs/multiselect/multi-select.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/combo-tree/style.css')}}" rel="stylesheet" type="text/css" />

<!-- Intl Tel -->
<link href="{{asset('assets/libs/intl-tel/prism.css')}}" rel='stylesheet' />
<link href="{{asset('assets/libs/intl-tel/intlTelInput.css')}}" rel='stylesheet' />
@endsection


@section('custom_css')
<link href="{{asset('assets/css/combotree-plugin-customize.css')}}" rel='stylesheet' />

    <style>

        .iti__selected-flag {
            align-items: center !important;
        }

    </style>
@endsection


@section('content')
<!-- breadcrumb -->
<div class="row">
    <div class="col-xl-12">
        <!-- top info -->
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">User management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin-user-management-view') }}">Users</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
            <h4 class="page-title">{{ $pageTitle }}</h4>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@include('includes.flash-messages')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin-user-management-store'.$admin->id) }}" method="post" id="validate-form" class="form-horizontal absolute-error-form">
                    @csrf
                    <div class="row mb-3">
                        <label for="firstname" class="col-3 form-label">Auth Method <span class="required text-danger">*</span></label>
                        <div class="col-9">
                            <select name="auth_method" class="form-control">
                                <!-- <option value="">Select</option> -->
                                <option value="Manual" {{ old('auth_method') == 'Manual' ? 'selected' : '' }}>Manual</option>
                                <option value="SSO" {{ old('auth_method') == 'SSO' ? 'selected' : '' }}>SSO</option>
                                <!-- <option value="LDAP" {{ old('auth_method') == 'LDAP' ? 'selected' : '' }}>LDAP</option> -->
                            </select>
                            <div class="invalid-feedback d-block">
                                @if ($errors->has('auth_method'))
                                {{ $errors->first('auth_method') }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="firstname" class="col-3 form-label">First Name <span class="required text-danger">*</span></label>
                        <div class="col-9">
                            <input type="text" name="first_name" class="form-control" placeholder="First Name" tabindex="1" value="{{old('first_name', decodeHTMLEntity($admin->first_name))}}">
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
                            <input type="text" name="last_name" class="form-control" placeholder="Last Name" tabindex="2" value="{{old('last_name', decodeHTMLEntity($admin->last_name))}}">
                            <div class="invalid-feedback d-block">
                                @if ($errors->has('last_name'))
                                {{ $errors->first('last_name') }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="email" class="col-3 form-label">Email <span class="required text-danger">*</span></label>
                        <div class="col-9">
                            <input type="text" name="email" class="form-control" placeholder="Email Address" tabindex="3" value="{{old('email', decodeHTMLEntity($admin->email))}}">
                            <div class="invalid-feedback d-block">
                                @if ($errors->has('email'))
                                {{ $errors->first('email') }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="contact_number" class="col-3 form-label">Contact Number </label>
                        <div class="col-9">
                        <input type="hidden" name="contact_number_country_code" value="ae">
                        <input id="phone" type="tel" name="contact_number" class="form-control" placeholder="Contact Number" tabindex="4"
                                value="{{ old('contact_number', decodeHTMLEntity($admin->contact_number)) }}">
                        <div class="invalid-feedback d-block">
                            @if ($errors->has('contact_number'))
                            {{ $errors->first('contact_number') }}
                            @endif
                        </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="department" class="col-3 form-label">User Department
                            <span class="required text-danger">*</span>
                        </label>
                        <div class="col-9">
                            {{-- <select name="department_id" class="form-control"  id="user-department-select2" data-placeholder="User Department"   tabindex="5">
                            <option value="" selected disabled>Choose Department</option>
                                @foreach($departments as $department)
                                <option value="{{$department->id}}" @if($department->id == $admin->department_id) selected @endif >{{$department->name}}</option>
                                @endforeach
                            </select> --}}
                            <input name="department" style=" " class="easyui-combotree" autocomplete="off" id="department-tree-select" />
                            <input type="hidden" name="department_id" id="department-input" value="0">

                            <div class="invalid-feedback d-block">
                                @if ($errors->has('department_id'))
                                {{ $errors->first('department_id') }}
                                @endif
                            </div>

                            @if($errors->has('department_id.*'))
                                @foreach($errors->get('department_id.*') as $errors)
                                    @foreach($errors as $error)
                                    <div class="invalid-feedback d-block">{{ $error }}</div>
                                    @endforeach
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="roles" class="col-3 form-label">User Roles
                            <span class="required text-danger">*</span>
                        </label>
                        <div class="col-9">
                            <select name="roles[]" class="form-control select2-multiple" id="user-roles-select2" multiple="multiple" data-placeholder="Search Roles..." tabindex="5">
                                @foreach($roles as $role)
                                <option value="{{$role}}" @if(in_array($role, old('roles')?old('roles'):$admin->roles)) selected @endif >{{$role}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback d-block">
                                @if ($errors->has('roles'))
                                {{ $errors->first('roles') }}
                                @endif
                            </div>

                            @if($errors->has('roles.*'))
                                @foreach($errors->get('roles.*') as $errors)
                                    @foreach($errors as $error)
                                    <div class="invalid-feedback d-block">{{ $error }}</div>
                                    @endforeach
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mb-3">
                        <button type="submit" class="btn btn-primary waves-effect waves-light" tabindex="6">@if($admin->id) Update @else Create @endif User</button>
                        <a href="{{route('admin-user-management-view')}}">
                        <button type="button" class="ms-2 btn btn-danger waves-effect waves-light" tabindex="7">Back To List</button></a>
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
<script src="{{ asset('assets/libs/combo-tree/comboTreePlugin.js') }}"></script>

<!-- Intl Tel -->
<script src="{{asset('assets/libs/intl-tel/prism.js')}}"></script>
<script src="{{asset('assets/libs/intl-tel/intlTelInput.js')}}"></script>
@endsection

@section('custom_js')
<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/js/users-management/index.js') }}"></script>

<script nonce="{{ csp_nonce() }}">
$( document ).ready(function() {
    //initialize select2 for department
    $("#user-department-select2").select2();

    // getting ldap user info
    $(document).on('change', 'select[name=auth_method]', function(){
        let authMethod = $('select[name=auth_method]').val()
        let email = $('input[name=email]').val()

        loadLdapUserInfo(authMethod, email)
    })

    $(document).on('keyup', 'input[name=email]', function(){
        let authMethod = $('select[name=auth_method]').val()
        let email = $('input[name=email]').val()

        loadLdapUserInfo(authMethod, email)
    })

    function loadLdapUserInfo(authMethod, email){
        if(authMethod == 'LDAP'){
            $.get( "{{ route('get-ldap-user-info') }}", { email:  email} )
            .done(function( res ) {
                if(res.success){
                    let data = res.data

                    $('input[name=first_name]').val(_.unescape(data.firstName))
                    $('input[name=last_name]').val(_.unescape(data.lastName))
                    $('input[name=contact_number]').val(_.unescape(data.contactNumber))
                } else {
                    $('input[name=first_name]').val("")
                    $('input[name=last_name]').val("")
                    $('input[name=contact_number]').val("")
                }
            });
        }
    }

    $.validator.addMethod("validate_email", function (value, element) {
        if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Please enter a valid email address.");

    $("#validate-form").validate({
        errorClass: 'invalid-feedback',
        rules: {
            auth_method: {
                required: true,
            },
            first_name: {
                required: true,
                maxlength: 190
            },
            last_name: {
                required: true,
                maxlength: 190
            },
            email: {
                required: true,
                validate_email: true
            },
            contact_number: {

                digits: true,
                minlength: 9,
                maxlength: 11
            },
           department_id: {
                required: true,
                maxlength: 190
            },
            department: {
                required: true,
                maxlength: 190
            },

            'roles[]': {
                required: true
            }
        },
        messages: {
            auth_method: {
                required: 'The Auth Method field is required',
            },
            first_name: {
                required: 'The First Name field is required',
                maxlength: 'The First Name may not be greater than 190 characters'
            },
            last_name: {
                required: 'The Last Name field is required',
                maxlength: 'The Last Name may not be greater than 190 characters'
            },
            email: {
                required: 'The Email field is required',
                validate_email: 'Please enter a valid email address'
            },
            department_id: {
                required: 'The Department field is required',

            },
            department: {
                required: 'The Department field is required',

            },
            contact_number: {

                digits: 'Enter a valid contact number',
                minlength: 'The contact number must be between 9 and 11 digits',
                maxlength: 'The contact number must be between 9 and 11 digits'
            },
            'roles[]': {
                required: 'The Roles field is required'
            }
        },
        submitHandler: function (form) {
            form.submit();
            $(form).find('button[type=submit]').prop('disabled', true)
        }
    });

// tel country code
    var input = document.querySelector("#phone");
    // init plugin
    var iti = window.intlTelInput(input);
    iti.setCountry( $("input[name=contact_number_country_code]").val() );

    input.addEventListener("countrychange", function() {
    // do something with iti.getSelectedCountryData()
        var countryData = iti.getSelectedCountryData();

        $("input[name=contact_number_country_code]").val(countryData.iso2)
    });


    /* Department select */
    departmentTreeSelect = $('#department-tree-select').comboTree({
        source : @json($departmentTreeData),
        isMultiple: false,
        cascadeSelect: true,
        selected: ['0'],
    });

    departmentTreeSelect.onChange(function () {

        $('#department-input').val(departmentTreeSelect.getSelectedIds());
        var departmentIds = departmentTreeSelect.getSelectedIds();

        if(departmentIds == 0)
        {
            $("#user-roles-select2 option[value='Global Admin']")
            .removeAttr("disabled")
            .siblings().removeAttr("disabled");

        }else{
            $("#user-roles-select2 option[value='Global Admin']")
            .attr("disabled", "disabled")
            .siblings().removeAttr("disabled");
             // users role select2-multiple init
              // users role select2-multiple init
              $("#user-roles-select2").find(`option[value='Global Admin']`).prop("selected",false);
              $("#user-roles-select2").trigger("change");
        }

    })

});
</script>
@endsection
