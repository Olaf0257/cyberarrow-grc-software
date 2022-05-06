import React, {useEffect, useState} from "react";

import {useForm, usePage} from "@inertiajs/inertia-react";
import {useSelector} from "react-redux";

import moment from "moment/moment";
import Modal from "react-bootstrap/Modal";
import Select from "react-select";
import Datetime from "react-datetime";

const DuplicateProjectModal = ({show, handleClose, reload, selectedProject}) => {
    const {frequencies, timezones} = usePage().props;
    const [questionnaires, setQuestionnaires] = useState([]);
    const [vendors, setVendors] = useState([]);

    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);
    const {data, setData, errors, post, processing, reset, clearErrors, transform} = useForm({
        name: '',
        questionnaire_id: null,
        vendor_id: null,
        frequency: null,
        timezone: null,
        launch_date: moment(),
        due_date: null,
        data_scope: appDataScope
    });

    useEffect(() => {
        if(selectedProject){
            setData(previousData => ({
                ...previousData,
                name: `Copy - ${selectedProject.name}`,
                questionnaire_id: selectedProject.questionnaire_id,
                vendor_id: selectedProject.vendor_id,
                frequency: selectedProject.frequency,
                timezone: selectedProject.timezone
            }));
        }
    }, [selectedProject]);

    useEffect(() => {
        axiosFetch.get(route('third-party-risk.projects.options'), {
            params: {
                data_scope: appDataScope
            }
        }).then(({data}) => {
            setQuestionnaires(data.questionnaires);
            setVendors(data.vendors);
        })
    }, [appDataScope]);

    useEffect(() => {
        clearErrors();
    }, [show])

    const handleSubmit = e => {
        e.preventDefault();
        transform((data) => ({
            ...data,
            due_date: moment(data.due_date).format("YYYY-MM-DD HH:mm:ss"),
            launch_date: moment(data.launch_date).format("YYYY-MM-DD HH:mm:ss")
        }));
        post(route('third-party-risk.projects.store'), {
            onSuccess: () => {
                AlertBox({
                    title: "Project Scheduled!",
                    text: "This project has been scheduled for launch!",
                    // showCancelButton: true,
                    confirmButtonColor: '#b2dd4c',
                    confirmButtonText: 'OK',
                    closeOnConfirm: false,
                    icon: 'success',
                }, function () {
                    reset();
                    handleClose();
                    reload(true);
                });
            }
        });
    }

    return (
        <Modal
            size={'lg'}
            show={show}
            onHide={handleClose}
        >
            <Modal.Header className='px-3 pt-3 pb-0' closeButton>
                <Modal.Title className="my-0">Duplicate Project</Modal.Title>
            </Modal.Header>
            <form onSubmit={handleSubmit} method="post">
                <Modal.Body className="p-3">
                    <div className="row">
                        <div className="col-md-12 mb-3">
                            <label className="form-label">
                                Name <span className="required text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                className="form-control"
                                value={data.name}
                                onChange={e => setData('name', e.target.value)}
                            />
                            {errors.name && (
                                <div className="invalid-feedback d-block">
                                    {errors.name}
                                </div>
                            )}
                        </div>
                        <div className="col-md-12 mb-3">
                            <label className="form-label">
                                Questionnaire <span className="required text-danger">*</span>
                            </label>
                            <Select
                                value={questionnaires.find(q => q.value === data.questionnaire_id)}
                                options={questionnaires}
                                onChange={option => setData('questionnaire_id', option.value)}
                            />
                            {errors.questionnaire_id && (
                                <div className="invalid-feedback d-block">
                                    {errors.questionnaire_id}
                                </div>
                            )}
                        </div>
                        <div className="col-md-12 mb-3">
                            <label className="form-label">
                                Vendor <span className="required text-danger">*</span>
                            </label>
                            <Select
                                value={vendors.find(v => v.value === data.vendor_id)}
                                options={vendors}
                                onChange={option => setData('vendor_id', option.value)}
                            />
                            {errors.vendor_id && (
                                <div className="invalid-feedback d-block">
                                    {errors.vendor_id}
                                </div>
                            )}
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-4 mb-3">
                            <label className="form-label">
                                Launch Date <span className="required text-danger">*</span>
                            </label>
                            <Datetime
                                value={data.launch_date}
                                onChange={datetime => setData('launch_date', datetime)}
                                isValidDate={(currentDate) => currentDate.isAfter(moment().subtract(1, 'day'))}
                                inputProps={{
                                    readOnly: true
                                }}
                                closeOnSelect
                            />
                            {errors.launch_date && (
                                <div className="invalid-feedback d-block">
                                    {errors.launch_date}
                                </div>
                            )}
                        </div>
                        <div className="col-md-4 mb-3">
                            <label className="form-label">
                                Due Date <span className="required text-danger">*</span>
                            </label>
                            <Datetime
                                value={data.due_date}
                                onChange={datetime => setData('due_date', datetime)}
                                isValidDate={(currentDate) => currentDate.isAfter(data.launch_date)}
                                inputProps={{
                                    readOnly: true
                                }}
                                closeOnSelect
                            />
                            {errors.due_date && (
                                <div className="invalid-feedback d-block">
                                    {errors.due_date}
                                </div>
                            )}
                        </div>
                        <div className="col-md-4 mb-3">
                            <label className="form-label">
                                Timezone <span className="required text-danger">*</span>
                            </label>
                            <Select
                                value={timezones.find(t => t.value === data.timezone)}
                                options={timezones}
                                onChange={option => setData('timezone', option.value)}
                            />
                            {errors.timezone && (
                                <div className="invalid-feedback d-block">
                                    {errors.timezone}
                                </div>
                            )}
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-12 mb-3">
                            <label className="form-label">
                                Frequency <span className="required text-danger">*</span>
                            </label>
                            <Select
                                value={frequencies.find(f => f.value === data.frequency)}
                                options={frequencies}
                                onChange={option => setData('frequency', option.value)}
                            />
                            {errors.frequency && (
                                <div className="invalid-feedback d-block">
                                    {errors.frequency}
                                </div>
                            )}
                        </div>
                    </div>
                </Modal.Body>
                <Modal.Footer className='px-3 pt-0 pb-3'>
                    <button type="button" className="btn btn-secondary" onClick={handleClose}>
                        Close
                    </button>
                    <button type="submit" className="btn btn-primary" disabled={processing}>Launch Project</button>
                </Modal.Footer>
            </form>
        </Modal>
    )
};

export default DuplicateProjectModal;
