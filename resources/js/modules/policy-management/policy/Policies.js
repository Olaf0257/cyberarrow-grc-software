import React, { useState, useEffect, useCallback } from "react";

import { Modal, Alert, Dropdown } from "react-bootstrap";
import { useDropzone } from "react-dropzone";
import { useFieldArray, useForm } from "react-hook-form";

import AppLayout from "../../../layouts/app-layout/AppLayout";
import Breadcrumb from "../../../common/breadcumb/Breadcumb";
import DataTable from "../../../common/data-table/DataTable";

import Swal from "sweetalert2";
import { useSelector } from "react-redux";

import "./policies.scss";
import { Inertia } from "@inertiajs/inertia";
import FlashMessages from "../../../common/FlashMessages";

import * as Yup from "yup";
import { yupResolver } from "@hookform/resolvers/yup";
import CustomDropify from "../../../common/custom-dropify/CustomDropify";
import { useDidMountEffect } from "../../../custom-hooks";
import fileDownload from "js-file-download";
import { Link } from "@inertiajs/inertia-react";

const breadcumbsData = {
    title: "Policies - Policy Management",
    breadcumbs: [
        {
            title: "Policy Management",
            href: route("policy-management.campaigns"),
        },
        {
            title: "Policies",
            href: "#",
        },
    ],
};

const policies_fetchurl = "policy-management/policies/list";

const EditPolicyModal = ({
    appDataScope,
    policy,
    show,
    onClose,
    refresh,
    setRefresh,
}) => {
    const validationSchema = Yup.object().shape({
        display_name: Yup.string().required("Display name is required"),
        version: Yup.string().required("Version is required"),
        description: Yup.string().required("Description is required"),
        path: Yup.string().when("null", {
            is: () => policy?.type === "doculink",
            then: Yup.string().required("Url is required"),
        }),
    });
    const [file, setFile] = useState(null);
    const formOptions = {
        resolver: yupResolver(validationSchema, { abortEarly: false }),
    };
    const { handleSubmit, register, setValue, formState } =
        useForm(formOptions);
    const { errors } = formState;
    const [backendErrorMsg, setBackendErrorMsg] = useState("");

    useEffect(() => {
        document.title = "Policies";
        if (policy) {
            setValue("display_name", policy.display_name);
            setValue("version", policy.version);
            setValue("description", policy.description);
            if (policy.type === "doculink") setValue("path", policy.path);
        }
    }, [policy]);

    const submitHandler = (data) => {
        const formData = new FormData();
        formData.append("display_name", data.display_name);
        formData.append("version", data.version);
        formData.append("description", data.description);
        formData.append("data_scope", appDataScope);

        if (policy?.type === "document") {
            formData.append("policy_file", file ?? "");
        } else {
            formData.append("link", data.path);
        }

        Inertia.post(
            route("policy-management.policies.update-policies", policy.id),
            formData,
            {
                onSuccess: () => {
                    setRefresh(!refresh);
                    handleModalClose();
                },
                onError: (errors) => {
                    for (const key in errors) {
                        if (Object.hasOwnProperty.call(errors, key)) {
                            const element = errors[key];
                            setBackendErrorMsg(element);
                        }
                    }
                },
            }
        );
    };

    /* Trigger on modal close */
    const handleModalClose = () => {
        setBackendErrorMsg("");
        onClose();
    };

    return (
        <Modal show={show} size="lg" onHide={handleModalClose}>
            <Modal.Header className="px-3 pt-3 pb-0" closeButton>
                <Modal.Title className="my-0">Edit Policies</Modal.Title>
            </Modal.Header>
            <form onSubmit={handleSubmit(submitHandler)}>
                <Modal.Body className="p-3">
                    <div className="row">
                        {policy && policy.type === "document" ? (
                            <div className="col-12">
                                <CustomDropify
                                    onSelect={(file) => setFile(file)}
                                    file={file}
                                    accept={".png, .jpg, .jpeg, .gif, .pdf"}
                                    maxSize={10485760}
                                />

                                <div className="file-validation-limit">
                                    <div className="mb-3">
                                        <p>
                                            <span>Accepted File Types:</span>
                                        </p>
                                        <p>.png, .jpg, .jpeg, .gif, .pdf</p>
                                    </div>

                                    <p>
                                        <span>Maximum File Size: </span>10MB
                                    </p>
                                    <p>
                                        <span>Maximum Character Length:</span>{" "}
                                        250
                                    </p>
                                </div>
                            </div>
                        ) : null}
                        <div className="col-12">
                            {backendErrorMsg && (
                                <Alert variant="danger">
                                    <strong>{backendErrorMsg}</strong>
                                </Alert>
                            )}
                        </div>

                        <div className="col-6 mb-3">
                            <label
                                className="form-label"
                                htmlFor="display-name"
                            >
                                Display Name
                            </label>
                            <input
                                type="text"
                                placeholder="Display Name"
                                className="form-control"
                                {...register("display_name")}
                            />
                            {errors.display_name && (
                                <label className="invalid-feedback d-block">
                                    {errors.display_name.message}
                                </label>
                            )}
                        </div>
                        <div className="col-6 mb-3">
                            <label className="form-label" htmlFor="version">
                                Version
                            </label>
                            <input
                                type="text"
                                placeholder="Version"
                                className="form-control"
                                {...register("version")}
                            />
                            {errors.version && (
                                <label className="invalid-feedback d-block">
                                    {errors.version.message}
                                </label>
                            )}
                        </div>
                        {policy && policy.type === "doculink" ? (
                            <div className="col-12" id="link-input-section">
                                <label className="form-label" htmlFor="link">
                                    URL
                                </label>
                                <input
                                    type="text"
                                    placeholder="URL"
                                    className="form-control"
                                    {...register("path")}
                                />
                                {errors.path && (
                                    <label className="invalid-feedback d-block">
                                        {errors.path.message}
                                    </label>
                                )}
                            </div>
                        ) : null}
                        <div className="col-12">
                            <label className="form-label" htmlFor="link">
                                Description
                            </label>
                            <textarea
                                placeholder="Description"
                                cols={3}
                                rows={2}
                                className="form-control"
                                {...register("description")}
                            />

                            {errors.description && (
                                <label className="invalid-feedback d-block">
                                    {errors.description.message}
                                </label>
                            )}
                        </div>
                    </div>
                </Modal.Body>
                <Modal.Footer className="px-3 pt-0 pb-3">
                    <button
                        type="button"
                        className="btn btn-secondary waves-effect"
                        onClick={onClose}
                    >
                        Close
                    </button>
                    <button
                        type="submit"
                        className="btn btn-primary waves-effect waves-light"
                    >
                        Submit
                    </button>
                </Modal.Footer>
            </form>
        </Modal>
    );
};

const LinkPoliciesModal = React.memo(
    ({ appDataScope, show, onClose, refresh, setRefresh }) => {
        const validationSchema = Yup.object().shape({
            policies: Yup.array().of(
                Yup.object().shape({
                    display_name: Yup.string().test({
                        name: "custom",
                        message: "Display name is required",
                        test: function (value) {
                            return !(
                                !value &&
                                (this.parent.link ||
                                    this.parent.version ||
                                    this.parent.description)
                            );
                        },
                    }),
                    link: Yup.string().test({
                        name: "custom",
                        message: "Link is required",
                        test: function (value) {
                            return !(
                                !value &&
                                (this.parent.display_name ||
                                    this.parent.version ||
                                    this.parent.description)
                            );
                        },
                    }),
                    version: Yup.string().test({
                        name: "custom",
                        message: "Version is required",
                        test: function (value) {
                            return !(
                                !value &&
                                (this.parent.link ||
                                    this.parent.display_name ||
                                    this.parent.description)
                            );
                        },
                    }),
                    description: Yup.string().test({
                        name: "custom",
                        message: "Description is required",
                        test: function (value) {
                            return !(
                                !value &&
                                (this.parent.link ||
                                    this.parent.version ||
                                    this.parent.display_name)
                            );
                        },
                    }),
                })
            ),
        });
        const defaultFields = [
            {
                display_name: "",
                link: "",
                version: "",
                description: "",
            },
            {
                display_name: "",
                link: "",
                version: "",
                description: "",
            },
            {
                display_name: "",
                link: "",
                version: "",
                description: "",
            },
            {
                display_name: "",
                link: "",
                version: "",
                description: "",
            },
            {
                display_name: "",
                link: "",
                version: "",
                description: "",
            },
        ];
        const formOptions = {
            resolver: yupResolver(validationSchema, { abortEarly: false }),
        };
        const [backendErrorMsg, setBackendErrorMsg] = useState("");
        const [errorEffectedRows, setErrorEffectedRows] = useState([]);

        const { handleSubmit, register, control, formState, reset } =
            useForm(formOptions);
        const { errors } = formState;
        const { fields, append } = useFieldArray({ name: "policies", control });

        useEffect(() => {
            defaultFields.forEach((item) => append(item));
        }, []);

        const submitHandler = ({ policies }) => {
            const data = new FormData();
            policies.forEach(({ display_name, version, description, link }) => {
                data.append("display_name[]", display_name);
                data.append("version[]", version);
                data.append("description[]", description);
                data.append("link[]", link);
            });
            data.append("data_scope", appDataScope);

            Inertia.post(
                route("policy-management.policies.store-link-policies"),
                data,
                {
                    onSuccess: () => {
                        setRefresh(!refresh);

                        /* Resetting fields*/
                        handleModalClose();
                    },
                    onError: (errors) => {
                        let effectedRows = [];
                        let errorMsg = "";

                        for (const key in errors) {
                            let keyArray = key.split(".");

                            if (keyArray[0] == "display_name") {
                                effectedRows.push(parseInt(keyArray[1]));
                                errorMsg = errors[key];
                            }
                        }
                        setBackendErrorMsg(errorMsg);
                        setErrorEffectedRows(effectedRows);
                    },
                }
            );
        };
        /* Triggers when modal closes */
        const handleModalClose = () => {
            /* Resetting fields*/
            reset({
                policies: defaultFields,
            });
            setBackendErrorMsg("");
            setErrorEffectedRows([]);
            onClose();
        };

        return (
            <Modal show={show} onHide={handleModalClose} size={"lg"}>
                <Modal.Header className="px-3 pt-3 pb-0" closeButton>
                    <Modal.Title className="my-0">Link Policies</Modal.Title>
                </Modal.Header>

                <form
                    onSubmit={handleSubmit(submitHandler)}
                    id="create-link-policies-form"
                >
                    <Modal.Body className="p-3">
                        {backendErrorMsg && (
                            <Alert
                                variant="danger"
                                onClose={() => setBackendErrorMsg("")}
                                dismissible
                            >
                                <strong>{backendErrorMsg}</strong>
                            </Alert>
                        )}

                        <div className="table-responsive">
                            <table className="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Display Name</th>
                                        <th>Link</th>
                                        <th>Version</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {fields.map((field, i) => {
                                        return (
                                            <tr key={`row.${i}`}>
                                                <td>
                                                    <input
                                                        className={`form-control ${
                                                            errorEffectedRows.includes(
                                                                i
                                                            )
                                                                ? "border-danger"
                                                                : ""
                                                        }`}
                                                        name={`policies[${i}]display_name`}
                                                        {...register(
                                                            `policies.${i}.display_name`
                                                        )}
                                                        type="text"
                                                    />
                                                    <div className="invalid-feedback d-block text-truncate">
                                                        {
                                                            errors.policies?.[i]
                                                                ?.display_name
                                                                ?.message
                                                        }
                                                    </div>
                                                </td>
                                                <td>
                                                    <input
                                                        className={`form-control ${
                                                            errorEffectedRows.includes(
                                                                i
                                                            )
                                                                ? "border-danger"
                                                                : ""
                                                        }`}
                                                        name={`policies[${i}]link`}
                                                        {...register(
                                                            `policies.${i}.link`
                                                        )}
                                                        type="text"
                                                    />
                                                    <div className="invalid-feedback d-block text-truncate">
                                                        {
                                                            errors.policies?.[i]
                                                                ?.link?.message
                                                        }
                                                    </div>
                                                </td>
                                                <td>
                                                    <input
                                                        className={`form-control ${
                                                            errorEffectedRows.includes(
                                                                i
                                                            )
                                                                ? "border-danger"
                                                                : ""
                                                        }`}
                                                        name={`policies[${i}]version`}
                                                        {...register(
                                                            `policies.${i}.version`
                                                        )}
                                                        type="text"
                                                    />
                                                    <div className="invalid-feedback d-block text-truncate">
                                                        {
                                                            errors.policies?.[i]
                                                                ?.version
                                                                ?.message
                                                        }
                                                    </div>
                                                </td>
                                                <td>
                                                    <input
                                                        className={`form-control ${
                                                            errorEffectedRows.includes(
                                                                i
                                                            )
                                                                ? "border-danger"
                                                                : ""
                                                        }`}
                                                        name={`policies[${i}]description`}
                                                        {...register(
                                                            `policies.${i}.description`
                                                        )}
                                                        type="text"
                                                    />
                                                    <div className="invalid-feedback d-block text-truncate">
                                                        {
                                                            errors.policies?.[i]
                                                                ?.description
                                                                ?.message
                                                        }
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    </Modal.Body>
                    <Modal.Footer className="px-3 pt-0 pb-3">
                        <button
                            type="button"
                            className="btn btn-secondary waves-effect"
                            onClick={handleModalClose}
                        >
                            Close
                        </button>
                        <button
                            type="submit"
                            className="btn btn-primary waves-effect waves-light"
                        >
                            Submit
                        </button>
                    </Modal.Footer>
                </form>
            </Modal>
        );
    }
);

function Policies() {
    const appDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope.value
    );
    const [show, setShow] = useState(false);
    const [loaded, setLoaded] = useState(null);
    const [uploadFiles, setUploadFiles] = useState([]);
    const [fileUploadErrorMessage, setFileUploadErrorMessage] = useState([]);

    const [meta, setMeta] = useState([]);
    const [refresh, setRefresh] = useState(false);
    const [linkPoliciesShow, setLinkPoliciesShow] = useState(false);

    const [selectedLink, setSelectedLink] = useState(null);
    const [editPolicyShow, setEditPolicyShow] = useState(false);


    const [uploadPolicyMsg, setUploadPolicyMsg] = useState("");

    const {
        register,
        handleSubmit,
        setValue,
        getValues,
        formState: { errors },
        reset,
    } = useForm({
        defaultValues: {},
    });

    const handleDeletePolicy = (id) => {
        Swal.fire({
            title: "Are you sure?",
            text: "You will not be able to recover this policy!",
            showCancelButton: true,
            confirmButtonColor: "#ff0000",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false,
            icon: 'warning',
            iconColor: '#ff0000',
        }).then((confirmed) => {
            if (confirmed.value) {
                Inertia.get(
                    route("policy-management.policies.delete-policies", id),
                    {
                        data_scope: appDataScope,
                    },
                    {
                        onSuccess: () => {
                            setRefresh(!refresh);
                        },
                    }
                );
            }
        });
    };

    /* Downloads policies */
    const handleDownloadPolicy = async (url) => {
        let { data, headers } = await axiosFetch.get(url, {
            params: { data_scope: appDataScope },
            responseType: "blob", // Important
        });
        let fileNameStr = headers["content-disposition"].split("filename=")[1];
        let fileName = fileNameStr.substring(1, fileNameStr.length - 1);

        const extension = fileName.split('.').reverse()[0];
        const isValid = ['jpg', 'gif', 'jpeg', 'ico', 'webp', 'png','pdf'].includes(extension);
        if(isValid){
            fileDownload(data, fileName);
        }
        else{
            fileDownload(data, fileNameStr);
        }
    };

    const handleOpenLinkPolicy = (url) => {
        window.open(url, "_blank") 
    }

    const policies_column = [
        {
            accessor: "display_name",
            label: "Name",
            priorityLevel: 1,
            position: 1,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "description",
            label: "Description",
            priorityLevel: 1,
            position: 2,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "version",
            label: "Version",
            priorityLevel: 1,
            position: 3,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "created_at",
            label: "Created At",
            priorityLevel: 2,
            position: 4,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "updated_at",
            label: "Updated At",
            priorityLevel: 2,
            position: 5,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "actions",
            label: "Action",
            sortable: false,
            CustomComponent: ({ row }) => {
                let icon = "mdi-link-variant";
                let target = "_blank";
                let title = "Link";
                let href = row.path;
                let action = handleOpenLinkPolicy

                if (row.type === "document") {
                    icon = "mdi-download-outline";
                    target = "_self";
                    title = "Download";
                    href = route(
                        "policy-management.policies.download-policies",
                        row.id
                    );

                    action = handleDownloadPolicy
                }

                return (
                    <Dropdown className='d-inline-block'>
                        <Dropdown.Toggle
                            as="a"
                            bsPrefix="card-drop arrow-none cursor-pointer"
                        >
                            <i className="mdi mdi-dots-horizontal m-0 text-muted h3" />
                        </Dropdown.Toggle>
                        <Dropdown.Menu className="dropdown-menu-end">
                            <button
                                className="dropdown-item d-flex align-items-center"
                                target={target}
                                onClick={() => action(href)}
                            >
                                <i className={"mdi " +icon+ " font-18 me-1"}/> {title}
                            </button>
                            <button
                                className="dropdown-item d-flex align-items-center"
                                target={target}
                                onClick={() => {
                                    setSelectedLink({ ...row });
                                    setEditPolicyShow(true);
                                }}
                            >
                                <i className="mdi mdi-square-edit-outline font-18 me-1"/> Edit Information
                            </button>
                            <button
                                className="dropdown-item d-flex align-items-center"
                                target={target}
                                onClick={() => handleDeletePolicy(row.id)}
                            >
                                <i className="mdi mdi-delete-outline font-18 me-1"/> Delete
                            </button>
                        </Dropdown.Menu>
                    </Dropdown>
                    // <div className="btn-group">
                    //     <a
                    //         className="btn btn-secondary text-white btn-xs waves-effect waves-light"
                    //         title={title}
                    //         target={target}
                    //         onClick={() => action(href)}
                    //     >
                    //         <i className={icon} />
                    //     </a>
                    //     <button
                    //         className="edit-action btn btn-info btn-xs waves-effect waves-light"
                    //         onClick={() => {
                    //             setSelectedLink({ ...row });
                    //             setEditPolicyShow(true);
                    //         }}
                    //         title="Edit Information"
                    //     >
                    //         <i className="fe-edit" />
                    //     </button>
                    //     <a
                    //         className="policy-delete-link btn btn-danger btn-xs waves-effect waves-light"
                    //         title="Delete"
                    //         onClick={() => handleDeletePolicy(row.id)}
                    //     >
                    //         <i className="fe-trash-2 text-white" />
                    //     </a>
                    // </div>
                );
            },
        },
    ];

    /*
     * Triggers only when appDataScope changes
     */
    useDidMountEffect(() => {
        setRefresh(!refresh);
    }, [appDataScope]);

    /**
     * on file add
     */
    const onDrop = useCallback(
        (acceptedFiles) => {
            setFileUploadErrorMessage([]);
            Object.keys(acceptedFiles).forEach(function (key) {
                if (isValidFile(acceptedFiles[key])) {
                    uploadFiles.push({ file: acceptedFiles[key] });
                }
            });
            // uploadFiles.reverse();
            setUploadFiles(uploadFiles);
            setLoaded(new Date().getTime());
            // reverseFormField();
            // Do something with the files
        },
        [uploadFiles]
    );

    const { getRootProps, getInputProps } = useDropzone({
        accept: ".png, .jpg, .jpeg, .gif, .pdf",
        maxFiles: 5,
        onDrop,
    });

    /**
     * check if file name and size is valid
     * @param {fileblob} file
     */
    const isValidFile = function (file) {
        let validFileNameLength = file.name.length < 250;
        let validFileSize = file.size <= 10 * 1024 * 1024;
        if (!validFileNameLength) {
            setFileUploadErrorMessage(fileUploadErrorMessage => [...fileUploadErrorMessage,{message: file.name + " name is more than 250 character."}]);
            setLoaded(new Date().getTime());

            return false;
        }else if(!validFileSize){
            setFileUploadErrorMessage(fileUploadErrorMessage => [...fileUploadErrorMessage,{message: file.name + " size is more than 10MB."}]);
            setLoaded(new Date().getTime());
            return false;
        }else{
            return true;
        }
    };

    /**
     * round the value and return human understandable file size
     * @param {size of file} num
     * @param {round up to which decimal point} X
     * @returns
     */
    const roundToXAndGiveKb = function (num = 0, X = 20) {
        var _return = 0.000977 * num;
        var unit = " KB";
        if (_return > 200) {
            _return = 0.000977 * _return;
            unit = " MB";
        }
        var rounded_return = +(Math.round(_return + `e${X}`) + `e-${X}`);
        return rounded_return + unit;
    };

    /**
     * return file name without extension
     * @param {file name with extension} name
     * @returns
     */
    const giveFileName = function (name) {
        return name.split(".").slice(0, -1).join(".");
    };

    /**
     * remove specific file from state
     * @param {file blob object} file
     */
    const deleteFile = function (file, index) {
        let newFile = [...uploadFiles];
        newFile = newFile.filter((el) => el.file !== file);

        setUploadFiles(newFile);
        setLoaded(new Date().getTime());
        if (newFile.length > 0) {
            resetFormField(index, newFile.length);
        } else {
            reset({});
        }
        //
        const tempMeta = [...meta];
        tempMeta.splice(index, 1);
        setMeta(tempMeta);
    };

    /**
     * reset form values and order it
     * @param {deleted index} index
     * @param {number of files} length
     */
    const resetFormField = function (index, length) {
        let description;
        let version;
        let name;

        const current_values = getValues();
        const new_values = Object.values(current_values).filter(
            (el, ind) => ind !== index
        );

        for (let loop_index in new_values) {
            name = loop_index + ".name";
            version = loop_index + ".version";
            description = loop_index + ".description";
            setValue(name, new_values[loop_index].name);
            setValue(version, new_values[loop_index].version);
            setValue(description, new_values[loop_index].description);
        }

        let last_index = new_values.length;
        // console.log(last_index);
        name = last_index + ".name";
        version = last_index + ".version";
        description = last_index + ".description";
        setValue(version, "");
        setValue(description, "");
    };

    /**
     * post upload policies
     * @param {name,version,description} data
     * @returns
     */
    const onSubmit = function (data) {
        const mergedItems = uploadFiles.map((v, index) => ({
            ...v,
            data: data[index],
        }));
        (async () => {
            let tempMeta = [];
            for (const value of mergedItems) {
                const { file, data } = value;
                try {
                    const formData = new FormData();

                    formData.append("display_name", data.name);
                    formData.append("description", data.description);
                    formData.append("version", data.version);
                    formData.append("policy_file", file);
                    formData.append("policy_files", file);
                    formData.append("data_scope", appDataScope);

                    await axiosFetch.post(
                        route("policy-management.policies.upload-policies"),
                        formData
                    );

                    tempMeta = [
                        ...tempMeta,
                        {
                            progress: 100,
                            result: "Policy uploaded successfully.",
                            is_error: false,
                        },
                    ];
                    setUploadPolicyMsg("Policy uploaded successfully.");
                    setShow(false);
                } catch ({ response }) {
                    const errors = response.data.errors;
                    let error = response.data.message;
                    if (Object.keys(errors).length > 0)
                        error = errors[Object.keys(errors)[0]][0];
                    tempMeta = [
                        ...tempMeta,
                        { progress: 100, result: error, is_error: true },
                    ];
                }
            }
            setMeta(tempMeta);
            if (tempMeta.filter((m) => !m.is_error).length > 0)
                setRefresh(!refresh);
        })();
    };

    const resetAll = () => {
        // remove the files
        // reset the form validation
        setFileUploadErrorMessage([]);
        setUploadFiles([]);
        setMeta([]);
        reset();
    };

    useEffect(() => {
        if (show) resetAll();

    }, [show]);

    useDidMountEffect(() => {
        console.log(fileUploadErrorMessage,'error msg');
    },[fileUploadErrorMessage]);

    const files = uploadFiles.map((file, index) => (
        <div
            className="table table-striped files mb-0"
            id="policy-upload-preview-container"
            key={index}
        >
            <div
                id="policy-upload-preview-template"
                className="card border mb-0 p-3"
            >
                {/* <!-- This is used as the file preview template --> */}
                <div className="row">
                    <div className="col-9 col-md-11 py-0">
                        <p className="name mb-1">{file.file.name}</p>
                        <strong className="error text-danger" />
                    </div>
                    {/* <!--/.col--> */}
                    <div className="col-3 col-md-1 py-0 text-end">
                        <button
                            type="button"
                            className="btn-close"
                            onClick={() => deleteFile(file.file, index)}
                        ></button>
                    </div>
                    {/* <!--/.col --> */}
                </div>
                {/* <!--/.row--> */}
                <div>
                    <p className="size">
                        {roundToXAndGiveKb(file.file.size, 2)}
                    </p>
                    {meta[index] && meta[index].result && (
                        <Alert
                            variant={
                                meta[index].is_error ? "danger" : "success"
                            }
                        >
                            <strong>{meta[index].result}</strong>
                        </Alert>
                    )}
                    <div
                        className="progress progress-striped active my-2"
                        role="progressbar"
                        aria-valuemin="0"
                        aria-valuemax="100"
                        aria-valuenow="0"
                    >
                        <div
                            className="progress-bar progress-bar-success"
                            style={{
                                width: `${
                                    meta[index] ? meta[index].progress : 0
                                }%`,
                            }}
                        />
                    </div>
                </div>
                <div className="row mt-2">
                    <div className="col-6 py-0 mb-3">
                        <input
                            type="text"
                            name={`display_name` + index}
                            placeholder="Name"
                            defaultValue={giveFileName(file.file.name)}
                            {...register(`${index}.name`, { required: true })}
                            className="form-control"
                        />
                        {errors[index] && errors[index].version && (
                            <span className="invalid-feedback d-block">
                                The Display Name field is required.
                            </span>
                        )}
                    </div>
                    <div className="col-6 py-0 mb-3">
                        <input
                            type="text"
                            name={"version" + index}
                            placeholder="Version"
                            {...register(`${index}.version`, {
                                required: true,
                            })}
                            defaultValue={file.version ? file.version : ""}
                            className="form-control"
                        />
                        {errors[index] && errors[index].version && (
                            <span className="invalid-feedback d-block">
                                The Version field is required.
                            </span>
                        )}
                    </div>
                    <div className="col-12 py-0">
                        <textarea
                            name={"description" + index}
                            className="form-control"
                            placeholder="Description"
                            {...register(`${index}.description`, {
                                required: true,
                            })}
                            cols="3"
                            defaultValue={
                                file.description ? file.description : ""
                            }
                            rows="2"
                        />
                        {errors[index] && errors[index].description && (
                            <span className="invalid-feedback d-block">
                                The Description field is required.
                            </span>
                        )}
                    </div>
                </div>
                {/* <!--/.row--> */}
            </div>
        </div>
    ));

    const handleClose = () => setShow(false);
    const handleShow = () => setShow(true);

    return (
        <AppLayout>
            <div id="policy-management_policies-page">
                {/* breadcrumbs */}
                <Breadcrumb data={breadcumbsData} />
            </div>
            <FlashMessages />
            <LinkPoliciesModal
                appDataScope={appDataScope}
                show={linkPoliciesShow}
                refresh={refresh}
                setRefresh={setRefresh}
                onClose={() => setLinkPoliciesShow(false)}
            />
            <EditPolicyModal
                appDataScope={appDataScope}
                show={editPolicyShow}
                refresh={refresh}
                setRefresh={setRefresh}
                onClose={() => setEditPolicyShow(false)}
                policy={selectedLink}
            />

            {uploadPolicyMsg && 
                <Alert variant="success" onClose={() => setUploadPolicyMsg("")} dismissible>
                    <strong>{uploadPolicyMsg}</strong>
                </Alert>}
        
            <div className="row">
                <div className="col-xl-12">
                    <div className="card">
                        <div className="card-body">
                            {/* <!-- link policies --> */}

                            <button
                                onClick={() => setLinkPoliciesShow(true)}
                                className="btn btn-sm btn-primary waves-effect waves-light ms-sm-2 mb-2 float-sm-end"
                            >
                                <i
                                    className="mdi mdi-plus-circle"
                                    title="Link Policies"
                                />{" "}
                                Link Policies
                            </button>
                            {/* <!-- uploads policies --> */}
                            <a
                                href="#"
                                type="button"
                                data-toggle="modal"
                                data-target="#upload-policies-modal"
                                onClick={handleShow}
                                className="btn btn-sm btn-primary waves-effect waves-light ms-2 mb-2 float-sm-end"
                            >
                                <i
                                    className="mdi mdi-plus-circle"
                                    title="Upload Policies"
                                />{" "}
                                Upload Policies
                            </a>
                            <h4 className="header-title my-3 my-sm-0 mb-sm-4">
                                Manage Policies
                            </h4>

                            <DataTable
                                search
                                columns={policies_column}
                                fetchURL={policies_fetchurl}
                                refresh={refresh}
                            />
                        </div>
                    </div>
                </div>
                {/* <!-- end col --> */}
                {/* Policy upload MODAL */}
                <Modal
                    show={show}
                    onHide={handleClose}
                    centered={true}
                    size="lg"
                >
                    <form
                        onSubmit={handleSubmit(onSubmit)}
                        style={{ display: "block" }}
                    >
                        <Modal.Header className="px-3 pt-3 pb-0" closeButton>
                            <Modal.Title className="my-0">
                                New Policies
                            </Modal.Title>
                        </Modal.Header>

                        <Modal.Body className="p-3">
                            <div>
                                <div
                                    {...getRootProps()}
                                    className="dropzone clsbox mb-3 dz-clickable"
                                >
                                    <input {...getInputProps()} />
                                    <div className="dz-message needsclick">
                                        <i className="h1 text-muted dripicons-cloud-upload" />
                                        <h3>Drag and drop files or click</h3>
                                        <span className="text-muted font-13">
                                            (Selected files are{" "}
                                            <strong>shown</strong> below.)
                                        </span>
                                    </div>
                                </div>
                            </div>
                            {/* <!-- file update error and success message --> */}
                            <div>
                                {fileUploadErrorMessage.length > 0 && fileUploadErrorMessage.map((error, index) => {
                                    return (<ul key={index} className="dz-error-message">
                                        <li className="error text-danger">
                                            {error.message}
                                        </li>
                                    </ul>)
                                })}
                                <div className="dz-file-success-message"></div>
                            </div>
                            {/* <!-- file validation types--> */}
                            <div className="file-validation-limit mb-0">
                                <div className="mb-3">
                                    <p>
                                        <span>Accepted File Types:</span>
                                    </p>
                                    <p>.png, .jpg, .jpeg, .gif, .pdf</p>
                                </div>

                                <p>
                                    <span>Maximum File Size: </span>10MB
                                </p>
                                <p className="mb-0">
                                    <span>Maximum Character Length:</span> 250
                                </p>
                            </div>
                            <aside>
                                <ul className="ps-0 pt-3 mb-0">{files}</ul>
                            </aside>
                        </Modal.Body>
                        <Modal.Footer className="px-3 pt-0 pb-3">
                            <button
                                type="button"
                                className="btn btn-secondary waves-effect"
                                id="clear-completed-uploads"
                                onClick={resetAll}
                            >
                                Clear
                            </button>
                            <button
                                type="submit"
                                className="btn btn-primary waves-effect waves-light"
                                onClick={() => {
                                    handleSubmit(onSubmit);
                                }}
                            >
                                Submit
                            </button>
                        </Modal.Footer>
                    </form>
                </Modal>
                {/* Policy upload MODAL END*/}
            </div>
            {/* <!-- end row --> */}
        </AppLayout>
    );
}

export default Policies;
