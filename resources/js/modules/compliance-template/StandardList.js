import React, { Fragment, useEffect, useState } from "react";
import { Link, usePage } from "@inertiajs/inertia-react";
import DataTable from "../../common/data-table/DataTable";
import ComplianceTemplate from "./ComplianceTemplate";
import { Inertia } from "@inertiajs/inertia";
import Alert from "react-bootstrap/Alert";
import { ButtonGroup, Dropdown } from "react-bootstrap";

export default function StandardList(props) {

    useEffect(() => {
        document.title = "Compliance Templates";
    }, []);

    const fetchURL = route('compliance-template-get-json-data');
    let propsData = { props };
    const [statusMessage, setStatusMessage] = useState(
        propsData.props.flash.success ? propsData.props.flash.success : null
    );

    const handleDelete = async (id) => {
        AlertBox(
            {
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                confirmButtonColor: "#ff0000",
                allowOutsideClick: false,
                icon: "warning",
                iconColor:'#ff0000',
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
            },
            function (result) {
                if (result.isConfirmed) {
                    Inertia.get(route("compliance-template-delete", id));
                }
            }
        );
    };

    //For DataTable
    const columns = [
        {
            accessor: "id",
            label: "ID",
            priorityLevel: 1,
            position: 1,
            minWidth: 50,
            sortable: false,
        },
        {
            accessor: "name",
            label: "Name",
            priorityLevel: 1,
            position: 2,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "version",
            label: "Version",
            priorityLevel: 1,
            position: 3,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "controls",
            label: "Controls",
            priorityLevel: 1,
            position: 4,
            minWidth: 150,
            sortable: false,
            CustomComponent: ({ row }) => {
                return (
                    <Fragment>
                        <span className="badge bg-info">
                            {row.controls_count} Controls
                        </span>
                    </Fragment>
                );
            },
        },
        {
            accessor: "created_date",
            label: "Created On",
            priorityLevel: 2,
            position: 5,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "action",
            label: "Action",
            priorityLevel: 0,
            position: 6,
            minWidth: 150,
            sortable: false,
            CustomComponent: ({ row }) => {
                return (
                    <Fragment>
                        <Dropdown className='d-inline-block'>
                            <Dropdown.Toggle
                                as="a"
                                bsPrefix="card-drop arrow-none cursor-pointer"
                            >
                                <i className="mdi mdi-dots-horizontal m-0 text-muted h3" />
                            </Dropdown.Toggle>

                            <Dropdown.Menu className="dropdown-menu-end">
                                <Link
                                    href={route("compliance-template-view-controls", row.id)}
                                    className="dropdown-item d-flex align-items-center"
                                >
                                    <i className="mdi mdi-eye-outline font-18 me-1"></i> View
                                </Link>
                                <Link
                                    href={route("compliance-template-dublicate",row.id)}
                                    className="dropdown-item d-flex align-items-center"
                                >
                                    <i className="mdi mdi-content-copy font-18 me-1"></i> Duplicate Standard
                                </Link>
                                {!row.is_default && (
                                    <Link
                                        href={route("compliance-template-create-controls",row.id)}
                                        className="dropdown-item d-flex align-items-center"
                                    >
                                        <i className="mdi mdi-plus-box-outline font-18 me-1"></i> Add Control
                                    </Link>
                                )}
                                {!row.is_default && (
                                    <Link
                                        href={route("compliance-template-edit",row.id)}
                                        className="dropdown-item d-flex align-items-center"
                                    >
                                        <i className="mdi mdi-pencil-outline font-18 me-1"></i> Edit Information
                                    </Link>
                                )}
                                {!row.is_default && (
                                    <button
                                        onClick={() => handleDelete(row.id)}
                                        className="dropdown-item d-flex align-items-center"
                                    >
                                        <i className="mdi mdi-delete-outline font-18 me-1"></i> Delete
                                    </button>
                                )}
                            </Dropdown.Menu>
                        </Dropdown>
                    </Fragment>
                );
            },
        },
    ];

    const breadcumbsData = {
        title: "View Standards",
        breadcumbs: [
            {
                title: "Administration",
                href: "",
            },
            {
                title: "Compliance Template",
                href: route("compliance-template-view"),
            },
            {
                title: "View",
                href: "",
            },
        ],
    };

    return (
        <ComplianceTemplate breadcumbsData={breadcumbsData}>
            {statusMessage && (
                <Alert
                    variant="success"
                    onClose={() => setStatusMessage(null)}
                    dismissible
                >
                    {statusMessage}
                </Alert>
            )}
            <div className="row">
                <div className="col-12">
                    <div className="card">
                        <div className="card-body">
                            <Link
                                href={route("compliance-template-create")}
                                type="button"
                                className="btn btn-sm btn-primary waves-effect waves-light float-end"
                            >
                                <i
                                    className="mdi mdi-plus-circle"
                                    title="Add New Standard"
                                ></i>{" "}
                                Add New Standard
                            </Link>
                            <h4 className="header-title mb-4">
                                Manage Standards
                            </h4>

                            <DataTable
                                columns={columns}
                                fetchURL={fetchURL}
                                search
                            />
                        </div>
                    </div>
                </div>
                {/* <!-- end col --> */}
            </div>
            {/* <!-- end row --> */}
        </ComplianceTemplate>
    );
}
