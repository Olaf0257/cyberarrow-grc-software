@extends('layouts.user-login-like-layout')

@section('title', 'EBDAA GRC')

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
                    <div class="row align-items-center position-relative mb-0 text-center">
                        <button id="login-btn" class="btn btn-primary d-grid secondary-bg-color" type="submit">Register Domain</button>
                    </div>
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
    $('#login-btn').click(function(){
        window.location='/register_domain';
    });
</script>
@endsection
