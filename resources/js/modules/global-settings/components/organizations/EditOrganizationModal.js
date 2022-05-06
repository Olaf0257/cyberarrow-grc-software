import React, { useState } from "react";
import { Modal, Button } from "react-bootstrap";
import { useForm } from "@inertiajs/inertia-react";
import { useDispatch } from "react-redux";
import { fetchDataScopeDropdownTreeData } from "../../../../store/actions/data-scope-dropdown";

const EditOrganizationModal = ({ config, handleClose }) => {
    const dispatch = useDispatch();
    const [id, setId] = useState(-1);

    const { data, setData, processing, post, errors } = useForm({
        name: null,
    });

    React.useEffect(() => {
        setData("name", config.organization.name);
        setId(config.organization.id);
    }, [config]);

    const handleSubmit = () => {
        post(route("global-settings.organizations.update", id), {
            onSuccess: () => {
                handleClose();
                /* Updating the data scope dropdown data */
                dispatch(fetchDataScopeDropdownTreeData());
            },
        });
    };

    return (
        <Modal show={config.shown} onHide={handleClose}>
            <Modal.Header className="px-3 pt-3 pb-0" closeButton>
                <Modal.Title className="my-0">Edit Organization</Modal.Title>
            </Modal.Header>
            <Modal.Body className="p-3">
                <div className="row">
                    <div className="col-md-12">
                        <form>
                            <div className="mb-0">
                                <label
                                    htmlFor="organization-name"
                                    className="form-label"
                                >
                                    Name
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    className="form-control"
                                    id="organization-name"
                                    value={data.name ?? ""}
                                    onChange={(e) =>
                                        setData("name", e.target.value)
                                    }
                                />
                            </div>
                        </form>
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

export default EditOrganizationModal;
