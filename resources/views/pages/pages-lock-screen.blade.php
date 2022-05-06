@extends('layouts.user-login-like-layout')

@section('title', 'Session Timeout')

@section('custom_css')
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card bg-pattern">

            <div class="card-body p-4">

                <!-- LOGO DISPLAY NAME -->
                @include('layouts.partials.user-login-layout.company-logo-display-name')

                <div class="text-center w-75 m-auto">
                    <h4 class="text-dark-50 text-center mt-3">Hi! <span id="user-name"> <span></h4>
                    <p class="text-muted mb-4">Your session timed out due to inactivity.
                        @if( !$loggedInWithSSO )
                            Please enter your password to regain access.
                        @else
                            Please click on the button below to regain access.
                        @endif
                    </p>
                </div>

                @if( !$loggedInWithSSO )
                <form action="{{route('login')}}" method="post" id="session-timeout-login">
                    @csrf
                    <input type="hidden" name="email" value="">
                    <input type="hidden" name="session_timeout" value="1">
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input class="form-control" type="password" name="password" required="" id="password" placeholder="Enter your password">
                        <span class="error-msg msg">
                        @if ($error = $errors->first('password'))
                            {{ $error }}
                        @endif
                        </span>
                    </div>

                    <div class="mb-0 text-center">
                        <button class="btn btn-primary w-100" type="submit"> Log In </button>
                    </div>

                </form>
                @else
                    <a href="{{ route('saml2.login') }}" class="btn btn-primary w-100 secondary-bg-color"> SSO  </a>
                @endif

            </div> <!-- end card-body -->
        </div>
        <!-- end card -->

        <div class="row mt-3">
            <div class="col-12 text-center">
                <p class="text-white-50">Not you? Return to<a href="{{ route('login') }}" class="text-white ms-1"><b>Sign In</b></a></p>
            </div> <!-- end col -->
        </div>
        <!-- end row -->

    </div> <!-- end col -->
</div>
<!-- end row -->
@endsection


@section('custom_js')
<script nonce="{{ csp_nonce() }}">

const userEmail = "{{ $email }}";
const userName = "{!! decodeHTMLEntity($full_name) !!}";

// saving data to localstorage
if(userEmail){
    localStorage.setItem("email", userEmail);
}

if(userName){
    localStorage.setItem("username", userName);
}

// getting data from localstorage
var localStorageEmail = localStorage.getItem("email")
var localStorageUsername = localStorage.getItem("username")

if(localStorageEmail){
    $("input[name=email]").val(localStorageEmail );
}

if(localStorageUsername){
    $("#user-name").text(localStorageUsername);
}

    $("#session-timeout-login").validate({
        errorClass: 'invalid-feedback',
        rules: {
            password: {
                required: true
            }
        },
        messages: {
            password: {
                required: 'Please enter your password'
            }
        },
        submitHandler: function (form) {
            form.submit();
        }
    });

</script>
@endsection
