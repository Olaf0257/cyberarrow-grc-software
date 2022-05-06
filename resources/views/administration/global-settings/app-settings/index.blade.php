<div class="tab-pane fade bg-white global {{ ($activeTab == 'global_settings') ? 'show active' : ''}}" id="global-setting" role="tabpanel" aria-labelledby="profile-tab">
    <!-- form starts -->
    <form action="{{ route('global-settings.store') }}" method="post" enctype="multipart/form-data" class="">
    @csrf
        <input type="hidden" name="global_settings" value="1">
        <div class="row mb-3">
            <label for="displayname" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">Display Name <span class="required text-danger">*</span></label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <input type="text" name="display_name" class="form-control" value="{{ htmlspecialchars_decode($globalSetting->display_name) }}">
                <div class="invalid-feedback d-block">
                    @if ($errors->has('display_name'))
                    {{ $errors->first('display_name') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="color" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">Primary Color <span class="required text-danger">*</span></label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <input type="text" id="hexa-colorpicker" name="primary_color" readonly autocomplete="off" class="form-control colorpicker-element" value="{{ htmlspecialchars_decode($globalSetting->primary_color) }}">
                <div class="invalid-feedback d-block">
                    @if ($errors->has('primary_color'))
                    {{ $errors->first('primary_color') }}
                    @endif
                </div>
            </div>
        </div>

        <!-- secondary color -->
        <div class="row mb-3">
            <label for="color" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">Secondary Color <span class="required text-danger">*</span></label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <input type="text" class="form-control colorpicker-element" name="secondary_color"  value="{{ htmlspecialchars_decode($globalSetting->secondary_color) }}">
                <div class="invalid-feedback d-block">
                    @if ($errors->has('secondary_color'))
                        {{ $errors->first('secondary_color') }}
                    @endif
                </div>
            </div>
        </div>

        <!-- default text color -->
        <div class="row mb-3">
            <label for="color" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">Default Text Color <span class="required text-danger">*</span></label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <input type="text" class="form-control colorpicker-element" readonly autocomplete="off" name="default_text_color" value="{{ htmlspecialchars_decode($globalSetting->default_text_color) }}">
                <div class="invalid-feedback d-block">
                    @if ($errors->has('default_text_color'))
                    {{ $errors->first('default_text_color') }}
                    @endif
                </div>
            </div>
        </div>
        <!-- default Timezone -->
        <div class="row my-2">
            <div class="col-xl-3">
                <label class="form-label">Time Zone</label>
            </div>
            <div class="col-xl-9">
                <select class="form-control timezone-select" name="timezone">
                    @foreach( $timezones as $index => $timezone )
                    <option value="{{$index}}" {{ $globalSetting->timezone == $index ? 'selected' : '' }}>{{ $timezone }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block">
                    @if ($errors->has('timezone'))
                    {{ $errors->first('timezone') }}
                    @endif
                </div>
            </div>
        </div>

        <!-- company logo -->
        <div class="row my-2">
            <div class="col-xl-3 col-lg-3 col-md-3">
                <label>Company Logo</label>
            </div>
            <div class="col-xl-9 col-lg-9 col-md-9">
                <p class="sub-header">
                    The recommended image size for Company Logo is 300 by 300 pixels.
                </p>
                <input type="file" name="company_logo" data-default-file="{{$globalSetting->company_logo ? $globalSetting->company_logo =='assets/images/ebdaa-Logo.png' ? asset($globalSetting->company_logo): tenant_asset($globalSetting->company_logo) : '' }}" class="dropify" data-height="300" />
                <div class="invalid-feedback d-block">
                    @if ($errors->has('company_logo'))
                    {{ $errors->first('company_logo') }}
                    @endif
                </div>
            </div><!-- end col -->
        </div>
        <!-- end row -->

        <!-- Favicon -->
        <div class="row my-2">
            <div class="col-xl-3 col-lg-3 col-md-3">
                <label>Favicon</label>
            </div>
            <div class="col-xl-9 col-lg-9 col-md-9">
                <p class="sub-header">
                    The recommended image size for Favicon is 64 by 64 pixels.
                </p>
                <input type="file" name="favicon" class="dropify" data-default-file="{{$globalSetting->favicon ? $globalSetting->favicon =='assets/images/ebdaa-Logo.png' ? asset($globalSetting->favicon) : tenant_asset($globalSetting->favicon) : '' }}" data-height="300" />
                <div class="invalid-feedback d-block">
                    @if ($errors->has('favicon'))
                    {{ $errors->first('favicon') }}
                    @endif
                </div>
            </div><!-- end col -->
        </div>
        <!-- Favicon -->

        <!-- icons 1 -->
        <div class="row my-2">
            <div class="col-lg-3 col-md-5 col-sm-6 col-6">
                <p>Document Upload Allowed</p>
            </div>
            <div class="col-lg-9 col-md-7 col-sm-6 col-6">
                <input type="checkbox" class="switchery" id="allow_document_upload" data-color="@red"  {{ $globalSetting->allow_document_upload ? 'checked' : ''}}>
                <input type="hidden" name="allow_document_upload" value="{{ $globalSetting->allow_document_upload }}">
            </div>
        </div> <!-- row ends -->

        <!-- icons 2 -->
        <div class="row">
            <div class="col-lg-3 col-md-5 col-sm-6 col-6">
                <p>Links Upload Allowed</p>
            </div>
            <div class="col-lg-9 col-md-7 col-sm-6 col-6">
                <input type="checkbox" class="switchery" id="allow_document_link"  {{ $globalSetting->allow_document_link ? 'checked' : ''}}>
                <input type="hidden" name="allow_document_link" value="{{ $globalSetting->allow_document_link }}">
            </div>
        </div> <!-- row ends -->

        <!-- session -->
        <div class="row my-2">
            <div class="col-xl-3">
                <label class="form-label">Session Timeout</label>
            </div>
            <div class="col-xl-9">
                <select name="session_timeout" class="form-control">
                    @foreach( $sessionExpiryTimes as $index => $expiryTimes )
                    <option value="{{ $index }}" {{ $globalSetting->session_timeout == $index ? 'selected' : '' }}>{{ $expiryTimes }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block">
                    @if ($errors->has('session_timeout'))
                    {{ $errors->first('session_timeout') }}
                    @endif
                </div>
            </div>
        </div>

        <!-- MFA Login -->
        <div class="row">
            <div class="col-xl-3">
                <label class="form-label">Secure MFA Login</label>
            </div>
            <div class="col-xl-9">
                <select name="secure_mfa_login" class="form-control">
                    <option value="0" {{ $globalSetting->secure_mfa_login == 0 ? "selected" : "" }}>Optional</option>
                    <option value="1" {{ $globalSetting->secure_mfa_login == 1 ? "selected" : "" }}>Mandatory</option>
                </select>
                <div class="invalid-feedback d-block">
                    @if ($errors->has('secure_mfa_login'))
                    {{ $errors->first('secure_mfa_login') }}
                    @endif
                </div>
            </div>
        </div>

        <!-- button save -->
        <div class="save-button d-flex justify-content-end my-3">
            <input type="submit" class="btn btn-primary width-lg secondary-bg-color" value="Save">
        </div> <!-- save button ends -->
    </form>
<!-- form ends -->
</div> <!-- tab-pane for account settings ends -->
