import React from "react";

import {Link, useForm, usePage} from "@inertiajs/inertia-react";
import Select from "react-select";

const CreateForm = () => {
    const {domains, questionnaire} = usePage().props;
    const {data, setData, errors, post, processing} = useForm({
        text: '',
        domain_id: null
    });

    const handleSubmit = e => {
        e.preventDefault();
        post(route('third-party-risk.questionnaires.questions.store', [questionnaire.id]));
    }

    return (
        <div className="table-left">
            <h4>Create a New Question</h4>
            <h5 className="mb-3 sub-header">Fields with <span className="text-danger">*</span> are
                required.</h5>
            <form onSubmit={handleSubmit} method="post">
                <div className="mb-3">
                    <label className="form-label" htmlFor="text">Question<span>*</span></label>
                    <input
                        type="text"
                        value={data.text}
                        onChange={e => setData('text', e.target.value)}
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
                    <label htmlFor="domain">Domain<span>*</span></label>
                    <Select
                        options={domains.map(domain => ({label: domain.name, value: domain.id}))}
                        onChange={domain => setData('domain_id', domain.value)}
                    />
                    {errors.domain_id && (
                        <div className="invalid-feedback d-block">
                            {errors.domain_id}
                        </div>
                    )}
                </div>
                <button className="btn btn-primary me-2" type="submit" disabled={processing}>{processing ? 'Creating' : 'Create'}</button>
                <Link href={route('third-party-risk.questionnaires.questions.index', [questionnaire.id])} className="btn btn-danger">Back to List</Link>
            </form>
        </div>
    )
};

export default CreateForm;
