import React, { useEffect, useRef } from "react";

import { Inertia } from "@inertiajs/inertia";
import { useSelector } from "react-redux";
import { Tabs, Tab } from "react-bootstrap";
import moment from "moment/moment";

import { usePage } from "@inertiajs/inertia-react";
import AppLayout from "../../../../layouts/app-layout/AppLayout";

import DetailsTab from "./Tabs/DetailsTab";
import TasksTab from "./Tabs/TasksTab";
import FlashMessages from "../../../../common/FlashMessages";

import '../../../global-settings/styles/style.css';
import '../styles/styles.css';
import BreadcumbComponent from '../../../../common/breadcumb/Breadcumb';

const Index = () => {
    const { projectControl, nextReviewDate, activeTabs } = usePage().props;
    const [activeKey, setActiveKey] = React.useState("details");

    const projectControlDeadline = projectControl.deadline;
    const taskUnlockingDate = moment(projectControlDeadline)
        .subtract(14, "days")
        .format("YYYY-MM-DD");
    const today = moment().format("YYYY-MM-DD");

    const appDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope.value
    );

    const dataScopeRef = useRef(appDataScope);
    useEffect(() => {
        if (dataScopeRef.current !== appDataScope) {
            Inertia.get(route("compliance-projects-view"));
        }
    }, [appDataScope]);

    useEffect(() => {
        if (["details", "tasks"].includes(activeTabs)) {
            setActiveKey(activeTabs);
        }
    }, []);

    const getTaskStatusClass = (status) => {
        switch (status) {
            case "Not Implemented":
                return "task-status-red text-white";
            case "Under Review":
                return "task-status-blue";
            case "Implemented":
                return "task-status-green";
            case "Rejected":
                return "task-status-orange";
        }
    };

    const breadcumbsData = {
        title: "My Dashboard",
        breadcumbs: [
            {
                title: "Compliance",
                href: route("compliance-dashboard"),
            },
            {
                title: "Projects",
                href: route("compliance-projects-view"),
            },
            {
                title: "Controls",
                href: route('compliance-project-show', [projectControl.project_id, 'controls']),
            },
            {
                title: "Details",
                href: '#',
            },
        ],
    };

    return (
        <AppLayout>
            <BreadcumbComponent data={breadcumbsData}/>
            <FlashMessages/>
            <div className="row">
                <div className="col-xl-12">
                    <div className="card">
                        <div className="card-body position-relative">
                            <Tabs
                                activeKey={activeKey}
                                onSelect={(eventKey) => setActiveKey(eventKey)}
                            >
                                <Tab eventKey="details" title="Details">
                                    <DetailsTab
                                        getTaskStatusClass={getTaskStatusClass}
                                    />
                                </Tab>
                                <Tab eventKey="tasks" title="Tasks">
                                    <TasksTab active={activeKey === "tasks"} />
                                </Tab>
                            </Tabs>

                            <div
                                className="status-pill"
                                id="control-status-badge"
                            >
                                {projectControl.status !== "Implemented" && projectControl.deadline ? (
                                    <span
                                        className="badge me-2"
                                        style={{ background: "#444" }}
                                    >
                                        Deadline: {moment(projectControl.deadline).format("DD-MM-YYYY")}
                                    </span>
                                ) : null}

                                {nextReviewDate &&
                                projectControl.status !== "Implemented" &&
                                today >= taskUnlockingDate &&
                                today <= projectControlDeadline ? (
                                    <span
                                        className="badge me-2"
                                        style={{ background: "#444" }}
                                    >
                                        Review deadline approaching
                                    </span>
                                ) : null}
                                <span
                                    className={`badge ${getTaskStatusClass(
                                        projectControl.status
                                    )}`}
                                >
                                    {projectControl.status}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
};

export default Index;
