import React, { useState, useEffect, useRef } from "react";

import {Inertia} from "@inertiajs/inertia";
import {useForm, usePage, Link} from "@inertiajs/inertia-react";
import {useSelector} from "react-redux";

import Select from "react-select";
import Switch from "rc-switch";
import Swal from "sweetalert2";
import moment from "moment/moment";
import DataTable from "../../../../../common/data-table/DataTable";
import { Modal, Nav, Tab } from "react-bootstrap";

import CustomDropify from "../../../../../common/custom-dropify/CustomDropify";
import fileDownload from "js-file-download";
import LoadingButton from '../../../../../common/loading-button/LoadingButton';

import 'rc-switch/assets/index.css';

const diffForHumans = date => moment(date).fromNow();



const RequestAmendmentModal = ({show, onClose}) => {
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);
    const {authUser, projectControl, project} = usePage().props;
    const requested_by = authUser.id === projectControl.responsible ? 'responsible' : 'approver';
    const {data, setData, post, reset, processing} = useForm({
        requested_by,
        justification: '',
        data_scope: appDataScope
    });

    const handleSubmit = e => {
        e.preventDefault();
        post(route('compliance.project-controls-request-amendment', [project.id, projectControl.id]), {
            onSuccess: () => {
                Inertia.reload();
                onClose();
            }
        });
    }

    React.useEffect(() => {
        if (!show) {
            // reset the form values
            reset();
        }
    }, [show])

    return (
        <Modal onHide={onClose} show={show} centered>
            <Modal.Header closeButton>
                <Modal.Title>Request evidence amendment</Modal.Title>
            </Modal.Header>
            <form onSubmit={handleSubmit} method="post">
                <Modal.Body>
                    <h4 className="mt-0 mb-4">Justification Message</h4>
                    <div className="row">
                        <div className="col-md-12">
                            <div className="form-group no-margin">
                            <textarea
                                className="form-control"
                                id="justification_textarea"
                                placeholder="Write justification message here"
                                value={data.justification}
                                onChange={e => setData('justification', e.target.value)}
                                required
                            />
                            </div>
                        </div>
                    </div>
                </Modal.Body>
                <Modal.Footer>
                    <LoadingButton className="btn btn-primary mx-2 waves-effect waves-light" loading={processing}>Submit</LoadingButton>
                    <button
                        type="button"
                        className="btn btn-danger"
                        onClick={onClose}
                    >
                        Cancel
                    </button>
                </Modal.Footer>
            </form>
        </Modal>
    );
}

const RejectAmendmentModal = ({show, onClose}) => {
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);
    const {authUser, projectControl, project} = usePage().props;
    const requested_by = authUser.id === projectControl.responsible ? 'responsible' : 'approver';
    const {data, setData, post, reset, processing} = useForm({
        requested_by,
        justification: '',
        solution: 'rejected',
        data_scope: appDataScope
    });

    const handleSubmit = e => {
        e.preventDefault();
        post(route("compliance.project-controls-amend-request-decision", [project.id, projectControl.id]), {
            onSuccess: () => {
                Inertia.reload();
                onClose();
            }
        });
    }

    React.useEffect(() => {
        if (!show) {
            // reset the form values
            reset();
        }
    }, [show])

    return (
        <Modal onHide={onClose} show={show} centered>
            <Modal.Header closeButton>
                <Modal.Title>Reject evidence amendment request</Modal.Title>
            </Modal.Header>
            <form onSubmit={handleSubmit} method="post">
                <Modal.Body>
                    <h4 className="mt-0 mb-4">Justification Message</h4>
                    <div className="row">
                        <div className="col-md-12">
                            <div className="form-group no-margin">
                            <textarea
                                className="form-control"
                                id="justification_textarea"
                                placeholder="Write justification message here"
                                value={data.justification}
                                onChange={e => setData('justification', e.target.value)}
                                required
                            />
                            </div>
                        </div>
                    </div>
                </Modal.Body>
                <Modal.Footer>
                    <LoadingButton className="btn btn-primary mx-2 waves-effect waves-light" loading={processing}>Submit</LoadingButton>
                    <button
                        type="button"
                        className="btn btn-danger"
                        onClick={onClose}
                    >
                        Cancel
                    </button>
                </Modal.Footer>
            </form>
        </Modal>
    );
}

const TruncateText = ({text, maxLines = null}) => {
    const max = maxLines ?? 2;
    const lines = text.split("<br />");

    const [isTruncated, setIsTruncated] = useState(true);

    if (lines.length <= max)
        return <p dangerouslySetInnerHTML={{ __html: text }} />;
    return (
        <>
            <p
                className="text-break"
                dangerouslySetInnerHTML={{
                    __html: isTruncated
                        ? `${lines.splice(0, max).join("<br>")}`
                        : `${text}<br>`,
                }}
            />
            <button
                className="btn btn-link p-0"
                onClick={() => setIsTruncated(!isTruncated)}
            >
                {isTruncated ? "... Read more" : "Close"}
            </button>
        </>
    );
};

const CommentItem = React.memo(({ comment }) => {
    const { authUser } = usePage().props;
    return (
        <div className="comment-sec mb-2">
            <div className="comment-head d-flex align-items-center">
                <span className="avatar">{comment.sender?.avatar}</span>
                <h5 className="title m-2">
                    {comment.sender.id === authUser.id
                        ? "You"
                        : decodeHTMLEntity(comment.sender.full_name)}
                </h5>
                <small className="fw-bold my-s ms-auto time">
                    {diffForHumans(comment.created_at)}
                </small>
            </div>
            <div className="comment-body">
                <TruncateText text={comment.comment} maxLines={1} />
            </div>
        </div>
    );
});

const ControlsModal = ({ showModal, onClose, onSelectRow }) => {
    const { projectControl, allStandards } = usePage().props;

    const [ajaxData, setAjaxData] = useState([]);
    const [projects, setProjects] = useState([]);

    //selected project id and standard id
    const [selectedStandardId, setSelectedStandardId] = useState("");
    const [selectedProjectId, setSelectedProjectId] = useState("");

    useEffect(() => {
        setAjaxData([]);
    }, [showModal]);

    const handleStandardChange = ({ value }) => {
        if (value === -1) {
            setSelectedStandardId("");
            setProjects([]);
            return;
        }

        setSelectedStandardId(value);
        axiosFetch
            .get(route("compliance.tasks.get-projects-by-standards"), {
                params: {
                    standardId: value,
                },
            })
            .then((res) => {
                setProjects(res.data);
            });
    };

    const handleProjectChange = ({ value }) => {
        if (value === -1) return setSelectedProjectId("");
        setSelectedProjectId(value);
    };

    const handleSearch = () => {
        const data = {
            standard_filter: selectedStandardId,
            project_filter: selectedProjectId,
        };
        setAjaxData(data);
    };

    const columns = [
        {accessor: 'project_name', label: 'Project', priorityLevel: 1, position: 1, minWidth: 150, sortable: false},
        {accessor: 'standard', label: 'Standard', priorityLevel: 1, position: 2, minWidth: 150, sortable: false},
        {accessor: 'control_id', label: 'Control ID', priorityLevel: 1, position: 3, minWidth: 50, sortable: false},
        {
            accessor: 'control_name',
            label: 'Control Name',
            priorityLevel: 1,
            position: 4,
            minWidth: 150,
            sortable: false
        },
        {accessor: 'desc', label: 'Control Description', priorityLevel: 2, position: 5, minWidth: 150, sortable: false},
        {accessor: 'frequency', label: 'Frequency', priorityLevel: 2, position: 5, minWidth: 150, sortable: false},
        {
            accessor: 'select',
            label: 'Select',
            sortable: false,
            CustomComponent: ({row}) => <Switch onClick={() => onSelectRow(row)}/>
        }
    ];
    return (
        <Modal show={showModal} onHide={onClose} size={"xl"} centered>
            <Modal.Header className="px-3 pt-3 pb-0" closeButton>
                <Modal.Title className="my-0">Control Mapping</Modal.Title>
            </Modal.Header>
            <div className="row linking-existing-controls-modal__filters d-flex mt-1 justify-content-center justify-content-md-end map-controls-div">
                <div className="col-11 col-lg-4 ms-1 mb-1">
                    <Select
                        className="react-select"
                        classNamePrefix="react-select"
                        defaultValue={{
                            label: "Select Standard",
                            value: -1,
                        }}
                        options={[
                            { label: "Select Standard", value: -1 },
                            ...allStandards.map((s) => ({
                                label: s.name,
                                value: s.id,
                            })),
                        ]}
                        onChange={handleStandardChange}
                        isDisabled={allStandards.length === 0}
                    />
                </div>
                <div className="col-11 col-lg-4 ms-1 mb-1">
                    <Select
                        className="react-select"
                        classNamePrefix="react-select"
                        defaultValue={{
                            label: "Select Project",
                            value: -1,
                        }}
                        options={[
                            { label: "Select Project", value: -1 },
                            ...projects.map((p) => ({
                                label: p.name,
                                value: p.id,
                            })),
                        ]}
                        onChange={handleProjectChange}
                        isDisabled={projects.length === 0}
                    />
                </div>
                <div className="ms-1 mb-1 text-end">
                    <button
                        name="search"
                        className="btn btn-primary"
                        onClick={handleSearch}
                    >
                        Search
                    </button>
                </div>
            </div>
            <Modal.Body className="p-3">
                <DataTable
                    columns={columns}
                    fetchURL={route(
                        "compliance.project-controls.get-all-implemented-controls",
                        projectControl.id
                    )}
                    ajaxData={ajaxData}
                />
            </Modal.Body>
        </Modal>
    );
};

const RejectModal = ({ showModal, onClose }) => {
    const { project, projectControl } = usePage().props;
    const { processing, data, setData, post } = useForm({
        justification: "",
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(
            route("compliance.project-controls-review-reject", [
                project.id,
                projectControl.id,
            ]),
            {
                onSuccess: () => {
                    Inertia.reload();
                },
                onFinish: onClose,
            }
        );
    };

    return (
        <Modal show={showModal} onHide={onClose} centered>
            <Modal.Header className="px-3 pt-3 pb-3" closeButton>
                <Modal.Title className="my-0">
                    Reject Evidence Confirmation
                </Modal.Title>
            </Modal.Header>
            <h4 className={"ms-3"}>Justification Message</h4>
            <form onSubmit={handleSubmit} method="post" className="justification-form">
                <Modal.Body className={'p-3'}>
                    <div className="row">
                        <div className="col-md-12">
                            <div className="mb-3">
                            <textarea
                                className="form-control" name="justification"
                                id="justification_textarea"
                                placeholder="Write justification message here"
                                value={data.justification}
                                onChange={e => setData('justification', e.target.value)}
                                required
                            />
                            </div>
                        </div>
                    </div>
                </Modal.Body>
                <Modal.Footer className="px-3 pt-0 pb-3 d-flex justify-content-center">
                    <LoadingButton className="btn btn-primary mx-2 waves-effect waves-light"
                                                    loading={processing}>Submit</LoadingButton>
                    <button
                        type="button"
                        className="btn btn-danger"
                        onClick={onClose}
                    >
                        Cancel
                    </button>
                </Modal.Footer>
            </form>
        </Modal>
    );
};

const TextEvidenceModal = ({ showModal, onClose, heading = "", body = "" }) => {
    return (
        <Modal show={showModal} onHide={onClose} size={"xl"} centered>
            <Modal.Header className="px-3 pt-3 pb-0" closeButton>
                <Modal.Title className="my-0">{heading}</Modal.Title>
            </Modal.Header>
            <Modal.Body className="p-3">{body}</Modal.Body>
            <Modal.Footer className="px-3 pt-0 pb-3">
                <button className="btn btn-secondary" onClick={onClose}>
                    Close
                </button>
            </Modal.Footer>
        </Modal>
    );
};

const EvidenceItem = ({ evidence, handleEvidenceAction }) => {
    const { project, meta, projectControl, APP_URL } = usePage().props;

    const isControl = evidence.type === "control";
    const name = evidence.name;

    let url = null;
    let icon = "fe-link";
    let title = "Link";

    const handleEvidenceDelete = (evidenceDeleteLink) => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonColor: "#f1556c",
            confirmButtonText: "Yes, delete it!",
            icon: 'warning',
            iconColor: '#f1556c',
        }).then((confirmed) => {
            if (confirmed.value) {
                axiosFetch.get(evidenceDeleteLink).then(() => {
                    Swal.fire({
                        title: "Deleted!",
                        text: "Your file has been deleted.",
                        confirmButtonColor: "#b2dd4c",
                        icon:'success',
                    });
                    Inertia.reload({ only: ["projectControl"] });
                });
            }
        });
    };

    switch (evidence.type) {
        case "document":
            icon = "fe-download";
            title = "Download";
            url = route("compliance-project-control-evidences-download", [
                project.id,
                evidence.project_control_id,
                evidence.id,
            ]);
            break;
        case "control":
            url = route("project-control-linked-controls-evidences-view", [
                project.id,
                evidence.path,
                evidence.project_control_id,
            ]);
            break;
        case "link":
            url = evidence.path;
            icon = 'fe-eye';
            break;
        case 'text':
            url = '#';
            icon = 'fe-type';
            title = 'Display';
            break;
    }

    return (
        <tr>
            <td>{isControl ? (
                <>
                    This control is linked to <Link className="link-primary"
                                                    href={route('project-control-linked-controls-evidences-view', [project.id, evidence.path, evidence.project_control_id])}>{name}</Link>
                </>
            ) : name}</td>
            <td>{moment(evidence.deadline).format('D MMM YYYY')}</td>
            <td>{moment(evidence.created_at).format('D MMM YYYY')}</td>
            <td>
                <div className="btn-group">
                    <button
                        className="btn btn bg-secondary text-white btn-xs waves-effect waves-light"
                        title={title}
                        onClick={() =>
                            handleEvidenceAction(evidence.type, {
                                url,
                                name: evidence.name,
                                text: evidence.text_evidence,
                            })
                        }
                    >
                        <i className={icon} style={{ fontSize: "12px" }} />
                    </button>

                    {meta.evidence_delete_allowed ? (
                        <button
                            className='evidence-delete-link btn btn-danger text-white btn-xs waves-effect waves-light'
                            onClick={() => handleEvidenceDelete(route('compliance-project-control-evidences-delete', [project.id, projectControl.id, evidence.id]))}
                            title='Delete'><i className='fe-trash-2' style={{fontSize: '12px'}}/></button>
                    ) : null}
                </div>
            </td>
        </tr>
    );
};

const TasksTab = ({active}) => {
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);
    const commentsBoxRef = useRef(null);

    const [activeTab, setActiveTab] = useState(null);
    const [buttonValue, setButtonValue] = useState("Upload");

    const [showModal, setShowModal] = useState(false);
    const [isReviewSubmitting, setIsReviewSubmitting] = useState(false);
    const [iseApproving, setIsApproving] = useState(false);
    const [requestAmendModalShow, setRequestAmendModalShow] = useState(false);
    const [rejectAmendModalShow, setRejectAmendModalShow] = useState(false);
    const [acceptingAmendment, setAcceptingAmendment] = useState(false);

    const [selectedRow, setSelectedRow] = useState(null);
    //text evidence modal
    const [textEvidenceModalShow, setTextEvidenceModalShow] = useState(false);
    const [textEvidenceHeading, setTextEvidenceHeading] = useState("");
    const [textEvidenceText, setTextEvidenceText] = useState("");
    //reject modal
    const [rejectModalShow, setRejectModalShow] = useState(false);
    const {
        globalSetting,
        projectControl,
        comments,
        meta,
        authUser,
        authUserRoles,
        APP_URL,
        latestJustification,
        project,
        justificationStatus
    } = usePage().props;

    const controlStatus = projectControl.status;
    const allowedRoles = [
        "Global Admin",
        "Compliance Administrator",
        "Contributor",
    ];

    const commentForm = useForm({
        comment: "",
    });

    const evidencesForm = useForm({
        project_control_id: projectControl.id,
        name2: "",
        evidences: null,
        name: "",
        link: "",
        linked_to_project_control_id: "",
        active_tab: "upload-docs",
        text_evidence_name: "",
        text_evidence: "",
    });

    const handleRowSelected = (row) => {
        evidencesForm.setData(
            "linked_to_project_control_id",
            row.project_control_id
        );
        setSelectedRow(row);
        // hide the modal
        setShowModal(false);
    };

    const handleOnSubmitComment = (e) => {
        e.preventDefault();
        commentForm.post(
            route("compliance.project-controls-comments", [
                project.id,
                projectControl.id,
            ]),
            {
                preserveScroll: true,
                onSuccess: () => {
                    Inertia.reload({ only: ["comments"] });
                    commentForm.reset("comment");
                },
            }
        );
    };

    useEffect(() => {
        // always scroll down when tab active
        if (active)
            commentsBoxRef.current.scrollTop =
                commentsBoxRef.current.scrollHeight;
    }, [comments, active]);

    useEffect(() => {
        // change button value when navigating tabs
        if (activeTab !== null) {
            evidencesForm.setData("active_tab", activeTab);
            if (activeTab === "upload-docs") return setButtonValue("Upload");
            setButtonValue("Save");
        }
    }, [activeTab]);

    const handleOnEvidencesSubmit = (e) => {
        e.preventDefault();
        evidencesForm.post(
            route("compliance-project-control-evidences-upload", [
                project.id,
                projectControl.id,
            ]),
            {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    Inertia.reload({ only: ["projectControl"] });
                    evidencesForm.reset(
                        "name2",
                        "evidences",
                        "name",
                        "link",
                        "linked_to_project_control_id",
                        "text_evidence_name",
                        "text_evidence"
                    );
                    // reset selectedRow
                    setSelectedRow(null);
                },
            }
        );
    };

    const handleOnSubmitReview = (e) => {
        e.preventDefault();
        Swal.fire({
            title: "Confirm submission?",
            text: "Review your evidence before submitting.",
            icon: 'question',
            iconColor:'#b2dd4c',
            showCancelButton: true,
            confirmButtonColor: "#b2dd4c",
            confirmButtonText: "Submit",
        }).then((confirmed) => {
            if (confirmed.value) {
                Inertia.post(
                    route("compliance.project-controls-review-submit", [
                        project.id,
                        projectControl.id,
                    ]),
                    null,
                    {
                        onSuccess: (page) => {
                            if (!page.props.flash.error) {
                                Swal.fire({
                                    title: "Submitted!",
                                    text: "Your evidence was submitted successfully.",
                                    confirmButtonColor: "#b2dd4c",
                                    icon: 'success',
                                });
                                Inertia.reload();
                            }
                        },
                        onStart: () => setIsReviewSubmitting(true),
                    }
                );
            }
        });
        //
    };

    const handleApproveEvidence = () => {
        Swal.fire({
            title: "Approve Evidence Confirmation",
            text: "Are you sure?",
            showCancelButton: true,
            confirmButtonColor: "#b2dd4c",
            confirmButtonText: "Approve",
            icon: 'question',
            iconColor: '#b2dd4c',
        }).then((confirmed) => {
            if (confirmed.value) {
                Inertia.post(
                    route("compliance.project-controls-review-approve", [
                        project.id,
                        projectControl.id,
                    ]),
                    null,
                    {
                        onStart: () => setIsApproving(true),
                        onFinish: () => setIsApproving(false),
                        onSuccess: () => {
                            Swal.fire({
                                title: "Success!",
                                text: "The evidence was approved successfully.",
                                confirmButtonColor: "#b2dd4c",
                                icon: 'success'
                            });
                            Inertia.reload();
                        },
                    }
                );
            }
        });
    };

    const defaultActiveTab = globalSetting.allow_document_upload
        ? "upload-docs"
        : globalSetting.allow_document_link
        ? "create-link"
        : "existing-control";
    const handleExistingControlClick = () => {
        setShowModal(true);
        setSelectedRow(null);
        evidencesForm.setData("linked_to_project_control_id", null);
    };

    const downloadEvidence = async (url) => {
        try {
            let {data, headers} = await axiosFetch.get(url,
                {
                    responseType: "blob", // Important
                });
            if (headers["content-disposition"]) {
                let fileNameStr = headers["content-disposition"].split("filename=")[1];
                let fileName = fileNameStr.substring(1, fileNameStr.length - 1);
                let fileExtension = fileName.split('.').pop();
                let extension_arr=['doc','docx','ppt','pptx','xls','xlsx','jpg','png','jpeg','gif','pdf','msg','eml'];
                if(extension_arr.includes(fileExtension))
                    fileDownload(data, fileName);
                else
                    fileDownload(data, fileNameStr);

            } else {
                AlertBox({
                    text: 'File Not Found',
                    confirmButtonColor: '#f1556c',
                    icon: 'error',
                });
            }
        } catch (error) {
        }
    };

    const handleEvidenceAction = (type, {url, name, text}) => {
        switch (type) {
            case "text":
                setTextEvidenceHeading(name);
                setTextEvidenceText(text);
                setTextEvidenceModalShow(true);
                break;
            case 'document':
                downloadEvidence(url);
                break;
            case 'control':
                Inertia.visit(url)
                break;
            default:
                window.open(url, '_blank');
        }
    };

    const handleAcceptAmendment = () => {
        Inertia.post(route("compliance.project-controls-amend-request-decision", [project.id, projectControl.id]), {
            solution: 'accepted',
            data_scope: appDataScope
        }, {
            onStart: () => setAcceptingAmendment(true),
            onFinish: () => {
                Inertia.reload();
                setAcceptingAmendment(false);
            }
        })
    }

    return (
        <div className="tab-padding">
            <div className="row">
                <div className="col-xl-6">
                    <RejectAmendmentModal
                        show={rejectAmendModalShow}
                        onClose={() => setRejectAmendModalShow(false)}
                    />
                    <RequestAmendmentModal
                        show={requestAmendModalShow}
                        onClose={() => setRequestAmendModalShow(false)}
                    />
                    <TextEvidenceModal
                        onClose={() => setTextEvidenceModalShow(false)}
                        showModal={textEvidenceModalShow}
                        body={textEvidenceText}
                        heading={textEvidenceHeading}
                    />
                    <ControlsModal
                        showModal={showModal}
                        onClose={() => setShowModal(false)}
                        onSelectRow={handleRowSelected}
                    />
                    <RejectModal
                        showModal={rejectModalShow}
                        onClose={() => setRejectModalShow(false)}
                    />
                    {authUserRoles.some((role) =>
                        allowedRoles.includes(role)
                    ) &&
                    authUser.id === projectControl.responsible &&
                    meta.evidence_upload_allowed ? (
                        <div id="evidence-form-section" className="pb-5 mb-3">
                            <form
                                method="POST"
                                id="evidence-upload-form"
                                encType="multipart/form-data"
                                onSubmit={handleOnEvidencesSubmit}
                            >
                                <Tab.Container
                                    onSelect={(eventKey) =>
                                        setActiveTab(eventKey)
                                    }
                                    defaultActiveKey={defaultActiveTab}
                                >
                                    <Nav variant="pills" className="flex-row">
                                        {globalSetting.allow_document_upload ? (
                                            <Nav.Item>
                                                <Nav.Link
                                                    className="btn bg-secondary text-white me-2"
                                                    eventKey="upload-docs"
                                                >
                                                    Upload Document
                                                </Nav.Link>
                                            </Nav.Item>
                                        ) : null}

                                        {globalSetting.allow_document_link ? (
                                            <Nav.Item>
                                                <Nav.Link
                                                    className="btn bg-secondary text-white me-2"
                                                    eventKey="create-link"
                                                >
                                                    Create Link
                                                </Nav.Link>
                                            </Nav.Item>
                                        ) : null}

                                        <Nav.Item>
                                            <Nav.Link
                                                className="btn bg-secondary text-white me-2"
                                                onClick={
                                                    handleExistingControlClick
                                                }
                                                eventKey="existing-control"
                                            >
                                                Existing Control
                                            </Nav.Link>
                                        </Nav.Item>

                                        <Nav.Item>
                                            <Nav.Link
                                                className="btn bg-secondary text-white"
                                                eventKey="text-input"
                                            >
                                                Text Input
                                            </Nav.Link>
                                        </Nav.Item>
                                    </Nav>
                                    <Tab.Content className="mt-2">
                                        {globalSetting.allow_document_upload ? (
                                            <Tab.Pane eventKey="upload-docs">
                                                <div className="row mb-3">
                                                    <label
                                                        className="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                        htmlFor="name2"
                                                    >
                                                        Name:{" "}
                                                        <span className="required text-danger">
                                                            *
                                                        </span>
                                                    </label>
                                                    <div className="col-xl-10 col-lg-10 col-md-10">
                                                        <input
                                                            type="text"
                                                            name="name2"
                                                            className="form-control"
                                                            id="name2"
                                                            value={
                                                                evidencesForm
                                                                    .data.name2
                                                            }
                                                            onChange={(e) =>
                                                                evidencesForm.setData(
                                                                    "name2",
                                                                    e.target
                                                                        .value
                                                                )
                                                            }
                                                        />
                                                        {evidencesForm.errors
                                                            .name2 && (
                                                            <div className="invalid-feedback d-block">
                                                                {
                                                                    evidencesForm
                                                                        .errors
                                                                        .name2
                                                                }
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                                <div
                                                    className="row mb-3"
                                                    id="evidence-section"
                                                >
                                                    <label
                                                        className="col-xl-2 col-lg-2 col-md-2 col-form-label"
                                                        htmlFor="evidences"
                                                    >
                                                        Evidence:{" "}
                                                        <span className="required text-danger">
                                                            *
                                                        </span>
                                                    </label>
                                                    <div className="col-xl-10 col-lg-10 col-md-10">
                                                        <CustomDropify
                                                            maxSize={15728640}
                                                            file={evidencesForm.data.evidences}
                                                            onSelect={file => evidencesForm.setData(previousData => ({
                                                                ...previousData,
                                                                evidences: file
                                                            }))}
                                                            accept={'.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.png,.jpeg,.gif,.pdf,.msg,.eml'}
                                                        />

                                                        {evidencesForm.errors
                                                            .evidences && (
                                                            <div className="invalid-feedback d-block">
                                                                {
                                                                    evidencesForm
                                                                        .errors
                                                                        .evidences
                                                                }
                                                            </div>
                                                        )}
                                                        <div className="file-validation-limit mt-3">
                                                            <div>
                                                                <p>
                                                                    <span
                                                                        className="me-1">Accepted File Types: </span>
                                                                    .doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.png,.jpeg,.gif,.pdf,.msg,.eml
                                                                </p>
                                                            </div>
                                                            <p>
                                                                <span className="me-1">
                                                                    Maximum File
                                                                    Size:{" "}
                                                                </span>
                                                                15MB
                                                            </p>
                                                            <p>
                                                                <span className="me-1">
                                                                    Maximum
                                                                    Character
                                                                    Length:{" "}
                                                                </span>
                                                                250
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </Tab.Pane>
                                        ) : null}
                                        {globalSetting.allow_document_link ? (
                                            <Tab.Pane eventKey="create-link">
                                                <div className="row mb-3">
                                                    <label
                                                        className="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                        htmlFor="name"
                                                    >
                                                        Name:{" "}
                                                        <span className="required text-danger">
                                                            *
                                                        </span>
                                                    </label>
                                                    <div className="col-xl-10 col-lg-10 col-md-10">
                                                        <input
                                                            value={
                                                                evidencesForm
                                                                    .data.name
                                                            }
                                                            onChange={(e) =>
                                                                evidencesForm.setData(
                                                                    "name",
                                                                    e.target
                                                                        .value
                                                                )
                                                            }
                                                            type="text"
                                                            name="name"
                                                            className="form-control"
                                                        />
                                                        {evidencesForm.errors
                                                            .name && (
                                                            <div className="invalid-feedback d-block">
                                                                {
                                                                    evidencesForm
                                                                        .errors
                                                                        .name
                                                                }
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                                <div className="row mb-3">
                                                    <label
                                                        className="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                        htmlFor="link"
                                                    >
                                                        Link:{" "}
                                                        <span className="required text-danger">
                                                            *
                                                        </span>
                                                    </label>
                                                    <div className="col-xl-10 col-lg-10 col-md-10">
                                                        <input
                                                            value={
                                                                evidencesForm
                                                                    .data.link
                                                            }
                                                            onChange={(e) =>
                                                                evidencesForm.setData(
                                                                    "link",
                                                                    e.target
                                                                        .value
                                                                )
                                                            }
                                                            type="text"
                                                            name="link"
                                                            className="form-control"
                                                            id="link"
                                                        />
                                                        {evidencesForm.errors
                                                            .link && (
                                                            <div className="invalid-feedback d-block">
                                                                {
                                                                    evidencesForm
                                                                        .errors
                                                                        .link
                                                                }
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            </Tab.Pane>
                                        ) : null}
                                        <Tab.Pane eventKey="existing-control">
                                            <div className="row mb-3">
                                                <label
                                                    className="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                    htmlFor="name"
                                                >
                                                    Standards:{" "}
                                                    <span className="required text-danger">
                                                        *
                                                    </span>
                                                </label>
                                                <div className="col-xl-10 col-lg-10 col-md-10">
                                                    <input
                                                        type="text"
                                                        className="form-control"
                                                        value={
                                                            selectedRow
                                                                ? selectedRow.standard
                                                                : ""
                                                        }
                                                        disabled
                                                    />
                                                </div>
                                            </div>
                                            <div className="row mb-3">
                                                <label
                                                    className="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                    htmlFor="link"
                                                >
                                                    Projects:{" "}
                                                    <span className="required text-danger">
                                                        *
                                                    </span>
                                                </label>
                                                <div className="col-xl-10 col-lg-10 col-md-10">
                                                    <input
                                                        type="text"
                                                        className="form-control"
                                                        value={
                                                            selectedRow
                                                                ? selectedRow.project_name
                                                                : ""
                                                        }
                                                        disabled
                                                    />
                                                </div>
                                            </div>
                                            <div className="row mb-3">
                                                <label
                                                    className="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                    htmlFor="link"
                                                >
                                                    Controls:{" "}
                                                    <span className="required text-danger">
                                                        *
                                                    </span>
                                                </label>
                                                <div className="col-xl-10 col-lg-10 col-md-10">
                                                    <input
                                                        type="text"
                                                        className="form-control"
                                                        value={
                                                            selectedRow
                                                                ? selectedRow.control_name
                                                                : ""
                                                        }
                                                        disabled
                                                    />
                                                    {evidencesForm.errors
                                                        .linked_to_project_control_id && (
                                                        <div className="invalid-feedback d-block">
                                                            {
                                                                evidencesForm
                                                                    .errors
                                                                    .linked_to_project_control_id
                                                            }
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                        </Tab.Pane>
                                        <Tab.Pane eventKey="text-input">
                                            <div className="row mb-3">
                                                <label
                                                    className="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                    htmlFor="text_evidence_name"
                                                >
                                                    Name:{" "}
                                                    <span className="required text-danger">
                                                        *
                                                    </span>
                                                </label>
                                                <div className="col-xl-10 col-lg-10 col-md-10">
                                                    <input
                                                        type="text"
                                                        name="text_evidence_name"
                                                        className="form-control"
                                                        onChange={(e) =>
                                                            evidencesForm.setData(
                                                                "text_evidence_name",
                                                                e.target.value
                                                            )
                                                        }
                                                        value={
                                                            evidencesForm.data
                                                                .text_evidence_name
                                                        }
                                                    />
                                                    {evidencesForm.errors
                                                        .text_evidence_name && (
                                                        <div className="invalid-feedback d-block">
                                                            {
                                                                evidencesForm
                                                                    .errors
                                                                    .text_evidence_name
                                                            }
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                            <div
                                                className="row mb-3"
                                                id="evidence-section"
                                            >
                                                <label
                                                    className="col-xl-2 col-lg-2 col-md-2 form-label col-form-label"
                                                    htmlFor="text_evidence"
                                                >
                                                    Text:{" "}
                                                    <span className="required text-danger">
                                                        *
                                                    </span>
                                                </label>
                                                <div className="col-xl-10 col-lg-10 col-md-10">
                                                    <textarea
                                                        name="text_evidence"
                                                        className="form-control send-message"
                                                        rows="3"
                                                        placeholder="Write your evidence text here ..."
                                                        value={
                                                            evidencesForm.data
                                                                .text_evidence
                                                        }
                                                        onChange={(e) =>
                                                            evidencesForm.setData(
                                                                "text_evidence",
                                                                e.target.value
                                                            )
                                                        }
                                                        autoFocus
                                                    />
                                                    {evidencesForm.errors
                                                        .text_evidence && (
                                                        <div className="invalid-feedback d-block">
                                                            {
                                                                evidencesForm
                                                                    .errors
                                                                    .text_evidence
                                                            }
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                        </Tab.Pane>
                                    </Tab.Content>
                                </Tab.Container>

                                {globalSetting.allow_document_link ||
                                globalSetting.allow_document_upload ? (
                                    <button
                                        type="submit"
                                        className="btn btn-primary float-end"
                                        id="evidence-submit"
                                        disabled={evidencesForm.processing}
                                    >
                                        {buttonValue}
                                    </button>
                                ) : null}
                            </form>
                        </div>
                    ) : null}

                    <h4 className="pb-2 upload-text p-0">
                        Uploaded Evidences for Control ID:{" "}
                        {projectControl.controlId}
                    </h4>

                    <div className="uploaded-evidence-main p-2">
                        <table
                            className="table nowrap text-center table-bordered border-light low-padding w-100"
                        >
                            <thead className="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Task Deadline</th>
                                <th>Created On</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody className="table-light name-table__overflow">
                                {projectControl.evidences.length > 0 ? (
                                    projectControl.evidences.map(
                                        (evidence, index) => (
                                            <EvidenceItem
                                                key={index}
                                                evidence={evidence}
                                                handleEvidenceAction={
                                                    handleEvidenceAction
                                                }
                                            />
                                        )
                                    )
                                ) : (
                                    <tr className="odd">
                                        <td
                                            valign="top"
                                            colSpan="4"
                                            className="dataTables_empty"
                                        >
                                            No data available in table
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {
                        projectControl.status === 'Implemented' ? (
                            <div className="request-evidence text-center">
                                {projectControl.amend_status === 'requested_responsible' && authUser.id === projectControl.approver ? (
                                    <>
                                        <button
                                            type="button"
                                            className="btn btn-primary my-2 me-1 request-decision-btn"
                                            id="accept-amendment"
                                            disabled={acceptingAmendment}
                                            onClick={handleAcceptAmendment}
                                        >Accept evidence amendment request
                                        </button>
                                        <button
                                            type="button"
                                            className="btn btn-primary my-2 request-decision-btn"
                                            id="reject-amendment"
                                            onClick={() => setRejectAmendModalShow(true)}
                                        >
                                            Reject evidence amendment request
                                        </button>
                                    </>
                                ) :

                                    (((['solved', 'rejected', null].includes(projectControl.amend_status)) &&
                                            (authUserRoles.some(role => allowedRoles.includes(role)) ||
                                                [projectControl.approver, projectControl.responsible].includes(authUser.id))) ? (
                                            <button
                                                type="button"
                                                className="btn btn-primary my-2"
                                                id="request-amendment"
                                                onClick={() => setRequestAmendModalShow(true)}
                                            >
                                                Request evidence amendment
                                            </button>
                                        ) : null

                                    )
                                }
                            </div>
                        ) : null
                    }
                </div>

                <div className="col-xl-6" id="control-comments-wp">
                    {(projectControl.required_evidence) ? (
                        <>
                            <h4 className="comment-text pb-2">Required Evidence</h4>
                            <div className="comment-box">
                                <p dangerouslySetInnerHTML={{__html: projectControl.required_evidence}}></p>
                            </div>
                        </>
                    ) : ''}

                    <h4 className="comment-text pb-2">Comments</h4>
                    <div className="comment-box" ref={commentsBoxRef}>
                        {comments.length > 0 ? (
                            comments.map((comment) => (
                                <CommentItem
                                    key={comment.id}
                                    comment={comment}
                                />
                            ))
                        ) : (
                            <p>No, comments available</p>
                        )}
                    </div>
                    {authUserRoles.some((role) =>
                        allowedRoles.includes(role)
                    ) &&
                    (authUser.id === projectControl.responsible ||
                        authUser.id === projectControl.approver) ? (
                        <div className="post-comment clearfix">
                            <form
                                id="control-comment-form"
                                onSubmit={handleOnSubmitComment}
                                method="POST"
                            >
                                <textarea
                                    name="comment"
                                    id="comment"
                                    className="form-control send-message"
                                    rows={2}
                                    placeholder="Write a comment here ..."
                                    autoFocus
                                    value={commentForm.data.comment}
                                    onChange={(e) =>
                                        commentForm.setData(
                                            "comment",
                                            e.target.value
                                        )
                                    }
                                />
                                {commentForm.errors.comment && (
                                    <div className="invalid-feedback d-block">
                                        {commentForm.errors.comment}
                                    </div>
                                )}
                                <button
                                    type="submit"
                                    disabled={commentForm.processing}
                                    className="float-end btn btn-primary my-2"
                                >
                                    Comment
                                </button>
                            </form>
                        </div>
                    ) : null}

                    <div id="justification-section">
                        {((controlStatus === 'Rejected' || ["requested_approver", "requested_responsible", "accepted", "rejected"].includes(projectControl.amend_status)) && latestJustification !== null) ? (
                            <div
                                className="toast show w-100 mb-2 shadow-sm"
                                role="alert"
                                aria-live="assertive"
                                aria-atomic="true"
                                data-toggle="toast"
                            >
                                <div className="toast-header">
                                    <span className="avatar">
                                        {latestJustification.creator?.avatar}
                                    </span>
                                    <strong className="me-auto m-2">
                                        {latestJustification.creator_id ===
                                        authUser.id
                                            ? "Me"
                                            : decodeHTMLEntity(
                                                  latestJustification.creator
                                                      .full_name
                                              )}
                                    </strong>
                                    <small>
                                        {diffForHumans(
                                            latestJustification.created_at
                                        )}
                                    </small>
                                </div>
                                <div className="toast-body readmore">
                                    <strong>Status: {justificationStatus}</strong>
                                    <p className="comment-box"
                                       dangerouslySetInnerHTML={{__html: latestJustification.justification}}/>
                                </div>
                            </div>
                        ) : null}
                    </div>
                    {/*end justification*/}
                </div>
                {/* task right ends */}
            </div>

            {/*    NEW*/}
            <div
                className="d-flex justify-content-center justify-content-sm-center justify-content-md-end  mt-4"
                id="evidence-submit-buttons-wp"
            >
                {authUserRoles.some((role) => allowedRoles.includes(role)) &&
                authUser.id === projectControl.responsible &&
                projectControl.isEligibleForReview ? (
                    <form
                        onSubmit={handleOnSubmitReview}
                        id="submit-for-review"
                    >
                        {!projectControl.isEligibleForReview ? 
                        <button
                            type="submit"
                            className="btn btn-primary"
                            disabled="disabled"
                        >Submit for review
                        </button> 
                        :
                        <LoadingButton className="btn btn-primary waves-effect waves-light"
                         loading={isReviewSubmitting}>Submit for review</LoadingButton> 
                         }
                    </form>
                ) : null}

                {authUserRoles.some((role) => allowedRoles.includes(role)) &&
                authUser.id === projectControl.approver &&
                projectControl.status === "Under Review" ? (
                    <>
                        <LoadingButton className="btn btn-primary waves-effect waves-light"
                                                    onClick={handleApproveEvidence} loading={iseApproving}>Approve</LoadingButton>
                        <button
                            type="button"
                            className="btn btn-primary mx-3"
                            id="reject-btn"
                            onClick={() => setRejectModalShow(true)}
                        >
                            Reject
                        </button>
                    </>
                ) : null}
            </div>
        </div>
    );
};

export default TasksTab;
