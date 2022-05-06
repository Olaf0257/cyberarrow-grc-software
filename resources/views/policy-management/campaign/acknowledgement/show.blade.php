@extends('layouts.user-login-like-layout')

@section('custom_css')
<style nonce="{{ csp_nonce() }}">
.title-heading p{
    margin-bottom: 5px;
}

.m-30{
    margin: 30px 0;
}
.policy-list.active{
    background-color :var(--secondary-color);

}
#policies-list-tab{
    width: 100%;
    display: inline-block;
}
.list-group-item{
    border: 0;
}
</style>
@endsection

@section('content')
<div class="card bg-pattern">
     <!-- LOGO DISPLAY NAME -->
     @include('layouts.partials.user-login-layout.company-logo-display-name')


    <div class="card-body">
        <div class="row">
            <div class="col-12">
            </div>
            <div class="col-12 m-30 title-heading text-center">
                <h5 class="card-title">Hi {{decodeHTMLEntity($campaignAcknowledgmentUserToken->user->first_name)}} {{decodeHTMLEntity($campaignAcknowledgmentUserToken->user->last_name)}},</h5>
                <p>
                    You have been enrolled in the <strong>{{decodeHTMLEntity($campaignAcknowledgmentUserToken->campaign->name)}}</strong> policy management campaign.

                    Please read the policy(ies) below and acknowledge the following policy(ies).
                </p>
            </div>
            <!-- aside links -->
            <div class="col-12 col-sm-4 text-center">
                <div class="list-group mb-3" id="policies-list-tab" role="tablist">
                    @foreach($campaignAcknowledgments as $index => $campaignAcknowledgment)
                    <a class="policy-list list-group-item list-group-item-action {{ $loop->first ? 'active' : ''}}" data-toggle="list" href="#list-{{ $index }}" role="tab" aria-controls="home">
                        {{ decodeHTMLEntity($campaignAcknowledgment->policy->display_name)}}
                    </a>
                    @endforeach
                </div>
            </div>
            <div class="col-12 col-sm-8">
                <form action="{{ route('policy-management.campaigns.acknowledgement.confirm') }}" method="post" >
                    @csrf
                    <input type="hidden" name="campaign_acknowledgment_user_token" value="{{ $campaignAcknowledgmentUserToken->token }}">
                    <div class="tab-content p-0" id="nav-tabContent">
                    @foreach($campaignAcknowledgments as $index => $campaignAcknowledgment)
                        <div class="tab-pane fade {{ $loop->first ? 'active show' : ''}}" id="list-{{$index}}" role="tabpanel" aria-labelledby="list-home-list">

                            <div class="card-text">
                                @if($campaignAcknowledgment->policy->type == 'doculink')
                                <p>This Policy is a doculink. Please follow the url below to see the policy, and confirm that you acknowledge the policy after viewing</p>
                                <a href="{{ $campaignAcknowledgment->policy->path }}" target="_blank">{{ $campaignAcknowledgment->policy->path }}</a>
                                @else
                                    <!--file preview section -->
                                    <div>
                                       <!-- fetching extension of policy's file type  -->
                                       @php
                                          @$ext = pathinfo(storage_path($campaignAcknowledgment->policy->path), PATHINFO_EXTENSION);
                                       @endphp
                                       <!-- if the  file type cant be displayed-->
                                      @if($ext == 'csv'|| $ext == 'pptx'|| $ext == 'ppt'|| $ext == 'xls'|| $ext == 'xlsx' || $ext == 'rtf' || $ext == 'docx')
                                         <embed src="{{ tenant_asset($campaignAcknowledgment->policy->path) }}" height="0">
                                      @else
                                       <!-- if the  file can be displayed-->
                                        <embed src="{{ tenant_asset($campaignAcknowledgment->policy->path) }}" width="100%" height="500">
                                     @endif
                                    </div>
                                @endif
                                <div class="col-12 mt-3 text-center">
                                    <p> I understand that if i have any questions, at any time, i will consult with my immediate supervisor or my Human Resource Staff members.</p>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agreed_policy[]" value="{{$campaignAcknowledgment->token}}" id="checkmeout_{{$index}}">
                                        <label class="form-check-label" for="checkmeout_{{$index}}">I have read and understood the above policy.</label>
                                        <div class="invalid-feedback d-block">
                                            @if ($errors->has('agreed_policy'))
                                                {{ $errors->first('agreed_policy') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                        <!-- next and prev button section -->
                                <div class="row mt-5 ">
                                    <div class="col-12 text-center clearfix">
                                        @if(count($campaignAcknowledgments))
                                            @if(!$loop->first)
                                                <button type="button" class="btn btn-primary btnPrevious" >Previous</button>
                                            @endif
                                        @endif

                                        @if (!$loop->last)
                                            <button type="button" class="btn btn-primary btnNext" >Next</button>
                                        @endif

                                        @if($loop->last)
                                            <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ./tab-pane -->
                    @endforeach
                    </div>
                </form>
            </div>
        </div>

    </div>
</div> <!-- end card -->
@endsection

@section('custom_js')
<script nonce="{{ csp_nonce() }}">
$(document).ready(function () {
    $('.btnNext').click(function(){
        $('#policies-list-tab > .active').next('a').trigger('click');
    });

    $('.btnPrevious').click(function(){
        $('#policies-list-tab > .active').prev('a').trigger('click');
    });
});
</script>
@endsection
