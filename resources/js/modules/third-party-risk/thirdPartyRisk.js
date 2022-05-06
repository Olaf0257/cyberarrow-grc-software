import React, { Fragment, useEffect, useState, useRef, createRef } from "react";
import Chart from "react-apexcharts";
import AppLayout from "../../layouts/app-layout/AppLayout";
import { useStateIfMounted } from "use-state-if-mounted";
import DepartmentFilter from "../risk-management/dashboard/components/department-filter/DepartmentFilter";
import { useDidMountEffect } from "../../custom-hooks";
import "./style/style.scss";
import DataTable from "../../common/data-table/DataTable";
import { Link } from "react-router-dom";

function ThirdPartyRisk(props) {
    let resData = [
        {
            color: "#ff0000",
            risk_count: "2",
            name: "Level 1",
        },
        {
            color: "#ffc000",
            risk_count: "3",
            name: "Level 2",
        },
        {
            color: "#ffff00",
            risk_count: "20",
            name: "Level 3",
        },
        {
            color: "#92d050",
            risk_count: "45",
            name: "Level 4",
        },
        {
            color: "#00b050",
            risk_count: "2",
            name: "Level 5",
        },
    ];
    const departmentFilterRef = useRef(null);
    const [vendorCountWithinLevels, setVendorCountWithinLevels] =
        useStateIfMounted(resData);
    const [vendorLevelChart, setVendorLevelChart] = useState({});
    const [questionariesProgressChart, setQuestionariesProgressChart] =
        useState([]);
    const [topVendorDatatable, setTopVendorDatatable] = useState([]);

    useEffect(() => {
        renderCharts();
        renderDataTable();
    }, []);

    const renderCharts = () => {
        //Donut piechart vendor by level
        let rsc = {};
        rsc.labels = vendorCountWithinLevels.map(function (item) {
            return item.name;
        });
        rsc.series = vendorCountWithinLevels.map(function (item) {
            return Number(item.risk_count);
        });
        rsc.colors = vendorCountWithinLevels.map(function (item) {
            return item.color;
        });

        rsc.options = {
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
            colors: rsc.colors,
            labels: rsc.labels,
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
                                label: rsc.labels[1],
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
        setVendorLevelChart(rsc);
        // Risk onb basic of risk closed status
        let rcs = {};
        setQuestionariesProgressChart(rcs);
        (rcs.labels = ["Not started", "In progress", "Completed", "Overdue"]),
            (rcs.colors = ["#414141", "#5bc0de", "#359f1d", "#cf1110"]),
            (rcs.series = [10, 30, 50, 70]);
        rcs.options = {
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
                            label: rcs.labels[1],
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
            labels: rcs.labels,
            colors: rcs.colors,
        };
        setQuestionariesProgressChart(rcs);
    };

    const renderDataTable = () => {
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
                CustomComponent: ({ row }) => {
                    return (
                        <Fragment>
                            <span
                                className="badge text-white"
                                style={{
                                    textOverflow: "ellipsis",
                                    overflow: "hidden",
                                    backgroundColor:
                                        row.maturity === "Level 1"
                                            ? "#ff0000"
                                            : row.maturity === "Level 2"
                                            ? "#ffc000"
                                            : row.maturity === "Level 3"
                                            ? "#ffff00"
                                            : row.maturity === "Level 4"
                                            ? "#92d050"
                                            : "#00b050",
                                }}
                            >
                                {row.maturity}
                            </span>
                        </Fragment>
                    );
                },
            },
            {
                accessor: "status",
                label: "Status",
                priorityLevel: 1,
                position: 4,
                minWidth: 50,
                CustomComponent: ({ row }) => {
                    return (
                        <Fragment>
                            <span
                                className={
                                    row.status === "Not started"
                                        ? "badge bg-dark"
                                        : row.status === "In Progress"
                                        ? "badge bg-blue"
                                        : row.status === "Completed"
                                        ? "badge bg-success"
                                        : "badge bg-danger"
                                }
                                style={{
                                    textOverflow: "ellipsis",
                                    overflow: "hidden",
                                }}
                            >
                                {row.status}
                            </span>
                        </Fragment>
                    );
                },
            },
            {
                accessor: "contact",
                label: "Contact Name",
                priorityLevel: 1,
                position: 5,
                minWidth: 150,
            },
            {
                accessor: "action",
                label: "Action",
                priorityLevel: 1,
                position: 6,
                minWidth: 50,
                CustomComponent: ({ row }) => {
                    return (
                        <Fragment>
                            <span style={{ display: "block" }}>
                                <a
                                    className="btn btn-primary btn-view btn-sm width-sm"
                                    href="#"
                                >
                                    View
                                </a>
                            </span>
                        </Fragment>
                    );
                },
            },
        ];
        let trd = {
            columns: columns,
            url: "risks/dashboard/dashboard-data/datatable-data",
        };
        setTopVendorDatatable(trd);
    };

    return (
        <AppLayout>
            <div id="third-party-risk-page">
                {/* <!-- breadcrumbs --> */}
                <div className="row">
                    <div className="col-12">
                        <div className="page-title-box">
                            <div className="page-title-right">
                                <button
                                    type="button"
                                    onClick={() => {
                                        generateReport();
                                    }}
                                    className="btn btn-primary risk-export_btn width-md"
                                >
                                    Export to PDF
                                </button>
                            </div>
                            <div
                                className="page-title-right"
                                style={{ marginRight: "10px" }}
                            >
                                <DepartmentFilter
                                    ref={departmentFilterRef}
                                ></DepartmentFilter>
                            </div>

                            <h4 className="page-title">Dashboard</h4>
                        </div>
                    </div>
                </div>
                {/* <!-- end of breadcrumbs --> */}

                {/* <!-- current vulnerability --> */}
                <div className="row">
                    <div className="col-xl-12">
                        <div className="risk-stat-div pb-1">
                            <h4 className="risk-stat-text">
                                Summary - Vendor Maturity
                            </h4>
                        </div>
                    </div>
                </div>

                <div className="row row-cols-2 row-cols-lg-5 g-2 g-lg-3">
                    {vendorCountWithinLevels.map(function (vcwl, index) {
                        return (
                            <div className="col" key={index}>
                                <div className="card">
                                    <div className="card-body">
                                        <div className="widget-rounded-circle">
                                            <div className="row">
                                                <div className="col-6">
                                                    <div
                                                        className="avatar-lg rounded-circle bg-soft-primary vulnerability__icon"
                                                        style={{
                                                            background: vcwl.color,
                                                        }}
                                                    >
                                                        <i
                                                            id="alert_icon"
                                                            className="icon fa fa-user-shield"
                                                        ></i>
                                                    </div>
                                                </div>
                                                <div className="col-6">
                                                    <div className="text-end">
                                                        <h3 className="text-dark mt-1">
                                                            {vcwl.risk_count}
                                                        </h3>
                                                        <p className="text-muted mb-1 text-truncate">
                                                            {vcwl.name}
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

                {/* <!-- current vulnerability ends --> */}

                {/* <!-- pie charts --> */}
                <div className="row">
                    <div className="col-xl-12">
                        {/* <!-- pie charts --> */}
                        <div className="pie-charts">
                            <div className="row">
                                <div className="col-xl-6">
                                    <div className="card">
                                        <div className="card-body">
                                            <div className="donut-pie-chart">
                                                <h4 className="header-title">
                                                    Vendors on the basis of maturity
                                                </h4>
                                                {vendorLevelChart.series && (
                                                    <Chart
                                                        options={
                                                            vendorLevelChart.options
                                                        }
                                                        series={vendorLevelChart.series}
                                                        type="donut"
                                                        height={260}
                                                    />
                                                )}
                                            </div>
                                        </div>  
                                    </div>
                                </div>

                                <div className="col-xl-6">
                                    <div className="card">
                                        <div className="card-body">
                                            <div className="radial-pie-chart">
                                                <h4 className="header-title">
                                                    Vendor risk questionnaire progress
                                                </h4>
                                                {questionariesProgressChart.series && (
                                                    <Chart
                                                        className="apexcharts"
                                                        options={
                                                            questionariesProgressChart.options
                                                        }
                                                        series={
                                                            questionariesProgressChart.series
                                                        }
                                                        type="radialBar"
                                                        height={300}
                                                    />
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* <!-- top risks table --> */}
                <div className="row">
                    <div className="col-xl-12">
                        <div className="card">
                            <div className="card-body">
                                <div className="top-risk pb-1">
                                    <h4 className="top-risk-text mt-0">
                                        Top Vendors
                                    </h4>
                                </div>
                                {topVendorDatatable.columns && (
                                    <DataTable
                                        columns={topVendorDatatable.columns}
                                        fetchURL={topVendorDatatable.url}
                                        refresh={topVendorDatatable}
                                        search
                                        //   ajaxData={ajaxData}
                                    />
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

export default ThirdPartyRisk;