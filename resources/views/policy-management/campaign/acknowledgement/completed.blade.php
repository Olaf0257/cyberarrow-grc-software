@extends('layouts.user-login-like-layout')

@section('content')
<div class="card bg-pattern">
     <!-- LOGO DISPLAY NAME -->
     @include('layouts.partials.user-login-layout.company-logo-display-name')

    <div class="card-body">
        <div class="row">
            <div class="col-12 text-center">
                <h5 class="card-title">
                    Hi {{ decodeHTMLEntity($user->first_name). ' ' . decodeHTMLEntity($user->last_name)}},
                </h5>

                <div class="card-text">
                    <p class="text-center h4 mb-3">Thank you for acknowledging the following policy(ies): </p>

                    @foreach($campaignAcknowledgments as $index => $campaignAcknowledgment)
                        <h5 class="text-center">#{{$index+1}} {{ decodeHTMLEntity($campaignAcknowledgment->policy->display_name)}}</h5>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div> <!-- end card -->
@endsection
