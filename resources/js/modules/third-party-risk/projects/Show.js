import React, {useEffect, useRef, useState} from 'react';

import {Inertia} from "@inertiajs/inertia";
import {useDispatch, useSelector} from "react-redux";
import {Link, usePage} from "@inertiajs/inertia-react";
import {transformDateTime} from "../../../utils/date";
import fileDownload from "js-file-download";

import AppLayout from "../../../layouts/app-layout/AppLayout";
import BreadcumbComponent from "../../../common/breadcumb/Breadcumb";
import FlashMessages from "../../../common/FlashMessages";
import DataTable from "../../../common/data-table/DataTable";
import Dropdown from "react-bootstrap/Dropdown";
import Chart from 'react-apexcharts';
import './styles/style.css';

const Show = () => {
    const [sending, setSending] = useState(false);
    const {project} = usePage().props;

    const dispatch = useDispatch();
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);

    const dataScopeRef = useRef(appDataScope);
    useEffect(() => {
        if (dataScopeRef.current !== appDataScope) {
            Inertia.get(route('third-party-risk.projects.index'));
        }
    }, [appDataScope]);

    const breadcrumbs = {
        title: 'Project - Third Party Risk',
        breadcumbs: [
            {
                title: 'Third Party Risk',
                href: route('third-party-risk.dashboard')
            },
            {
                title: 'Projects',
                href: route('third-party-risk.projects.index')
            },
            {
                title: 'Show',
                href: ''
            }
        ]
    };

    const options = {
        chart: {
            height: 280,
            type: "radialBar",
        },
        fill: {
            colors: [function ({value}) {
                if (value >= 0 && value <= 20) {
                    return '#ff0000';
                } else if (value >= 21 && value <= 40) {
                    return '#ffc000';
                } else if (value >= 41 && value <= 60) {
                    return '#ffff00';
                } else if (value >= 61 && value <= 80) {
                    return '#92d050';
                } else {
                    return '#00b050';
                }
            }]
        },
        plotOptions: {
            radialBar: {
                hollow: {
                    margin: 15,
                    size: "70%"
                },

                dataLabels: {
                    showOn: "always",
                    name: {
                        offsetY: -10,
                        show: true,
                        color: "#888",
                        fontSize: "13px"
                    },
                    value: {
                        offsetY: 3,
                        color: "#111",
                        fontSize: "30px",
                        show: true
                    }
                }
            }
        },
        grid: {
            padding: {
                top: -20,
                bottom: -15
            }
        },
        labels: ["Vendor Score"]
    };

    const columns = [
        {accessor: 'text', label: 'Question'},
        {
            accessor: 'single_answer',
            label: 'Answer',
            CustomComponent: ({row}) =>
                <span>{row.single_answer ? row.single_answer.answer : 'Not answered yet.'}</span>
        }
    ]

    const handleSendReminder = () => {
        setSending(true);
        Inertia.post(route('third-party-risk.projects.send-project-reminder', [project.id]), null, {
            onFinish: () => setSending(false)
        });
    };

    const handleExportCSV = () => {
        dispatch({type: "reportGenerateLoader/show"});
        axiosFetch.get(route('third-party-risk.projects.export-csv', [project.id]), {
            responseType: 'blob'
        }).then(res => {
            fileDownload(res.data, 'project-details.csv');
        }).finally(() => {
            dispatch({type: "reportGenerateLoader/hide"});
        })

    }

    const handleExportPDF = () => {
        dispatch({type: "reportGenerateLoader/show"});
        axiosFetch.get(route('third-party-risk.projects.export-pdf', [project.id]), {
            responseType: 'blob'
        }).then(res => {
            fileDownload(res.data, 'third-party-project-details.pdf');
        }).finally(() => {
            dispatch({type: "reportGenerateLoader/hide"});
        })

    }

    return (
        <AppLayout>
            <BreadcumbComponent data={breadcrumbs}/>
            <FlashMessages/>
            <div className="row">
                <div className="col-12">
                    <div className='card'>
                        <div className="card-body campaign-brief-details">
                            <div className="campaign-brief-details-inner">
                                <div className="clearfix">
                                    <Dropdown className='float-end cursor-pointer'>
                                        <Dropdown.Toggle className="btn btn-primary theme-bg-secondary " variant="success">
                                            Export
                                        </Dropdown.Toggle>

                                        <Dropdown.Menu className="dropdown-menu-end">
                                            <Dropdown.Item onClick={handleExportPDF}>PDF</Dropdown.Item>
                                            <Dropdown.Item onClick={handleExportCSV}>CSV</Dropdown.Item>
                                        </Dropdown.Menu>
                                    </Dropdown>
                                </div>
                                <h3>Result for {project.name}</h3>
                                <div className="row mt-2">
                                    <div className="col-md-6 col-sm-12">
                                        <ul className="list-group campaign-info-list list-group-flush campaign-card-date">
                                            <li className="list-group-item border-0 ps-0">
                                                <strong>Start Date: </strong>
                                                <span className="text-muted">{transformDateTime(project.launch_date)}</span>
                                            </li>
                                            <li className="list-group-item border-0 ps-0">
                                                <strong>Due date: </strong>
                                                <span className="text-muted">{transformDateTime(project.due_date)}</span>
                                            </li>
                                            <li className="list-group-item border-0 ps-0">
                                                <strong>Vendor: </strong>
                                                <span>{project.vendor.contact_name}</span>
                                            </li>
                                            <li className="list-group-item border-0 ps-0">
                                                <strong>Questionnaire: </strong>
                                                <Link
                                                    href={route('third-party-risk.questionnaires.questions.index', [project.questionnaire.id])}
                                                    className="badge bg-soft-info text-info">{project.questionnaire.name}</Link>
                                            </li>
                                            <li className="list-group-item border-0 ps-0">
                                                <strong>Frequency: </strong>
                                                <span>{project.frequency}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div className="col-md-6 col-sm-12">
                                        <Chart options={options} series={[project.score]} type="radialBar" height={250}/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="col-xl-12">
                    <div className='card'>
                        <div className="card-body">
                            <div className="top-risk pb-1 mb-2 align-items-center d-flex justify-content-between">
                                <h4 className="top-risk-text">
                                    Details
                                </h4>
                                <button className="btn btn-sm btn-primary" onClick={handleSendReminder}
                                        disabled={sending}>Send Reminder
                                </button>
                            </div>
                            <DataTable
                                fetchURL={route('third-party-risk.projects.get-project-answers', [project.id])}
                                columns={columns}
                                search
                            />
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    )
};

export default Show;
