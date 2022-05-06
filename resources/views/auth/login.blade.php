@extends('layouts.user-login-like-layout')

@section('title', 'Login')

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
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="login__main card bg-pattern mt-5">

            <div class="card-body p-4">
             <!-- LOGO DISPLAY NAME -->
                @include('layouts.partials.user-login-layout.company-logo-display-name')

                @if(Session::has('saml2_error_detail'))
                    @foreach(Session::get('saml2_error_detail') as $error)
                    <p class="error-msg msg">{{ $error }}</p>
                    @endforeach
                @endif

                <form class="absolute-error-form" action="{{route('login')}}" autocomplete="off"  method="post" id="login-form">
                    @csrf
                    <div id="email-group" class="position-relative mb-3">
                        <label class="form-label" for="email">Email address <span class="text-danger">*</span></label>
                        <input class="form-control" name="email" type="text" id="emailaddress" placeholder="Enter your email" value="{{old('email')}}">
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
                        <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                        <input class="form-control" name="password" type="password" autocomplete="new-password" id="password" placeholder="Enter your password">
                        <span class="error-msg msg">
                            @if ($error = $errors->first('password'))
                                {{ $error }}
                            @endif
                            @if ($error = $errors->first('email'))
                                {{ $error }}
                            @endif
                        </span>
                    </div>

                    <div class="position-relative mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="checkbox-signin">
                            <label class="form-check-label" for="checkbox-signin">Remember me</label>
                        </div>
                    </div>


                    <div class="position-relative mb-0 text-center">
                        <button id="login-btn" class="btn btn-primary w-100 secondary-bg-color" type="submit"> Log In </button>
                        @if($isSsoConfigured)
                        <a href="{{ route('saml2.login') }}" class="btn btn-primary w-100 secondary-bg-color"> SSO  </a>
                        @endif
                    </div>


                </form>

            </div> <!-- end card-body -->
        </div>
        <!-- end card -->

        <div class="row mt-3">
            <div class="col-12 text-center">
                <p> <a href="{{route('forget-password')}}" class="text-white-50 ms-1">Forgot your password?</a></p>
            </div> <!-- end col -->
        </div>
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

    $("#login-form").validate({
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
</script>
@endsection
