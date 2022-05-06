import React, {useEffect, useState} from "react";

import {useSelector, useDispatch} from "react-redux";
import fileDownload from "js-file-download";

import AppLayout from "../../layouts/app-layout/AppLayout";
import DataTable from "../../common/data-table/DataTable";
import Chart from "react-apexcharts";

import "../third-party-risk/style/style.scss";
import "./dashboard.scss"
import {Link} from "@inertiajs/inertia-react";

const defaultLevels = [
    {
        color: "#ff0000",
        count: 0,
        name: "Level 1",
    },
    {
        color: "#ffc000",
        count: 0,
        name: "Level 2",
    },
    {
        color: "#ffff00",
        count: 0,
        name: "Level 3",
    },
    {
        color: "#92d050",
        count: 0,
        name: "Level 4",
    },
    {
        color: "#00b050",
        count: 0,
        name: "Level 5",
    },
];
const defaultProgress = {
    "Overdue": 0,
    "Completed": 0,
    "In Progress": 0,
    "Not Started": 0
}

const Dashboard = () => {
    const [vendorLevels, setVendorLevels] = useState(defaultLevels);
    const [projectsProgress, setProjectsProgress] = useState(defaultProgress);
    const [refreshToggle, setRefreshToggle] = useState(false);

    const dispatch = useDispatch();
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);

    useEffect(() => {
        setRefreshToggle(!refreshToggle);
        axiosFetch.get(route('third-party-risk.dashboard.get-vendors-data'), {
            params: {
                data_scope: appDataScope,
            }
        })
            .then(({data: {levels, projects_progress}}) => {
                setVendorLevels(levels);
                setProjectsProgress(projects_progress);
            })
    }, [appDataScope]);

    useEffect(() => {
        document.title = "KPI Dashboard";
    }, []);

    const progressChartOptions = {
        plotOptions: {
            radialBar: {
                dataLabels: {
                    name: {
                        fontSize: "6px",
                        fontFamily: "Arial",
                    },
                    value: {
                        fontSize: "20px",
                        fontWeight: 400,
                        fontFamily: "Arial",
                        formatter: function (val) {
                            return val;
                        },
                    },
                    total: {
                        show: true,
                        label: Object.keys(projectsProgress)[1],
                        fontSize: "20px",
                        fontFamily: "Arial",
                        fontWeight: 500,
                        formatter: function (w) {
                            // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                            return w.config.series[1];
                        },
                    },
                },
            },
        },
        labels: Object.keys(projectsProgress),
        colors: ["#414141", "#5bc0de", "#359f1d", "#cf1110"],
    }

    const vendorLevelChartOptions = {
        chart: {
            zoom: {
                enabled: true,
                type: "x",
                autoScaleYaxis: false,
                zoomedArea: {
                    fill: {
                        color: "#90CAF9",
                        opacity: 0.4,
                    },
                    stroke: {
                        color: "#0D47A1",
                        opacity: 0.4,
                        width: 1,
                    },
                },
            },
        },
        colors: vendorLevels.map(v => v.color),
        labels: vendorLevels.map(v => v.name),
        dataLabels: {
            enabled: false,
        },

        plotOptions: {
            pie: {
                expandOnClick: true,
                donut: {
                    size: "70%",
                    background: "transparent",
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: "25px",
                            color: "black",
                        },
                        total: {
                            show: true,
                            label: vendorLevels.map(v => v.name)[1],
                            formatter: function (w) {
                                return w.config.series[1];
                            },
                        },
                    },
                },
            },
        },
        tooltip: {
            enabled: false,
        },
    };

    const columns = [
        {
            accessor: "name",
            label: "Vendor Name",
            priorityLevel: 1,
            position: 1,
            minWidth: 150,
        },
        {
            accessor: "score",
            label: "Score",
            priorityLevel: 1,
            position: 2,
            minWidth: 150,
        },
        {
            accessor: "maturity",
            label: "Maturity",
            priorityLevel: 1,
            position: 3,
            minWidth: 50,
            CustomComponent: ({row}) => {
                return (
                    <>
                        <span
                            className="badge text-white"
                            style={{
                                textOverflow: "ellipsis",
                                overflow: "hidden",
                                backgroundColor:
                                    row.level === 1
                                        ? "#ff0000"
                                        : row.level === 2
                                            ? "#ffc000"
                                            : row.level === 3
                                                ? "#ffff00"
                                                : row.level === 4
                                                    ? "#92d050"
                                                    : "#00b050",
                            }}
                        >
                            {`Level ${row.level}`}
                        </span>
                    </>
                );
            },
        },
        {
            accessor: "status",
            label: "Status",
            priorityLevel: 1,
            position: 4,
            minWidth: 50,
            CustomComponent: ({row}) => {
                return (
                    <span
                        className={`badge ${row.status === 'active' ? 'bg-success' : 'bg-dark'}`}
                        style={{
                            textOverflow: "ellipsis",
                            overflow: "hidden",
                        }}
                    >
                                {row.status === 'active' ? 'Active' : 'Disabled'}
                            </span>
                );
            },
        },
        {
            accessor: "contact_name",
            label: "Contact Name",
            priorityLevel: 1,
            position: 5,
            minWidth: 150,
        }
    ];

    const handleExportPDF = () => {
        dispatch({type: "reportGenerateLoader/show"});
        axiosFetch.get(route('third-party-risk.dashboard.export-pdf'), {
                responseType: 'blob'
            }
        ).then(res => {
            fileDownload(res.data, 'third-party-risk.pdf');
        }).finally(() => {
            dispatch({type: "reportGenerateLoader/hide"});
        })
    }

    return (
        <AppLayout>
            <div className="position-relative mt-5">
                <div id="third-party-risk-page">
                    {/* <div className="row">
                    <div className="col-12">
                        <div className="page-title-box">
                            <div className="page-title-right">
                                <button
                                    type="button"
                                    onClick={handleExportPDF}
                                    className="btn btn-primary risk-export_btn width-md"
                                >
                                    Export to PDF
                                </button>
                            </div>

                            <h4 className="page-title">Dashboard</h4>
                        </div>
                    </div>
                </div> */}
                    <div className="row">
                        <div className="col-xl-12">
                            <div className="risk-stat-div pb-1">
                                <h4 className="risk-stat-text">
                                    Summary - Vendor Maturity
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div className="row row-cols-1 row-cols-md-2 row-cols-lg-5 g-3">
                        {vendorLevels.map(function (level, index) {
                            return (
                                <div className="col" key={index}>
                                    <div className="card mb-0">
                                        <div className="card-body">
                                            <div className="widget-rounded-circle">
                                                <div className="row">
                                                    <div className="col-6">
                                                        <div
                                                            className="avatar-lg rounded-circle vulnerability__icon"
                                                            style={{
                                                                background: level.color,
                                                            }}
                                                        >
                                                            <i
                                                                id="alert_icon"
                                                                className="icon fa fa-user-shield"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div className="col-6">
                                                        <div className="text-end">
                                                            <h3 className="text-dark mt-1">
                                                                {level.count}
                                                            </h3>
                                                            <p className="text-muted mb-1 text-truncate">
                                                                {level.name}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                    <div className="row">
                        <div className="col-xl-12">
                            {/* <!-- pie charts --> */}
                            <div className="pie-charts">
                                <div className="row">
                                    <div className="col-xl-6">
                                        <div className="card mt-3 mb-0 mb-xl-3">
                                            <div className="card-body">
                                                <div className="donut-pie-chart">
                                                    <h4 className="header-title">
                                                        Vendors on the basis of maturity
                                                    </h4>
                                                    <Chart
                                                        options={vendorLevelChartOptions}
                                                        series={vendorLevels.map(l => l.count)}
                                                        type="donut"
                                                        height={260}
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-xl-6">
                                        <div className="card mt-3">
                                            <div className="card-body">
                                                <div className="radial-pie-chart">
                                                    <h4 className="header-title">
                                                        Vendor risk questionnaire progress
                                                    </h4>
                                                    <Chart
                                                        className="apexcharts"
                                                        options={progressChartOptions}
                                                        series={Object.values(projectsProgress)}
                                                        type="radialBar"
                                                        height={300}
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div className="overlay">
                    <div>
                        <h3>Connect your integrations to use this module.</h3>
                        <Link className="btn btn-primary mt-2" href={route("integrations.index")}>Connect</Link>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

export default Dashboard;