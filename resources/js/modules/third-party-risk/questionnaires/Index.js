import React, {useEffect, useState} from 'react';

import {useSelector} from "react-redux";
import {Inertia} from "@inertiajs/inertia";
import {Link} from "@inertiajs/inertia-react";

import AppLayout from "../../../layouts/app-layout/AppLayout";
import DataTable from "../../../common/data-table/DataTable";
import BreadcumbComponent from "../../../common/breadcumb/Breadcumb";
import FlashMessages from "../../../common/FlashMessages";
import {transformDate} from "../../../utils/date";
import { Dropdown } from 'react-bootstrap';

const breadcrumbs = {
    title: 'View Questionnaires',
    breadcumbs: [
        {
            "title": "Third Party Risk",
            "href": ""
        },
        {
            "title": "Questionnaires",
            "href": route('third-party-risk.questionnaires.index')
        }
    ]
};

const Index = () => {
    const [refreshToggle, setRefreshToggle] = useState(false);
    const fetchUrl = route('third-party-risk.questionnaires.get-json-data');
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);

    useEffect(() => {
        document.title = "Third Party Risk Questionnaires";
    }, []);

    const handleDeleteQuestionnaire = id => {
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
                Inertia.post(route('third-party-risk.questionnaires.destroy', [id]), {_method: 'delete'}, {
                    onFinish: () => setRefreshToggle(!refreshToggle)
                });
            }
        })
    }

    const columns = [
        {accessor: 'name', label: 'Name', position: 1, sortable: false},
        {accessor: 'version', label: 'Version', position: 2, sortable: false},
        {
            accessor: 'questions_count',
            label: 'Questions',
            position: 3,
            sortable: false,
            CustomComponent: ({row}) => (<span className="badge bg-info">{row.questions_count} questions</span>)
        },
        {
            accessor: 'created_at',
            label: 'Created On',
            position: 4,
            sortable: false,
            CustomComponent: ({row}) => (<span>{transformDate(row.created_at)}</span>)
        },
        {
            accessor: 'actions',
            label: 'Actions',
            position: 5,
            sortable: false,
            CustomComponent: ({row}) => (
                <Dropdown className='d-inline-block'>
                    <Dropdown.Toggle
                        as="a"
                        bsPrefix="card-drop arrow-none cursor-pointer"
                    >
                        <i className="mdi mdi-dots-horizontal m-0 text-muted h3" />
                    </Dropdown.Toggle>
                    <Dropdown.Menu className="dropdown-menu-end">
                        <Link
                           
                            className="dropdown-item d-flex align-items-center"
                            href={route('third-party-risk.questionnaires.questions.index', [row.id])}
                        >
                            <i className="mdi mdi-eye-outline font-18 me-1"/> View
                        </Link>

                        <Link
                            
                            className="dropdown-item d-flex align-items-center"
                            href={route('third-party-risk.questionnaires.duplicate.index', [row.id])}
                        >
                            <i className="mdi mdi-content-copy font-18 me-1"/> Duplicate Questionnaire
                        </Link>
                        {!row.is_default ? (
                            <>
                                <Link
                                    className="dropdown-item d-flex align-items-center"
                                    href={route('third-party-risk.questionnaires.questions.create', [row.id])}
                                >
                                    <i className="mdi mdi-plus-box-outline font-18 me-1"/> Add Question
                                </Link>
                                <Link
                                    className="dropdown-item d-flex align-items-center"
                                    href={route('third-party-risk.questionnaires.edit', [row.id])}
                                >
                                    <i className="mdi mdi-pencil-outline font-18 me-1"/> Edit Information
                                </Link>
                                <button
                                    className="dropdown-item d-flex align-items-center"
                                    onClick={() => handleDeleteQuestionnaire(row.id)}
                                >
                                    <i className="mdi mdi-delete-outline font-18 me-1"/> Delete
                                </button>
                            </>
                        ): null }
                    </Dropdown.Menu>
                </Dropdown>
                // <div className="btn-group">
                //     <Link
                //         title="View"
                //         className="btn btn-secondary btn-xs waves-effect waves-light"
                //         href={route('third-party-risk.questionnaires.questions.index', [row.id])}
                //     >
                //         <i className="fe-eye"/>
                //     </Link>

                //     <Link
                //         title="Duplicate Questionnaire"
                //         className="btn btn-primary btn-xs waves-effect waves-light"
                //         href={route('third-party-risk.questionnaires.duplicate.index', [row.id])}
                //     >
                //         <i className="far fa-plus-square"/>
                //     </Link>
                //     {!row.is_default ? (
                //         <>
                //             <Link
                //                 title="Add question(s)"
                //                 className="btn btn-warning btn-xs waves-effect waves-light"
                //                 href={route('third-party-risk.questionnaires.questions.create', [row.id])}
                //             >
                //                 <i className="fa fa-plus"/>
                //             </Link>
                //             <Link
                //                 title="Edit Information"
                //                 className="btn btn-info btn-xs waves-effect waves-light"
                //                 href={route('third-party-risk.questionnaires.edit', [row.id])}
                //             >
                //                 <i className="fe-edit"/>
                //             </Link>
                //             <button
                //                 title="Delete"
                //                 className="btn btn-danger btn-xs waves-effect waves-light"
                //                 onClick={() => handleDeleteQuestionnaire(row.id)}
                //             >
                //                 <i className="fe-trash-2"/>
                //             </button>
                //         </>
                //     ) : null}
                // </div>
            )
        }
    ];

    useEffect(() => {
        setRefreshToggle(!refreshToggle);
    }, [appDataScope]);

    return (
        <AppLayout>
            <BreadcumbComponent data={breadcrumbs}/>
            <FlashMessages/>
            <div className="row">
                <div className="col-12">
                    <div className='card'>
                        <div className="card-body">
                            <Link
                                href={route('third-party-risk.questionnaires.create')}
                                className="btn btn-sm btn-primary waves-effect waves-light float-end"
                            >
                                <i className="mdi mdi-plus-circle" title="Add New Questionnaire"/>&nbsp;
                                Add New Questionnaire
                            </Link>
                            <h4 className="header-title mb-4">Manage Questionnaires</h4>

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
