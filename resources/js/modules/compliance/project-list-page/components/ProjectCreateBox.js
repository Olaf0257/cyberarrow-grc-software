import React, { Fragment } from "react";
import { Link } from "@inertiajs/inertia-react";

function ProjectCreateBox(props) {
    return (
        <Fragment>
            <div className="col-lg-4 col-sm-6">
                <Link href={route("compliance-projects-create")}>
                    <div className="card">
                        <div
                            className="card-body project-box project-div d-flex justify-content-center align-items-center"
                            style={{
                                minHeight: "15.5rem",
                                fontSize: "4rem",
                                color: "#323b43",
                            }}
                        >
                            <i className="mdi mdi-plus" />
                        </div>{" "}
                        {/* end card box*/}
                    </div>
                </Link>
            </div>
        </Fragment>
    );
}

export default ProjectCreateBox;
