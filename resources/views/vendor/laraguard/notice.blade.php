@extends('layouts.user-login-like-layout')


@php $pageTitle = "Two Factor Authentication"; @endphp

@section('title', $pageTitle)

@section('custom_css')
<style>
    .card-body {
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        padding: 70px;
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
            <h4 class="text-center pb-2">Two factor authentication required</h4 class="text-center">
            <p class="text-center">To proceed, you need to enable Two Factor Authentication.</p>
            <a href="{{ route('setup-mfa') }}">
              <button type="submit" class="btn btn-primary w-100">Enable</button>
            </a>
          </div>
        </div>
    </div>
</div>
@endsection


