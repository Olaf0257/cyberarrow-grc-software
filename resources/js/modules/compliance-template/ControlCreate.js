import React from 'react';
import { usePage } from '@inertiajs/inertia-react';
import ComplianceTemplate from './ComplianceTemplate';
import ControlCreateForm from './ControlCreateForm';
import ControlCreateUploadForm from './ControlCreateUploadForm';
import FlashMessages from "../../common/FlashMessages";
import './controls.scss';

export default function ControlCreate(props) {

    const propsData = usePage().props;
    const standardControl = propsData.control;
    const standard = propsData.standard;
    const idSeparators = propsData.idSeparators;
    const id = standardControl ? standardControl.id : null;
    const errors = propsData.errors;
    let error = propsData.flash.error ? propsData.flash.error : '';

    const breadcumbsData = {
        "title": `${id ? 'Edit' : 'Create'} Control`,
        "breadcumbs": [
            {
                "title": "Administration",
                "href": ""
            },
            {
                "title": "Compliance Template",
                "href": route('compliance-template-view')
            },
            {
                "title": "Controls",
                "href": route('compliance-template-view-controls', [standardControl.standard.id])
            },
            {
                "title": `${id ? 'Edit' : 'Create'}`,
                "href": ""
            },
        ]
    };

    return (
        <ComplianceTemplate breadcumbsData={breadcumbsData}>
            {
                error &&
                <FlashMessages />
            }

            <section id="table">
                <div className="row bg-white py-3 px-2">
                    <ControlCreateForm standardControl={standardControl} standard={standard} idSeparators={idSeparators} errors={errors} />
                    <ControlCreateUploadForm standardControl={standardControl} standard={standard} idSeparators={idSeparators} />
                </div>
            </section>

        </ComplianceTemplate>
    );
}