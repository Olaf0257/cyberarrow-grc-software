@extends('layouts.user-login-like-layout')


@php $pageTitle = "Two Factor Authentication"; @endphp

@section('title', $pageTitle)

@section('custom_css')

<style>

    .card-body {
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        padding: 70px;
    }
    .two-fa-login label{
        position: absolute;
        bottom: 84px;
        font-size: 0.75rem;
        color: #f1556c;
    }
    .two-fa-login .invalid-feedback{
        position: absolute;
        font-weight: 600;
    }
</style>

@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card bg-pattern">
            <div class="card-body p-4">
                <!-- LOGO DISPLAY NAME -->
                @include('layouts.partials.user-login-layout.company-logo-display-name')
                <h4 class="text-center">Two factor authentication required</h4 class="text-center">
                <hr>
                <p>To log in, open your Authenticator app and fill up the 6-digit code.</p>
                <form action="{{ $action }}" method="post" id="2fa-login" class="two-fa-login">
                    @csrf
                    @foreach($credentials as $name => $value)
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}">

                    @endforeach


                    @if($remember)
                        <input type="hidden" name="remember" value="on">
                    @endif
                    <div class="mb-3">
                        <input name="2fa_code" id="2fa_code"
                          class="@if($error) is-invalid @endif form-control"
                           minlength="6" placeholder="123456" required>
                        @if($error)
                        <div class="invalid-feedback d-block">
                            {{ __('The Code is invalid or expired') }}
                        </div>
                         @endif
                        <button type="submit" class="btn btn-primary mt-3 w-100">Log In</button>
                    </div>
                </form>
            </div>

        </div>

        <!--Form that requestes to reset your MFA   -->
        <form action="{{ route('send-mfa-reset-link')}}" method="post" id="reset-mfa-form">
            {{ csrf_field() }}
            <input type="hidden" name="email" value="{{ $credentials['email'] }}">
        </form>

        <div class="row mt-3">
            <div class="col-12 text-center">
                <p> <a href="#" class="text-white-50 ms-1" id="send-reset-mfa-link">Reset Your MFA</a></p>
            </div> <!-- end col -->
        </div>


    </div>

</div>
@endsection

@section('custom_js')
<script nonce="{{ csp_nonce() }}">
    $("#2fa-login").validate({
        errorClass: 'msg invalid-auth',
        highlight: function(element, errorClass, validClass) {
            $(".error-msg").html("");
            $(element).css('border', '1px solid red');
            $(element).parent().addClass(errorClass);

            // removing server side validation error
            $("div.invalid-feedback").remove()
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).css('border', '');
            $(element).parent().removeClass(errorClass);
        },
        rules: {
            "2fa_code": {
                required: true,
                minlength: 6,
                maxlength: 6
            }
        },
        messages: {
            "2fa_code": {
                required: 'The Code is required',
                minlength: 'The Code must contain 6 characters',
                maxlength: 'The Code must contain 6 characters'
            }
        },
        submitHander: function(form) {
            form.submit();
        }
    });

    /* Handling reset MFA */
    $(document).on('click', '#send-reset-mfa-link', function (event) {
        event.preventDefault()
        $("#reset-mfa-form").submit()
    })
</script>
@endsection
