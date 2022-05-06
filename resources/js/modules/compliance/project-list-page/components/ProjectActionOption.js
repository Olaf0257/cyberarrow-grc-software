import React, { Fragment } from "react";
import Dropdown from "react-bootstrap/Dropdown";
import { Link } from "@inertiajs/inertia-react";
import { Inertia } from "@inertiajs/inertia";
import { useSelector } from "react-redux";
import route from "ziggy-js";

function ProjectActionOption(props) {
    const { project, loadProjects } = props;
    const appDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope.value
    );

    const handleProjectDelete = (event) => {
        event.preventDefault();

        AlertBox(
            {
                title: "Are you sure?",
                text: "You will not be able to recover this project!",
                showCancelButton: true,
                confirmButtonColor: "#ff0000",
                confirmButtonText: "Yes, delete it!",
                icon:'warning',
                iconColor:'#ff0000'
            },
            function (confirmed) {
                if (confirmed.value && confirmed.value == true) {
                    Inertia.delete(
                        route("compliance-projects-delete", project.id),
                        {
                            data: {
                                data_scope: appDataScope,
                            },
                            onFinish: () => {
                                loadProjects();
                                AlertBox({
                                    title: "Deleted!",
                                    text: "The project was deleted successfully",
                                    confirmButtonColor: "#b2dd4c",
                                    icon:'success',
                                });
                            },
                        }
                    );
                }
            }
        );
    };

    return (
        <Fragment>
            <Dropdown className="float-end">
                <Dropdown.Toggle
                    as="a"
                    bsPrefix="card-drop arrow-none cursor-pointer"
                >
                    <i className="mdi mdi-dots-horizontal m-0 text-muted h3" />
                </Dropdown.Toggle>

                <Dropdown.Menu className="dropdown-menu-end">
                    <Link
                        href={route("compliance-projects-edit", project.id)}
                        className="dropdown-item d-flex align-items-center"
                    >
                       <i className="mdi mdi-pencil-outline font-18 me-1" /> Edit
                    </Link>
                    <Dropdown.Item eventKey="2" onClick={handleProjectDelete} className="d-flex align-items-center">
                        <i className="mdi mdi-delete-outline font-18 me-1" /> Delete
                    </Dropdown.Item>
                </Dropdown.Menu>
            </Dropdown>
        </Fragment>
    );
}

export default ProjectActionOption;
