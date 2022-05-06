import React, {useEffect, useState} from 'react';

import {Link, useForm, usePage} from '@inertiajs/inertia-react';
import {useSelector} from "react-redux";
import {Inertia} from '@inertiajs/inertia';

import BreadcumbsComponent from '../../../common/breadcumb/Breadcumb';
import AppLayout from '../../../layouts/app-layout/AppLayout';
import DataTable from '../../../common/data-table/DataTable';
import Modal from 'react-bootstrap/Modal';
import FlashMessages from "../../../common/FlashMessages";
import Select from "react-select";
import countryList from "react-select-country-list";
import { Dropdown } from 'react-bootstrap';
import "../style/style.scss";

const VendorModal = ({show, onClose, reload, vendor}) => {
    const {industries} = usePage().props;
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);
    const {data, setData, reset, processing, post, errors, transform, clearErrors} = useForm({
        name: '',
        contact_name: '',
        email: '',
        country: '',
        industry_id: null,
        data_scope: appDataScope
    });

    useEffect(() => {
        document.title = "Third Party Risk Vendors";
    }, []);

    useEffect(() => {
        if (!show) {
            // reset onClose
            reset();
            clearErrors();
        }
    }, [show]);

    useEffect(() => {
        if (vendor) {
            setData(previousData => ({
                ...previousData,
                name: vendor.name,
                contact_name: vendor.contact_name,
                email: vendor.email,
                country: vendor.country,
                industry_id: vendor.industry_id,
            }));
        }
    }, [vendor])

    useEffect(() => {
        setData('data_scope', appDataScope);
    }, [appDataScope]);

    const handleSubmit = e => {
        e.preventDefault();
        let url = route('third-party-risk.vendors.store');
        if (vendor) {
            url = route('third-party-risk.vendors.update', [vendor.id]);
            transform(data => ({
                ...data,
                _method: 'put'
            }));
        }

        post(url, {
            onSuccess: () => {
                onClose();
                reload();
            }
        });
    }
    const countries = countryList().getData().map(country => ({
        label: country.label,
        value: country.label
    }));

    return (
        <Modal
            show={show}
            onHide={onClose}
            size="lg"
        >
            <Modal.Header className='px-3 pt-3 pb-0' closeButton>
                <Modal.Title className='my-0'>
                    {vendor ? 'Edit Vendor' : 'Add Vendor'}
                </Modal.Title>
            </Modal.Header>
            <form onSubmit={handleSubmit}>
                <Modal.Body className='p-3'>
                <div className="row">
                    <div className="col-md-12">
                        <div className="mb-3">
                            <label htmlFor="vendor-name" className="form-label">
                                Name&nbsp;<span className="required text-danger">*</span>
                            </label>
                            <input className="form-control" id="vendor-name" value={data.name}
                                    onChange={e => setData('name', e.target.value)} placeholder="Vendor name"/>
                            {errors.name && (
                                <div className="invalid-feedback d-block">
                                    {errors.name}
                                </div>
                            )}
                        </div>
                        <div className="mb-3">
                            <label htmlFor="contact-name" className="form-label">
                                Contact Name&nbsp;<span className="required text-danger">*</span>
                            </label>
                            <input className="form-control" id="contact-name" value={data.contact_name}
                                    onChange={e => setData('contact_name', e.target.value)}
                                    placeholder="Contact name"/>
                            {errors.contact_name && (
                                <div className="invalid-feedback d-block">
                                    {errors.contact_name}
                                </div>
                            )}
                        </div>
                        <div className="mb-3">
                            <label htmlFor="email" className="form-label">
                                Email&nbsp;<span className="required text-danger">*</span>
                            </label>
                            <input type="email" className="form-control" id="email" value={data.email}
                                    onChange={e => setData('email', e.target.value)} placeholder="Email"/>
                            {errors.email && (
                                <div className="invalid-feedback d-block">
                                    {errors.email}
                                </div>
                            )}
                        </div>

                        <div className="mb-3">
                            <label className="form-label">Industry</label>
                            <Select
                                className='react-select'
                                classNamePrefix='react-select'
                                isClearable
                                placeholder="Select Industry... (optional)"
                                value={industries.find(i => i.value === data.industry_id)}
                                options={industries}
                                onChange={option => setData('industry_id', option ? option.value : '')}
                            />
                            {errors.industry_id && (
                                <div className="invalid-feedback d-block">
                                    {errors.industry_id}
                                </div>
                            )}
                        </div>

                        <div className="mb-0">
                            <label htmlFor="country" className="form-label">Country </label>
                            <Select
                                className='react-select'
                                classNamePrefix='react-select'
                                isClearable
                                placeholder="Select Country... (optional)"
                                value={countries.find(c => c.value === data.country)}
                                options={countries}
                                onChange={option => setData('country', option ? option.value : '')}
                            />
                            {errors.country && (
                                <div className="invalid-feedback d-block">
                                    {errors.country}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
                </Modal.Body>
                <Modal.Footer className='px-3 pt-0 pb-3'>
                    <button type="button" className="btn btn-secondary waves-effect" onClick={onClose}>Close</button>
                    <button type="submit" className="btn btn-primary waves-effect waves-light"
                            disabled={processing}>Submit
                    </button>
                </Modal.Footer>
            </form>
        </Modal>
    )
}

const Index = () => {
    const [selectedVendor, setSelectedVendor] = useState(null);
    const [refreshToggle, setRefreshToggle] = useState(false);
    const [vendorModalShown, setVendorModalShown] = useState(false);

    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);

    const handleDeleteVendor = (id) => {
        AlertBox({
            title: "Are you sure ?",
            text: "You will not be able to retrieve vendor!",
            showCancelButton: true,
            confirmButtonColor: '#f1556c',
            confirmButtonText: 'Yes, delete it!',
            icon:'warning',
            iconColor: '#f1556c',
        }, function (confirmed) {
            if (confirmed.value) {
                Inertia.delete(route('third-party-risk.vendors.destroy', [id]), {
                    onSuccess: () => {
                        reload();
                    }
                });
            }
        });
    }
    const handleEditVendor = (row) => {
        setSelectedVendor(row);
        setVendorModalShown(true);
    }

    const breadcumbsData = {
        "title": "Third Party Risk - Vendors",
        "breadcumbs": [
            {
                "title": "Third Party Risk",
                "href": ""
            },
            {
                "title": "Vendors",
                "href": route('third-party-risk.vendors.index')
            },
        ]
    };

    const fetchURL = route('third-party-risk.vendors.get-json-data');
    const columns = [
        {accessor: 'name', label: 'Vendor Name', priorityLevel: 1, position: 1, minWidth: 150,},
        {accessor: 'contact_name', label: 'Contact Name', priorityLevel: 1, position: 2, minWidth: 150,},
        {accessor: 'email', label: 'Contact Email', priorityLevel: 1, position: 3, minWidth: 50,},
        {accessor: 'country', label: 'Country', priorityLevel: 1, position: 4, minWidth: 150,},
        {
            accessor: 'industry',
            label: 'Industry',
            priorityLevel: 2,
            position: 5,
            minWidth: 150,
            CustomComponent: ({row}) => (<span>{row.industry?.name}</span>)

        },
        {
            accessor: '5', label: 'Action', priorityLevel: 0, position: 13, minWidth: 150, sortable: false,
            CustomComponent: ({row}) => {
                return (
                    <Dropdown className="d-inline-block">
                        <Dropdown.Toggle
                            as="a"
                            bsPrefix="card-drop arrow-none cursor-pointer"
                        >
                            <i className="mdi mdi-dots-horizontal m-0 text-muted h3" />
                        </Dropdown.Toggle>
                        <Dropdown.Menu className="dropdown-menu-end">
                            <button
                                className="dropdown-item d-flex align-items-center"
                                onClick={() => handleEditVendor(row)}
                            >
                                <i className="mdi mdi-square-edit-outline font-18 me-1"/> Edit
                            </button>
                            <button
                                className="dropdown-item d-flex align-items-center"
                                onClick={() => handleDeleteVendor(row.id)}
                            >
                                <i className="mdi mdi-delete-outline font-18 me-1"/> Delete
                            </button>
                        </Dropdown.Menu>
                    </Dropdown>
                );
            },
        },
    ];

    const reload = () => setRefreshToggle(!refreshToggle);
    const handleClose = () => {
        setVendorModalShown(false);
        setSelectedVendor(null);
    }

    useEffect(() => {
        reload();
    }, [appDataScope]);

    return (
        <AppLayout>
            <>
                <BreadcumbsComponent data={breadcumbsData}/>
                <FlashMessages/>
                <VendorModal
                    vendor={selectedVendor}
                    reload={reload}
                    show={vendorModalShown}
                    onClose={handleClose}
                />
                <div className="row">
                    <div className="col-12">
                        <div className='card'>
                            <div className="card-body">
                                <button
                                    type="button"
                                    className="btn btn-sm btn-primary waves-effect waves-light float-end"
                                    onClick={() => setVendorModalShown(true)}>
                                    <i className="mdi mdi-plus-circle"/> New Vendor
                                </button>
                                <DataTable
                                    search
                                    columns={columns}
                                    fetchURL={fetchURL}
                                    refresh={refreshToggle}
                                />
                            </div>
                        </div>
                    </div>
                </div>

            </>
        </AppLayout>
    );
}

export default Index;
