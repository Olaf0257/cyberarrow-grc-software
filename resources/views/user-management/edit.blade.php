@extends('layouts.layout')

@section('body-class', 'user-disabling-page')

@section('plugins_css')

<link href="{{ asset('assets/libs/switchery/switchery.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/multiselect/multi-select.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/combo-tree/style.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom.css' )}}" rel="stylesheet" type="text/css" />


<!-- Intl Tel -->
<link href="{{asset('assets/libs/intl-tel/prism.css')}}" rel='stylesheet' />
<link href="{{asset('assets/libs/intl-tel/intlTelInput.css')}}" rel='stylesheet' />
@endsection


@section('custom_css')
<link href="{{asset('assets/css/combotree-plugin-customize.css')}}" rel='stylesheet' />
<style>


    .left-box .status,
    .left-box h3 {
        display: inline-block;
    }

    .bg-success {
        top: -3px !important;
        position: relative;
    }

    .bg-primary {
        background: var(--primary-color);
        padding: 5px 10px;
    }

    table tbody tr td i {
        height: 20px;
        width: 40px;
        color: var(--secondary-color);
    }


    .iti__selected-flag {
        align-items: center !important;

    }


    span.select2-container.select2-container--default.select2-container--open {
        z-index: 1060 !important;
    }

    #mfa-qrcode-wp svg {
            width: 290px !important;
            height: 290px !important;
            margin-top: -15px;
            margin-left: -15px;
        }

    .modal-dialog {
        width: 100%;
        max-width: 800px;
    }

    .modal .instruction__box {
        background: var(--primary-color);
        padding: 10px 10px;
    }

.instruction__box p {
        margin-top:0 !important;
        margin-bottom: 0;
    }

.enable__btn {
        min-width: 245px;
    }

.modal-header {
        padding: 2px 20px;
    }

    /******************
    === RESPONSIVE ===
    ******************/
    @media (max-width: 575px) {
        .modal .instruction__box {
            padding: 8px;
            margin: 0 -3px 0 0;
        }

         .top-text span.token {
            font-weight: lighter;
            font-size: small;
         }

         .qrcode-right input {
            max-width: 243px;
         }

         .qcode-left svg {

         }

        /* .enable__btn {
            min-width: 288px;
        } */
    }



    /***** 320px to 575px */

    @media (min-width: 576px) and (max-width: 767px) {
        .qrcode-right input {
            width: 243px !important;

         }

    }

    @media (max-width: 618.5px) {
        .form-control {
            margin-top: 10px;
        }

    }

</style>

@endsection

@section('content')
@php
    $mfaEnabled = $admin->hasTwoFactorEnabled();
@endphp

<!-- breadcrumbs -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">User management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin-user-management-view') }}">Users</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
            <h4 class="page-title">User Management Page</h4>
        </div>
    </div>
</div>

@include('includes.flash-messages')
<div id="page-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body project-box">
                    <div class="row">
                        <div class="col-3 left-box-col box-col">
                            <div class="left-box">
                                <h3 class="text-capitalize">{{ htmlspecialchars_decode($admin->full_name) }}</h3>
                                <span class="status badge bg-success">{{ Str::ucfirst($admin->status) }}</span>

                                <div class="py-2">
                                    @foreach($admin->roles as $role)
                                    <span class="badge bg-primary">{{ $role }}</span>
                                    @endforeach
                                </div>

                                <div class="pt-2">
                                    @if($admin->status == 'active' && ($loggedInUser->id == $admin->id))
                                    <a href="{{  $mfaEnabled ? '' : route('setup-mfa') }}">
                                        <button class="btn btn-primary {{ $mfaEnabled ? 'reset-mfa' : 'setup-mfa'}}">{{ $mfaEnabled ? 'Reset MFA' : 'Set up MFA' }}</button>
                                    </a>
                                    @endif

                                    @if( $loggedInUser->hasRole('Global Admin') )

                                        @if($admin->status == 'unverified')
                                        <a href="{{ route('users.resend-email-verification-link', $admin->id) }}" class="btn btn-primary resend-activation">Resend Activation</a>
                                        @endif

                                        @if($admin->status == 'active' && $admin->id != $loggedInUser->id)
                                        <a class='btn btn-secondary  disable-user'
                                            data-user-id="{{ $admin->id }}"
                                            data-assignment-transferable-user-url="{{ route('user.assignments-transferable-users', [$admin->id]) }}"
                                            data-user-project-assignments-url="{{ route('user.project-assignments', [$admin->id]) }}"
                                            href="{{ route('admin-user-management-make-disable', [$admin->id]) }}"
                                            data-transfer-assignments-url="{{ route('user.transfer-assignments', [$admin->id]) }}">
                                            Disable
                                        </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-9 right-box-col box-col">
                            <div class="right-box">
                                <div class="table-responsive">
                                        <table class="table mb-0">

                                            <tbody class="tbody-light">
                                                <tr>
                                                    <td><i class="fas fa-envelope-open"></i>Email</td>
                                                    <td>{{ decodeHTMLEntity($admin->email) }}</td>
                                                </tr>

                                                <tr>
                                                    <td><i class="fas fa-phone"></i>Phone</td>
                                                    <td>{{ decodeHTMLEntity($admin->contact_number) }}</td>
                                                </tr>

                                                <tr>
                                                    <td><i class="far fa-calendar-alt"></i>Created on</td>
                                                    <td>{{ $admin->created_at }}</td>
                                                </tr>

                                                <tr>
                                                    <td><i class="far fa-calendar-alt"></i>Last Modified</td>
                                                    <td>{{ $admin->updated_at }}</td>
                                                </tr>

                                                <tr>
                                                    <td><i class="fas fa-sign-in-alt"></i>Last Login</td>
                                                    <td>{{ $admin->last_login }}</td>
                                                </tr>

                                                <tr>
                                                    <td><i class="fas fa-lock"></i>MFA Secure Login</td>
                                                    <td>{{ $mfaEnabled ? 'Enabled' : 'Disabled' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- last row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-bordered">
                        <li class="nav-item">
                            <a href="#home-b1" data-toggle="tab" aria-expanded="false" class="nav-link {{ !old('update-password-form') ? 'active' : '' }}">
                                Edit
                            </a>
                        </li>
                        @if($admin->auth_method == 'Manual')
                        @if($admin->status == 'active')
                        <li class="nav-item">
                            <a href="#profile-b1" data-toggle="tab" aria-expanded="true" class="nav-link {{ old('update-password-form') ? 'active' : '' }}">
                                Change Password
                            </a>
                        </li>
                        @endif
                        @endif
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane  {{ !old('update-password-form') ? 'show active' : '' }}" id="home-b1">
                            <form class="form-horizontal absolute-error-form" action="{{ route('admin-user-management-update', $admin->id) }}" method="post" id="user-info-update-form">
                                @csrf
                                <div class="row mb-3">
                                    <label for="firstname" class="col-3 form-label form-label">Auth Method <span class="required text-danger">*</span></label>
                                    <div class="col-9">
                                        <select name="" class="form-control disabled" disabled>
                                            <option value="">Select</option>
                                            <option value="Manual" {{ $admin->auth_method == 'Manual' ? 'selected' : '' }}>Manual</option>
                                            <option value="LDAP" {{ $admin->auth_method == 'LDAP' ? 'selected' : '' }}>LDAP</option>
                                        </select>
                                        <div class="invalid-feedback d-block">
                                            @if ($errors->has('auth_method'))
                                            {{ $errors->first('auth_method') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="firstname" class="col-3 form-label col-form-label">First Name <span class="required text-danger">*</span> </label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" name="first_name" id="firstname" value="{{ old('first_name', htmlspecialchars_decode($admin->first_name)) }}" placeholder="First Name">
                                        <div class="invalid-feedback d-block">
                                            @if ($errors->has('first_name'))
                                            {{ $errors->first('first_name') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="lastname" class="col-3 form-label col-form-label">Last Name <span class="required text-danger">*</span></label>
                                    <div class="col-9">
                                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', htmlspecialchars_decode($admin->last_name)) }}" id="lastname" placeholder="Last Name">
                                        <div class="invalid-feedback d-block">
                                            @if ($errors->has('last_name'))
                                            {{ $errors->first('last_name') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="email" class="col-3 form-label col-form-label">Email <span class="required text-danger">*</span></label>
                                    <div class="col-9">
                                        <input type="email" name="email" class="form-control" value="{{ old('email', htmlspecialchars_decode($admin->email)) }}" id="email" placeholder="E-mail">
                                        <div class="invalid-feedback d-block">
                                            @if ($errors->has('email'))
                                            {{ $errors->first('email') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="contact_number" class="col-3 form-label col-form-label">Contact Number</label>
                                    <div class="col-9 contact-number--iti">
                                        <input type="hidden" name="contact_number_country_code" value="{{ old('contact_number_country_code', $admin->contact_number_country_code) }}">
                                        <input id="phone" min="9" type="number" name="contact_number" class="form-control"  value="{{ old('contact_number', decodeHTMLEntity($admin->contact_number)) }}" id="contact_number" placeholder="Contact">
                                        <div class="invalid-feedback d-block">
                                            @if ($errors->has('contact_number'))
                                            {{ $errors->first('contact_number') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputPassword5" class="col-3 form-label col-form-label">User Department <span class="required text-danger">*</span></label>
                                        <div class="col-9">
                                        {{-- <select name="department_id" class="form-control" id="user-department-select2"  tabindex="5" >
                                            <option value="" selected disabled>Choose Department</option>
                                                @foreach($departments as $department)
                                                <option value="{{$department->id}}" @if($department->id == $admin->department_id) selected @endif >{{$department->name}}</option>
                                                @endforeach
                                        </select> --}}
                                        <input type="text" name="department" id="departments-tree-select" {{ !$loggedInUser->hasRole('Global Admin') ? 'disabled' : '' }} autocomplete="off" >
                                        @php
                                            $departmentId = $admin->department ?  (is_null($admin->department->department_id)  ? 0 : $admin->department->department_id) : 0
                                        @endphp
                                        <input type="hidden" name="department_id" value="{{$departmentId}}">

                                        <div class="invalid-feedback d-block">
                                            @if ($errors->has('department_id'))
                                            {{ $errors->first('department_id') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputPassword5" class="col-3 form-label col-form-label">User Roles <span class="required text-danger">*</span></label>
                                        <div class="col-9">
                                            <select  name="roles[]" class="form-control select2-multiple" id="user-roles-select2" multiple="multiple" data-placeholder="Search Roles..." tabindex="5" {{ !$loggedInUser->hasRole('Global Admin') ? 'disabled' : '' }}>
                                                @foreach($roles as $role)
                                                <option value="{{$role}}" @if(in_array($role, old('roles')?old('roles'):$admin->roles)) selected @endif >{{$role}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback d-block">
                                                @if ($errors->has('roles'))
                                                {{ $errors->first('roles') }}
                                                @endif
                                            </div>
                                        </div>
                                </div>
                                @if( $loggedInUser->hasRole('Global Admin') )
                                @if($admin->status == 'active' && $admin->id != $loggedInUser->id)
                                <div class="row mb-3">
                                    <label for="inputPassword5" class="col-3 col-form-label"> Require MFA</label>
                                    <div class="col-9">
                                        <input type="hidden" name="require_mfa" value="{{ $admin->require_mfa }}">
                                        <input type="checkbox" class="js-switch" id="require_mfa" data-plugin="switchery" data-color="#64b0f2" data-size="small" {{ $admin->require_mfa ? 'checked' : '' }} style="display: none;">
                                    </div>
                                </div>
                                @endif
                                @endif

                                <div class="row mt-3 mb-3">
                                    <div class="d-flex ms-auto">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Update User</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if($admin->auth_method == 'Manual')
                        @if($admin->status == 'active')
                        <div class="tab-pane {{ old('update-password-form') ? 'show active' : '' }}" id="profile-b1">
                            <div class="password">
                                <form class="form-horizontal" name="update-password-form" action="{{ route('admins.update-password', $admin->id) }}" method="POST">
                                @csrf
                                    <input type="hidden" name="update-password-form" value="1">
                                    @if($admin->id === Auth::guard('admin')->user()->id)
                                    <div class="row mb-3">
                                        <label for="current_password" class="col-3 form-label col-form-label">Current Password <span class="required text-danger">*</span></label>
                                        <div class="col-9">
                                            <input type="password" class="form-control" name="current_password" id="current_password" placeholder="Current Password">
                                            <div class="invalid-feedback d-block">
                                                @if ($errors->has('current_password'))
                                                {{ $errors->first('current_password') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="row mb-3">
                                        <label for="new_password" class="col-3 form-label col-form-label">New Password <span class="required text-danger">*</span> </label>
                                        <div class="col-9">
                                            <input type="password" class="form-control" name="new_password" id="new_password" placeholder="New Password">
                                            <div class="invalid-feedback d-block">
                                                @if ($errors->has('new_password'))
                                                {!! $errors->first('new_password') !!}
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="password" class="col-3 form-label col-form-label">Confirm New Password <span class="required text-danger">*</span> </label>
                                        <div class="col-9">
                                            <input type="password" name="new_password_confirmation" class="form-control" id="inputEmail3" placeholder="Confirm New Password">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary d-flex ms-auto">Confirm</button>
                                </form>
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>
                </div> <!-- end card--> 
            </div>
        </div> <!-- end col -->
    </div>
<!-- last row ends -->
<!-- end of breadcrumbs -->

 <!-- Modal -->
<div class="modal fade" id="mfaSetupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <div class="top-text">
                <h4>Setup MFA for your Account</h4>
                    <div class="instruction__box my-2">
                        <p class="text-white">In order to use Multi Factor Authentication, you will need to install an authenticator application such as 'Google Authenticator'.</p>
                    </div>
                <h5 class="text-dark">Secret Token:
                    <span class="text-muted token" id="secret-token-wp">
                    </span>
                </h5>
            </div>

        </div>
        <div class="modal-body">
         <!-- barcode box -->
          <div class="qrcode-box">
              <div class="row">
                  <div class="col-xl-5 col-lg-5 col-md-5 col-sm-6 col-12">
                      <div class="qcode-left" id="mfa-qrcode-wp">
                      </div>
                  </div>

                  <div class="col-xl-7 col-lg-7 col-md-7 col-sm-6 col-12">
                      <div class="qrcode-right">
                            <p  class="text-dark qrcode-right-text pt-1">Scan the barcode or type out the token to  add the token to the authenticator.</p>
                            <p class="text-dark qrcode-right-text">A new token will be generated everytime  you refresh or disable/enable MFA.</p>
                            <p class="text-dark mb-2 qrcode-right-text">Please enter the first code that shows in  the authenticator.</p>

                          <form action="{{ route('confirm-mfa') }}" method="Post" id="set-up-mfa">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-lg-7 col-md-7 col-sm-9">
                                    <input type="text" name="2fa_code" id="2fa_code" class="2fa__code form-control @if($errors->first('2fa_code')) is-invalid @endif"placeholder="123456">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary enable__btn">Enable Secure MFA Login</button>
                        </form>
                  </div>
              </div>


          </div>
      </div>
            <!-- barcode box ends-->
        </div>
      </div>
    </div>
</div>
<!-- ends model -->
</div>
@endsection

@section('plugins_js')
<script src="{{ asset('assets/libs/switchery/switchery.min.js') }}"></script>
<script src="{{ asset('assets/libs/multiselect/jquery.multi-select.js') }}"></script>
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('assets/libs/combo-tree/comboTreePlugin.js') }}"></script>


<!-- Intl Tel -->
<script src="{{asset('assets/libs/intl-tel/prism.js')}}"></script>
<script src="{{asset('assets/libs/intl-tel/intlTelInput.js')}}"></script>
@endsection

@section('custom_js')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{ asset('assets/js/users-management/index.js') }}"></script>
<script nonce="{{ csp_nonce() }}">
$(document).ready(function () {

    /* Department tree select */
    departmentTreeSelect = $('#departments-tree-select').comboTree(
        {
            source : @json($departmentTreeData),
            cascadeSelect: true,
            selected: ['{{ old("department_id", $departmentId) }}'],

        }
    );


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
    //initialize select2 for department
    $("#user-department-select2").select2();



    const checkTopDep = (event) => {

        var departmentIds = departmentTreeSelect.getSelectedIds();

        $("#user-roles-select2 option[value='Global Admin']")
            .attr("disabled", "disabled")
            .siblings().removeAttr("disabled");

         if(departmentIds == 0)
         {
            $("#user-roles-select2 option[value='Global Admin']")
            .removeAttr("disabled")
            .siblings().removeAttr("disabled");

         }
    };


    window.onload = checkTopDep;
    window.onreload = checkTopDep;
    // resetting MFA setting

    $(".reset-mfa").on('click', function(event){
        event.preventDefault();

        swal({
            title: "Are you sure?",
            text: "You want to reset MFA?",
            showCancelButton: true,
            confirmButtonColor: '#ff0000',
            confirmButtonText: 'Yes',
            closeOnConfirm: false,
            imageUrl: '{{ asset('assets/images/warning.png') }}',
            imageWidth: 120
        }).then((result) => {
            if(result.value){
                $.post( "{{ route('reset-mfa') }}", { _token: "{{ csrf_token() }}" })
                  .done(function( res ) {
                    if(res.success){
                        Swal.fire({
                            text: res.message,
                            confirmButtonColor: '#b2dd4c',
                            imageUrl: '{{ asset('assets/images/success.png') }}',
                            imageWidth: 120
                        }).then((result) => {
                            location.reload();
                        })
                    }
                  });
            }
        })
    });

    // handling setup mfa model

    // preparing MFA
    $(".setup-mfa").on('click', function(event){
        event.preventDefault();

        $.get( "{{ route('setup-mfa') }}", function( res ) {
            if (res.success) {
                let data = res.data

                $("#mfa-qrcode-wp").html(data.as_qr_code);
                $("#secret-token-wp").html(data.as_string);

                // firing the model
                $("#mfaSetupModal").modal('toggle')
            }
        });
    });



     $("#set-up-mfa").validate({
            errorClass: 'msg',
            highlight: function(element, errorClass, validClass) {
                $(".error-msg").html("");
                $(element).css('border', '1px solid red');
                $(element).parent().addClass(errorClass);
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).css('border', '');
                $(element).parent().removeClass(errorClass);
            },
            rules: {
                "2fa_code": {
                    required: true,
                    minlength: 6,
                    maxlength: 6,
                    remote: {
                        url: "{{ route('validate-mfa-code') }}",
                        type: "post",
                        data: {
                          "_token": "{{ csrf_token() }}",
                          "2fa_code": function() {
                            return $( "#2fa_code" ).val();
                          }
                        }
                    }
                }
            },
            messages: {
                "2fa_code": {
                    required: 'The Code is required',
                    minlength: 'The Code must contain 6 characters',
                    maxlength: 'The Code must contain 6 characters',
                    remote: 'The Code is invalid or expired'
                }
            },
            submitHandler: function(form) {
                let actionURL = form.action

                $.ajax({
                    url: actionURL,
                    type: "POST",
                    data: $(form).serialize(),
                    cache: false,
                    processData: false,
                    success: function(res) {
                        if(res.success){
                            Swal.fire({
                                text: res.data.message,
                                confirmButtonColor: '#b2dd4c',
                                imageUrl: '{{ asset('assets/images/success.png') }}',
                                imageWidth: 120
                            }).then((result) => {
                                location.reload();
                            })
                        }
                    }
                });

                return false;
            }
        });

    // Handling setup MFA ends here



    // resend activation confirm
    $(".resend-activation").on('click', function(event){
        event.preventDefault()

        swal({
                title: "Are you sure?",
                text: "Do you want to resend the activation email?",
                showCancelButton: true,
                confirmButtonColor: '#ff0000',
                confirmButtonText: 'Yes',
                closeOnConfirm: false,
                imageUrl: '{{ asset('assets/images/warning.png') }}',
                imageWidth: 120
            }).then((result) => {
                if(result.value){
                    window.open(this.href, "_top");
                }
            })
    });

    $.validator.addMethod("validate_email", function (value, element) {
        if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Please enter a valid email address");

    //switchery
    let secondaryColor = '{{ $globalSetting->secondary_color }}'

    var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));


        elems.forEach(function(html) {
            var switchery = new Switchery(html, {color: secondaryColor });
        });

    function validateUserInfoUpdateForm() {
        $("#user-info-update-form").validate({
            errorClass: 'invalid-feedback',
            rules: {
                auth_method: {
                    required: true,
                },
                firstname: {
                    required: true,
                    maxlength: 190
                },
                lastname: {
                    required: true,
                    maxlength: 190
                },
                email: {
                    required: true,
                    validate_email: true
                },
                department_id: {
                    required: true,
                },
                department: {
                    required: true,
                },
                contact_number: {
                    digits: true,
                    minlength: 9,
                    maxlength: 11
                },
                'roles[]': {
                    required: true
                }
            },
            messages: {
                auth_method: {
                    required: 'The Auth Method field is required',
                },
                firstname: {
                    required: 'The First Name field is required',
                    maxlength: 'The First Name field may not be greater than 190 characters.'
                },
                lastname: {
                    required: 'The Last Name field is required',
                    maxlength: 'The Last Name field may not be greater than 190 characters.'
                },
                email: {
                    required: 'The Email field is required',
                    validate_email: 'Please enter a valid email address.'
                },
                contact_number: {
                    digits: 'Enter a valid contact number',
                    minlength: 'The contact number must be between 9 and 11 digits',
                    maxlength: 'The contact number must be between 9 and 11 digits'
                },
                department_id: {
                    required: 'The Department field is required',
                },
                department: {
                    required: 'The Department field is required',
                },
                'roles[]': {
                    required: 'The Roles field is required'
                }
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
    }

    validateUserInfoUpdateForm()

    // contact country code
    var input = document.querySelector("#phone");
    // init plugin
    var iti = window.intlTelInput(input);
    iti.setCountry( $("input[name=contact_number_country_code]").val() );

    input.addEventListener("countrychange", function() {
    // do something with iti.getSelectedCountryData()
        var countryData = iti.getSelectedCountryData();

        $("input[name=contact_number_country_code]").val(countryData.iso2)
    });


    // Switchery

    var switcheryElems = document.querySelectorAll('.js-switch');

    for (var i = 0; i < switcheryElems.length; i++) {
        var switchery = new Switchery(switcheryElems[i], {color: secondaryColor });
    }

    $(switcheryElems).on("change" , function() {

        let elId = this.id
        let checked = this.checked

        $(`input[name=${elId}]`).val(checked ? 1 : 0)

    //code stuff
    });

    //disabling user
    @include('user-management.disable-user-script')
}); // End of document ready
</script>
@endsection
