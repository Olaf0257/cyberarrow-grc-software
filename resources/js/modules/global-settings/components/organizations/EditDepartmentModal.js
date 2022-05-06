import React, {useState} from 'react';
import {Button, Modal} from "react-bootstrap";
import {Inertia} from "@inertiajs/inertia";
import { useDispatch } from "react-redux";
import { fetchDataScopeDropdownTreeData } from "../../../../store/actions/data-scope-dropdown";

const EditDepartmentModal = ({config, handleClose}) => {
    const dispatch = useDispatch();
    const [name, setName] = useState('');

    const [departments, setDepartments] = useState([]);
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState(null);

    const fetchDepartments = async () => {
        try{
            const response = await axiosFetch.get(route('global-settings.organizations.departments', config.organization_id));
            setDepartments(response.data.data);
        }catch(e)
        {}
    }

    const handleSubmit = () => {
        Inertia.post(route('global-settings.organizations.departments.update', [config.organization_id, config.department.id]), {
            name,
            parent_id: config.department.parent_id
        }, {
            onStart: () => setProcessing(true),
            onSuccess: () => {
                setProcessing(false);
                Inertia.reload({ only: ['organizations'] });
                handleClose();

                /* Updating the data scope dropdown data */
                dispatch(fetchDataScopeDropdownTreeData());
            },
            onError: (err) => {
                setErrors(err);
                setProcessing(false);
            }
        });
    };

    React.useEffect(() => {
        if(config.shown) {
            setErrors(null);
            setName(config.department.name);

            if(config.department.parent_id > 0) fetchDepartments();
        }
    }, [config]);

    return (
        <Modal show={config.shown} onHide={handleClose}>
            <Modal.Header className='px-3 pt-3 pb-0' closeButton>
                <Modal.Title className='my-0'>Edit Department</Modal.Title>
            </Modal.Header>
            <Modal.Body className="p-3">
                <div className="row">
                    <div className="col-md-12">
                        <div className="mb-3">
                            <label htmlFor="department-name" className="form-label">Name</label>
                            <input type="text" name="name" className="form-control" id="department-name"
                                   placeholder="Department Name" value={name} onChange={e => setName(e.target.value)} required />
                            {errors && errors.name && (
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
                            <label htmlFor="parent_id" className="form-label">Parent department</label>
                            <select disabled name="parent_id" id="parent_id" value={config.department.parent_id ?? ''} className="form-control cursor-pointer parent-department">
                                <option value="">No Parent</option>
                                {departments.map(d => <option value={d.id} key={d.id}>{d.name}</option>)}
                                {/*/!**!/*/}
                            </select>
                        </div>
                    </div>
                </div>
            </Modal.Body>
            <Modal.Footer className='px-3 pt-0 pb-3'>
                <Button variant="secondary" onClick={handleClose}>
                    Close
                </Button>
                <Button variant="info" onClick={handleSubmit} disabled={processing}>
                    {processing ? 'Saving' : 'Save Changes'}
                </Button>
            </Modal.Footer>
        </Modal>

    )
};

export default EditDepartmentModal;