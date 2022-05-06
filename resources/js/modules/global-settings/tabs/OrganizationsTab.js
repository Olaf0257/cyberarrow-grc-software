import React, { useState } from "react";

import { fetchDataScopeDropdownTreeData } from "../../../store/actions/data-scope-dropdown";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-react";
import { useDispatch } from "react-redux";
import withReactContent from "sweetalert2-react-content";

import {
    Accordion,
    useAccordionButton,
    OverlayTrigger,
    Tooltip,
} from "react-bootstrap";
import Nestable from "react-nestable/dist/Nestable";
import Select from "react-select";
import Swal from "sweetalert2";

import AddDepartmentModal from "../components/organizations/AddDepartmentModal";
import EditOrganizationModal from "../components/organizations/EditOrganizationModal";
import EditDepartmentModal from "../components/organizations/EditDepartmentModal";
import AddOrganizationModal from "../components/organizations/AddOrganizationModal";

import "react-nestable/dist/styles/index.css";

const CustomToggle = ({ children, eventKey }) => {
    const handleOnClick = useAccordionButton(eventKey, () => {});

    return (
        <a
            className="text-dark organization-collapse-el cursor-pointer"
            onClick={handleOnClick}
        >
            {children}
        </a>
    );
};

const OrganizationsTab = () => {
    const { organizations, APP_URL } = usePage().props;

    const [departmentsOrder, setDepartmentsOrder] = useState({});
    const [loading, setLoading] = useState(false);

    const dispatch = useDispatch();

    const deleteDepartment = (organization_id, department_id) => {
        axiosFetch
            .get(
                route("global-settings.organizations.departments.delete", [
                    organization_id,
                    department_id,
                ])
            )
            .then(({ data }) => {
                if (data.success) {
                    Swal.fire({
                        title: "Department deleted successfully",
                        confirmButtonColor: "#b2dd4c",
                        icon: 'success'
                    });
                    Inertia.reload({ only: ["organizations"] });
                }
            });
    };

    React.useEffect(() => {
        // refresh the data scope dropdown
        dispatch(fetchDataScopeDropdownTreeData());
        setDepartmentsOrder(
            Object.assign(
                {},
                ...organizations.map((o) => ({ [o.id]: o.departments }))
            )
        );
    }, [organizations]);

    const handleDeleteDepartment = (organization_id, department_id) => {
        axiosFetch
            .get(
                route(
                    "global-settings.organizations.departments.department-transferable-user-count",
                    [organization_id, department_id]
                )
            )
            .then(({ data: { data: count } }) => {
                if (count > 0) {
                    //    should be transferred
                    axiosFetch
                        .get(
                            route(
                                "global-settings.organizations.departments.department-transferable-user",
                                [organization_id, department_id]
                            )
                        )
                        .then(({ data }) => {
                            if (data.success) {
                                const departmentsSelectOptions = data.data.map(
                                    (d) => ({ value: d.id, label: d.name })
                                );

                                const MySwal = withReactContent(Swal);
                                let selectedTransferTo = null;
                                MySwal.fire({
                                    title: "Select&nbsp;a&nbsp;department&nbsp;to&nbsp;transfer&nbsp;user(s)&nbsp;to:",
                                    showCloseButton: true,
                                    showCancelButton: true,
                                    confirmButtonColor: "#b2dd4c",
                                    imageUrl:
                                        APP_URL + "assets/images/info1.png",
                                    imageWidth: 120,
                                    html: (
                                        <div style={{ padding: "4px" }}>
                                            <Select
                                                onChange={(option) =>
                                                    (selectedTransferTo =
                                                        option.value)
                                                }
                                                menuPortalTarget={document.body}
                                                styles={{
                                                    menuPortal: (base) => ({
                                                        ...base,
                                                        zIndex: 9999,
                                                    }),
                                                }}
                                                options={
                                                    departmentsSelectOptions
                                                }
                                            />
                                        </div>
                                    ),
                                }).then((res) => {
                                    if (
                                        res.isConfirmed &&
                                        selectedTransferTo !== null
                                    ) {
                                        Inertia.post(
                                            route(
                                                "global-settings.organizations.departments.department-transferable",
                                                [organization_id, department_id]
                                            ),
                                            {
                                                transfer_to: selectedTransferTo,
                                            },
                                            {
                                                onSuccess: () => {
                                                    selectedTransferTo = null;
                                                    deleteDepartment(
                                                        organization_id,
                                                        department_id
                                                    );
                                                },
                                            }
                                        );
                                    }
                                });
                                // console.log(departments);
                            }
                        });
                } else {
                    //    we can proceed
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You will not be able to recover this",
                        showCancelButton: true,
                        confirmButtonColor: "#ff0000",
                        confirmButtonText: "Yes, delete it!",
                        icon: 'warning',
                        iconColor: '#ff0000',
                    }).then((confirmed) => {
                        if (confirmed.value) {
                            deleteDepartment(organization_id, department_id);
                        }
                    });
                }
            });
    };

    const recursiveSearch = (x, index = 0, results = []) => {
        let temp = [...results];
        if (x[index] !== undefined) {
            if (x[index].departments.length === 0) {
                temp.push({ id: x[index].id });
            } else {
                temp.push({
                    id: x[index].id,
                    children: [...recursiveSearch(x[index].departments)],
                });
            }
            return recursiveSearch(x, index + 1, temp);
        }
        return temp;
    };

    const handleChangeOrder = (organization_id, items) => {
        setDepartmentsOrder({
            ...departmentsOrder,
            [organization_id]: items,
        });
    };

    const handleSaveChanges = (organization_id) => {
        const data = departmentsOrder.hasOwnProperty(organization_id)
            ? recursiveSearch(departmentsOrder[organization_id])
            : [];
        Inertia.post(
            route(
                "global-settings.organizations.departments.save-nested-departments",
                [organization_id]
            ),
            {
                nested_department_array: JSON.stringify(data),
            },
            {
                forceFormData: true,
                onStart: () => setLoading(true),
                onSuccess: () => {
                    setLoading(false);
                    Inertia.reload({ only: ["organizations"] });
                },
            }
        );
    };

    // Add Org Modal
    const [addOrgModalShown, setAddOrgModalShown] = useState(false);
    //
    //Edit Org Modal
    const [editOrgModalConfig, setEditOrgModalConfig] = useState({
        shown: false,
        organization: {
            id: null,
            name: null,
        },
    });
    const handleEditOrgModalClose = () =>
        setEditOrgModalConfig({ ...editOrgModalConfig, shown: false });
    const handleEditOrgModalSelected = (id, name) =>
        setEditOrgModalConfig({
            ...editOrgModalConfig,
            organization: { id, name },
            shown: true,
        });
    //
    // Add Department Modal
    const [addDepModalConfig, setAddDepModalConfig] = useState({
        shown: false,
        department_id: null,
        organization_id: null,
    });
    const handleAddDepModalClose = () =>
        setAddDepModalConfig({ ...addDepModalConfig, shown: false });
    const handleAddDepModalSelected = (department_id, organization_id) =>
        setAddDepModalConfig({
            ...addDepModalConfig,
            department_id,
            organization_id,
            shown: true,
        });
    //
    //Edit Department Modal
    const [editDepModalConfig, setEditDepModalConfig] = useState({
        shown: false,
        department: {
            id: null,
            name: null,
            parent_id: null,
        },
        organization_id: null,
    });
    const handleEditDepModalClose = () =>
        setEditDepModalConfig({ ...editDepModalConfig, shown: false });
    const handleEditDepModalSelected = (id, name, parent_id, organization_id) =>
        setEditDepModalConfig({
            department: {
                id,
                name,
                parent_id,
            },
            organization_id,
            shown: true,
        });
    //

    const renderItem = ({ item, collapseIcon }) => {
        const { id, name, organization_id, parent_id } = item;
        return (
            <div
                className={
                    "d-flex flex-wrap justify-content-between align-items-center text-dark department-item"
                }
            >
                <div>
                    {collapseIcon}&nbsp;{name}
                </div>
                <div>
                    <OverlayTrigger
                        placement="top"
                        trigger="hover"
                        overlay={(props) => (
                            <Tooltip id={`add_dep_${id}`} {...props}>
                                Add Department
                            </Tooltip>
                        )}
                    >
                        <button
                            onClick={() =>
                                handleAddDepModalSelected(id, organization_id)
                            }
                            className="btn btn-primary btn-xs waves-effect waves-light"
                        >
                            <i className="fe-plus" />
                        </button>
                    </OverlayTrigger>

                    <OverlayTrigger
                        placement="top"
                        trigger="hover"
                        overlay={(props) => (
                            <Tooltip id={`edit_dep_${id}`} {...props}>
                                Edit Department
                            </Tooltip>
                        )}
                    >
                        <button
                            onClick={() =>
                                handleEditDepModalSelected(
                                    id,
                                    name,
                                    parent_id,
                                    organization_id
                                )
                            }
                            className="btn btn-info btn-xs waves-effect waves-light"
                            style={{ margin: "0 3px" }}
                        >
                            <i className="fe-edit" />
                        </button>
                    </OverlayTrigger>

                    <OverlayTrigger
                        placement="top"
                        trigger="hover"
                        overlay={(props) => (
                            <Tooltip id={`delete_dep_${id}`} {...props}>
                                Delete Department
                            </Tooltip>
                        )}
                    >
                        <button
                            onClick={() =>
                                handleDeleteDepartment(organization_id, id)
                            }
                            className="btn btn-danger btn-xs waves-effect waves-light"
                        >
                            <i className="fe-trash-2" />
                        </button>
                    </OverlayTrigger>
                </div>
            </div>
        );
    };

    return (
        <div className="row global pb-3">
            <div className="col-xl-12">
                <AddOrganizationModal
                    shown={addOrgModalShown}
                    handleClose={() => setAddOrgModalShown(false)}
                />
                <EditOrganizationModal
                    config={editOrgModalConfig}
                    handleClose={handleEditOrgModalClose}
                />
                <AddDepartmentModal
                    config={addDepModalConfig}
                    handleClose={handleAddDepModalClose}
                />
                <EditDepartmentModal
                    config={editDepModalConfig}
                    handleClose={handleEditDepModalClose}
                />
                {organizations.length > 0 ? (
                    <Accordion
                        defaultActiveKey={`toggle_org_${organizations[0].id}`}
                    >
                        {organizations.map((organization) => (
                            <div className="card mb-1" key={organization.id}>
                                <div className="card-header organization-card-header">
                                    <h5 className="position-relative m-0">
                                        <CustomToggle
                                            eventKey={`toggle_org_${organization.id}`}
                                        >
                                            <i className="mdi mdi-office-building me-1 secondary-text-color" />
                                            {organization.name}
                                        </CustomToggle>
                                        <div className="organization-actions">
                                            <OverlayTrigger
                                                placement="top"
                                                trigger="hover"
                                                overlay={(props) => (
                                                    <Tooltip
                                                        id={`add_dep_${organization.id}`}
                                                        {...props}
                                                    >
                                                        Add Department
                                                    </Tooltip>
                                                )}
                                            >
                                                <button
                                                    onClick={() =>
                                                        handleAddDepModalSelected(
                                                            0,
                                                            organization.id
                                                        )
                                                    }
                                                    className="btn btn-primary btn-xs waves-effect waves-light text-white add-department-link"
                                                >
                                                    <i className="fe-plus" />
                                                </button>
                                            </OverlayTrigger>

                                            <OverlayTrigger
                                                placement="top"
                                                trigger="hover"
                                                overlay={(props) => (
                                                    <Tooltip
                                                        id={`edit_org_${organization.id}`}
                                                        {...props}
                                                    >
                                                        Edit Organization
                                                    </Tooltip>
                                                )}
                                            >
                                                <button
                                                    onClick={() =>
                                                        handleEditOrgModalSelected(
                                                            organization.id,
                                                            organization.name
                                                        )
                                                    }
                                                    className="btn btn-info edit-organizations-action btn-xs waves-effect waves-light text-white me-1"
                                                    style={{
                                                        marginLeft: "3px",
                                                    }}
                                                >
                                                    <i className="fe-edit" />
                                                </button>
                                            </OverlayTrigger>
                                        </div>
                                    </h5>
                                </div>

                                <Accordion.Collapse
                                    eventKey={`toggle_org_${organization.id}`}
                                >
                                    <div className={"card-body"}>
                                        {organization.departments.length > 0 ? (
                                            <div className="row">
                                                <div className="col-md-12">
                                                    <Nestable
                                                        items={
                                                            departmentsOrder[
                                                                organization.id
                                                            ]
                                                        }
                                                        onChange={(items) =>
                                                            handleChangeOrder(
                                                                organization.id,
                                                                items.items
                                                            )
                                                        }
                                                        renderItem={renderItem}
                                                        childrenProp={
                                                            "departments"
                                                        }
                                                        renderCollapseIcon={({
                                                            isCollapsed,
                                                        }) =>
                                                            isCollapsed ? (
                                                                <span className="iconCollapse">
                                                                    +
                                                                </span>
                                                            ) : (
                                                                <span className="iconCollapse">
                                                                    -
                                                                </span>
                                                            )
                                                        }
                                                    />
                                                </div>

                                                <div className="col-md-12">
                                                    <button
                                                        type="submit"
                                                        className="btn btn-primary float-end mt-3"
                                                        disabled={loading}
                                                        onClick={() =>
                                                            handleSaveChanges(
                                                                organization.id
                                                            )
                                                        }
                                                    >
                                                        {loading
                                                            ? "Saving"
                                                            : "Save changes"}
                                                    </button>
                                                </div>
                                            </div>
                                        ) : null}
                                    </div>
                                </Accordion.Collapse>
                            </div>
                        ))}
                    </Accordion>
                ) : (
                    <>
                        <p className="sub-header text-center">
                            <strong>Organization</strong> must be added before
                            proceeding further.
                        </p>
                        <div className="row">
                            <div className="col-lg-4 col-sm-6 offset-lg-4 offset-sm-3">
                                <div className="card">
                                    <a
                                        href="#"
                                        className="card-body project-box project-div d-flex justify-content-center align-items-center"
                                        style={{
                                            minHeight: "15.5rem",
                                            fontSize: "4rem",
                                            color: "#323b43",
                                        }}
                                        onClick={() =>
                                            setAddOrgModalShown(true)
                                        }
                                    >
                                        <i className="mdi mdi-plus" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </>
                )}
            </div>
        </div>
    );
};

export default OrganizationsTab;
