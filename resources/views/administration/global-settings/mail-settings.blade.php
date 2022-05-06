<div class="tab-pane fade bg-white global {{ ($activeTab == 'mail_settings') ? 'show active' : ''}}" id="mail-setting" role="tabpanel" aria-labelledby="mail-setting-tab">
    <!-- form starts -->
    <form action="{{ route('global-settings.mail-settings') }}" method="post" class="">
        @csrf
            <input type="hidden" name="mail_settings" value="1">
            <div class="row mb-3">
                <label for="displayname" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">SMTP Host <span class="text-danger">*</span></label>
                <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                    <input type="text" name="mail_host" class="form-control" value="{{ old('mail_host', decodeHTMLEntity($mailSettings->mail_host) ?: env('MAIL_HOST') ) }}">
                    <div class="invalid-feedback d-block">
                        @if ($errors->has('mail_host'))
                            {{ $errors->first('mail_host') }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <label for="displayname" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">SMTP Port <span class="text-danger">*</span></label>
                <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                    <input type="text" name="mail_port" class="form-control" value="{{ old('mail_port', decodeHTMLEntity($mailSettings->mail_port) ?: env('MAIL_PORT') ) }}">
                    <div class="invalid-feedback d-block">
                        @if ($errors->has('mail_port'))
                            {{ $errors->first('mail_port') }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <label for="color" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">SMTP Security <span class="text-danger">*</span></label>
                <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                    <select name="mail_encryption" class="form-control">
                        <option value="tls" {{ strtolower( old('mail_encryption', $mailSettings->mail_encryption ?: env('MAIL_ENCRYPTION')) ) == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ strtolower( old('mail_encryption', $mailSettings->mail_encryption ?: env('MAIL_ENCRYPTION')) ) == 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                    <div class="invalid-feedback d-block">
                        @if ($errors->has('mail_encryption'))
                            {{ $errors->first('mail_encryption') }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- SMTP Username -->
            <div class="row mb-3">
                <label for="color" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">SMTP Username <span class="text-danger">*</span></label>
                <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                    <input type="text" class="form-control" name="mail_username"  value="{{ old('mail_username', decodeHTMLEntity($mailSettings->mail_username) ?: env('MAIL_USERNAME') ) }}">
                    <div class="invalid-feedback d-block">
                        @if ($errors->has('mail_username'))
                            {{ $errors->first('mail_username') }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- SMTP Password-->
            <div class="row mb-3">
                <label for="color" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">SMTP Password <span class="text-danger">*</span></label>
                <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                    <input type="password" class="form-control" name="mail_password" value="{{ old('mail_password', $mailSettings->mail_password ?: env('MAIL_PASSWORD') )  }}">
                    <div class="invalid-feedback d-block">
                        @if ($errors->has('mail_password'))
                        {{ $errors->first('mail_password') }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mail From Address Password-->
            <div class="row mb-3">
                <label for="color" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">From Address <span class="text-danger">*</span></label>
                <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                    <input type="text" class="form-control" name="mail_from_address" value="{{ old('mail_from_address', decodeHTMLEntity($mailSettings->mail_from_address) ?: env('MAIL_FROM_ADDRESS', 'hello@example.com') )  }}">
                    <div class="invalid-feedback d-block">
                        @if ($errors->has('mail_from_address'))
                        {{ $errors->first('mail_from_address') }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mail From Address Password-->
            <div class="row mb-3">
                <label for="color" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">From Name <span class="text-danger">*</span></label>
                <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                    <input type="text" class="form-control" name="mail_from_name" value="{{ old('mail_from_name', decodeHTMLEntity($mailSettings->mail_from_name) ?: env('MAIL_FROM_NAME', 'Example') ) }}">
                    <div class="invalid-feedback d-block">
                        @if ($errors->has('mail_from_name'))
                        {{ $errors->first('mail_from_name') }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- button save -->
            <div class="save-button d-flex justify-content-end my-3">
                @if($mailSettings->mail_host && $mailSettings->mail_port && $mailSettings->mail_encryption && $mailSettings->mail_username && $mailSettings->mail_password)
                    <a href="{{ route('global-settings.test-mail-connection') }}" class="btn btn-primary width-lg secondary-bg-colorn">
                        Test Connection
                    </a>
                @endif

                <input type="submit" class="btn btn-primary width-lg secondary-bg-color ms-3" value="Save">
            </div> <!-- save button ends -->
        </form>
<!-- form ends -->

</div>
