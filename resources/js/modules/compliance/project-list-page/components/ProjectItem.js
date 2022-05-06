import React, { Fragment, useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import ProjectActionOption from "./ProjectActionOption";
import { fetchProjectList } from "../../../../store/actions/compliance/project";
import { useStateIfMounted } from "use-state-if-mounted";
import { Link } from "@inertiajs/inertia-react";

function ProjectItem(props) {
    const { searchQuery, authUserRoles } = props;
    const [showProjectActionOptions, setShowProjectActionOptions] =
        useStateIfMounted(false);
    const dispatch = useDispatch();
    const appDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope.value
    );
    const { projects } = useSelector(
        (state) => state.complianceReducer.projectReducer
    );

    /* data scope update */
    useEffect(() => {
        loadProjects();
    }, [appDataScope, searchQuery]);

    useEffect(() => {
        if (
            authUserRoles.includes("Global Admin") ||
            authUserRoles.includes("Compliance Administrator")
        ) {
            setShowProjectActionOptions(true);
        } else {
            setShowProjectActionOptions(false);
        }
    }, []);

    const loadProjects = async () => {
        dispatch(
            fetchProjectList({
                project_name: searchQuery,
                data_scope: appDataScope,
            })
        );
    };

    return (
        <Fragment>
            {projects.map(function (project, index) {
                const {
                    implemented_controls_count,
                    not_implemented_controls_count,
                    applicable_controls_count,
                } = project;

                const implementedControlPercentage =
                    implemented_controls_count > 0 &&
                    applicable_controls_count > 0
                        ? (implemented_controls_count /
                              applicable_controls_count) *
                          100
                        : 0;

                return (
                    <div className="col-lg-4 col-sm-6" key={index}>
                        <div className="card">
                            <div className="card-body project-box project-div">
                                {showProjectActionOptions ? (
                                    <ProjectActionOption
                                        loadProjects={loadProjects}
                                        project={project}
                                    ></ProjectActionOption>
                                ) : (
                                    ""
                                )}
                                {/* <a href={`${appBaseURL}/compliance/projects/${project.id}/show`} className="text-dark"> */}
                                <Link
                                    href={route(
                                        "compliance-project-show",
                                        project.id
                                    )}
                                    className="text-dark"
                                >
                                    {/* Title*/}
                                    <h4 className="mt-0">
                                        {decodeHTMLEntity(project.name)}
                                    </h4>
                                    {/* <p class="text-muted text-uppercase"><i class="mdi mdi-account-circle"></i> <small>Orange Limited</small></p> */}
                                    <p />
                                    <p className="fw-bold">
                                            Standard:{" "}
                                            {decodeHTMLEntity(project.standard)}
                                    </p>
                                    <p className="text-muted font-13 mb-3 sp-line-2">
                                        {decodeHTMLEntity(project.description)}
                                    </p>

                                    {/* Task info*/}
                                    <p className="mb-1">
                                        <span className="pe-2 text-nowrap mb-2 d-inline-block">
                                            {/* <i class="mdi mdi-format-list-bulleted-type text-muted"></i> */}
                                            <b>{applicable_controls_count}</b>{" "}
                                            Controls
                                        </span>
                                    </p>
                                    {/* Progress*/}
                                    <p className="mb-2 fw-bold">
                                        Controls Implemented{" "}
                                        <span className="float-end">
                                            {implemented_controls_count}
                                        </span>
                                    </p>
                                    <div
                                        className="progress mb-1"
                                        style={{ height: 7 }}
                                    >
                                        <div
                                            className="progress-bar"
                                            role="progressbar"
                                            aria-valuenow={
                                                implementedControlPercentage
                                            }
                                            aria-valuemin={0}
                                            aria-valuemax={100}
                                            style={{
                                                width: `${implementedControlPercentage}%`,
                                            }}
                                        ></div>
                                        {/* /.progress-bar .progress-bar-danger */}
                                    </div>
                                    {/* /.progress .no-rounded */}
                                    {/* </a> */}
                                </Link>
                            </div>{" "}
                        </div>
                    </div>
                );
            })}
        </Fragment>
    );
}

export default ProjectItem;
