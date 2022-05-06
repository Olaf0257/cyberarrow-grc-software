import React, { useState, useRef, useEffect } from 'react';
import { useForm, Controller } from "react-hook-form";
import IntlTelInput from 'react-intl-tel-input';
import 'react-intl-tel-input/dist/main.css';
import Select from 'react-select';
import TreeSelect from "rc-tree-select";
import "rc-tree-select/assets/index.less";
import "rc-tree/assets/index.css";
import LoadingButton from '../../../common/loading-button/LoadingButton';
import { Inertia } from '@inertiajs/inertia';
import { Link, usePage } from '@inertiajs/inertia-react'
import FlashMessages from '../../../common/FlashMessages';
import UserLayout from '../UserLayout';

function UserCreatePage(props) {

    const { tenancy_enabled } = usePage().props;

    const selectRolesRef = useRef();
    const contactNumberRef = useRef();

    const [isFormSubmitting, setIsFormSubmitting] = useState(false);
    const propsData = usePage().props;
    const selectDepartments = propsData.departmentTreeData;
    const selectRoles = propsData.roles;
    const [contactNumberCountryCode, setContactNumberCountryCode] = useState('');
    const apiErrorMessages = propsData.errors;
    const [defaultCountryCode, setDefaultCountryCode] = useState('NP')

    const [refresh, setRefresh] = useState(false);

    const { register, formState: { errors }, control, handleSubmit, getValues, reset } = useForm({
        mode: 'onSubmit',
    });

    const [authMethodOptions, setAuthMethodOptions] = useState([]);

    useEffect(() => {
        document.title = "Create New User";
        if (tenancy_enabled) {
            setAuthMethodOptions([
                { label: 'Manual', value: 'Manual' },
                { label: 'SSO', value: 'SSO' },
            ])
        } else {
            setAuthMethodOptions([
                { label: 'Manual', value: 'Manual' },
                { label: 'SSO', value: 'SSO' },
                { label: 'LDAP', value: 'LDAP' },
            ])
        }
    }, [])

    // Setting Default Country Flag from API
    useEffect(() => {
        fetch('https://ipapi.co/json/')
            .then(res => res.json())
            .then(response => {
                setDefaultCountryCode(response.country_code.toLowerCase())
            })
            .catch((data) => {
                console.log('IPAPI JSON REQUEST FAILED:', data);
            });
    }, [])

    let nonStateRoleOptions = [];
    let selectData;
    for (let selectRole of selectRoles) {
        selectData = { label: selectRole, value: selectRole };
        nonStateRoleOptions.push(selectData);
    }
    const [roleOptions] = useState(nonStateRoleOptions);

    const getLDAPUsers = () => {
        if (getValues().auth_method == "LDAP") {
            axiosFetch.get(route('get-ldap-user-info', { email: getValues().email })).then(res => {
                if (res.data.success) {
                    let data = res.data.data
                    reset({
                        auth_method: 'LDAP',
                        first_name: data.firstName,
                        last_name: data.lastName,
                        email: data.email,
                        contact_number: data.contactNumber
                    });
                    contactNumberRef.current.state.value = data.contactNumber
                    setRefresh(prevState => !prevState);
                }
            });
        }
    }
    // Form Submission
    const onSubmit = (data) => {
        setIsFormSubmitting(true);
        const formData = getValues();
        formData.contact_number_country_code = contactNumberCountryCode;
        Inertia.post(route('admin-user-management-store'), formData, {
            onFinish: () => setIsFormSubmitting(false)
        })
    };

    const onDepartmentChange = (departmentId) => {
        // clear selected roles
        let globalAdminIsSelected = selectRolesRef.current.state.selectValue.filter((item) => item.label == 'Global Admin')
        if (globalAdminIsSelected[0]) {
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
        "title": 'Create User',
        "breadcumbs": [
            {
                "title": "User Management",
                "href": ""
            },
            {
                "title": "Users",
                "href": route('admin-user-management-view')
            },
            {
                "title": "Create",
                "href": ""
            },
        ]
    };

    return (
        <UserLayout breadcumbsData={breadcumbsData}>
            <FlashMessages />
            <div className="row">
                <div className="col-12">
                    <div className="card">
                        <div className="card-body">
                            <form onSubmit={handleSubmit(onSubmit)} className="form-horizontal absolute-error-form">
                                <div className="form-group row mb-3">
                                    <label htmlFor="auth_method" className="col-3 col-form-label control-label">Auth Method <span className="required text-danger">*</span></label>
                                    <div className="col-9">
                                        <Controller
                                            control={control}
                                            name="auth_method"
                                            rules={{ required: true }}
                                            render={({ field: { onChange } }) => (
                                                <Select
                                                    className="react-select"
                                                    classNamePrefix="react-select"
                                                    ref={selectRolesRef}
                                                    onChange={(val) => { onChange(val.value); getLDAPUsers(); }}
                                                    options={authMethodOptions}
                                                />
                                            )}
                                        />
                                        {
                                            errors.auth_method && errors.auth_method.type === "required" && (
                                                <div className="invalid-feedback d-block">The Auth Method field is required</div>
                                            )
                                        }
                                    </div>
                                </div>

                                <div className="form-group row mb-3">
                                    <label htmlFor="firstname" className="col-3 col-form-label control-label">First Name <span className="required text-danger">*</span></label>
                                    <div className="col-9">
                                        <input type="text"
                                            {...register("first_name", {
                                                required: true,
                                                maxLength: 190,
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

                                <div className="form-group row mb-3">
                                    <label htmlFor="lastname" className="col-3 col-form-label control-label">Last Name <span className="required text-danger">*</span></label>
                                    <div className="col-9">
                                        <input type="text"
                                            {...register("last_name", {
                                                required: true,
                                                maxLength: 190,
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

                                <div className="form-group row mb-3">
                                    <label htmlFor="email" className="col-3 col-form-label control-label">Email <span className="required text-danger">*</span></label>
                                    <div className="col-9">
                                        <input type="text"
                                            {...register("email", {
                                                required: true,
                                                maxLength: 190,
                                                pattern: /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/,
                                                onBlur: getLDAPUsers
                                            })}
                                            className="form-control"
                                            id="email"
                                            name="email"
                                            placeholder="Email"
                                            tabIndex={3}
                                        />
                                        {
                                            errors.email && errors.email.type === "required" && (
                                                <div className="invalid-feedback d-block">The Email field is required...</div>
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
                                            apiErrorMessages.email && (
                                                <div className="invalid-feedback d-block">{apiErrorMessages.email}</div>
                                            )
                                        }
                                    </div>
                                </div>

                                <div className="form-group row mb-3">
                                    <label htmlFor="contact_number" className="col-3 col-form-label control-label">Contact Number </label>
                                    <div className="col-9">
                                        <Controller
                                            name="contact_number"
                                            control={control}
                                            rules={{ pattern: /^([0-9]*$)/, minLength: 9, maxLength: 15 }}
                                            render={({ field: { onChange } }) => (
                                                <IntlTelInput
                                                    ref={contactNumberRef}
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
                                                    defaultCountry={defaultCountryCode}
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
                                                <div className="invalid-feedback d-block">The Contact Number may not be greater than 12 characters</div>
                                            )
                                        }
                                        {
                                            errors.contact_number && errors.contact_number.type === "minLength" && (
                                                <div className="invalid-feedback d-block">The Contact Number may not be smaller than 9 characters</div>
                                            )
                                        }
                                        {
                                            apiErrorMessages.contact_number && (
                                                <div className="invalid-feedback d-block">{apiErrorMessages.contact_number}</div>
                                            )
                                        }
                                    </div>
                                </div>

                                <div className="form-group row mb-3">
                                    <label htmlFor="department_id" className="col-3 col-form-label control-label">User Department
                                        <span className="required text-danger">*</span>
                                    </label>
                                    <div className="col-9">
                                        <Controller
                                            control={control}
                                            name="department_id"
                                            rules={{ required: true }}
                                            render={({ field: { onChange } }) => (
                                                <TreeSelect
                                                    dropdownClassName="user-department-dropdown"
                                                    className="form-control"
                                                    dropdownStyle={{ zIndex: '1002', position: 'fixed' }}
                                                    dropdownMatchSelectWidth
                                                    treeLine="true"
                                                    treeDefaultExpandAll
                                                    style={{ width: '100%' }}
                                                    treeIcon="&nbsp;"
                                                    treeData={selectDepartments}
                                                    onChange={val => { onDepartmentChange(val); onChange(val); }}
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

                                <div className="form-group row mb-3">
                                    <label htmlFor="roles" className="col-3 col-form-label control-label">User Roles
                                        <span className="required text-danger">*</span>
                                    </label>
                                    <div className="col-9">
                                        <Controller
                                            control={control}
                                            name="roles"
                                            rules={{ required: true }}
                                            render={({ field: { onChange, value, ref } }) => (
                                                <Select
                                                    className="react-select"
                                                    classNamePrefix="react-select"
                                                    ref={selectRolesRef}
                                                    onChange={(val) => { onChange(val.map(c => c.value)); onRolesChange(val); }}
                                                    options={roleOptions}
                                                    isMulti
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

                                <div className="form-group d-flex justify-content-end">
                                    <LoadingButton
                                        className="btn btn-primary waves-effect waves-light"
                                        type="submit"
                                        loading={isFormSubmitting}
                                        disabled={isFormSubmitting}
                                    >
                                        Create
                                    </LoadingButton>
                                    <Link href={route('admin-user-management-view')} type="button" className="ms-2 btn btn-danger waves-effect waves-light" tabIndex="7">
                                        Back To List
                                    </Link>
                                </div>
                            </form>
                        </div >
                        {/* < !--end card box-- > */}
                    </div >
                </div >
            </div >
        </UserLayout >
    );
}

export default UserCreatePage;
