import React from "react";
import { usePage } from "@inertiajs/inertia-react";
import {
    OverlayTrigger,
    Tooltip,
    InputGroup,
    FormControl,
} from "react-bootstrap";

const SAMLSettingsInfo = () => {
    const { saml_information } = usePage().props;
    return (
        <>
            <h4 className="mb-3">SAML Information</h4>
            <form>
                <fieldset>
                    <div className="row mb-3">
                        <label
                            htmlFor="entity-id"
                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                        >
                            Entity ID&nbsp;
                            <OverlayTrigger
                                placement="bottom"
                                overlay={(props) => (
                                    <Tooltip id="id" {...props}>
                                        Entity ID || Audience || Identifier
                                    </Tooltip>
                                )}
                            >
                                <i className="fa fa-info-circle" />
                            </OverlayTrigger>
                        </label>
                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                            <InputGroup className="mb-3">
                                <FormControl
                                    type="text"
                                    defaultValue={saml_information.metadata}
                                    readOnly
                                />
                                <OverlayTrigger
                                    placement="top"
                                    overlay={(props) => (
                                        <Tooltip id="copy-2" {...props}>
                                            Copy to clipboard
                                        </Tooltip>
                                    )}
                                >
                                    <InputGroup.Text
                                        id="basic-addon1"
                                        className="cursor-pointer copy-input-option"
                                        onClick={() =>
                                            navigator.clipboard.writeText(
                                                saml_information.acs
                                            )
                                        }
                                    >
                                        <i className="fe-copy" />
                                    </InputGroup.Text>
                                </OverlayTrigger>
                            </InputGroup>
                        </div>
                    </div>
                    <div className="row mb-3">
                        <label
                            htmlFor="idp-id"
                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                        >
                            Callback or ACS URL&nbsp;
                            <OverlayTrigger
                                placement="bottom"
                                overlay={(props) => (
                                    <Tooltip id="acs" {...props}>
                                        ACS URL
                                    </Tooltip>
                                )}
                            >
                                <i className="fa fa-info-circle" />
                            </OverlayTrigger>
                        </label>
                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                            <InputGroup className="mb-3">
                                <FormControl
                                    type="text"
                                    defaultValue={saml_information.acs}
                                    readOnly
                                />
                                <OverlayTrigger
                                    placement="top"
                                    overlay={(props) => (
                                        <Tooltip id="copy-2" {...props}>
                                            Copy to clipboard
                                        </Tooltip>
                                    )}
                                >
                                    <InputGroup.Text
                                        id="basic-addon1"
                                        className="cursor-pointer copy-input-option"
                                        onClick={() =>
                                            navigator.clipboard.writeText(
                                                saml_information.acs
                                            )
                                        }
                                    >
                                        <i className="fe-copy" />
                                    </InputGroup.Text>
                                </OverlayTrigger>
                            </InputGroup>
                        </div>
                    </div>
                    <div className="row mb-3">
                        <label
                            htmlFor="saml-url-label"
                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                        >
                            Sign in URL&nbsp;
                            <OverlayTrigger
                                placement="bottom"
                                overlay={(props) => (
                                    <Tooltip id="login" {...props}>
                                        SAML Endpoint | Login URL
                                    </Tooltip>
                                )}
                            >
                                <i className="fa fa-info-circle" />
                            </OverlayTrigger>
                        </label>
                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                            <InputGroup className="mb-3">
                                <FormControl
                                    type="text"
                                    defaultValue={saml_information.login}
                                    readOnly
                                />
                                <OverlayTrigger
                                    placement="top"
                                    overlay={(props) => (
                                        <Tooltip id="copy-2" {...props}>
                                            Copy to clipboard
                                        </Tooltip>
                                    )}
                                >
                                    <InputGroup.Text
                                        id="basic-addon1"
                                        className="cursor-pointer copy-input-option"
                                        onClick={() =>
                                            navigator.clipboard.writeText(
                                                saml_information.acs
                                            )
                                        }
                                    >
                                        <i className="fe-copy" />
                                    </InputGroup.Text>
                                </OverlayTrigger>
                            </InputGroup>
                        </div>
                    </div>
                    <div className="row mb-3">
                        <label
                            htmlFor="saml-url-label"
                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                        >
                            Sign out URL
                        </label>
                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                            <InputGroup className="mb-3">
                                <FormControl
                                    type="text"
                                    defaultValue={saml_information.sls}
                                    readOnly
                                />
                                <OverlayTrigger
                                    placement="top"
                                    overlay={(props) => (
                                        <Tooltip id="copy-2" {...props}>
                                            Copy to clipboard
                                        </Tooltip>
                                    )}
                                >
                                    <InputGroup.Text
                                        id="basic-addon1"
                                        className="cursor-pointer copy-input-option"
                                        onClick={() =>
                                            navigator.clipboard.writeText(
                                                saml_information.acs
                                            )
                                        }
                                    >
                                        <i className="fe-copy" />
                                    </InputGroup.Text>
                                </OverlayTrigger>
                            </InputGroup>
                        </div>
                    </div>
                </fieldset>
            </form>
        </>
    );
};

export default SAMLSettingsInfo;
