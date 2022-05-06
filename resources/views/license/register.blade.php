@extends('layouts.user-login-like-layout')

@section('title', 'License Activation')

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
                @include('partials.user-login-layout.company-logo-display-name')


                <form class="absolute-error-form" action="{{route('license.activate')}}" autocomplete="off"  method="post" id="license-activation-form">
                    @csrf
                    <div id="license-group" class="position-relative mb-3">
                        <label for="password">License Code<span class="text-danger">*</span></label>
                        <input class="form-control" name="license" type="text" autocomplete="license-key" id="license" placeholder="Enter your purchase/license code">

                                <div class="invalid-feedback d-block">
                                @if($errors->has('license'))
                                  {{ $errors->first('license') }}
                                @endif
                                </div>


                    </div>

                    <div id="client-group" class="position-relative mb-3">
                        <label for="client">Enter your client name<span class="text-danger">*</span></label>
                        <input class="form-control" name="client" type="text" id="client" placeholder="Enter your client name" value="{{old('client')}}">

                        <div class="invalid-feedback d-block">
                                @if($errors->has('client'))
                                    {{ $errors->first('client') }}
                                @endif
                            </div>
                    </div>

                    <div class="position-relative mb-0 text-center">
                        <button id="license-activation-btn" class="btn btn-primary d-gird secondary-bg-color" type="submit"> Activate </button>
                    </div>


                    @if($logBtn)
                    <p class="text-center p-2 mb-0"><b>OR</b></p>
                    <div class="position-relative mb-0 text-center">
                        <a href="{{ url('/')}}" class="btn btn-primary d-gird secondary-bg-color"> Already activated ? Log In </a>
                    </div>
                    @endif
                    
                </form>

            </div> <!-- end card-body -->
        </div>
        <!-- end card -->


        <!-- end row -->    `

    </div> <!-- end col -->
</div>
<!-- end row -->

@endsection
@section('custom_js')
<script nonce="{{ csp_nonce() }}">
 $("#license-activation-form").validate({
        errorClass: 'invalid-feedback',
        rules: {
            license: {
                required: true,
                maxlength: 190
            },
            client: {
                required: true,
                maxlength: 190
            },
            
        },
        messages: {

            license: {
                required: 'The license code field is required',

            },
            client: {
                required: 'The client Name field is required',

            },

        },
        submitHandler: function (form) {
            form.submit();
            $(form).find('button[type=submit]').prop('disabled', true)
        }
    });
</script>
@endsection

