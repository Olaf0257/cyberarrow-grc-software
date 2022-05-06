import React from "react";
import { useForm, usePage } from "@inertiajs/inertia-react";
import { OverlayTrigger, Tooltip } from "react-bootstrap";

const SAMLSettingsRemoteForm = () => {
    const {
        form_actions: { saml_settings },
    } = usePage().props;
    const { data, setData, processing, post, errors } = useForm({
        saml_provider_remote_metadata: "",
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(saml_settings.remote_import);
    };

    return (
        <>
            <form onSubmit={handleSubmit} method="post">
                <label className="form-label" htmlFor="remote-metadata">
                    Remote metadata xml&nbsp;
                    <OverlayTrigger
                        placement="bottom"
                        overlay={(props) => (
                            <Tooltip id="remote-metadata" {...props}>
                                Automatically parses your remote metadata and
                                fills out of the form for you.
                            </Tooltip>
                        )}
                    >
                        <i className="fa fa-info-circle" />
                    </OverlayTrigger>
                </label>
                <div className="row mb-2">
                    <div className="col-8 metadate_col">
                        <input
                            type="text"
                            name="saml_provider_remote_metadata"
                            className="form-control"
                            id="remote-metadata"
                            value={data.saml_provider_remote_metadata}
                            onChange={(e) =>
                                setData(
                                    "saml_provider_remote_metadata",
                                    e.target.value
                                )
                            }
                        />
                    </div>
                    <div className="col-2 import_col">
                        <button
                            type="submit"
                            className="btn btn-primary waves-effect waves-light"
                            disabled={processing}
                        >
                            {processing ? "Importing" : "Import"}
                        </button>
                    </div>
                </div>
            </form>
            {errors.saml_provider_remote_metadata && (
                <div className="invalid-feedback d-block">
                    {errors.saml_provider_remote_metadata}
                </div>
            )}
        </>
    );
};

export default SAMLSettingsRemoteForm;
