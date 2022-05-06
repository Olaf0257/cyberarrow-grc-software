import React, { Fragment, useEffect, useRef, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import CalendarWidget from "./components/calendar-widget/CalendarWidget";
import ControlStatusWidget from "./components/control-status-widget/ControlStatusWidget";
import TaskMonitorWidget from "./components/task-monitor-widget/TaskMonitorWidget";
import ImplementationProgress from "./components/ImplementationProgress";
import TaskCompletionPercentageWidget from "./components/task-completion-percentage-widget/TaskCompletionPercentageWidget";
import DepartmentFilter from "./components/department-filter/DepartmentFilter";
import ProjectFilter from "./components/project-filter/ProjectFilter";
import AppLayout from "../../layouts/app-layout/AppLayout";
import { fetchPageData } from "../../store/actions/global-dashboard";
import ContentLoader from "../../common/content-loader/ContentLoader";
import { useDidMountEffect } from "../../custom-hooks";
import { useStateIfMounted } from "use-state-if-mounted";
import fileDownload from "js-file-download";
import "./global-dashboard.scss";
import { Card, Row } from "react-bootstrap";
import { Col } from "react-bootstrap";

function GlobalDashboard(props) {
    document.title = "Global Dashboard";

    const propsData = { props };
    const globalSetting = propsData.props.globalSetting;

    const projectFilterRef = useRef(null);
    const departmentFilterRef = useRef(null);
    const [pageData, setPageData] = useStateIfMounted({});
    const appDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope.value
    );
    const { selectedProjects } = useSelector(
        (store) => store.globalDashboardReducer.projectFilterReducer
    );
    const [showContentLoader, setShowContentLoader] = useStateIfMounted(false);
    const dispatch = useDispatch();

    /* On selectedProjects update */
    useDidMountEffect(async () => {
        setShowContentLoader(true);
        let res = await dispatch(
            fetchPageData({
                data_scope: appDataScope,
                projects: selectedProjects.join(),
            })
        );

        /* Setting the page data */
        if (res.payload && res.payload.success) {
            setPageData(res.payload.data);
        }

        setShowContentLoader(false);
    }, [selectedProjects]);

    /* Generate the PDF report */
    const generateReport = async () => {
        let URL = route("global.dashboard.generate-report");

        /* showing report generate loader */
        dispatch({ type: "reportGenerateLoader/show" });

        try {
            let response = await axiosFetch({
                url: URL,
                method: "Post",
                data: {
                    data_scope: appDataScope,
                    projects: selectedProjects.join(),
                },
                responseType: "blob", // Important
            });

            fileDownload(response.data, "global-report.pdf");

            /* hiding report generate loader */
            dispatch({ type: "reportGenerateLoader/hide" });
        } catch (error) {
            /* hiding report generate loader */
            dispatch({ type: "reportGenerateLoader/hide" });
        }
    };

    /* Selects all projects */
    const selectAllProjects = () => {
        projectFilterRef.current.selectAll();
    };

    return (
        <AppLayout>
            <div id="global-dashboard">
                {/* top section */}
                <Row>
                    <Col xl={12}>
                        <div className="overview-div mt-3 d-flex">
                            <div className="overview-div-text">
                                <h4 className="overview-text">
                                    Current Overview:{" "}
                                    <span className="overview-break-text">
                                        All projects
                                    </span>
                                </h4>
                            </div>
                            <div className="select-dropdown ms-auto">
                                <div className="row g-0">
                                    <div className="col px-0 projects-filter-wp mb-3">
                                        <DepartmentFilter
                                            ref={departmentFilterRef}
                                        ></DepartmentFilter>
                                    </div>
                                    <div className="col px-0 projects-filter-wp mb-3 ms-2">
                                        <ProjectFilter
                                            ref={projectFilterRef}
                                        ></ProjectFilter>
                                    </div>
                                    <div className="col px-0 mb-2">
                                        <button
                                            type="button"
                                            onClick={() => {
                                                selectAllProjects();
                                            }}
                                            className="btn btn-primary all-projects-btn mx-2 dashboard-btn"
                                        >
                                            All Projects
                                        </button>
                                    </div>
                                    <div className="col px-0 mb-2">
                                        <button
                                            onClick={() => {
                                                generateReport();
                                            }}
                                            className="btn btn-primary global-export_btn dashboard-btn"
                                        >
                                            Export to PDF
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Col>
                </Row>

                <ContentLoader show={showContentLoader}>
                    <div className="task-box loader-overlay">
                        <ControlStatusWidget
                            allControls={pageData.allControls}
                            notApplicableControls={
                                pageData.notApplicableControls
                            }
                            implementedControls={
                                pageData.implementedControls
                            }
                            underReviewControls={
                                pageData.underReviewControls
                            }
                            notImplementedControls={
                                pageData.notImplementedControls
                            }
                        ></ControlStatusWidget>
                    </div>
                    <div className="task-box loader-overlay my-3">
                        <Row>
                            <Col xl={8} className="mb-2 mb-md-0">
                                <Card className="h-100">
                                    <Card.Body>
                                        <h4 className="head-title mt-0">Task Monitor</h4>
                                        <Row>
                                            <Col md={6}>
                                            <TaskCompletionPercentageWidget
                                                completedTasksPercent={
                                                    pageData.completedTasksPercent
                                                }
                                                globalSetting={globalSetting}
                                            ></TaskCompletionPercentageWidget>
                                            </Col>
                                            <Col md={6}>
                                                <TaskMonitorWidget
                                                    allUpcomingTasks={pageData.allUpcomingTasks}
                                                    allDueTodayTasks={pageData.allDueTodayTasks}
                                                    allPassDueTasks={pageData.allPassDueTasks}
                                                ></TaskMonitorWidget>
                                            </Col>
                                        </Row>
                                    </Card.Body>
                                </Card>
                            </Col>
                            <Col xl={4}>
                                <ImplementationProgress
                                    allControls={pageData.allControls}
                                    notApplicableControls={
                                        pageData.notApplicableControls
                                    }
                                    implementedControls={
                                        pageData.implementedControls
                                    }
                                    underReviewControls={
                                        pageData.underReviewControls
                                    }
                                    notImplementedControls={
                                        pageData.notImplementedControls
                                    }
                                ></ImplementationProgress>
                            </Col>
                        </Row>
                    </div>
                    {/* calendar here */}
                    <CalendarWidget></CalendarWidget>
                    {/* calendar here ends*/}
                </ContentLoader>
            </div>
        </AppLayout>
    );
}

export default GlobalDashboard;
