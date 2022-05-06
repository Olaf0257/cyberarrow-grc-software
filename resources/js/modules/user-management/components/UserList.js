import React, { Fragment, useState, useEffect } from 'react';
import { Inertia } from '@inertiajs/inertia';
import DataTable from '../../../common/data-table/DataTable';
import { Link, usePage } from "@inertiajs/inertia-react";
import "../styles/react-collapsing-table.css";
import Dropdown from "react-bootstrap/Dropdown";
import FlashMessages from "../../../common/FlashMessages";
import UserLayout from "../UserLayout";

function UserList(props) {

    useEffect(() => {
        document.title = "User Management";
    }, []);

    const fetchURL = "users/get-user-data-react";
    const [refresh, setRefresh] = useState(false);
    const { authUser } = usePage().props;

    const handleUserEdit = async (id) => {
        Inertia.get(route("admin-user-management-edit", id));
    };

    function disableUser(id) {
        axiosFetch.get(route('admin-user-management-disable-user', id))
            .then(res => {
                if (res.status === 200 && res.data.success) {
                    AlertBox({
                        text: res.data.message,
                        confirmButtonColor: "#b2dd4c",
                        icon: 'success',
                    });
                    setRefresh((prevState) => !prevState);
                } else {
                    AlertBox({
                        text: res.data.message,
                        confirmButtonColor: "#f1556c",
                        icon: 'error',
                    });
                }
            })
            .catch(function (e) {
                console.log(e);
            });
    }

    const handleUserDisable = async (id) => {
        axiosFetch.get(route("user.project-assignments", id)).then((res) => {
            if (res.data.data > 0) {
                var selectOptions = {};
                axiosFetch
                    .get(route("user.assignments-transferable-users", id))
                    .then((res) => {
                        if (res.data.success) {
                            let users = res.data.data;
                            Object.keys(users).forEach((index) => {
                                selectOptions[
                                    users[index].id
                                ] = `${users[index].full_name} - ${users[index].email}`;
                            });
                        }
                        AlertBox({
                            input: 'select',
                            confirmButtonColor: '#b2dd4c',
                            imageUrl: `${appBaseURL}/assets/images/info1.png`,
                            imageWidth: 120,
                            title: 'Select&nbsp;a&nbsp;user&nbsp;to&nbsp;transfer&nbsp;responsibility&nbsp;to:',
                            inputOptions: selectOptions,
                            inputPlaceholder: 'Select a user',
                            showCloseButton: true,
                            showCancelButton: true,
                            inputValidator: (value) => {
                                return new Promise((resolve) => {
                                    if (value) {
                                        resolve()
                                    } else {
                                        resolve('Please select a user!')
                                    }
                                })
                            }
                        },
                            function (confirmed) {
                                if (confirmed.isConfirmed && confirmed.value) {
                                    axiosFetch
                                        .post(
                                            route(
                                                "user.transfer-assignments",
                                                id
                                            ),
                                            { transfer_to: confirmed.value }
                                        )
                                        .then((res) => {
                                            if (res.data.success) {
                                                disableUser(id);
                                            } else {
                                                AlertBox({
                                                    title: "Oops...",
                                                    text: res.data.message,
                                                    icon: 'error',
                                                    confirmButtonColor: "#ff0000",
                                                });
                                            }
                                        });
                                }
                            }
                        );
                    });
            } else {
                disableUser(id);
            }
        });
    };

    const handleUserActive = async (id) => {
        axiosFetch
            .get(route("admin-user-management-activate-user", id))
            .then((res) => {
                if (res.status === 200 && res.data.success) {
                    AlertBox({
                        text: res.data.message,
                        confirmButtonColor: "#b2dd4c",
                        icon: 'success',
                    });
                    setRefresh((prevState) => !prevState);
                } else {
                    AlertBox({
                        text: res.data.message,
                        confirmButtonColor: "#f1556c",
                        icon: 'error',
                    });
                }
            })
            .catch(function (e) {
                console.log(e);
            });
    };

    const handleUserDelete = (id) => {
        AlertBox(
            {
                title: "Are you sure?",
                text: "You will not be able to recover this user!",
                showCancelButton: true,
                confirmButtonColor: "#ff0000",
                confirmButtonText: "Yes, delete it!",
                icon:'warning',
                iconColor: '#ff0000',
            },
            function (result) {
                if (result.isConfirmed) {
                    axiosFetch.delete(
                        route("admin-user-management-delete", id)
                    ).then(res => {
                        if (res.status === 200 && res.data.success) {
                            AlertBox({
                                text: res.data.message,
                                confirmButtonColor: '#b2dd4c',
                                icon: 'success',
                            });
                            setRefresh(prevState => (!prevState));
                        } else {
                            AlertBox({
                                text: res.data.message,
                                confirmButtonColor: '#f1556c',
                                icon: 'error',
                            });
                        }
                    }).catch(
                        function (e) {
                            console.log(e);
                        }
                    );
                }
            }
        );
    }

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
            accessor: "auth_method",
            label: "Auth Method",
            priorityLevel: 1,
            position: 2,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "first_name",
            label: "First Name",
            priorityLevel: 1,
            position: 3,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "last_name",
            label: "Last Name",
            priorityLevel: 1,
            position: 4,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "department_name",
            label: "Department",
            priorityLevel: 2,
            position: 5,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "email",
            label: "Email",
            priorityLevel: 2,
            position: 6,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "contact_number",
            label: "Phone",
            priorityLevel: 2,
            position: 7,
            minWidth: 150,
            width: 300,
            sortable: false,
            CustomComponent: ({ row }) => {
                return (
                    <Fragment>
                        (&nbsp;{row.contact_number_country_code} &nbsp;)&nbsp;
                        {row.contact_number}
                    </Fragment>
                );
            },
        },
        {
            accessor: "role_names",
            label: "Roles",
            priorityLevel: 2,
            position: 8,
            minWidth: 150,
            sortable: false,
            CustomComponent: ({ row }) => {
                return (
                    <Fragment>
                        {row.roles.map((role, index) => {
                            return (
                                <span key={index} className="badge bg-soft-info text-info">
                                    {role.name}
                                </span>
                            );
                        })}
                    </Fragment>
                );
            },
        },
        {
            accessor: "status",
            label: "Status",
            priorityLevel: 2,
            position: 9,
            minWidth: 100,
            sortable: false,
            CustomComponent: ({ row }) => {
                return (
                    <Fragment>
                        <div
                            className={
                                "badge " +
                                (row.status == "active"
                                    ? "bg-info"
                                    : row.status == "disabled"
                                    ? "bg-danger"
                                    : "bg-warning")
                            }
                            style={{ textTransform: "capitalize" }}
                        >
                            {row.status}
                        </div>
                    </Fragment>
                );
            },
        },
        {
            accessor: "created_date",
            label: "Created At",
            priorityLevel: 2,
            position: 10,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "updated_date",
            label: "Updated At",
            priorityLevel: 2,
            position: 11,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "last_login",
            label: "Last Login",
            priorityLevel: 2,
            position: 12,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "action",
            label: "Action",
            priorityLevel: 1,
            position: 13,
            minWidth: 150,
            sortable: false,
            CustomComponent: ({ row }) => {
                return (
                    <Fragment>
                        <Dropdown className="d-inline-block">
                            <Dropdown.Toggle
                                variant=""
                                size="sm"
                                className="table-action-btn arrow-none btn"
                                aria-expanded="false"
                            >
                                <i className="mdi mdi-dots-horizontal text-muted h3 my-0"></i>
                            </Dropdown.Toggle>

                            <Dropdown.Menu className=" dropdown-menu-end">
                                <Dropdown.Item
                                    href="#"
                                    onClick={() => handleUserEdit(row.id)}
                                >
                                    <i className="mdi mdi-pencil-outline me-1 text-muted font-18 vertical-middle"></i>
                                    Edit
                                </Dropdown.Item>
                                {row.status === "active" &&
                                    row.status !== "unverified" &&
                                    row.id != authUser.id && (
                                        <Dropdown.Item
                                            href="#"
                                            onClick={() =>
                                                handleUserDisable(row.id)
                                            }
                                        >
                                            <i className="mdi mdi-delete-outline me-1 text-muted font-18 vertical-middle"></i>
                                            Disable
                                        </Dropdown.Item>
                                    )}
                                {row.status === "disabled" &&
                                    row.status !== "unverified" && (
                                        <Dropdown.Item
                                            href="#"
                                            onClick={() =>
                                                handleUserActive(row.id)
                                            }
                                        >
                                            <i className="mdi mdi-account-check-outline me-2 text-muted font-18 vertical-middle"></i>
                                            Activate
                                        </Dropdown.Item>
                                    )}
                                {row.status === 'unverified' &&
                                    <Dropdown.Item href="#" onClick={() => handleUserDelete(row.id)}>
                                        <i className='mdi mdi-delete-outline me-1 text-muted font-18 vertical-middle'></i>Delete
                                    </Dropdown.Item>
                                }
                            </Dropdown.Menu>
                        </Dropdown>
                    </Fragment>
                );
            },
        },
    ];

    const breadcumbsData = {
        title: "Users View",
        breadcumbs: [
            {
                "title": "User Management",
                "href": ""
            },
            {
                "title": "Users",
                "href": route('admin-user-management-view')
            },
            {
                title: "List",
                href: "",
            },
        ],
    };

    return (
        <UserLayout breadcumbsData={breadcumbsData}>
            <FlashMessages />
            <div className="col-xl-12">
                <div className="card">
                    <div className="card-body" id="users-list">
                        <Link
                            href={route("admin-user-management-create")}
                            type="button"
                            className="btn btn-sm btn-primary waves-effect waves-light float-end"
                        >
                            <i className="mdi mdi-plus-circle"></i> Add User
                        </Link>
                        <h4 className="header-title mb-4">Manage Users</h4>

                        <DataTable
                            columns={columns}
                            fetchURL={fetchURL}
                            refresh={refresh}
                            search
                        />
                    </div>
                </div>
            </div>
        </UserLayout>
    );
}

export default UserList;
