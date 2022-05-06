import React, { Fragment, useEffect, useState } from "react";
import ProjectCreateBox from "./components/ProjectCreateBox";
import ProjectItem from "./components/ProjectItem";
import AppLayout from "../../../layouts/app-layout/AppLayout";
import "./project-list-page.scss";
import Breadcrumb from "../../../common/breadcumb/Breadcumb";
import { useSelector } from "react-redux";
import ContentLoader from "../../../common/content-loader/ContentLoader";
import FlashMessages from "../../../common/FlashMessages";

const breadcumbsData = {
    title: "View Projects",
    breadcumbs: [
        {
            title: "Compliance",
            href: route("compliance-dashboard"),
        },
        {
            title: "Projects",
            href: "#",
        },
    ],
};

function ProjectListPage(props) {

    useEffect(() => {
        document.title = "Compliance Projects";
    }, []);

    const { authUserRoles } = props;

    const [searchQuery, setSearchQuery] = useState("");
    const { loading } = useSelector(
        (state) => state.complianceReducer.projectReducer
    );

    const handleSearchQueryChange = (event) => {
        setSearchQuery(event.target.value);
    };

    const renderProjectCreateBox = () => {
        return authUserRoles.includes("Global Admin") ||
            authUserRoles.includes("Compliance Administrator") ? (
            <ProjectCreateBox />
        ) : (
            ""
        );
    };

    return (
        <AppLayout>
            <div id="compliance-project-list-page">
                {/* breadcrumbs */}
                <Breadcrumb data={breadcumbsData}></Breadcrumb>
                {/* end of breadcrumbs */}

                <FlashMessages />

                <div className="row">
                    <div className="col">
                        <div className="float-end">
                            <div className="ms-3 mb-3">
                            <div className="row align-items-center">
                                <input
                                    type="text"
                                    onChange={handleSearchQueryChange}
                                    name="project_name"
                                    className="form-control form-control-sm"
                                    placeholder={"Search..."}
                                />
                            </div>
                            </div>
                        </div>
                    </div>
                </div>

                <ContentLoader show={loading}>
                    <div className="row" id="projects-wp">
                        {/* project list */}
                        {renderProjectCreateBox()}

                        <ProjectItem
                            searchQuery={searchQuery}
                            authUserRoles={authUserRoles}
                        ></ProjectItem>
                    </div>
                </ContentLoader>
            </div>
        </AppLayout>
    );
}

export default ProjectListPage;
