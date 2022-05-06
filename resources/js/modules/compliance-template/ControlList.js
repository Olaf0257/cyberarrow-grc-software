import React, { Fragment, useState, useEffect } from 'react';
import { Link } from '@inertiajs/inertia-react';
import DataTable from '../../common/data-table/DataTable';
import ComplianceTemplate from './ComplianceTemplate';
import { Inertia } from '@inertiajs/inertia';
import Alert from 'react-bootstrap/Alert'
import { Dropdown } from 'react-bootstrap';

export default function ControlList(props) {
    let propsData = { props };
    const [statusMessage, setStatusMessage] = useState(
        propsData.props.flash.error ? propsData.props.flash.error : null
    );
    const [successMessage, setSuccessMessage] = useState(
        propsData.props.flash.success ? propsData.props.flash.success : null
    );
    const standard = propsData.props.standard;
    const fetchURL = route(
        "compliance-template-controls-get-json-data",
        standard.id
    );

    useEffect(() => {
        document.title = "Controls";
    }, []);

    const handleDelete = async (standardId, controlId) => {
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
                    Inertia.get(
                        route("compliance-template-delete-controls", [
                            standardId,
                            controlId,
                        ])
                    );
                }
            }
        );
    };

    //For DataTable
    const columns = standard.is_default
        ? [
              {
                  accessor: "controlId",
                  label: "ID",
                  priorityLevel: 1,
                  position: 1,
                  minWidth: 100,
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
                  accessor: "description",
                  label: "Description",
                  priorityLevel: 1,
                  position: 3,
                  minWidth: 150,
                  sortable: false,
              },
          ]
        : [
              {
                  accessor: "controlId",
                  label: "ID",
                  priorityLevel: 1,
                  position: 1,
                  minWidth: 100,
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
                  accessor: "description",
                  label: "Description",
                  priorityLevel: 1,
                  position: 3,
                  minWidth: 150,
                  sortable: false,
              },
              {
                  accessor: "action",
                  label: "Action",
                  priorityLevel: 0,
                  position: 6,
                  minWidth: 50,
                  sortable: false,
                  sortable: false,
                  CustomComponent: ({ row }) => {
                      return (
                            <Dropdown align="end" className='d-inline-block'>
                                <Dropdown.Toggle
                                    as="a"
                                    bsPrefix="card-drop arrow-none cursor-pointer"
                                >
                                    <i className="mdi mdi-dots-horizontal m-0 text-muted h3" />
                                </Dropdown.Toggle>
                                <Dropdown.Menu className="dropdown-menu-end">
                                {!standard.is_default && (
                                                <Link
                                                    href={route(
                                                        "compliance-template-edit-controls",
                                                        [standard.id, row.id]
                                                    )}
                                                    className="dropdown-item d-flex align-items-center"
                                                >
                                                    <i className="mdi mdi-square-edit-outline font-18 me-1"/> Edit Information
                                                </Link>
                                            )}
                                            {!standard.is_default && (
                                                <button
                                                    onClick={() =>
                                                        handleDelete(standard.id, row.id)
                                                    }
                                                    className="dropdown-item d-flex align-items-center"
                                                >
                                                        <i className="mdi mdi-delete-outline font-18 me-1"/> Delete
                                                </button>
                                            )}
                                
                                </Dropdown.Menu>
                            </Dropdown>
                      );
                  },
              },
          ];

    const breadcumbsData = {
        title: "View Controls",
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
                title: "Controls",
                href: "",
            },
        ],
    };

    return (
        <ComplianceTemplate breadcumbsData={breadcumbsData}>
            {statusMessage && (
                <Alert
                    variant="danger"
                    onClose={() => setStatusMessage(null)}
                    dismissible
                >
                    <Alert.Heading>{statusMessage}</Alert.Heading>
                </Alert>
            )}
            {successMessage && (
                <Alert
                    variant="success"
                    onClose={() => setSuccessMessage(null)}
                    dismissible
                >
                    <Alert.Heading>{successMessage}</Alert.Heading>
                </Alert>
            )}
            <div className="row">
                <div className="col-12">
                    <div className="card">
                        <div className="card-body" id="control-list">
                            <Link
                                href={route(
                                    "compliance-template-create-controls",
                                    standard.id
                                )}
                                type="button"
                                className="btn btn-sm btn-primary waves-effect waves-light float-end"
                            >
                                <i
                                    className="mdi mdi-plus-circle"
                                    title="Add New Standard"
                                ></i>{" "}
                                Add New Control
                            </Link>
                            <h4 className="header-title mb-4">
                                Manage Controls
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
