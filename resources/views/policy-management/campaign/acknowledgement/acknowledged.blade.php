@extends('layouts.user-login-like-layout')

@section('content')
<div class="card bg-pattern">
     <!-- LOGO DISPLAY NAME -->
     @include('layouts.partials.user-login-layout.company-logo-display-name')

    <div class="card-body">
        <div class="row">
            <div class="col-12 text-center">
                <h5 class="card-title">
                    Hi {{ $user->first_name }} {{ $user->last_name }},
                </h5>

                <div class="card-text">
                    <p class="text-center h4 mb-3">This link is not valid anymore, you can safely close the page. </p>
                </div>
            </div>
        </div>
    </div>
</div> <!-- end card -->
@endsection
