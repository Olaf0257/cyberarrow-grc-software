<div class="text-center w-75 m-auto">
    <a href="{{ route('login') }}">
        <span><img class="logo-sm" src="{{ $globalSetting->company_logo =='assets/images/ebdaa-Logo.png' ? asset($globalSetting->company_logo): tenant_asset($globalSetting->company_logo) }}" alt="Company logo" height="" width="140"></span>
    </a>
    <p class="log-text text-muted mb-3 mt-3">{{ strtoupper(decodeHTMLEntity($globalSetting->display_name)) }}</p>

    @if(Session::has('status'))
        <span class="error-msg msg">
            {{ Session::get('status') }}
        </span>
    @endif
</div>
@if ($message = Session::get('exception'))
    <div class="alert alert-danger alert-block  mt-2">
        <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
    </div>
@endif
