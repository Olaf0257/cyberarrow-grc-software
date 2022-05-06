@extends('layouts.user-login-like-layout')

@section('title', 'Set Password')

@section('custom_css')

@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="verfiy__main card bg-pattern mt-5">

                <div class="card-body p-4">

                     <!-- LOGO DISPLAY NAME -->
                    @include('layouts.partials.user-login-layout.company-logo-display-name')

                    <p class="text-center mb-4 mt-3">Please set your password </p>


                    <form action="{{ route('admin-verity-email-set-password', $token) }}" method="post" id="login-form">
                        @csrf
                        <div id="password-group" class="mb-3">
                            <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                            <input class="form-control" name="password" type="password" id="password" placeholder="Enter your password">
                            <span class="error-msg msg">
                            @if ($errors->has('password'))
                                {!! $errors->first('password') !!}
                            @endif
                            </span>
                        </div>
                        <div id="password-group" class="mb-3">
                            <label class="form-label" for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                            <input class="form-control" name="password_confirmation" type="password" id="password_confirmation" placeholder="Re-enter your password">
                            <span class="error-msg msg">
                            @if ($errors->has('password_confirmation'))
                                    {{ $errors->first('password_confirmation') }}
                            @endif
                            </span>
                        </div>

                        <div class="mb-0 text-center">
                            <button id="login-btn" class="btn btn-primary w-100" type="submit"> Set Password </button>
                        </div>

                    </form>

                </div> <!-- end card-body -->
            </div>
            <!-- end card -->

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
        }, "Please enter a valid email address.");

        jQuery.validator.addMethod("strong_password", function(value, element) {
            return this.optional( element ) || /^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/.test( value );
        }, `Password must contain:
                <ul style="padding-left: 1.5rem;">
                    <li> a minimum of 8 characters and </li>
                    <li> a minimum of 1 lower case letter and </li>
                    <li> a minimum of 1 upper case letter and </li>
                    <li> a minimum of 1 special character and </li>
                    <li> a minimum of 1 numeric character </li>
                </ul>`);

        $("#login-form").validate({
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
                email: {
                    required: true,
                    validate_email: true
                },
                password: {
                    required: true,
                    strong_password: true
                },
                password_confirmation: {
                    required: true,
                    equalTo: '#password',
                    strong_password: true
                }
            },
            messages: {
                email: {
                    required: 'The email address is required.',
                    validate_email: 'Please enter a valid email address.'
                },
                password: {
                    required: 'The password field is required.',
                    minlength: 'The password must be atleast 8 characters.'
                },
                password_confirmation: {
                    required: 'The confirm password field is required.',
                    minlength: 'The confirm password must be atleast 8 characters.',
                    equalTo: 'The password and confirm password must be equal.'
                }
            },
            submitHander: function(form) {
                form.submit();
            }
        });
    </script>
@endsection
