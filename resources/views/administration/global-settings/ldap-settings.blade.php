<div class="tab-pane global {{ ($activeTab == 'ldap_settings') ? 'show active' : ''}}" id="ldap-settings">
    <form class="" action="{{ route('global-settings.ldap-settings') }}" method="post">
    @csrf
        <input type="hidden" name="ldap_settings" value="1">
        <div class="row mb-3">
            <label for="host-url" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">Host URL <span class="text-danger">*</span></label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <input type="text" name="hosts" class="form-control" value="{{ old('hosts', decodeHTMLEntity($ldapSettings->hosts)) }}">
                <div class="invalid-feedback d-block">
                    @if ($errors->has('hosts'))
                    {{ $errors->first('hosts') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="ssl-label" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">SSL</label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <select name="use_ssl" class="form-control" id="example-select">
                    <option value="">Choose</option>
                    <option value="yes" {{ old('use_ssl', $ldapSettings->use_ssl) == 'yes' ? 'selected' : ''}}>Yes</option>
                    <option value="no" {{ old('use_ssl', $ldapSettings->use_ssl) == 'no' ? 'selected' : ''}}>No</option>
                </select>
                <div class="invalid-feedback d-block">
                    @if ($errors->has('use_ssl'))
                    {{ $errors->first('use_ssl') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="port-label" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">Port </label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <input type="text" name="port" class="form-control" value="{{ old('port', decodeHTMLEntity($ldapSettings->port))}}">
                <div class="invalid-feedback d-block">
                    @if ($errors->has('port'))
                    {{ $errors->first('port') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="version-label" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">Version</label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <input type="text" name="version" class="form-control" value="{{ old('version', decodeHTMLEntity($ldapSettings->version)) }}">
                <div class="invalid-feedback d-block">
                    @if ($errors->has('version'))
                    {{ $errors->first('version') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="base-distinguish-label" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">Base Distinguished Name <span class="text-danger">*</span></label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <input type="text" name="base_dn" class="form-control" value="{{ old('base_dn', decodeHTMLEntity($ldapSettings->base_dn)) }}">
                <div class="invalid-feedback d-block">
                    @if ($errors->has('base_dn'))
                    {{ $errors->first('base_dn') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="username-label" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">Username <span class="text-danger">*</span></label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <input type="text" name="username" class="form-control" value="{{ old('username', decodeHTMLEntity($ldapSettings->username)) }}">
                <div class="invalid-feedback d-block">
                    @if ($errors->has('username'))
                    {{ $errors->first('username') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="password-label" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">Password <span class="text-danger">*</span></label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <input type="password" name="bind_password" class="form-control" value="{{ old('bind_password', decodeHTMLEntity($ldapSettings->password)) }}">
                <div class="invalid-feedback d-block">
                    @if ($errors->has('bind_password'))
                    {{ $errors->first('bind_password') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="data-mapping-text">
            <p class="datamap-head-text pt-2">Data Mapping</p>
        </div>

        <div class="line"></div>

        <div class="row mb-3">
            <label for="firstname" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">First Name <span class="text-danger">*</span></label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <input type="text" name="map_first_name_to" class="form-control" value="{{ old('map_first_name_to', decodeHTMLEntity($ldapSettings->map_first_name_to)) }}">
                <div class="invalid-feedback d-block">
                    @if ($errors->has('map_first_name_to'))
                    {{ $errors->first('map_first_name_to') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="surname" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">Surname <span class="text-danger">*</span></label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <input type="text" name="map_last_name_to" class="form-control" value="{{ old('map_last_name_to', decodeHTMLEntity($ldapSettings->map_last_name_to)) }}">
                <div class="invalid-feedback d-block">
                    @if ($errors->has('map_last_name_to'))
                    {{ $errors->first('map_last_name_to') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="email" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">Email Address <span class="text-danger">*</span></label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <input type="text" name="map_email_to" class="form-control" value="{{ old('map_email_to', decodeHTMLEntity($ldapSettings->map_email_to)) }}">
                <div class="invalid-feedback d-block">
                    @if ($errors->has('map_email_to'))
                    {{ $errors->first('map_email_to') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="mobile-num" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">Mobile Number </label>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <input type="tel" name="map_contact_number_to" class="form-control" value="{{ old('map_contact_number_to', decodeHTMLEntity($ldapSettings->map_contact_number_to)) }}">
                <div class="invalid-feedback d-block">
                    @if ($errors->has('map_contact_number_to'))
                    {{ $errors->first('map_contact_number_to') }}
                    @endif
                </div>
            </div>
        </div>
        <div class="save-button d-flex justify-content-end my-3">
                @if($ldapSettings->hosts && $ldapSettings->port  && $ldapSettings->username && $ldapSettings->password)
                    <a href="{{ route('global-settings.test-ldap-connection') }}" class="btn btn-primary width-lg secondary-bg-colorn">
                        Test Connection
                    </a>
                @endif

                <input type="submit" class="btn btn-primary width-lg secondary-bg-color ms-3" value="Save">
            </div> <!-- save button ends -->
            <!-- save button ends -->
    </form>
</div>
