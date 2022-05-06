import React, {useEffect} from "react";

import {Link, useForm} from "@inertiajs/inertia-react";
import {useSelector} from "react-redux";

import AppLayout from "../../../layouts/app-layout/AppLayout";
import BreadcumbComponent from "../../../common/breadcumb/Breadcumb";

const breadcrumbs = {
    title: 'Create Questionnaire',
    breadcumbs: [
        {
            "title": "Third Party Risk",
            "href": ""
        },
        {
            "title": "Questionnaires",
            "href": route('third-party-risk.questionnaires.index')
        },
        {
            "title": "Create",
            "href": ""
        }
    ]
};

const Create = () => {
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);
    const {data, setData, errors, processing, post} = useForm({
        name: '',
        version: '',
        data_scope: null
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('third-party-risk.questionnaires.store'));
    };

    useEffect(() => {
        setData('data_scope', appDataScope);
    }, [appDataScope]);

    return (
        <AppLayout>
            <BreadcumbComponent data={breadcrumbs}/>
            <div className="row">
                <div className="col-xl-12">
                    <div className="card">
                        <div className="card-body">
                            <h5 className="sub-header">Fields with <span className="text-danger">*</span> are required.
                            </h5>
                            <form onSubmit={handleSubmit}>
                                <div className="tab-pane">
                                    <div className="row">
                                        <div className="col-12">
                                            <div className="row mb-3">
                                                <label className="col-md-3 form-label col-form-label" htmlFor="name">Name <span
                                                    className="text-danger">*</span></label>
                                                <div className="col-md-9">
                                                    <input
                                                        value={data.name}
                                                        onChange={e => setData('name', e.target.value)}
                                                        className="form-control"
                                                        id="name"
                                                        placeholder="Name"
                                                    />
                                                    {errors.name && (
                                                        <div className="invalid-feedback d-block">
                                                            {errors.name}
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                            <div className="row mb-3">
                                                <label className="col-md-3 form-label col-form-label"
                                                       htmlFor="version">Version <span className="text-danger">*</span></label>
                                                <div className="col-md-9">
                                                    <input
                                                        value={data.version}
                                                        onChange={e => setData('version', e.target.value)}
                                                        className="form-control"
                                                        id="version"
                                                        placeholder="Version"
                                                    />
                                                    {errors.version && (
                                                        <div className="invalid-feedback d-block">
                                                            {errors.version}
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                            <ul className="list-inline mb-0 wizard">
                                                <li className="next list-inline-item float-end">
                                                    <Link href={route('third-party-risk.questionnaires.index')}>
                                                        <button type="button" className="btn btn-danger back-btn">Back
                                                            To List
                                                        </button>
                                                    </Link>
                                                    <button
                                                        className="btn btn-primary ms-2"
                                                        type="submit"
                                                        disabled={processing}
                                                    >
                                                        {processing ? 'Creating' : 'Create'}
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </AppLayout>
    )
};

export default Create;
