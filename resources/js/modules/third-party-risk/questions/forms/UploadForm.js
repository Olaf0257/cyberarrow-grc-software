import React, {useEffect, useState} from "react";

import {useForm, usePage} from "@inertiajs/inertia-react";

import CustomDropify from "../../../../common/custom-dropify/CustomDropify";

const UploadForm = () => {
    const {questionnaire} = usePage().props;
    const [validationErrors, setValidationErrors] = useState(null);
    const {data, setData, errors, processing, post} = useForm({
        csv_file: null
    });

    const handleSubmit = e => {
        e.preventDefault();
        post(route('third-party-risk.questionnaires.questions.batch-import', [questionnaire.id]), {
            onStart: () => setValidationErrors(null),
            onError: (err) => setValidationErrors(Object.values(err))
        });
    };

    const handleOnSelect = file => setData(previousData => ({
        ...previousData,
        'csv_file': file
    }));

    useEffect(() => {
        setValidationErrors(null);
    }, [data])

    return (
        <form onSubmit={handleSubmit}>
            <div className="table-end">
                <h4>Upload Question CSV</h4>
                <h5> Upload a CSV file to create new questions</h5>
                <div className="mt-3 mb-3">
                    <CustomDropify
                        file={data.csv_file}
                        onSelect={handleOnSelect}
                        accept=".csv"
                    />
                </div>
                {errors.csv_file && (
                    <div className="invalid-feedback d-block mb-3">
                        {errors.csv_file}
                    </div>
                )}
                <button className="btn btn-primary me-2" type="submit"
                        disabled={processing}>{processing ? 'Uploading' : 'Upload'}</button>
                <a href={route('third-party-risk.questionnaires.questions.download-sample')}
                   className="btn btn-primary">Download Sample</a>
                {validationErrors && (
                    <div className="alert alert-danger mt-3" role="alert">
                        <h5 className="alert-heading text-uppercase">The following errors occured</h5>
                        <ul>
                            {validationErrors.map((error, i) => <li key={i}>{error}</li>)}
                        </ul>
                    </div>
                )}
                <div className="cv-info">
                    <h5 className="text-uppercase text-white">The csv file should have the following:</h5>
                    <ul>
                        <li>question (required): 825 character limit</li>
                        <li>
                            domain (required):
                            <ul>
                                <li>Information Security Management and Governance {"=>"} 1</li>
                                <li>Human Resources Security {"=>"} 2</li>
                                <li>Information and Asset Management {"=>"} 3</li>
                                <li>Access Control {"=>"} 4</li>
                                <li>System acquisition, development and maintenance {"=>"} 5</li>
                                <li>Environmental and Physical Security {"=>"} 6</li>
                                <li>Operations, Systems and Communication Management {"=>"} 7</li>
                                <li>Supplier Relationships {"=>"} 8</li>
                                <li>Incident Management {"=>"} 9</li>
                                <li>Business Continuity {"=>"} 10</li>
                                <li>Compliance and Audit {"=>"} 11</li>
                                <li>Cloud Security {"=>"} 12</li>
                                <li>Other {"=>"} 13</li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </form>
    )
};

export default UploadForm;
