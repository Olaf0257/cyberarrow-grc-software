import React, { Fragment, useEffect, useState } from "react";
import { Link } from "@inertiajs/inertia-react";

function ProjectBox(props) {
    const [projects, setprojectData] = useState([]);

    useEffect(async () => {
        if (props.projectsData) {
            setprojectData(props.projectsData);
        }
    }, [props]);

    return (
        <Fragment>
            {/* Breadcomb Components */}
            {projects ? (
                <div className="row">
                    {projects.map(function (eachProject, index) {
                        return (
                            <div
                                key={index}
                                className="col-xl-6 col-lg-6 col-md-6"
                            >
                                <div className="card">
                                    <div
                                        id="mainContainerRiskSetupWizard"
                                        className="card-body project-box"
                                    >
                                        <div className="head-text text-center manual-content-box">
                                            <h4>{eachProject.title}</h4>
                                            <p className="my-3 manual-subtext">
                                                {eachProject.description}
                                            </p>
                                            <Link
                                                href={eachProject.href}
                                                className="btn btn-primary risk-btn"
                                            >
                                                Go
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            ) : (
                ""
            )}
        </Fragment>
    );
}

export default ProjectBox;
