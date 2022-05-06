import React, { createRef } from "react";

import { useForm, usePage } from "@inertiajs/inertia-react";

const SAMLSettingsUploadForm = () => {
    const {
        form_actions: { saml_settings },
    } = usePage().props;
    const { post, setData, processing, errors, data } = useForm({
        saml_provider_metadata_file: null,
    });

    const fileInputRef = createRef();

    const handleOnChange = (e) =>
        setData("saml_provider_metadata_file", e.target.files[0]);

    React.useEffect(() => {
        if (data.saml_provider_metadata_file !== null)
            post(saml_settings.upload, { preserveState: false });
    }, [data]);

    return (
        <>
            <form>
                <input
                    type="file"
                    name="saml_provider_metadata_file"
                    className="mb-2 d-none"
                    ref={fileInputRef}
                    onChange={handleOnChange}
                    id="saml-provider-metadata"
                />
                <button
                    type="button"
                    id="upload-saml-metadata-btn"
                    className="btn btn-primary waves-effect waves-light mb-2"
                    onClick={() => fileInputRef.current.click()}
                    disabled={processing}
                    style={{ marginTop: "28.5px" }}
                >
                    <span id="saml-provider-metadata-upload-icon">
                        <i className="fas fa-upload" />
                    </span>
                    &nbsp;{processing ? "Uploading" : "Upload SAML Metadata"}
                </button>
            </form>
            {errors.saml_provider_metadata_file && (
                <div className="invalid-feedback d-block">
                    {errors.saml_provider_metadata_file}
                </div>
            )}
        </>
    );
};

export default SAMLSettingsUploadForm;
