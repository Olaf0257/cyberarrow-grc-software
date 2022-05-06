import React, { Fragment, useEffect, useState } from "react";
import AppLayout from "../../../layouts/app-layout/AppLayout";
import CreateProjectWizard from "./components/CreateProjectWizard";
import BreadcumbComponent from "../../../common/breadcumb/Breadcumb";
import FlashMessages from "../../../common/FlashMessages";

function ProjectCreatePage(props) {
    const { project } = props;
    const [assignedControls, setAssignedControls] = useState(0);

    const breadcumbsData = {
        title: "View Projects",
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
                title: `${project.id ? "Update" : "Create"}`,
                href: "#",
            },
        ],
    };

    return (
        <Fragment>
            <AppLayout>
                {/* breadcrumbs */}
                <BreadcumbComponent data={breadcumbsData}></BreadcumbComponent>
                {/* end of breadcrumbs */}

                {/* Flash messese */}
                <FlashMessages/>

                <div className="row">
                    <div className="col-xl-12">
                        <div className="card">
                            <div className="card-body">
                                <CreateProjectWizard
                                    project={project}
                                    assignedControls={assignedControls}
                                ></CreateProjectWizard>
                            </div>
                        </div>
                    </div>
                </div>
            </AppLayout>
        </Fragment>
    );
}

export default ProjectCreatePage;
