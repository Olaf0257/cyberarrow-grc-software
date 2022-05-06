import React from "react";

import { Link, useForm, usePage } from "@inertiajs/inertia-react";

const SMTPSettingsTab = ({mailSettings}) => {
    const {
        form_actions: {mail_settings: mail_form_action},
        connection_test_routes: {mail_settings: connection_test_route},
        is_mail_testable
    } = usePage().props;
    const {data, setData, processing, post, errors} = useForm({
        mail_host: mailSettings?.mail_host ?? '',
        mail_port: mailSettings?.mail_port ?? '',
        mail_encryption: mailSettings?.mail_encryption ?? '',
        mail_username: mailSettings?.mail_username ?? '',
        mail_password: mailSettings?.mail_password ?? '',
        mail_from_address: mailSettings?.mail_from_address ?? '',
        mail_from_name: mailSettings?.mail_from_name ?? ''
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(mail_form_action);
    };
    return (
        <div className={"global"}>
            <form onSubmit={handleSubmit} method="post">
                <div className="row mb-3">
                    <label htmlFor="mail_host" className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">SMTP Host <span
                        className="text-danger">*</span></label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input type="text" name="mail_host" id="mail_host" className="form-control"
                               value={data.mail_host} onChange={e => setData('mail_host', e.target.value)}/>
                        {errors.mail_host && (
                            <div className="invalid-feedback d-block">
                                {errors.mail_host}
                            </div>
                        )}
                    </div>
                </div>

                <div className="row mb-3">
                    <label htmlFor="mail_port" className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">SMTP Port <span
                        className="text-danger">*</span></label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input type="text" id="mail_port" name="mail_port" className="form-control"
                               value={data.mail_port} onChange={e => setData('mail_port', e.target.value)}/>
                        {errors.mail_port && (
                            <div className="invalid-feedback d-block">
                                {errors.mail_port}
                            </div>
                        )}
                    </div>
                </div>

                <div className="row mb-3">
                    <label htmlFor="color" className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">SMTP Security <span
                        className="text-danger">*</span></label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <select name="mail_encryption" defaultValue={data.mail_encryption?.toLowerCase()}
                                onChange={e => setData('mail_encryption', e.target.value)}
                                className="form-control cursor-pointer">
                            <option value="">Choose</option>
                            <option value="tls">TLS</option>
                            <option value="ssl">SSL</option>
                        </select>
                        {errors.mail_encryption && (
                            <div className="invalid-feedback d-block">
                                {errors.mail_encryption}
                            </div>
                        )}
                    </div>
                </div>

                <div className="row mb-3">
                    <label htmlFor="mail_username" className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label">SMTP Username <span
                        className="text-danger">*</span></label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input type="text" id="mail_username" className="form-control" name="mail_username"
                               value={data.mail_username} onChange={e => setData('mail_username', e.target.value)}/>
                        {errors.mail_username && (
                            <div className="invalid-feedbackd-block">
                                {errors.mail_username}
                            </div>
                        )}
                    </div>
                </div>

                <div className="row mb-3">
                    <label
                        htmlFor="mail_password"
                        className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                    >
                        SMTP Password <span className="text-danger">*</span>
                    </label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input
                            type="password"
                            id="mail_password"
                            className="form-control"
                            name="mail_password"
                            value={data.mail_password}
                            onChange={(e) =>
                                setData("mail_password", e.target.value)
                            }
                        />
                        {errors.mail_password && (
                            <div className="invalid-feedback d-block">
                                {errors.mail_password}
                            </div>
                        )}
                    </div>
                </div>

                <div className="row mb-3">
                    <label
                        htmlFor="mail_from_address"
                        className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                    >
                        From Address <span className="text-danger">*</span>
                    </label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input
                            type="text"
                            id="mail_from_address"
                            className="form-control"
                            name="mail_from_address"
                            value={data.mail_from_address}
                            onChange={(e) =>
                                setData("mail_from_address", e.target.value)
                            }
                        />
                        {errors.mail_from_address && (
                            <div className="invalid-feedback d-block">
                                {errors.mail_from_address}
                            </div>
                        )}
                    </div>
                </div>

                <div className="row mb-3">
                    <label
                        htmlFor="mail_from_name"
                        className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label"
                    >
                        From Name <span className="text-danger">*</span>
                    </label>
                    <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <input
                            type="text"
                            id="mail_from_name"
                            className="form-control"
                            name="mail_from_name"
                            value={data.mail_from_name}
                            onChange={(e) =>
                                setData("mail_from_name", e.target.value)
                            }
                        />
                        {errors.mail_from_name && (
                            <div className="invalid-feedback d-block">
                                {errors.mail_from_name}
                            </div>
                        )}
                    </div>
                </div>

                <div className="save-button d-flex justify-content-end my-3">
                    {is_mail_testable ? (
                        <Link href={connection_test_route} className="btn btn-primary width-lg secondary-bg-color">Test
                            Connection</Link>
                    ) : null}
                    <button type="submit" className="btn btn-primary width-lg secondary-bg-color ms-3"
                            disabled={processing}>{processing ? 'Saving' : 'Save'}</button>
                </div>
            </form>
        </div>
    );
};

export default SMTPSettingsTab;
