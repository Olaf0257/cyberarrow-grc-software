@extends('layouts.user-login-like-layout')

@section('title', 'Register Domain')

@section('custom_css')
<style>
    #login-form .error-msg{
        position: absolute;
        font-size: 0.75rem;
        font-weight: 600;
        color: #f1556c;
    }
</style>

@endsection

@section('content')
@if(Session::has('domain'))
    <p class="alert alert-success">Successfully registered domain. Please checkout <a href="http://{{ Session::get('domain') }}" target="_blank">{{ Session::get('domain') }}</a>.</p>
@endif
<input id="domain_with" hidden value="{{Session::get('domain')}}" />
<div class="row justify-content-center">
   
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="login__main card bg-pattern mt-5">

            <div class="card-body p-4">
             <!-- LOGO DISPLAY NAME -->
                @include('partials.user-login-layout.company-logo-display-name')


                <form class="absolute-error-form" action="{{ route('register.domain.create') }}" autocomplete="off"  method="post" id="register-form">
                    @csrf
                    <div class="position-relative mb-3">
                        <label for="company">Company <span class="text-danger">*</span></label>
                        <input class="form-control" name="company" type="text" id="company" placeholder="Enter your company name" value="{{old('company')}}">
                    </div>
                    <div class="position-relative mb-3">
                        <label for="name">Full Name <span class="text-danger">*</span></label>
                        <input class="form-control" name="name" type="text" id="name" placeholder="Enter your full name" value="{{old('company')}}">
                    </div>
                    <label>Domain<span class="text-danger">*</span></label>
                    <div class="position-relative mb-3">
                    <input class="form-control" style="width: 60%;display:inline-flex"  name="domain" type="text" id="domain" placeholder="Enter your domain" value="{{ explode('.',old('domain'))[0] }}">
                        <span class="flex text-sm form-control bg-grey" style="width: 39%;display:inline-flex;background: gainsboro;">
                            <span>
                                .{{$url}}
                            </span>
                        </span>
                   
                    </div>
                    <span class="error-msg msg" style="position: relative;">
                            @if ($error = $errors->first('domain'))
                                {{ $error }}
                            @endif
                    </span>
                    <div id="email-group" class="position-relative mb-3">
                        <label for="email">Email address <span class="text-danger">*</span></label>
                        <input class="form-control" name="email" type="text" id="emailaddress" placeholder="Enter your email" value="{{old('email')}}">
                        <span class="error-msg msg">
                            @if ($error = $errors->first('password'))
                                {{ $error }}
                            @endif
                            @if ($error = $errors->first('email'))
                                {{ $error }}
                            @endif
                        </span>
                    </div>
                    @php
                        if($errors->first('password')) {
                            $errorClass = 'msg';
                            $borderClass = 'border-error';
                        } else {
                            $errorClass = '';
                            $borderClass = '';
                        }
                    @endphp

                    <div id="password-group" class="position-relative mb-3">
                        <label for="password">Password <span class="text-danger">*</span></label>
                        <input class="form-control" name="password" type="password" autocomplete="new-password" id="password" placeholder="Enter your password">
                       
                    </div>

                    <div class="position-relative mb-0 text-center">
                        <button id="login-btn" class="btn btn-primary d-grid secondary-bg-color" type="submit">Register</button>
                    </div>
                </form>

            </div> <!-- end card-body -->
        </div>
        <!-- end card -->

       
        <!-- end row -->

    </div> <!-- end col -->
</div>
<!-- end row -->

@endsection

@section('custom_js')
<script nonce="{{ csp_nonce() }}">
    $.validator.addMethod("validate_email", function(value, element) {
        if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Please enter a valid email address");

    $.validator.addMethod("validate_domain", function(value, element) {
        if (/^[a-zA-Z0-9_.-]*$/.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Please enter a valid domain");

    $("#register-form").validate({
        errorClass: 'invalid-feedback',
        highlight: function(element, errorClass, validClass) {
            $(".error-msg").html("");
            $(element).css('border', '1px solid red');
            // $(element).parent().addClass(errorClass);
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).css('border', '');
            // $(element).parent().removeClass(errorClass);
        },
        rules: {
            company: {
                required: true,
            },
            name: {
                required: true,
            },
            domain: {
                required: true,
                validate_domain: true
            },
            email: {
                required: true,
                validate_email: true
            },
            password: {
                required: true,
                // minlength: 6
            }
        },
        messages: {
            email: {
                required: 'The email address is required',
                validate_email: 'Please enter a valid email address'
            },
            password: {
                required: 'The password field is required',
                // minlength: 'The password must be atleast 6 characters.'
            }
        },
        submitHander: function(form) {
            form.submit();
        }
    });

    // open new domain in new tab after successfull register
    if($('#domain_with').val()!==""){
       var new_url= 'http://'+$('#domain_with').val();
       var win= window.open(new_url, 'name');
    }
</script>
@endsection
