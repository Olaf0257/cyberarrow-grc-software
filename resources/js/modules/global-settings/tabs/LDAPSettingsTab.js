import React from "react";

import { Link, useForm, usePage } from "@inertiajs/inertia-react";

const LDAPSettingsTab = ({ ldapSettings }) => {
    const {
        form_actions: { ldap_settings },
        connection_test_routes: { ldap_settings: connection_test_route },
    } = usePage().props;
    const { data, setData, processing, post, errors } = useForm({
        hosts: ldapSettings?.hosts ?? "",
        use_ssl: ldapSettings?.use_ssl ?? "",
        port: ldapSettings?.port ?? "",
        version: ldapSettings?.version ?? "",
        base_dn: ldapSettings?.base_dn ?? "",
        username: ldapSettings?.username ?? "",
        bind_password: ldapSettings?.password ?? "",
        map_first_name_to: ldapSettings?.map_first_name_to ?? "",
        map_last_name_to: ldapSettings?.map_last_name_to ?? "",
        map_email_to: ldapSettings?.map_email_to ?? "",
        map_contact_number_to: ldapSettings?.map_contact_number_to ?? "",
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(ldap_settings);
    };

    return (
        <div className={"global"}>
            <form onSubmit={handleSubmit} method="post">
                <div className="row mb-3">
                    <label
                        htmlFor="hosts"
                        className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                    >
                        Host URL <span className="text-danger">*</span>
                    </label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input
                            type="text"
                            id="hosts"
                            name="hosts"
                            className="form-control"
                            value={data.hosts}
                            onChange={(e) => setData("hosts", e.target.value)}
                        />
                        {errors.hosts && (
                            <div className="invalid-feedback d-block">
                                {errors.hosts}
                            </div>
                        )}
                    </div>
                </div>
                <div className="row mb-3">
                    <label
                        htmlFor="use_ssl"
                        className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                    >
                        SSL
                    </label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <select
                            name="use_ssl"
                            value={data.use_ssl}
                            onChange={(e) => setData("use_ssl", e.target.value)}
                            className="form-control cursor-pointer"
                            id="use_ssl"
                        >
                            <option value="">Choose</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        {errors.use_ssl && (
                            <div className="invalid-feedback d-block">
                                {errors.use_ssl}
                            </div>
                        )}
                    </div>
                </div>
                <div className="row mb-3">
                    <label
                        htmlFor="port"
                        className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                    >
                        Port{" "}
                    </label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input
                            type="text"
                            name="port"
                            id="port"
                            className="form-control"
                            value={data.port}
                            onChange={(e) => setData("port", e.target.value)}
                        />
                        {errors.port && (
                            <div className="invalid-feedback d-block">
                                {errors.port}
                            </div>
                        )}
                    </div>
                </div>
                <div className="row mb-3">
                    <label
                        htmlFor="version"
                        className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                    >
                        Version
                    </label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input
                            type="text"
                            name="version"
                            id="version"
                            className="form-control"
                            value={data.version}
                            onChange={(e) => setData("version", e.target.value)}
                        />
                        {errors.version && (
                            <div className="invalid-feedback d-block">
                                {errors.version}
                            </div>
                        )}
                    </div>
                </div>
                <div className="row mb-3">
                    <label
                        htmlFor="base_dn"
                        className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                    >
                        Base Distinguished Name{" "}
                        <span className="text-danger">*</span>
                    </label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input
                            type="text"
                            name="base_dn"
                            id="base_dn"
                            className="form-control"
                            value={data.base_dn}
                            onChange={(e) => setData("base_dn", e.target.value)}
                        />
                        {errors.base_dn && (
                            <div className="invalid-feedback d-block">
                                {errors.base_dn}
                            </div>
                        )}
                    </div>
                </div>
                <div className="row mb-3">
                    <label
                        htmlFor="username"
                        className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                    >
                        Username <span className="text-danger">*</span>
                    </label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input
                            type="text"
                            name="username"
                            id="username"
                            className="form-control"
                            value={data.username}
                            onChange={(e) =>
                                setData("username", e.target.value)
                            }
                        />
                        {errors.username && (
                            <div className="invalid-feedback d-block">
                                {errors.username}
                            </div>
                        )}
                    </div>
                </div>
                <div className="row mb-3">
                    <label
                        htmlFor="bind_password"
                        className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                    >
                        Password <span className="text-danger">*</span>
                    </label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input
                            type="password"
                            name="bind_password"
                            id="bind_password"
                            className="form-control"
                            value={data.bind_password}
                            onChange={(e) =>
                                setData("bind_password", e.target.value)
                            }
                        />
                        {errors.bind_password && (
                            <div className="invalid-feedback d-block">
                                {errors.bind_password}
                            </div>
                        )}
                    </div>
                </div>
                <div className="data-mapping-text">
                    <p className="datamap-head-text pt-2">Data Mapping</p>
                </div>
                <div className="line" />
                <div className="row mb-3">
                    <label
                        htmlFor="map_first_name_to"
                        className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                    >
                        First Name <span className="text-danger">*</span>
                    </label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input
                            type="text"
                            name="map_first_name_to"
                            id="map_first_name_to"
                            className="form-control"
                            value={data.map_first_name_to}
                            onChange={(e) =>
                                setData("map_first_name_to", e.target.value)
                            }
                        />
                        {errors.map_first_name_to && (
                            <div className="invalid-feedback d-block">
                                {errors.map_first_name_to}
                            </div>
                        )}
                    </div>
                </div>
                <div className="row mb-3">
                    <label
                        htmlFor="map_last_name_to"
                        className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                    >
                        Surname <span className="text-danger">*</span>
                    </label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input
                            type="text"
                            name="map_last_name_to"
                            id="map_last_name_to"
                            className="form-control"
                            value={data.map_last_name_to}
                            onChange={(e) =>
                                setData("map_last_name_to", e.target.value)
                            }
                        />
                        {errors.map_last_name_to && (
                            <div className="invalid-feedback d-block">
                                {errors.map_last_name_to}
                            </div>
                        )}
                    </div>
                </div>
                <div className="row mb-3">
                    <label
                        htmlFor="map_email_to"
                        className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                    >
                        Email Address <span className="text-danger">*</span>
                    </label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input
                            type="text"
                            name="map_email_to"
                            id="map_email_to"
                            className="form-control"
                            value={data.map_email_to}
                            onChange={(e) =>
                                setData("map_email_to", e.target.value)
                            }
                        />
                        {errors.map_email_to && (
                            <div className="invalid-feedback d-block">
                                {errors.map_email_to}
                            </div>
                        )}
                    </div>
                </div>
                <div className="row mb-3">
                    <label
                        htmlFor="map_contact_number_to"
                        className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                    >
                        Mobile Number{" "}
                    </label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input
                            type="tel"
                            name="map_contact_number_to"
                            className="form-control"
                            value={data.map_contact_number_to}
                            onChange={(e) =>
                                setData("map_contact_number_to", e.target.value)
                            }
                        />
                        {errors.map_contact_number_to && (
                            <div className="invalid-feedback d-block">
                                {errors.map_contact_number_to}
                            </div>
                        )}
                    </div>
                </div>
                <div className="save-button d-flex justify-content-end my-3">
                    {ldapSettings?.hosts &&
                        ldapSettings?.port &&
                        ldapSettings?.username &&
                        ldapSettings?.password && (
                            <Link
                                href={connection_test_route}
                                className="btn btn-primary width-lg secondary-bg-color"
                            >
                                Test Connection
                            </Link>
                        )}
                    <button
                        type="submit"
                        className="btn btn-primary width-lg secondary-bg-color ms-3"
                        disabled={processing}
                    >
                        {processing ? "Saving" : "Save"}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default LDAPSettingsTab;
