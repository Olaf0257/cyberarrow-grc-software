import React from "react";
import { Breadcrumb } from "react-bootstrap";

const Breadcrumbs = () => {
    return (
        <div className="row">
            <div className="col-12">
                <div className="page-title-box">
                    <div className="page-title-right">
                        {/* <ol className="breadcrumb m-0">
                            <li className="breadcrumb-item">
                                <a href={route("compliance-dashboard")}>
                                    Compliance
                                </a>
                            </li>
                            <li className="breadcrumb-item"><a
                                href={route('compliance-projects-view')}>Projects</a></li>
                            <li className="breadcrumb-item"><a
                                href={route('compliance-project-show', [1,'controls'])}>Controls</a>
                            </li>
                            <li className="breadcrumb-item">
                                <a href={route("compliance-project-show", [1])}>
                                    Controls
                                </a>
                            </li>
                            <li className="breadcrumb-item active">
                                <a href="#">Details</a>
                            </li>
                        </ol> */}

                        <Breadcrumb>
                            <Breadcrumb.Item
                                href={route("compliance-dashboard")}
                            >
                                Compliance
                            </Breadcrumb.Item>
                            <Breadcrumb.Item
                                href={route("compliance-projects-view")}
                            >
                                Projects
                            </Breadcrumb.Item>
                            <Breadcrumb.Item
                                href={route("compliance-project-show", [1])}
                            >
                                Controls
                            </Breadcrumb.Item>
                            <Breadcrumb.Item href="#" active>Details</Breadcrumb.Item>
                        </Breadcrumb>
                    </div>
                    <h4 className="page-title">My Dashboard</h4>
                </div>
            </div>
        </div>
    );
};

export default Breadcrumbs;
