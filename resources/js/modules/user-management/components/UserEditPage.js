import React, { useEffect, useRef, useState } from 'react';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-react'
import { useForm, Controller } from "react-hook-form";
import IntlTelInput from 'react-intl-tel-input';
import Select from 'react-select';
import Switch from 'rc-switch';
import 'rc-switch/assets/index.css'
import { Button, Modal, Tabs, Tab } from 'react-bootstrap';
import TreeSelect from "rc-tree-select";
import "rc-tree-select/assets/index.less";
import "rc-tree/assets/index.css";
import 'react-intl-tel-input/dist/main.css';
import '../../../../../public/assets/css/custom.css'
import LoadingButton from '../../../common/loading-button/LoadingButton';
import UserLayout from '../UserLayout';
import '../styles/mfa.css';
import FlashMessages from '../../../common/FlashMessages';

function UserEditPage(props) {

    const selectRolesRef = useRef();

    const [isFormSubmitting, setIsFormSubmitting] = useState(false);
    const [loading, setLoading] = useState(false);
    const propsData = usePage().props;
    const selectDepartments = propsData.departmentTreeData;
    const selectRoles = propsData.roles;
    const user = propsData.admin;
    const loggedInUser = propsData.loggedInUser;
    const hasMFA = propsData.hasMFA;
    const isGlobalAdmin = propsData.isGlobalAdmin;

    const [userRoles, setUserRoles] = useState([]);
    const [userDepartment, setUserDepartment] = useState('');
    const [contactNumberCountryCode, setContactNumberCountryCode] = useState('');
    const [defaultCountryCode, setDefaultCountryCode] = useState('NP')
    const [apiErrors, setApiErrors] = useState(usePage().props.errors);
    const [apiMFAErrorMessages, setApiMFAErrorMessages] = useState('');
    const [activeTab, setActiveTab] = useState('edit');

    //Update User
    const { register, reset, formState: { errors }, control, handleSubmit, getValues } = useForm({
        mode: 'onSubmit',
    });

    //Change Password
    const { register: register2, formState: { errors: errors2 }, handleSubmit: handleSubmit2, getValues: getValues2 } = useForm({
        mode: 'onSubmit',
    });

    //MFA Form
    const { register: register3, formState: { errors: errors3 }, handleSubmit: handleSubmit3, getValues: getValues3 } = useForm({
        mode: 'onSubmit',
    });

    const [modalShow, setModalShow] = React.useState(false);
    const [QRCode, setQRCode] = useState();
    const [secretToken, setSecretToken] = useState();

    let nonStateRoleOptions = [];
    let selectData;
    for (let selectRole of selectRoles) {
        selectData = { label: selectRole, value: selectRole };
        nonStateRoleOptions.push(selectData);
    }
    const [roleOptions, setRoleOptions] = useState(nonStateRoleOptions);

    /* Setting the form field value on load*/
    useEffect(() => {
        document.title = "Edit User";
        reset({
            auth_method: user.auth_method,
            first_name: user.first_name,
            last_name: user.last_name,
            email: user.email,
            roles: propsData.admin.roles.map((role) => role.name)
        });
        setUserRoles(propsData.admin.roles);
        setUserDepartment(propsData.departmentId);
        setLoading(true);
    }, [user]);

    function disableUser(id) {
        axiosFetch.get(route('admin-user-management-disable-user', id))
            .then(res => {
                if (res.status === 200 && res.data.success) {
                    AlertBox({
                        text: res.data.message,
                        confirmButtonColor: '#b2dd4c',
                        icon: 'success',
                    });
                    Inertia.visit(route('admin-user-management-edit', id))
                } else {
                    AlertBox({
                        text: res.data.message,
                        confirmButtonColor: '#f1556c',
                        icon: 'error',
                    });
                }
            })
            .catch(
                function (e) {
                    console.log(e);
                }
            );
    }

    const handleUserDisable = async (id) => {
        axiosFetch.get(route('user.project-assignments', id))
            .then(res => {
                if (res.data.data > 0) {
                    var selectOptions = {}
                    axiosFetch.get(route('user.assignments-transferable-users', id)).then(res => {
                        if (res.data.success) {
                            let users = res.data.data;
                            Object.keys(users).forEach((index) => {
                                selectOptions[users[index].id] = `${users[index].full_name} - ${users[index].email}`;
                            });
                        }

                        AlertBox({
                            input: 'select',
                            confirmButtonColor: '#b2dd4c',
                            imageUrl: `${appBaseURL}/assets/images/info1.png`,
                            imageWidth: 120,
                            title: 'Select a user to transfer responsibility to:',
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
                            },
                        },
                            function (confirmed) {
                                if (confirmed.isConfirmed && confirmed.value) {
                                    axiosFetch.post(route('user.transfer-assignments', id), { transfer_to: confirmed.value }).then(
                                        res => {
                                            if (res.data.success) {
                                                disableUser(id)
                                            } else {
                                                AlertBox({
                                                    title: 'Oops...',
                                                    text: res.data.message,
                                                    icon: 'error',
                                                    confirmButtonColor: '#ff0000'
                                                })
                                            }
                                        });
                                }
                            });

                    });
                }
                else {
                    disableUser(id)
                }
            });

    }

    //Make Enable
    const makeEnable = () => {
        axiosFetch.get(route('admin-user-management-activate-user', user.id))
            .then(res => {
                if (res.status === 200 && res.data.success) {
                    AlertBox({
                        text: res.data.message,
                        confirmButtonColor: '#b2dd4c',
                        icon: 'success',
                    });
                    Inertia.get('/users/edit/' + user.id);
                } else {
                    AlertBox({
                        text: res.data.message,
                        confirmButtonColor: '#f1556c',
                        icon: 'error',
                    });
                }
            })
            .catch(
                function (e) {
                    console.log(e);
                }
            );
    };

    //Resend Activation
    function resendActivation() {
        AlertBox({
            title: "Are you sure?",
            text: "Do you want to resend the activation email?",
            showCancelButton: true,
            confirmButtonText: 'Yes, Send!',
            confirmButtonColor: '#f1556c',
            allowOutsideClick: false,
            icon: 'warning',
        }, function (confirmed) {
            if (confirmed.value && confirmed.value == true) {
                setIsFormSubmitting(true);
                Inertia.get(route('users.resend-email-verification-link', user.id, {
                    onFinish: () => {
                        setIsFormSubmitting(false);
                    }
                }))
            }
        })

    }

    //MFA Functions
    const setupMFA = async () => {
        // '/mfa/setup-mfa'
        let response = await axiosFetch.get(route('setup-mfa'))

        if (response.status === 200) {
            let data = response.data;
            setQRCode(data.data.as_qr_code);
            setSecretToken(data.data.as_string);
        }
        setModalShow(true)
    }

    // Submit Enable MFA Login
    const onEnableMFALogin = (data) => {
        const formData = getValues3();
        // axiosFetch.post('/mfa/validate-mfa-code', formData)
        axiosFetch.post(route('validate-mfa-code'), formData)
            .then(res => {
                if (res.data) {
                    // console.log('Validation Success');
                    axiosFetch.post(route('confirm-mfa'), formData).then(response => {
                        AlertBox({
                            text: response.data.data.message,
                            confirmButtonColor: '#b2dd4c',
                            allowOutsideClick: false,
                            icon: 'success',
                        }, function (confirmed) {
                            if (confirmed.value && confirmed.value == true) {
                                Inertia.visit(route('admin-user-management-edit', user.id));
                            }
                        })
                    })
                } else {
                    setApiMFAErrorMessages('The Code is invalid or expired!')
                }
            })
            .catch(
                function (e) {
                    console.log(e);
                    AlertBox({
                        text: res.data,
                        confirmButtonColor: '#b2dd4c',
                        icon: 'success',
                    });
                }
            );
    };

    //Reset MFA
    const resetMFA = async () => {
        AlertBox({
            title: "Are you sure?",
            text: "MFA will be reset. You'll have to setup again.",
            showCancelButton: true,
            confirmButtonColor: '#b2dd4c',
            confirmButtonText: 'Yes, Reset!',
            icon:'success',
        }, function (confirmed) {
            if (confirmed.value && confirmed.value == true) {
                axiosFetch.post(route('reset-mfa'))
                    .then(res => {
                        if (res.data.success) {
                            AlertBox({
                                text: 'MFA Reset Successful',
                                confirmButtonColor: '#b2dd4c',
                                allowOutsideClick: false,
                                icon: 'success',
                            }, function (confirmed) {
                                if (confirmed.value && confirmed.value == true) {
                                    Inertia.visit(route('admin-user-management-edit', user.id));
                                }
                            })
                        } else {
                        }
                    })
                    .catch(
                        function (e) {
                            console.log(e);
                            alert('Failed!');
                        }
                    );
            }
        })
    }

    // Update Form Submission
    const onSubmit = (data) => {
        setIsFormSubmitting(true);
        const formData = getValues();
        formData.contact_number_country_code = contactNumberCountryCode;
        Inertia.post(route('admin-user-management-update', user.id), formData, {
            preserveState: false,
            errorBag: 'updateProfile',
            onFinish: () => {
                setIsFormSubmitting(false);
            }
        })
    };

    // Change Password Form Submission
    const onChangePasswordSubmit = (data) => {
        setIsFormSubmitting(true);
        const formData = getValues2();
        Inertia.post(route('admins.update-password', user.id), formData, {
            preserveState: false,
            errorBag: 'updatePassword',
            onFinish: () => {
                setIsFormSubmitting(false);
            }
        });
    };

    useEffect(() => {
        if(apiErrors.updatePassword && Object.keys(apiErrors.updatePassword).length > 0){
            setActiveTab('change_password');
        }
    },[isFormSubmitting]);

    useEffect(() => {
        //setDefaultCountryCode if contactNumber is Empty
        if (!user.contact_number) {
            fetch('https://ipapi.co/json/')
                .then(res => res.json())
                .then(response => {
                    setDefaultCountryCode(response.country_code.toLowerCase())
                })
                .catch((data) => {
                    console.log('IPAPI JSON REQUEST FAILED:', data);
                });
        }

        // Roles and Dept Dependent Conditions
        let selectedRoles = propsData.admin.roles;
        let rolesArray = [];
        let temp;
        for (let value of selectedRoles) {
            temp = { label: value.name, value: value.name };
            rolesArray.push(temp);
        }
        onRolesChange(rolesArray);
    }, []);

    const onDepartmentChange = (departmentId) => {
        // clear selected roles
        let filtered = selectRolesRef.current.state.selectValue.filter((item) => item.label == 'Global Admin')
        if (filtered[0]) {
            selectRolesRef.current.clearValue();
            reset({
                ...getValues(),
                roles: null
            });
        }

        if (departmentId == 0) {
            if (!getValues().roles) {
                roleOptions.filter((role) => role.value == 'Global Admin')[0]['isDisabled'] = false;
            }
        } else {
            roleOptions.filter((role) => role.value == 'Global Admin')[0]['isDisabled'] = true;
        }
    };

    const onRolesChange = (val) => {
        if (val[0]) {
            let globalAdminIsSelected = val.filter((item) => item.label == 'Global Admin');
            if (globalAdminIsSelected[0]) {
                roleOptions.filter((role) => role.value == 'Auditor')[0]['isDisabled'] = true;
                roleOptions.filter((role) => role.value == 'Contributor')[0]['isDisabled'] = true;
                roleOptions.filter((role) => role.value == 'Compliance Administrator')[0]['isDisabled'] = true;
                roleOptions.filter((role) => role.value == 'Policy Administrator')[0]['isDisabled'] = true;
                roleOptions.filter((role) => role.value == 'Risk Administrator')[0]['isDisabled'] = true;
                roleOptions.filter((role) => role.value == 'Third Party Risk Administrator')[0]['isDisabled'] = true;
            }
            else {
                roleOptions.filter((role) => role.value == 'Global Admin')[0]['isDisabled'] = true;
                roleOptions.filter((role) => role.value == 'Auditor')[0]['isDisabled'] = false;
                roleOptions.filter((role) => role.value == 'Contributor')[0]['isDisabled'] = false;
                roleOptions.filter((role) => role.value == 'Compliance Administrator')[0]['isDisabled'] = false;
                roleOptions.filter((role) => role.value == 'Policy Administrator')[0]['isDisabled'] = false;
                roleOptions.filter((role) => role.value == 'Risk Administrator')[0]['isDisabled'] = false;
                roleOptions.filter((role) => role.value == 'Third Party Risk Administrator')[0]['isDisabled'] = false;
            }
        }
        else {
            if (getValues().department_id == 0) {
                roleOptions.filter((role) => role.value == 'Global Admin')[0]['isDisabled'] = false;
            }
            roleOptions.filter((role) => role.value == 'Auditor')[0]['isDisabled'] = false;
            roleOptions.filter((role) => role.value == 'Contributor')[0]['isDisabled'] = false;
            roleOptions.filter((role) => role.value == 'Compliance Administrator')[0]['isDisabled'] = false;
            roleOptions.filter((role) => role.value == 'Policy Administrator')[0]['isDisabled'] = false;
            roleOptions.filter((role) => role.value == 'Risk Administrator')[0]['isDisabled'] = false;
            roleOptions.filter((role) => role.value == 'Third Party Risk Administrator')[0]['isDisabled'] = false;
        }
    }

    const breadcumbsData = {
        "title": 'User Management Page',
        "breadcumbs": [
            {
                "title": "User management",
                "href": ""
            },
            {
                "title": "Users",
                "href": route('admin-user-management-view')
            },
            {
                "title": "Edit",
                "href": ""
            },
        ]
    };

    return (
        <UserLayout breadcumbsData={breadcumbsData}>

            <div id="page-wrapper">
                <FlashMessages />
                <div className="row">
                    <div className="col-12">
                        <div className='card'>
                            <div className="card-body project-box">
                                <div className="row">
                                    <div className="col-3 left-box-col box-col">
                                        <div className="left-box">
                                            <h3 className="text-capitalize">{user.full_name}</h3>
                                            <span className="status text-capitalize badge bg-success ms-1">{user.status}</span>

                                            <div className="py-2">
                                                {userRoles.map((role, i) => {
                                                    return <span key={i} className="badge bg-primary">{role.name}</span>
                                                })}
                                            </div>

                                            <div className="pt-2">
                                                {user.status == 'active' && loggedInUser.id === user.id &&
                                                    <Button variant="primary" className={hasMFA ? "btn btn-primary reset-mfa" : "btn btn-primary setup-mfa"} onClick={() => hasMFA ? resetMFA() : setupMFA()}>
                                                        {hasMFA ? "Reset MFA" : "Set up MFA"}
                                                    </Button>
                                                }

                                                {
                                                    isGlobalAdmin &&
                                                    user.status == 'unverified' &&
                                                    <LoadingButton
                                                        onClick={() => resendActivation()}
                                                        className="btn btn-primary resend-activation"
                                                        loading={isFormSubmitting}
                                                        disabled={isFormSubmitting}
                                                    >
                                                        Resend Activation
                                                    </LoadingButton>
                                                }

                                                {
                                                    isGlobalAdmin &&
                                                    user.status == 'active' && user.id != loggedInUser.id &&
                                                    <a
                                                        style={{ color: 'white', cursor: 'pointer' }}
                                                        className='btn btn-secondary disable-user'
                                                        data-user-id={user.id}
                                                        data-assignment-transferable-user-url={`${appBaseURL}/users/` + user.id + `/assignments-transferable-users`}
                                                        data-user-project-assignments-url={`${appBaseURL}/users/` + user.id + `/project-assignments`}
                                                        href={void (0)}
                                                        data-transfer-assignments-url={`${appBaseURL}/users/` + user.id + `/transfer-assignments`}
                                                        onClick={() => handleUserDisable(user.id)}
                                                    >
                                                        Disable
                                                    </a>
                                                }

                                                {
                                                    isGlobalAdmin &&
                                                    user.status == 'disabled' && user.id != loggedInUser.id &&
                                                    <a className='btn btn-primary'
                                                        data-user-id={user.id}
                                                        href="#"
                                                        onClick={makeEnable}
                                                    >
                                                        Activate
                                                    </a>
                                                }

                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-9 right-box-col box-col">
                                        <div className="right-box">
                                            <div className="table-responsive">
                                                <table className="table mb-0">

                                                    <tbody className="table-light">
                                                        <tr>
                                                            <td><i className="fas fa-envelope-open me-1"></i>Email</td>
                                                            <td>{user.email}</td>
                                                        </tr>

                                                        <tr>
                                                            <td><i className="fas fa-phone me-1"></i>Phone</td>
                                                            <td>(&nbsp;{user.contact_number_country_code} &nbsp;)&nbsp;{user.contact_number}</td>
                                                        </tr>

                                                        <tr>
                                                            <td><i className="far fa-calendar-alt me-1"></i>Created on</td>
                                                            <td>{user.created_date}</td>
                                                        </tr>

                                                        <tr>
                                                            <td><i className="far fa-calendar-alt me-1"></i>Last Modified</td>
                                                            <td>{user.updated_date}</td>
                                                        </tr>

                                                        <tr>
                                                            <td><i className="fas fa-sign-in-alt me-1"></i>Last Login</td>
                                                            <td>{user.last_login}</td>
                                                        </tr>

                                                        <tr>
                                                            <td><i className="fas fa-lock me-1"></i>MFA Secure Login</td>
                                                            <td>{hasMFA ? "Enabled" : "Disabled"}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* <!-- last row --> */}
                <div className="row">
                    <div className="col-xl-12">
                        <div className='card'>
                            <div className="card-body">
                                    {loading &&
                                        <Tabs activeKey={activeTab} id="uncontrolled-tab-example" className="mb-3" onSelect={(k) => {setActiveTab(k)}}>
                                            <Tab eventKey="edit" title="Edit">
                                                <form key={1} onSubmit={handleSubmit(onSubmit)} className="form-horizontal absolute-error-form" id="user-info-update-form">
                                                    <div className="row mb-3">
                                                        <label htmlFor="auth_method" className="col-3 col-form-label form-label">Auth Method <span className="required text-danger">*</span></label>
                                                        <div className="col-9">
                                                            <Controller
                                                                control={control}
                                                                render={({ field: { onChange } }) => (
                                                                    <Select
                                                                        className="react-select"
                                                                        classNamePrefix="react-select"
                                                                        ref={selectRolesRef}
                                                                        onChange={(val) => { onChange(val.value); }}
                                                                        options={[
                                                                            { label: 'Manual', value: 'Manual' },
                                                                            { label: 'SSO', value: 'SSO' },
                                                                            { label: 'LDAP', value: 'LDAP' },
                                                                        ]}
                                                                        defaultValue={
                                                                            { label: user.auth_method, value: user.auth_method }
                                                                        }
                                                                        isDisabled={user}
                                                                    />
                                                                )}
                                                            />
                                                        </div>
                                                    </div>

                                                    <div className="row mb-3">
                                                        <label htmlFor="firstname" className="col-3 col-form-label form-label">First Name <span className="required text-danger">*</span></label>
                                                        <div className="col-9">
                                                            <input type="text"
                                                                {...register("first_name", {
                                                                    required: true,
                                                                    maxLength: 35,
                                                                })}
                                                                className="form-control"
                                                                id="firstname"
                                                                name="first_name"
                                                                placeholder="First Name"
                                                                tabIndex={1}
                                                            />
                                                            {
                                                                errors.first_name && errors.first_name.type === "required" && (
                                                                    <div className="invalid-feedback d-block">The First Name field is required</div>
                                                                )
                                                            }
                                                            {
                                                                errors.first_name && errors.first_name.type === "maxLength" && (
                                                                    <div className="invalid-feedback d-block">The First Name may not be greater than 190 characters</div>
                                                                )
                                                            }
                                                        </div>
                                                    </div>

                                                    <div className="row mb-3">
                                                        <label htmlFor="lastname" className="col-3 col-form-label form-label">Last Name <span className="required text-danger">*</span></label>
                                                        <div className="col-9">
                                                            <input type="text"
                                                                {...register("last_name", {
                                                                    required: true,
                                                                    maxLength: 35,
                                                                })}
                                                                className="form-control"
                                                                id="lastname"
                                                                name="last_name"
                                                                placeholder="Last Name"
                                                                tabIndex={2}
                                                            />
                                                            {
                                                                errors.last_name && errors.last_name.type === "required" && (
                                                                    <div className="invalid-feedback d-block">The Last Name field is required</div>
                                                                )
                                                            }
                                                            {
                                                                errors.last_name && errors.last_name.type === "maxLength" && (
                                                                    <div className="invalid-feedback d-block">The Last Name may not be greater than 190 characters</div>
                                                                )
                                                            }
                                                        </div>
                                                    </div>

                                                    <div className="row mb-3">
                                                        <label htmlFor="email" className="col-3 col-form-label form-label">Email <span className="required text-danger">*</span></label>
                                                        <div className="col-9">
                                                            <input type="text"
                                                                {...register("email", {
                                                                    required: true,
                                                                    maxLength: 35,
                                                                    pattern: /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/
                                                                })}
                                                                className="form-control"
                                                                id="email"
                                                                name="email"
                                                                placeholder="Email"
                                                                tabIndex={3}
                                                            />
                                                            {
                                                                errors.email && errors.email.type === "required" && (
                                                                    <div className="invalid-feedback d-block">The Email field is required</div>
                                                                )
                                                            }
                                                            {
                                                                errors.email && errors.email.type === "maxLength" && (
                                                                    <div className="invalid-feedback d-block">The Email may not be greater than 190 characters</div>
                                                                )
                                                            }
                                                            {
                                                                errors.email && errors.email.type === "pattern" && (
                                                                    <div className="invalid-feedback d-block">The Email format is incorrect</div>
                                                                )
                                                            }
                                                            {
                                                                apiErrors.updateProfile && (
                                                                    <div className="invalid-feedback d-block">{apiErrors.updateProfile.email}</div>
                                                                )
                                                            }
                                                        </div>
                                                    </div>


                                                    <div className="row mb-3">
                                                        <label htmlFor="contact_number" className="col-3 col-form-label form-label">Contact Number </label>
                                                        <div className="col-9">
                                                            <Controller
                                                                name="contact_number"
                                                                control={control}
                                                                rules={{
                                                                    pattern: /^([0-9]*$)/,
                                                                    minLength: 9,
                                                                    maxLength: 15,
                                                                }}
                                                                render={({ field: { onChange } }) => (
                                                                    <IntlTelInput
                                                                        containerClassName="intl-tel-input"
                                                                        inputClassName="form-control"
                                                                        placeholder="Contact Number"
                                                                        onPhoneNumberChange={(status, number, country_info) => {
                                                                            onChange(number);
                                                                            setContactNumberCountryCode(country_info.iso2);
                                                                        }}
                                                                        onSelectFlag={(status, country_info) => {
                                                                            setContactNumberCountryCode(country_info.iso2);
                                                                        }}
                                                                        style={{ width: '100%' }}
                                                                        defaultValue={user.contact_number}
                                                                        defaultCountry={user.contact_number_country_code ?? defaultCountryCode}
                                                                        formatOnInit={false}
                                                                    />
                                                                )}
                                                            />
                                                            {
                                                                errors.contact_number && errors.contact_number.type === "required" && (
                                                                    <div className="invalid-feedback d-block">The Contact Number field is required</div>
                                                                )
                                                            }
                                                            {
                                                                errors.contact_number && errors.contact_number.type === "pattern" && (
                                                                    <div className="invalid-feedback d-block">The Contact Number may only contain digits</div>
                                                                )
                                                            }
                                                            {
                                                                errors.contact_number && errors.contact_number.type === "maxLength" && (
                                                                    <div className="invalid-feedback d-block">The Contact Number may not be greater than 11 characters</div>
                                                                )
                                                            }
                                                            {
                                                                errors.contact_number && errors.contact_number.type === "minLength" && (
                                                                    <div className="invalid-feedback d-block">The Contact Number may not be smaller than 9 characters</div>
                                                                )
                                                            }
                                                        </div>
                                                    </div>

                                                    <div className="row mb-3">
                                                        <label htmlFor="department_id" className="col-3 col-form-label form-label">User Department
                                                            <span className="required text-danger">*</span>
                                                        </label>
                                                        <div className="col-9">
                                                            <Controller
                                                                control={control}
                                                                name="department_id"
                                                                rules={{ required: true }}
                                                                defaultValue={userDepartment}
                                                                render={({ field: { onChange } }) => (
                                                                    <TreeSelect
                                                                        className="form-control"
                                                                        dropdownClassName="user-department-dropdown"
                                                                        dropdownStyle={{ zIndex: '1002', position: 'fixed' }}
                                                                        dropdownMatchSelectWidth
                                                                        treeLine="true"
                                                                        treeDefaultExpandAll
                                                                        style={{ width: '100%' }}
                                                                        treeIcon="&nbsp;"
                                                                        treeData={selectDepartments}
                                                                        onChange={value => { onChange(value); onDepartmentChange(value) }}
                                                                        defaultValue={userDepartment}
                                                                        disabled={isGlobalAdmin ? false : true}
                                                                    />
                                                                )}
                                                            />
                                                            {
                                                                errors.department_id && errors.department_id.type === "required" && (
                                                                    <div className="invalid-feedback d-block">The User Department field is required</div>
                                                                )
                                                            }
                                                        </div>
                                                    </div>

                                                    <div className="row mb-3">
                                                        <label htmlFor="roles" className="col-3 col-form-label form-label">User Roles
                                                            <span className="required text-danger">*</span>
                                                        </label>
                                                        <div className="col-9">
                                                            <Controller
                                                                control={control}
                                                                name="roles"
                                                                rules={{ required: true }}
                                                                render={({ field: { onChange, value, ref } }) => (
                                                                    <Select
                                                                        ref={selectRolesRef}
                                                                        onChange={(val) => { onChange(val.map(c => c.value)); onRolesChange(val); }}
                                                                        options={roleOptions}
                                                                        className="react-select"
                                                                        classNamePrefix="react-select"
                                                                        defaultValue={
                                                                            userRoles.map((role) => {
                                                                                return { label: role.name, value: role.name }
                                                                            })
                                                                        }
                                                                        isMulti
                                                                        isDisabled={isGlobalAdmin ? false : true}
                                                                    />
                                                                )}
                                                            />
                                                            {
                                                                errors.roles && errors.roles.type === "required" && (
                                                                    <div className="invalid-feedback d-block">The User Roles field is required</div>
                                                                )
                                                            }
                                                        </div>
                                                    </div>

                                                    {
                                                        isGlobalAdmin &&
                                                        user.status == 'active' &&
                                                        user.id != loggedInUser.id &&
                                                        <div className="row mb-3">
                                                            <label htmlFor="inputPassword5" className="col-3 col-form-label"> Require MFA</label>
                                                            <div className="col-9">
                                                                <Controller
                                                                    control={control}
                                                                    name="require_mfa"
                                                                    render={({ field: { onChange } }) => (
                                                                        <Switch
                                                                            className="switch-class"
                                                                            onChange={(val, event) => { onChange(val) }}
                                                                            options={
                                                                                {
                                                                                    color: '#b2dd4c',
                                                                                }
                                                                            }
                                                                            defaultChecked={user.require_mfa ? true : false}
                                                                        />
                                                                    )}
                                                                />
                                                            </div>
                                                        </div>
                                                    }

                                                    <div className="row mt-3">
                                                        <div className="d-flex ms-auto">
                                                            <button type="submit" className="btn btn-primary waves-effect waves-light">Update User</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </Tab>
                                            {user.auth_method == 'Manual' && user.status == 'active' &&
                                                <Tab eventKey="change_password" title="Change Password">
                                                    <div className="password">
                                                        <form key={2} onSubmit={handleSubmit2(onChangePasswordSubmit)} className="form-horizontal" name="update-password-form">
                                                            <input type="hidden" name="update-password-form" value="1" />
                                                            {user.id === loggedInUser.id &&
                                                                <div className="row mb-3">
                                                                    <label htmlFor="current_password" className="col-3 fomr-label col-form-label">Current Password <span className="required text-danger">*</span></label>
                                                                    <div className="col-9">
                                                                        <input
                                                                            {...register2("current_password", {
                                                                                required: true,
                                                                                maxLength: 190,
                                                                                onChange: () => setApiErrors([])
                                                                            })}
                                                                            className="form-control"
                                                                            tabIndex={8}
                                                                            type="password"
                                                                            name="current_password"
                                                                            id="current_password"
                                                                            placeholder="Current Password"/>

                                                                        <div className="invalid-feedback d-block">
                                                                            {
                                                                                errors2.current_password && errors2.current_password.type === "required" && (
                                                                                    <div className="invalid-feedback d-block">The current password field is required</div>
                                                                                )
                                                                            }
                                                                            {
                                                                                apiErrors.updatePassword && (
                                                                                    <div className="invalid-feedback d-block">{apiErrors.updatePassword.current_password}</div>
                                                                                )
                                                                            }
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            }


                                                            <div className="row mb-3">
                                                                <label htmlFor="new_password" className="col-3 col-form-label">New Password <span className="required text-danger">*</span> </label>
                                                                <div className="col-9">
                                                                    <input
                                                                        {...register2("new_password", {
                                                                            required: true,
                                                                            maxLength: 190,
                                                                            pattern: /^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[@$!#%*?&])(?=\S*[\d])\S*$/,
                                                                            onChange: () => setApiErrors([])
                                                                        })}
                                                                        className="form-control"
                                                                        tabIndex={9}
                                                                        type="password"
                                                                        name="new_password"
                                                                        id="new_password"
                                                                        placeholder="New Password"/>
                                                                    <div className="invalid-feedback d-block">
                                                                        {
                                                                            errors2.new_password && errors2.new_password.type === "required" && (
                                                                                <div className="invalid-feedback d-block">The new password field is required</div>
                                                                            )
                                                                        }
                                                                        {
                                                                            errors2.new_password && errors2.new_password.type === "pattern" && (
                                                                                <div className="invalid-feedback d-block">
                                                                                    Password must contain:
                                                                                    <ul style={{ paddingLeft: '1.5rem' }}>
                                                                                        <li> a minimum of 8 characters and </li>
                                                                                        <li> a minimum of 1 lower case letter and </li>
                                                                                        <li> a minimum of 1 upper case letter and </li>
                                                                                        <li> a minimum of 1 special character and </li>
                                                                                        <li> a minimum of 1 numeric character </li>
                                                                                    </ul>
                                                                                </div>
                                                                            )
                                                                        }
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div className="row mb-3">
                                                                <label htmlFor="new_password_confirmation" className="col-3 col-form-label">Confirm New Password <span className="required text-danger">*</span> </label>
                                                                <div className="col-9">
                                                                    <input
                                                                        {...register2("new_password_confirmation", {
                                                                            required: true,
                                                                            maxLength: 190,
                                                                            pattern: /^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[@$!#%*?&])(?=\S*[\d])\S*$/,
                                                                            onChange: () => setApiErrors([])
                                                                        })}
                                                                        className="form-control"
                                                                        tabIndex={10}
                                                                        type="password"
                                                                        name="new_password_confirmation"
                                                                        id="new_password_confirmation"
                                                                        placeholder="Confirm New Password"/>
                                                                    <div className="invalid-feedback d-block">
                                                                        {
                                                                            errors2.new_password_confirmation && errors2.new_password_confirmation.type === "required" && (
                                                                                <div className="invalid-feedback d-block">The new password confirmation field is required</div>
                                                                            )
                                                                        }
                                                                        {
                                                                            errors2.new_password_confirmation && errors2.new_password_confirmation.type === "pattern" && (
                                                                                <div className="invalid-feedback d-block">
                                                                                    Password must contain:
                                                                                    <ul style={{ paddingLeft: '1.5rem' }}>
                                                                                        <li> a minimum of 8 characters and </li>
                                                                                        <li> a minimum of 1 lower case letter and </li>
                                                                                        <li> a minimum of 1 upper case letter and </li>
                                                                                        <li> a minimum of 1 special character and </li>
                                                                                        <li> a minimum of 1 numeric character </li>
                                                                                    </ul>
                                                                                </div>
                                                                            )
                                                                        }
                                                                        {
                                                                            apiErrors.updatePassword && (
                                                                                <div className="invalid-feedback d-block" dangerouslySetInnerHTML={{ __html: apiErrors.updatePassword.new_password }}></div>
                                                                            )
                                                                        }
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <LoadingButton
                                                                className="btn btn-primary d-flex ms-auto"
                                                                type="submit"
                                                                loading={isFormSubmitting}
                                                            >
                                                                Confirm
                                                            </LoadingButton>
                                                        </form>
                                                    </div>
                                                </Tab>
                                            }
                                        </Tabs>
                                    }
                                </div>
                       </div>
                    </div>
                    {/* <!-- end card-body --> */}
                </div >
                {/* <!-- end col --> */}
            </div >
            {/* <!-- last row ends --> */}
            {/* <!-- end of breadcrumbs --> */}


            {/* <SetupMFA
                qrcode={QRCode}
                secrettoken={secretToken}
                show={modalShow}
                onHide={() => setModalShow(false)}
            /> */}


            <Modal
                show={modalShow}
                onHide={() => setModalShow(false)}
                size="lg"
                aria-labelledby="contained-modal-title-vcenter"
                centered
            >
                <Modal.Header className='px-3 pt-3 pb-0' closeButton>
                    <div className="top-text">
                        <h4>Setup MFA for your Account</h4>
                        <div className="instruction__box my-2">
                            <p className="text-white">In order to use Multi Factor Authentication, you will need to install an authenticator application such as 'Google Authenticator'.</p>
                        </div>
                        <h5 className="text-dark">Secret Token:
                            <span
                                className="text-muted token"
                                id="secret-token-wp">
                                {secretToken}
                            </span>
                        </h5>
                    </div>
                </Modal.Header>
                <Modal.Body className='p-3'>
                    <div className="qrcode-box">
                        <div className="row">
                            <div className="col-xl-5 col-lg-5 col-md-5 col-sm-6 col-12">
                                <div dangerouslySetInnerHTML={{
                                    __html: QRCode
                                }} className="qcode-left" id="mfa-qrcode-wp">
                                </div>
                            </div>

                            <div className="col-xl-7 col-lg-7 col-md-7 col-sm-6 col-12">
                                <div className="qrcode-right">
                                    <p className="text-dark qrcode-right-text pt-1">Scan the barcode or type out the token to  add the token to the authenticator.</p>
                                    <p className="text-dark qrcode-right-text">A new token will be generated everytime  you refresh or disable/enable MFA.</p>
                                    <p className="text-dark mb-2 qrcode-right-text">Please enter the first code that shows in  the authenticator.</p>

                                    <form key={3} onSubmit={handleSubmit3(onEnableMFALogin)} id="set-up-mfa">
                                        <div className="row">
                                            <div className="col-lg-7 col-md-7 col-sm-9">
                                                <input type="text"
                                                    {...register3("two_factor_code", {
                                                        required: true,
                                                        maxLength: 6,
                                                        minLength: 6,
                                                    })}
                                                    className="2fa__code form-control"
                                                    id="two_factor_code"
                                                    name="two_factor_code"
                                                    placeholder="123456"
                                                // tabIndex={3}
                                                />
                                                {
                                                    errors3.two_factor_code && errors3.two_factor_code.type === "required" && (
                                                        <div className="invalid-feedback d-block">The Code field is required</div>
                                                    )
                                                }
                                                {
                                                    errors3.two_factor_code && errors3.two_factor_code.type === "maxLength" && (
                                                        <div className="invalid-feedback d-block">The Code may not be greater than 6 characters</div>
                                                    )
                                                }
                                                {
                                                    errors3.two_factor_code && errors3.two_factor_code.type === "minLength" && (
                                                        <div className="invalid-feedback d-block">The Code may not be less than 6 characters</div>
                                                    )
                                                }
                                                {
                                                    apiMFAErrorMessages && (
                                                        <div className="invalid-feedback d-block">{apiMFAErrorMessages}</div>
                                                    )
                                                }
                                                {/* <input type="text" name="2fa_code" id="2fa_code" className="2fa__code form-control @if($errors->first('2fa_code')) is-invalid @endif" placeholder="123456" /> */}
                                            </div>
                                        </div>

                                        <button type="submit" className="btn btn-primary enable__btn mt-1">Enable Secure MFA Login</button>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </Modal.Body>
                <Modal.Footer className='px-3 pt-0 pb-3'>
                    <Button onClick={() => setModalShow(false)}>Close</Button>
                </Modal.Footer>
            </Modal >

        </UserLayout >
    );
}

export default UserEditPage;