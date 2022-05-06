import React from "react";

import {Link} from "@inertiajs/inertia-react";
import Dropdown from "react-bootstrap/Dropdown";

import {transformDateTime} from "../../../../utils/date";

const ProjectOptions = ({handleDelete, handleDuplicate}) => {
    return (
        <Dropdown className="text-end">
            <Dropdown.Toggle
                as="a"
                bsPrefix="dropdown-toggle card-drop arrow-none cursor-pointer"
            >
                <i className="mdi mdi-dots-horizontal m-0 text-muted h3"/>
            </Dropdown.Toggle>
            <Dropdown.Menu className="dropdown-menu-end">
                <Dropdown.Item eventKey="duplicate" onClick={handleDuplicate} className="d-flex align-items-center">
                    <i className="mdi mdi-content-copy font-14 me-1" /> Duplicate
                </Dropdown.Item>
                <Dropdown.Item eventKey="delete" onClick={handleDelete} className="d-flex align-items-center">
                    <i className="mdi mdi-delete-outline font-18 me-1" /> Delete
                </Dropdown.Item>
            </Dropdown.Menu>
        </Dropdown>
    )
}

const ProjectItem = ({project, reload, handleDuplicate}) => {

    const handleDelete = () => {
        AlertBox(
            {
                title: "Are you sure?",
                text: "You will not be able to reactivate this project!",
                showCancelButton: true,
                confirmButtonColor: "#ff0000",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false,
                icon:'warning',
                iconColor:'#ff0000'
            },
            function (result) {
                if (result.isConfirmed) {
                    axiosFetch.post(route('third-party-risk.projects.destroy', [project.id]), {
                        _method: 'delete'
                    })
                        .then(() => {
                            reload();
                            AlertBox({
                                title: "Deleted!",
                                text: "The project was deleted successfully",
                                confirmButtonColor: "#b2dd4c",
                                icon:'success',
                            });
                        })
                }
            }
        );
    }
    return (
        <div className="col-lg-4 col-sm-6">
            <div className="card project__box">
                <ProjectOptions handleDuplicate={() => handleDuplicate(project)} handleDelete={handleDelete}/>
                <Link href={route('third-party-risk.projects.show', [project.id])} className="text-dark">
                    <h4 className="mt-0 text-truncate">{project.name}</h4>
                    <div className="my-2 d-flex flex-row justify-content-between align-items-center">
                        <div className="flex-grow-1 d-flex align-items-center">
                            <strong>Vendors Name: &nbsp;</strong>
                            <h5>{project.vendor.name}</h5>
                        </div>
                        <span
                            className={`badge ${project.project_status.badge}`}
                        >
                            {project.project_status.status}
                        </span>
                    </div>
                    <div>
                        <strong>Start Date: &nbsp;</strong>
                        <span className="text-muted">{transformDateTime(project.launch_date)}</span>
                    </div>
                    <div className="mt-2">
                        <strong>Due Date: &nbsp;</strong>
                        <span className="text-muted">{transformDateTime(project.due_date)}</span>
                    </div>
                </Link>
            </div>
        </div>
    );
}

export default ProjectItem;
