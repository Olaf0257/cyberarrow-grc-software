import React, { useState } from "react";
import { Button, Modal } from "react-bootstrap";
import { useForm } from "@inertiajs/inertia-react";
import { Inertia } from "@inertiajs/inertia";
import Select from "react-select";
import { useDispatch } from "react-redux";
import { fetchDataScopeDropdownTreeData } from "../../../../store/actions/data-scope-dropdown";

const AddDepartmentModal = ({ config, handleClose }) => {
    const dispatch = useDispatch();
    const [departments, setDepartments] = useState([]);
    const { data, setData, processing, errors, post, reset } = useForm({
        name: "",
        parent_id: null,
    });

    const fetchDepartments = async () => {
        try {
            const response = await axiosFetch.get(
                route(
                    "global-settings.organizations.departments",
                    config.organization_id
                )
            );

            const departmentsSelectOptions = response.data.data.map((d) => ({
                value: d.id,
                label: d.name,
            }));
            departmentsSelectOptions.push({
                value: 0,
                label: "No Parent",
            });
            setDepartments(departmentsSelectOptions);
        } catch (e) {}
    };

    const handleSubmit = () => {
        post(
            route(
                "global-settings.organizations.departments.store",
                config.organization_id
            ),
            {
                onSuccess: () => {
                    reset("name");
                    Inertia.reload({ only: ["organizations"] });
                    handleClose();

                    /* Updating the data scope dropdown data */
                    dispatch(fetchDataScopeDropdownTreeData());
                },
            }
        );
    };

    React.useEffect(() => {
        if (config.shown) {
            if (config.organization_id) {
                fetchDepartments();
            }
            // setData()
            setData("parent_id", config.department_id);
            // console.log('Updating');
        }
    }, [config]);

    return (
        <Modal show={config.shown} onHide={handleClose}>
            <Modal.Header className="px-3 pt-3 pb-0" closeButton>
                <Modal.Title className="my-0">Add Department</Modal.Title>
            </Modal.Header>
            <Modal.Body className="p-3">
                <div className="row">
                    <div className="col-md-12">
                        <div className="mb-3">
                            <label
                                htmlFor="department-name"
                                className="form-label"
                            >
                                Name
                            </label>
                            <input
                                type="text"
                                name="name"
                                className="form-control"
                                id="department-name"
                                placeholder="Department Name"
                                value={data.name}
                                onChange={(e) =>
                                    setData("name", e.target.value)
                                }
                                required
                            />
                            {errors.name && (
                                <div className="invalid-feedback d-block">
                                    {errors.name}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-12">
                        <div className="mb-0">
                            <label htmlFor="parent_id" className="form-label">
                                Parent department
                            </label>
                            <Select
                                className="react-select"
                                classNamePrefix="react-select"
                                value={departments.filter(
                                    (d) => d.value === data.parent_id
                                )}
                                onChange={(option) =>
                                    setData("parent_id", option.value)
                                }
                                options={departments}
                            />
                        </div>
                    </div>
                </div>
            </Modal.Body>
            <Modal.Footer className="px-3 pt-0 pb-3">
                <Button variant="secondary" onClick={handleClose}>
                    Close
                </Button>
                <Button
                    variant="info"
                    onClick={handleSubmit}
                    disabled={processing}
                >
                    {processing ? "Saving" : "Save Changes"}
                </Button>
            </Modal.Footer>
        </Modal>
    );
};

export default AddDepartmentModal;
