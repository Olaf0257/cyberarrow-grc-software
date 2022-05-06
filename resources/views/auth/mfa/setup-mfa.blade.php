@extends('layouts.user-login-like-layout')

@section('custom_css')
    <style>
        .qcode-left svg {
        height: 290px !important;
        width: 290px !important;
        margin-top: -15px;
    }

    .instruction__box {
        background: var(--primary-color);
        padding: 10px 10px;
    }

    .instruction__box p {
        margin-top:0 !important;
        margin-bottom: 0;
    }

    .project-box {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
    }

    .enable__btn {
        min-width: 245px;
    }

    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body project-box ">
                <div class="top-text">
                    <h4 class="">Setup MFA for your Account</h4>
                    <div class="instruction__box">
                                <p class="text-white">In order to use Multi Factor Authentication, you will need to install an authenticator application such as 'Google Authenticator'.</p>
                            </div>
                    <h5 class="text-dark my-3">Secret Token:
                        <span class="text-muted">
                        {{ $as_string }}
                        </span>
                    </h5>
                </div>

                <!-- barcode box -->
                <div class="qrcode-box">
                    <div class="row">
                        <div class="col-xl-5">
                            <div class="qcode-left">
                                {!! $as_qr_code !!}
                            </div>
                        </div>

                        <div class="col-xl-7">
                            <div class="qrcode-right">
                                        <p  class="text-dark qrcode-right-text">Scan the barcode or type out the token to  add the token to the authenticator.</p>
                                    <p class="py-2 text-dark qrcode-right-text">A new token will be generated everytime  you refresh or disable/enable MFA.</p>
                                    <p class="text-dark mb-2 qrcode-right-text">Please enter the first code that shows in  the authenticator.</p>

                                <form action="{{ route('confirm-mfa') }}" method="Post" id="set-up-mfa">
                                    @csrf
                                <div class="row mb-3">
                                    <div class="col-sm-8 col-lg-7">
                                        <input type="text" name="2fa_code" id="2fa_code" class="form-control @if($errors->first('2fa_code')) is-invalid @endif"placeholder="123456">
                                        @if($error = $errors->first('2fa_code'))
                                        <div class="invalid-feedback d-block">
                                            {{ __('The Code is invalid or has expired') }}
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary enable__btn">Enable Secure MFA Login</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                    <!-- barcode box ends-->

    </div>
</div>
@endsection

@section('custom_js')
<script nonce="{{ csp_nonce() }}">
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
                minlength: 'The Code must contain 6 charecter',
                maxlength: 'The Code must contain 6 charecter',
                remote: 'The Code is invalid or expired'
            }
        },
        submitHander: function(form) {
            form.submit();
        }
    });
</script>
@endsection
