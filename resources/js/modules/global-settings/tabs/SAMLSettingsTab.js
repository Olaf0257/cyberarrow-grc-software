import React from 'react';
import { usePage } from "@inertiajs/inertia-react";

import SAMLSettingsUploadForm from "../components/saml/SAMLSettingsUploadForm";
import SAMLSettingsManualForm from "../components/saml/SAMLSettingsManualForm";
import SAMLSettingsInfo from "../components/saml/SAMLSettingsInfo";
import SAMLSettingsRemoteForm from "../components/saml/SAMLSettingsRemoteForm";

const SAMLSettingsTab = () => {
    const { form_actions: { saml_settings }, samlSetting: samlSettings, saml_information } = usePage().props;

    return (
        <div className="row global">
            <div className="col-md-6">
                <h4 className="mb-3">SAML Provider Config</h4>
                <div className="alert alert-info" role="alert">
                    Import the metadata from your SSO provider to automatically fill out these
                    fields.
                </div>

                <div className="row mb-4">
                    <div className="col-lg-5">
                        <SAMLSettingsUploadForm />
                    </div>
                    <div className="col-lg-7">
                        <SAMLSettingsRemoteForm />
                    </div>
                </div>
                {/* Manual configuration */}
                <SAMLSettingsManualForm samlSettings={samlSettings} />
            </div>
            {/* Service provider metadata Info */}
            <div className="col-md-6">
                <SAMLSettingsInfo />
                <hr
                    className="mt-5"
                    style={{ borderTop: "2px solid var(--secondary-color)" }}
                />
                <div className="row">
                    <div className="col">
                        <h5>Download Metadata</h5>
                    </div>
                    <div className="col">
                        <a
                            href={saml_information.download}
                            className="btn btn-primary float-end"
                        >
                            Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
    )
};

export default SAMLSettingsTab;

