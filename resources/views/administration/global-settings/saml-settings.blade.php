<div class="tab-pane global {{ ($activeTab == 'saml_settings') ? 'show active' : ''}}" id="saml-settings">
    <div class="row">
        <!-- SSO provider or Identity provider configuration -->
        <div class="col-md-6">
            <h4 class="mb-3">
                SAML Provider Config
            </h4>
            <div class="alert alert-info" role="alert">
                Import the metadata from your SSO provider to automatically fill out these fields.
            </div>

            <div class="row align-items-center saml-upload mb-4">
                <div class="col-5 saml-upload-inner">
                    <form name="saml-provider-metadata-upload-form" action="{{ route('global-settings.saml-settings.saml-provider-metadata.upload') }}" method="post" enctype="multipart/form-data">
                    @csrf
                        <input type="hidden" name="saml_settings" value="1">
                        <label class="" for="inlineFormInput"></label>
                        <input type="file" name="saml_provider_metadata_file" class="mb-2" id="saml-provider-metadata" placeholder="Jane Doe" style="display: none;">
                        <button type="button" id="upload-saml-metadata-btn" class="btn btn-primary waves-effect waves-light mb-2">
                        <span id="saml-provider-metadata-upload-icon"><i class="fas fa-upload"></i></span>&nbsp;Upload SAML Metadata</button>
                    </form>
                </div>
                <div class="col-7 saml-upload-inner">
                    <form action="{{ route('global-settings.saml-settings.saml-provider-metadata.import') }}" method="post">
                        @csrf
                        <input type="hidden" name="saml_settings" value="1">
                        <label class="form-label" for="Remote-metadata">
                            Remote metadata xml <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Automatically parses your remote metadata and fills out of the form for you."></i>
                        </label>
                        <div class="row mb-2">
                            <div class="col-8 metadate_col">
                                <input type="text" name="saml_provider_remote_metadata" class="form-control" id="Remote-metadata" >
                            </div>
                            <div class="col-2 import_col">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Import</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-5">
                    <div class="invalid-feedback d-block">
                        @if($errors->has('saml_provider_metadata_file'))
                            {{ $errors->first('saml_provider_metadata_file') }}
                        @endif
                    </div>
                </div>
                <div class="col-7">
                    <div class="invalid-feedback d-block">
                        @if($errors->has('saml_provider_remote_metadata'))
                            {{ $errors->first('saml_provider_remote_metadata') }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- Manual configuration -->
            <form action="{{ route('global-settings.saml-settings') }}" method="post">
                @csrf
                <input type="hidden" name="saml_settings" value="1">
                <div class="row mb-3">
                    <label for="identity-name" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">
                    SSO Provider Name <span class="text-danger">*</span>
                    </label>
                    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input type="text" name="sso_provider" class="form-control" value="{{ old('sso_provider', decodeHTMLEntity($samlSetting->sso_provider)) }}">
                        <div class="invalid-feedback d-block">
                            @if($errors->has('sso_provider'))
                                {{ $errors->first('sso_provider') }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="idp-id" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">
                    IDP Entity ID <span class="text-danger">*</span>
                    </label>
                    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input type="text" name="entity_id" class="form-control" value="{{ old('entity_id', decodeHTMLEntity($samlSetting->entity_id)) }}">
                        <div class="invalid-feedback d-block">
                            @if ($errors->has('entity_id'))
                                {{ $errors->first('entity_id') }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="saml-url-label" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">
                    SSO URL <span class="text-danger">*</span>
                    </label>
                    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input type="text" name="sso_url" class="form-control" value="{{ old('sso_url', decodeHTMLEntity($samlSetting->sso_url)) }}">
                        <div class="invalid-feedback d-block">
                            @if ($errors->has('sso_url'))
                                {{ $errors->first('sso_url') }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="saml-url-label" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">
                    SLO URL <span class="text-danger">*</span>
                    </label>
                    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input type="text" name="slo_url" class="form-control" value="{{ old('slo_url', decodeHTMLEntity($samlSetting->slo_url)) }}">
                        <div class="invalid-feedback d-block">
                            @if ($errors->has('slo_url'))
                                {{ $errors->first('slo_url') }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="base-url-label" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">
                    X.509 Certificate <span class="text-danger">*</span>
                    </label>
                    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <textarea name="certificate" id="" rows="20" class="form-control">{{ old('certificate', decodeHTMLEntity($samlSetting->certificate)) }}</textarea>
                        <div class="invalid-feedback d-block">
                            @if ($errors->has('certificate'))
                                {{ $errors->first('certificate') }}
                            @endif
                        </div>
                    </div>
                </div>

                <!-- button save -->
                <div class="save-button d-flex justify-content-end my-3">
                    <input type="submit" class="btn btn-primary width-lg secondary-bg-color" value="Save">
                </div>
                <!-- save button ends -->
            </form>
        </div>
        <!-- Service provider metadata Info -->
        <div class="col-md-6">
            <h4 class="mb-3">
                SAML Information
            </h4>
            <form>
                <fieldset>
                    <div class="row mb-3">
                        <label for="identity-name" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">
                        Entity ID <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Entity ID || Audience || Identifier"></i>
                        </label>
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ route('saml2.metadata') }}" readonly>
                                <div class="copy-input-option" data-toggle="tooltip" data-placement="top" title="" data-original-title="Copy to clipboard">
                                    <span class="input-group-text">
                                        <i class="fe-copy"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="idp-id" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">
                        Callback or ACS URL <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="ACS URL"></i>
                        </label>
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ route('saml2.acs') }}" readonly>
                                <div class="copy-input-option" data-toggle="tooltip" data-placement="top" title="" data-original-title="Copy to clipboard">
                                    <span class="input-group-text">
                                        <i class="fe-copy"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="saml-url-label" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">
                        Sign in URL <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="SAML Endpoint | Login URL"></i>
                        </label>
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ route('saml2.login') }}" readonly>
                                <div class="copy-input-option" data-toggle="tooltip" data-placement="top" title="" data-original-title="Copy to clipboard">
                                    <span class="input-group-text">
                                        <i class="fe-copy"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="saml-url-label" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">
                        Sign out URL
                        </label>
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ route('saml2.sls') }}" readonly>
                                <div class="copy-input-option" data-toggle="tooltip" data-placement="top" title="" data-original-title="Copy to clipboard">
                                    <span class="input-group-text">
                                        <i class="fe-copy"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
            <hr class="mt-5" style="border-top: 2px solid var(--secondary-color);">
            <div class="row">
                <div class="col">
                    <h5>Download Metadata</h5>
                </div>
                <div class="col">
                    <a href="{{ route('global-settings.saml-settings.download.sp-metadata') }}" class="btn btn-primary float-end">Download</a>
                </div>
            </div>
        </div>
    </div>
</div>
