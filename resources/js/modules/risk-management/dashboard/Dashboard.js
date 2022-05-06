import React, { Fragment, useEffect, useState, useRef, createRef } from "react";
import { useDispatch, useSelector } from "react-redux";
import Chart from "react-apexcharts";
import DataTable from "../../../common/data-table/DataTable";
import AppLayout from "../../../layouts/app-layout/AppLayout";
import fileDownload from "js-file-download";
import { Link } from "@inertiajs/inertia-react";
import DepartmentFilter from "./components/department-filter/DepartmentFilter";
import { fetchDashboardData } from "../../../store/actions/risk-management/dashboard";
import "./styles/style.scss";
import { useDidMountEffect } from "../../../custom-hooks";
import { useStateIfMounted } from "use-state-if-mounted";

function Dashboard(props) {
    const [topRiskDatatableReload, setTopRiskDatatableReload] = useState(false);
    const [loaded, setLoaded] = useStateIfMounted(false);
    const [ajaxData, setAjaxData] = useStateIfMounted([]);
    const [riskCountWithinRiskLevels, setRiskCountWithinRiskLevels] = useStateIfMounted([]);
    const [riskSeverityChart, setRiskSeverityChart] = useState([]);
    const [riskCloseStatusChart, setRiskCloseStatusChart] = useState([]);
    const [riskByCategory, setRiskByCategory] = useState([]);
    const [topRiskDatatable, setTopRiskDatatable] = useState([]);
    const appDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope.value
    );
    const selectedDepartments = useSelector(
        (store) =>
            store.commonReducer.departmentFilterReducer.selectedDepartment
    );
    const dispatch = useDispatch();
    const departmentFilterRef = useRef(null);

    useEffect(() => {
        document.title = "Risk Dashboard";
    }, []);

    useDidMountEffect(async () => {
        let { payload } = await dispatch(
            fetchDashboardData({
                params: {
                    data_scope: appDataScope,
                    departments: selectedDepartments,
                },
            })
        );

        if (!payload.success) return;

        let resData = payload.data;
        setRiskCountWithinRiskLevels(resData.riskCountWithinRiskLevels);

        renderCharts(resData);

        const data = {
            departments: selectedDepartments,
        };
        setAjaxData(data);

        renderDataTable();
        setLoaded(!loaded);
    }, [selectedDepartments,appDataScope]);

    const renderCharts = (resData) => {
        //Donut piechart risk by severity
        let totalSeverityCount = resData.riskCountWithinRiskLevels.reduce(function(acc, val) { return acc + val.risk_count; }, 0);

        let rsc = {};
        rsc.labels = resData.riskCountWithinRiskLevels.map(function (item) {
            return item.name;
        });
        rsc.series = totalSeverityCount > 0 ? resData.riskCountWithinRiskLevels.map(function (item) {
            return item.risk_count;
        }) : [];
        rsc.options = {
            chart: {
                type: 'donut'
            },
            tooltip: {
                enabled: true,
                fillSeriesColor: false,
                theme:false,
                style:{
                    fontSize:'15px'
                }
            },
            colors: resData.riskLevelColors,
            labels: rsc.labels,
            legend: {
                show: totalSeverityCount > 0 ? true : false,
                position: 'right',
                formatter: function(seriesName, opts) {
                    return [seriesName, " - ", opts.w.globals.series[opts.seriesIndex]]
                }
            },
            dataLabels: {
                enabled: false,
                formatter: function (val, opts) {
                    return opts.w.config.series[opts.seriesIndex]
                }
            },
            states: {
                active: {
                    filter: {
                        type: 'none',
                    }
                }
            },
            plotOptions: {
                pie: {
                    expandOnClick: true,
                    donut: {
                        size: "90%",
                        background: "transparent",
                        labels: {
                            show: true,
                            name: {
                                show: false,
                                fontSize: "25px",
                                color: "black",
                            },
                            value:{
                                fontSize: "35px",
                                color: "#6e6b7b",
                            },
                            total: {
                                show: true,  
                                showAlways:true,
                                label:'Total Risks'
                            },
                        },
                    },
                },
            },
            responsive: [
                {
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 310,
                        },
                        legend: {
                            position: 'bottom'
                        }
                    },
                },
            ],
        };

        setRiskSeverityChart(rsc);

        // Risk onb basic of risk closed status

        let totalClosedRiskCount = resData.closedRiskCountOfDifferentLevels.reduce(function(acc, val) { return acc + val; }, 0);

        let rcs = {};
        rcs.series = totalClosedRiskCount > 0 ? resData.closedRiskCountOfDifferentLevels : [];
        rcs.options = {
            chart: {
                type: 'donut'
            },
            tooltip: {
                enabled: true,
                fillSeriesColor: false,
                theme:false,
                style:{
                    fontSize:'15px'
                }
            },
            colors: resData.riskLevelColors,
            labels: resData.riskLevelsList,
            legend: {
                show: totalClosedRiskCount > 0 ? true : false,
                position: 'right',
                formatter: function(seriesName, opts) {
                    return [seriesName, " - ", opts.w.globals.series[opts.seriesIndex]]
                }
            },
            dataLabels: {
                enabled: false,
                formatter: function (val, opts) {
                    return opts.w.config.series[opts.seriesIndex]
                }
            },
            states: {
                active: {
                    filter: {
                        type: 'none',
                    }
                }
            },
            plotOptions: {
                pie: {
                    expandOnClick: true,
                    donut: {
                        size: "90%",
                        background: "transparent",
                        labels: {
                            show: true,
                            name: {
                                show: false,
                                fontSize: "25px",
                                color: "black",
                            },
                            value:{
                                fontSize: "35px",
                                color: "#6e6b7b",
                            },
                            total: {
                                show: true,  
                                showAlways:true,
                                label:'Total Closed'
                            }
                        },
                    },
                },
            },
            responsive: [
                {
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 310,
                        },
                        legend: {
                            position: 'bottom'
                        }
                    },
                },
            ],
        };
        setRiskCloseStatusChart(rcs);

        // Apexchart stacked - risk by category
        let rbc = {};
        rbc.series = resData.riskCountWithinRiskLevelForCategories;
        rbc.options = {
            chart: {
                type: "bar",
                stacked: true,
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                },
            },
            stroke: {
                width: 1,
                colors: ["#fff"],
            },
            xaxis: {
                categories: resData.riskRegisterCategoriesList,
                labels: {
                    formatter: function (val) {
                        return val;
                    },
                },
            },
            yaxis: {
                title: {
                    text: undefined,
                },
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val;
                    },
                },
            },
            fill: {
                opacity: 1,
            },
            legend: {
                position: "top",
                horizontalAlign: "left",
                offsetX: 40,
            },
            colors: resData.riskLevelColors,
            dataLabels: {
                style: {
                    colors: ["#38414a"],
                },
                formatter: function (val) {
                    return val !== 0 ? val : "";
                },
            },
        };
        // CODE TO ASSIGN HEIGHT STARTS HERE
        let categoriesLength = rbc.options.xaxis.categories.length;
        rbc.options.chart.height =
            categoriesLength > 0 ? calculateChartHeight(categoriesLength) : 0;
        setRiskByCategory(rbc);
    };

    const calculateChartHeight = (categoriesLength) => {
        // 33 increment for  100 start form 2nd bar
        let incrementRate = 32;
        let chartHeight = 100;

        if (categoriesLength > 1) {
            for (var i = 1; i < categoriesLength; i++) {
                chartHeight = chartHeight + incrementRate;
            }
        }

        return chartHeight;
    };

    const renderDataTable = () => {
        const columns = [
            {
                accessor: "index",
                label: "Risk ID",
                priorityLevel: 1,
                position: 1,
                minWidth: 10,
            },
            {
                accessor: "name",
                label: "Risk Title",
                priorityLevel: 1,
                position: 2,
                minWidth: 200,
            },
            {
                accessor: "category",
                label: "Category",
                priorityLevel: 1,
                position: 3,
                minWidth: 200,
                CustomComponent: ({ row }) => {
                    return <Fragment>{row.category.name}</Fragment>;
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
                                    row.status === "Open"
                                        ? "badge bg-danger rounded-pill"
                                        : "badge bg-success rounded-pill"
                                }
                                style={{
                                    textOverflow: "ellipsis",
                                    overflow: "hidden",
                                }}
                            >
                                {row.status === "Close" ? "Closed" : row.status}
                            </span>
                        </Fragment>
                    );
                },
            },
            {
                accessor: "treatment_options",
                label: "Treatment Option",
                priorityLevel: 2,
                position: 5,
                minWidth: 150,
            },
            {
                accessor: "likelihood_name",
                label: "Likelihood",
                priorityLevel: 2,
                position: 6,
                minWidth: 50,
            },
            {
                accessor: "impact_name",
                label: "Impact",
                priorityLevel: 2,
                position: 7,
                minWidth: 50,
            },
            {
                accessor: "inherent_score",
                label: "Inherent Risk Score",
                priorityLevel: 2,
                position: 8,
                minWidth: 50,
            },
            {
                accessor: "residual_score",
                label: "Residual Risk Score",
                priorityLevel: 2,
                position: 9,
                minWidth: 50,
            },
            {
                accessor: "action",
                label: "Action",
                priorityLevel: 1,
                position: 10,
                minWidth: 50,
                CustomComponent: ({ row }) => {
                    return (
                        <Fragment>
                            <span style={{ display: "block" }}>
                                <Link
                                    className="btn btn-primary btn-view btn-sm width-sm"
                                    href={
                                        `${appBaseURL}/risks/risks-register/` +
                                        row.id +
                                        `/show`
                                    }
                                >
                                    View
                                </Link>
                            </span>
                        </Fragment>
                    );
                },
            },
        ];
        // axiosFetch.get('risks/dashboard/dashboard-data/datatable-data', {
        //     params: {
        //         data_scope: appDataScope
        //     }
        // });
        var url = "risks/dashboard/dashboard-data/datatable-data?";
        let trd = {
            columns: columns,
            url: "risks/dashboard/dashboard-data/datatable-data",
        };
        setTopRiskDatatable(trd);
        setTopRiskDatatableReload(!topRiskDatatableReload);
    };

    const generateReport = async () => {
        const URL = route("risks.dashboard.generate-pdf-report");

        try {
            /* showing report generate loader */
            dispatch({ type: "reportGenerateLoader/show" });

            let response = await axiosFetch({
                url: URL,
                method: "Post",
                data: {
                    data_scope: appDataScope,
                },
                responseType: "blob", // Important
            });

            fileDownload(response.data, "risks-report.pdf");

            /* hiding report generate loader */
            dispatch({ type: "reportGenerateLoader/hide" });
        } catch (error) {
            /* hiding report generate loader */
            dispatch({ type: "reportGenerateLoader/hide" });
        }
    };

    return (
        <AppLayout>
            <div id="risk-dashboard-page">
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
                                Summary - Current Risks
                            </h4>
                        </div>
                    </div>
                </div>
                <div className="row">
                    {riskCountWithinRiskLevels.map(function (rcwrl, index) {
                        return (
                            <div className="col-md-6 col-xl-3" key={index}>
                                <div className="card">
                                    <div className="widget-rounded-circle card-body">
                                        <div className="row">
                                            <div className="col-6">
                                                <div
                                                    className="avatar-lg rounded-circle vulnerability__icon"
                                                    style={{
                                                        background: rcwrl.color,
                                                    }}
                                                >
                                                    <i
                                                        id="alert_icon"
                                                        className="icon fa fa-exclamation-triangle"
                                                    ></i>
                                                </div>
                                            </div>
                                            <div className="col-6">
                                                <div className="text-end">
                                                    <h3 className="text-dark mt-1">
                                                        {rcwrl.risk_count}
                                                    </h3>
                                                    <p className="text-muted mb-1 text-truncate">
                                                        {rcwrl.name}
                                                    </p>
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
                                        <div className="donut-pie-chart card-body">
                                            <h4 className="header-title">
                                                Risks on the basis of severity
                                            </h4>
                                            {riskSeverityChart.series && (
                                                <Chart
                                                    options={
                                                        riskSeverityChart.options
                                                    }
                                                    labels={
                                                        riskSeverityChart.series
                                                    }
                                                    series={
                                                        riskSeverityChart.series
                                                    }
                                                    type="donut"
                                                    height={260}
                                                />
                                            )}
                                        </div>
                                    </div>
                                </div>

                                <div className="col-xl-6">
                                    <div className="card">
                                        <div className="radial-pie-chart card-body">
                                            <h4 className="header-title">
                                                Risks on the basis of closed
                                                status
                                            </h4>
                                            {riskCloseStatusChart.series && (
                                                <Chart
                                                    className="apexcharts"
                                                    options={
                                                        riskCloseStatusChart.options
                                                    }
                                                    series={
                                                        riskCloseStatusChart.series
                                                    }
                                                    type="donut"
                                                    height={260}
                                                />
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* <!-- risks-by-category starts --> */}
                <div className="row">
                    <div className="col-xl-12">
                        <div className="card">
                            <div className="risks-by-category mb-2 card-body">
                                <div className="risk-category-div">
                                    <h4 className="risk-category-text mt-0">
                                        Risks By Category
                                    </h4>
                                    {/* <div id="riskbycategory-chart" className="apexcharts"></div> */}
                                    {riskByCategory.series && (
                                        <Chart
                                            className="apexcharts"
                                            options={riskByCategory.options}
                                            series={riskByCategory.series}
                                            type="bar"
                                            height={
                                                riskByCategory.options.chart
                                                    .height
                                            }
                                        />
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {/* <!-- risks-by-category  ends--> */}

                {/* <!-- top risks table --> */}
                <div className="row">
                    <div className="col-xl-12">
                        <div className="card">
                            <div className="card-body">
                                <div className="top-risk pb-1">
                                    <h4 className="top-risk-text mt-0">
                                        Top Risks
                                    </h4>
                                </div>
                                {topRiskDatatable.columns && (
                                    <DataTable
                                        columns={topRiskDatatable.columns}
                                        fetchURL={topRiskDatatable.url}
                                        refresh={topRiskDatatableReload}
                                        ajaxData={ajaxData}
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

export default Dashboard;
