import React, { Fragment, useEffect, useState } from "react";
import { Link, usePage } from "@inertiajs/inertia-react";
import DataTable from "../../common/data-table/DataTable";
import { Inertia } from "@inertiajs/inertia";
import Alert from "react-bootstrap/Alert";
import { ButtonGroup } from "react-bootstrap";
import AppLayout from "../../layouts/app-layout/AppLayout";
import BreadcumbsComponent from '../../common/breadcumb/Breadcumb';
import "./style/style.scss"

export default function AssetManagememt(props) {

    useEffect(() => {
        document.title = "Asset Management";
    }, []);

    const fetchURL = route('compliance-template-get-json-data');
    let propsData = { props };
    

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
                        <ButtonGroup>
                            <Link
                                href={route(
                                    "compliance-template-view-controls",
                                    row.id
                                )}
                                title="View"
                                className="btn btn-secondary btn-xs waves-effect waves-light"
                                data-toggle="tooltip"
                                data-original-title="View"
                            >
                                <i className="fe-eye"></i>
                            </Link>
                            <Link
                                href={route(
                                    "compliance-template-dublicate",
                                    row.id
                                )}
                                title="Duplicate Standard"
                                className="btn btn-primary btn-xs waves-effect waves-light"
                                data-animation="blur"
                            >
                                <i className="far fa-plus-square"></i>
                            </Link>
                            {!row.is_default && (
                                <Link
                                    href={route(
                                        "compliance-template-create-controls",
                                        row.id
                                    )}
                                    title="Add control(s)"
                                    className="btn btn-warning btn-xs waves-effect waves-light"
                                    data-animation="blur"
                                >
                                    <i className="fa fa-plus"></i>
                                </Link>
                            )}
                            {!row.is_default && (
                                <Link
                                    href={route(
                                        "compliance-template-edit",
                                        row.id
                                    )}
                                    title="Edit Information"
                                    className="btn btn-info btn-xs waves-effect waves-light"
                                    data-animation="blur"
                                >
                                    <i className="fe-edit"></i>
                                </Link>
                            )}
                            {!row.is_default && (
                                <button
                                    // onClick={() => handleDelete(row.id)}
                                    title="Delete"
                                    className="btn btn-danger btn-xs waves-effect waves-light"
                                    data-animation="blur"
                                >
                                    <i className="fe-trash-2"></i>
                                </button>
                            )}
                        </ButtonGroup>
                    </Fragment>
                );
            },
        },
    ];

    const breadcumbsData = {
        title: "View Standards",
        breadcumbs: [
            {
                title: "Asset Management",
                href: "",
            }
        ],
    };

    return (
             <AppLayout>
                <div className="position-relative mt-3">
                    <div>
            {/* <BreadcumbsComponent data={breadcumbsData} /> */}
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
            </div>
            <div className="overlay">
            <div>
                <h3>Connect your asset management integration to use this module.</h3>
                <Link className="btn btn-primary mt-2" href={route("integrations.index")}>Connect</Link>
                </div>
            </div>
            </div>
            </AppLayout>
    );
}
