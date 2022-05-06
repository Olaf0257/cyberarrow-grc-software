import React, {useEffect, useRef} from 'react';

import {Inertia} from "@inertiajs/inertia";
import {Link, useForm, usePage} from "@inertiajs/inertia-react";
import {useSelector} from "react-redux";

import AppLayout from "../../../layouts/app-layout/AppLayout";
import BreadcumbComponent from "../../../common/breadcumb/Breadcumb";
import Select from "react-select";

const Edit = () => {
    const {question, questionnaire, domains} = usePage().props;
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);
    const {data, setData, errors, processing, put} = useForm({
        'text': question.text,
        'domain_id': question.domain_id
    });

    const breadcrumbs = {
        title: 'Edit Question',
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
                "title": "Questions",
                "href": route('third-party-risk.questionnaires.questions.index', [questionnaire.id])
            },
            {
                "title": "Edit",
                "href": ""
            }
        ]
    };

    const options = domains.map(domain => ({label: domain.name, value: domain.id}));

    const handleSubmit = e => {
        e.preventDefault();
        put(route('third-party-risk.questionnaires.questions.update', [questionnaire.id, question.id]));
    }

    const dataScopeRef = useRef(appDataScope);
    useEffect(() => {
        if (dataScopeRef.current !== appDataScope) {
            Inertia.get(route('third-party-risk.questionnaires.index'));
        }
    }, [appDataScope]);

    return (
        <AppLayout>
            <BreadcumbComponent data={breadcrumbs}/>
            <section id="table">
                <div className="row bg-white py-3 px-2">
                    <div className="col-xl-12">
                        <div className="table-left">
                            <h4>Update Question</h4>
                            <h5 className="mb-3 sub-header">Fields with <span className="text-danger">*</span> are
                                required.</h5>
                            <form onSubmit={handleSubmit} method="post">
                                <div className="mb-3">
                                    <label className='form-label' htmlFor="text">Question<span>*</span></label>
                                    <input
                                        value={data.text}
                                        onChange={e => setData('text', e.target.value)}
                                        type="text"
                                        className="form-control"
                                        id="text"
                                    />
                                    {errors.text && (
                                        <div className="invalid-feedback d-block">
                                            {errors.text}
                                        </div>
                                    )}
                                </div>
                                <div className="mb-3">
                                    <label htmlFor="text">Domain<span>*</span></label>
                                    <Select
                                        defaultValue={options.find(o => o.value === data.domain_id)}
                                        options={options}
                                        onChange={domain => setData('domain_id', domain.value)}
                                    />
                                    {errors.domain_id && (
                                        <div className="invalid-feedback d-block">
                                            {errors.domain_id}
                                        </div>
                                    )}
                                </div>

                                <div className="mt-4">
                                    <button type="submit" className="btn btn-primary me-2"
                                            disabled={processing}>{processing ? 'Saving' : 'Save'}</button>
                                    <Link className="btn btn-danger"
                                          href={route('third-party-risk.questionnaires.questions.index', [questionnaire.id])}>Back
                                        to List</Link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </AppLayout>
    )
};

export default Edit;
