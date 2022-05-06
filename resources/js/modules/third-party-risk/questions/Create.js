import React, {useEffect, useRef} from "react";

import {useSelector} from "react-redux";
import {usePage} from "@inertiajs/inertia-react";
import {Inertia} from "@inertiajs/inertia";

import AppLayout from "../../../layouts/app-layout/AppLayout";
import BreadcumbComponent from "../../../common/breadcumb/Breadcumb";
import UploadForm from "./forms/UploadForm";
import CreateForm from "./forms/CreateForm";

const Create = () => {
    const {questionnaire} = usePage().props;
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);

    const breadcrumbs = {
        title: 'Create Question',
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
                "title": "Create",
                "href": ""
            }
        ]
    };

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
                    <div className="col-xl-6">
                        <CreateForm/>
                    </div>
                    <div className="col-xl-6">
                        <UploadForm/>
                    </div>
                </div>
            </section>
        </AppLayout>
    )
};

export default Create;
