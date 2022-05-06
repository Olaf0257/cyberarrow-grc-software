import React, { Fragment, useEffect, useState } from "react";
import { useDispatch } from "react-redux";
import MyTaskMonitor from "./components/MyTaskMonitorWidget";
import TaskCompletionPercentageWidget from "./components/task-completion-percentage-widget/TaskCompletionPercentageWidget";
import CalendarWidget from "./components/calendar-widget/CalendarWidget";
import ApprovalWidget from "./components/approval-widget/ApprovalWidget";
import AppLayout from "../../../layouts/app-layout/AppLayout";
import fileDownload from "js-file-download";
import {useStateIfMounted} from "use-state-if-mounted"
import "./dashboard.scss";
import { Alert } from "react-bootstrap";

function Dashboard(props) {
    document.title = "Compliance Dashboard";

    const [pageData, setPageData] = useStateIfMounted({});
    const propsData = { props };
    const globalSetting = propsData.props.globalSetting;
    const [statusMessage, setStatusMessage] = useState(
        propsData.props.flash.status ? propsData.props.flash.status : null
    );
    const dispatch = useDispatch();

    /* Re-fetch page data on data scope  change */
    useEffect(() => {
        loadPageData();
    }, []);

    const loadPageData = async () => {
        let httpRes = await axiosFetch.get("/compliance/dashboard/data");
        let res = httpRes.data;

        if (res.success) {
            setPageData(res.data);
        }
    };

    const generateReport = async () => {
        const URL = route("compliance.dashboard.export-to-pdf");

        /* showing report generate loader */
        dispatch({ type: "reportGenerateLoader/show" });

        try {
            let response = await axiosFetch({
                url: URL,
                method: "POST",
                responseType: "blob", // Important
            });

            fileDownload(response.data, "compliance-report.pdf");

            /* hiding report generate loader */
            dispatch({ type: "reportGenerateLoader/hide" });
        } catch (error) {
            /* hiding report generate loader */
            dispatch({ type: "reportGenerateLoader/hide" });
        }
    };

    return (
        <AppLayout>
            <div id="compliance-dashboard-page">
                {/* breadcrumbs */}
                <div className="row">
                    <div className="col-12">
                        <div className="page-title-box">
                            <div className="page-title-right">
                                <button
                                    onClick={() => {
                                        generateReport();
                                    }}
                                    className="btn btn-primary compliance-export_btn width-md"
                                >
                                    Export to PDF
                                </button>
                            </div>
                            <h4 className="page-title">My Dashboard</h4>
                        </div>
                    </div>
                </div>
                {/* end of breadcrumbs */}

                {statusMessage && (
                    <Alert
                        variant="success"
                        onClose={() => setStatusMessage(null)}
                        dismissible
                    >
                        <Alert.Heading>{statusMessage}</Alert.Heading>
                    </Alert>
                )}

                <div className="row">
                    <div className="col-xl-4">
                        {/* first card */}
                        <TaskCompletionPercentageWidget
                            myCompletedTasksPercent={
                                pageData.myCompletedTasksPercent
                            }
                            globalSetting={globalSetting}
                        ></TaskCompletionPercentageWidget>
                        {/* first card ends */}
                        {/* second card */}
                        <MyTaskMonitor
                            totalTaskDueToday={pageData.totalTaskDueToday}
                            totalMyTaskPassDue={pageData.totalMyTaskPassDue}
                            myAllActiveTasks={pageData.myAllActiveTasks}
                        ></MyTaskMonitor>
                        {/* second card ends */}
                        {/* third card */}
                        <ApprovalWidget
                            totalNeedMyApprovalTasks={
                                pageData.totalNeedMyApprovalTasks
                            }
                            totalUnderReviewMyTasks={
                                pageData.totalUnderReviewMyTasks
                            }
                        ></ApprovalWidget>
                    </div>{" "}
                    {/* col-xl-4 ends */}
                    {/* col-xl-8 starts */}
                    <div className="col-xl-8">
                        <CalendarWidget
                            calendarTasks={pageData.calendarTasks}
                        ></CalendarWidget>
                    </div>{" "}
                    {/* end col */}
                    {/* col-xl-8 ends */}
                </div>
            </div>
        </AppLayout>
    );
}

export default Dashboard;
