import React from "react";
import { useForm, usePage } from "@inertiajs/inertia-react";
import SAMLSettingsRemoveButton from "../saml/SAMLSettingsRemoveButton";

const SAMLSettingsManualForm = ({ samlSettings, children }) => {
    const {
        form_actions: { saml_settings },
    } = usePage().props;
    const { data, setData, post, errors, processing } = useForm({
        sso_provider: samlSettings?.sso_provider ?? "",
        entity_id: samlSettings?.entity_id ?? "",
        sso_url: samlSettings?.sso_url ?? "",
        slo_url: samlSettings?.slo_url ?? "",
        certificate: samlSettings?.certificate ?? "",
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(saml_settings.manual);
    };

    return (
        <form onSubmit={handleSubmit} method="post">
            <div className="row mb-3">
                <label
                    htmlFor="sso_provider"
                    className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                >
                    SSO Provider Name <span className="text-danger">*</span>
                </label>
                <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                    <input
                        type="text"
                        name="sso_provider"
                        id="sso_provider"
                        className="form-control"
                        value={data.sso_provider}
                        onChange={(e) =>
                            setData("sso_provider", e.target.value)
                        }
                    />
                    {errors.sso_provider && (
                        <div className="invalid-feedback d-block">
                            {errors.sso_provider}
                        </div>
                    )}
                </div>
            </div>
            <div className="row mb-3">
                <label
                    htmlFor="entity_id"
                    className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                >
                    IDP Entity ID <span className="text-danger">*</span>
                </label>
                <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                    <input
                        type="text"
                        name="entity_id"
                        className="form-control"
                        id="entity_id"
                        value={data.entity_id}
                        onChange={(e) => setData("entity_id", e.target.value)}
                    />
                    {errors.entity_id && (
                        <div className="invalid-feedback d-block">
                            {errors.entity_id}
                        </div>
                    )}
                </div>
            </div>
            <div className="row mb-3">
                <label
                    htmlFor="sso_url"
                    className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                >
                    SSO URL <span className="text-danger">*</span>
                </label>
                <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                    <input
                        type="text"
                        name="sso_url"
                        className="form-control"
                        id="sso_url"
                        value={data.sso_url}
                        onChange={(e) => setData("sso_url", e.target.value)}
                    />
                    {errors.sso_url && (
                        <div className="invalid-feedback d-block">
                            {errors.sso_url}
                        </div>
                    )}
                </div>
            </div>
            <div className="row mb-3">
                <label
                    htmlFor="slo_url"
                    className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                >
                    SLO URL <span className="text-danger">*</span>
                </label>
                <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                    <input
                        type="text"
                        name="slo_url"
                        className="form-control"
                        id="slo_url"
                        value={data.slo_url}
                        onChange={(e) => setData("slo_url", e.target.value)}
                    />
                    {errors.slo_url && (
                        <div className="invalid-feedback d-block">
                            {errors.slo_url}
                        </div>
                    )}
                </div>
            </div>
            <div className="row mb-3">
                <label
                    htmlFor="certificate"
                    className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                >
                    X.509 Certificate <span className="text-danger">*</span>
                </label>
                <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                    <textarea
                        name="certificate"
                        id="certificate"
                        rows={20}
                        className="form-control"
                        value={data.certificate}
                        onChange={(e) => setData("certificate", e.target.value)}
                    />
                    {errors.certificate && (
                        <div className="invalid-feedback d-block">
                            {errors.certificate}
                        </div>
                    )}
                </div>
            </div>
            <div className="save-button d-flex justify-content-end my-3">
                <SAMLSettingsRemoveButton />
                <button
                    type="submit"
                    className="btn btn-primary width-lg secondary-bg-color"
                    disabled={processing}
                >
                    {processing ? "Saving" : "Save"}
                </button>
            </div>
        </form>
    );
};

export default SAMLSettingsManualForm;
