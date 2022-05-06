import React, {useEffect, useRef, useState} from 'react';

import {useSelector} from "react-redux";
import {Link, usePage} from "@inertiajs/inertia-react";
import {Inertia} from "@inertiajs/inertia";

import AppLayout from "../../../layouts/app-layout/AppLayout";
import DataTable from "../../../common/data-table/DataTable";
import BreadcumbComponent from "../../../common/breadcumb/Breadcumb";
import FlashMessages from "../../../common/FlashMessages";

import './styles/style.css';

const Index = () => {
    const [refreshToggle, setRefreshToggle] = useState(false);
    const {questionnaire} = usePage().props;
    const fetchUrl = route('third-party-risk.questionnaires.questions.get-json-data', [questionnaire.id]);
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);

    const breadcrumbs = {
        title: 'View Questions',
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
            }
        ]
    };

    const handleDeleteQuestion = id => {
        AlertBox({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            confirmButtonColor: '#f1556c',
            allowOutsideClick: false,
            icon: 'warning',
            iconColor: '#f1556c',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }, function (result) {
            if (result.isConfirmed) {
                Inertia.post(route('third-party-risk.questionnaires.questions.destroy', [questionnaire.id, id]), {_method: 'delete'}, {
                    onFinish: () => setRefreshToggle(!refreshToggle)
                });
            }
        })
    }

    const columns = [
        {accessor: 'text', label: 'Question', position: 1, sortable: false},
        {
            accessor: 'domain-name',
            label: 'Domain',
            position: 2,
            sortable: false,
            CustomComponent: ({row}) => (<span>{row.domain.name}</span>)
        }
    ];

    if (!questionnaire.is_default) {
        columns.push({
            accessor: 'actions',
            label: 'Actions',
            position: 3,
            sortable: false,
            CustomComponent: ({row}) => (
                <div className="btn-group">
                    <Link
                        title="Edit Information"
                        className="btn btn-info btn-xs waves-effect waves-light"
                        href={route('third-party-risk.questionnaires.questions.edit', [questionnaire.id, row.id])}
                    >
                        <i className="fe-edit"/>
                    </Link>
                    <button
                        title="Delete"
                        className="btn btn-danger btn-xs waves-effect waves-light"
                        onClick={() => handleDeleteQuestion(row.id)}
                    >
                        <i className="fe-trash-2"/>
                    </button>
                </div>
            )
        })
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
            <FlashMessages/>
            <div className="row">
                <div className="col-12">
                    <div className='card'>
                        <div className="card-body">
                            {!questionnaire.is_default ? (
                                <Link
                                    href={route('third-party-risk.questionnaires.questions.create', [questionnaire.id])}
                                    className="btn btn-sm btn-primary waves-effect waves-light float-end"
                                >
                                    <i className="mdi mdi-plus-circle" title="Add New Questionnaire"/>&nbsp;
                                    Add New Question
                                </Link>
                            ) : null}
                            <h4 className="header-title mb-4">Manage Questions</h4>

                            <DataTable
                                columns={columns}
                                fetchURL={fetchUrl}
                                refresh={refreshToggle}
                                search
                            />
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    )
};

export default Index;
