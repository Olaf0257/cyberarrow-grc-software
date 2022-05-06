import React, {Fragment, useEffect, useRef, useState} from 'react';
import BreadcumbsComponent from '../../../common/breadcumb/Breadcumb';
import AppLayout from '../../../layouts/app-layout/AppLayout';
import Tabs from 'react-bootstrap/Tabs';
import Tab from 'react-bootstrap/Tab';
import DataTable from '../../../common/data-table/DataTable';
import Modal from 'react-bootstrap/Modal';
import Dropdown from 'react-bootstrap/Dropdown';
import {Link, useForm, usePage} from '@inertiajs/inertia-react';
import ContentLoader from '../../../common/content-loader/ContentLoader';
import Spinner from 'react-bootstrap/Spinner';
import Button from 'react-bootstrap/Button';
import './stylesheet.scss';
import {Inertia} from '@inertiajs/inertia';
import FlashMessages from "../../../common/FlashMessages";
import fileDownload from "js-file-download";
import AddExistingUserModal from "./components/AddExistingUserModal";
import {useSelector} from "react-redux";
import route from "ziggy-js";
import {useStateIfMounted} from "use-state-if-mounted"
import {useDidMountEffect} from '../../../custom-hooks';
import Alert from "react-bootstrap/Alert";

function UsersAndGroups(props) {
    const [activeKey, setActiveKey] = useState("groups");
    const {activeTab} = props;

    useEffect(() => {
        document.title = "Users & Groups";
        if (activeTab) {
            return setActiveKey(activeTab);
        }
    }, [activeTab]);

    const addExistingUserModalRef = useRef();
    const [addGroupModelShow, setAddGroupModelShow] = useState(false);
    const [title, setTitle] = useState("Add New Group");
    const [userModelShow, setUserModelShow] = useState(false);
    const [showContentLoader, setShowContentLoader] = useStateIfMounted(false);
    const [ajaxData, setAjaxData] = useStateIfMounted([]);
    const [ajaxDataGroup, setAjaxDataGroup] = useStateIfMounted([]);
    const [groupUsers, setGroupUsers] = useState({groupsData: []});
    const [groupName, setGroupName] = useState("");
    const [groupId, setGroupId] = useState(0);
    const [groupNameError, setGroupNameError] = useState("");
    const [usersRequiredError, setUsersRequiredError] = useState("");
    const [showUserRequiredError,setShowUserRequiredError] = useState(false);
    const [groupsUserAddError, setGroupsUserAddError] = useState({errors: []});
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);
    const [groupsRefresh, setGroupsRefresh] = useState(false);
    const [usersRefresh, setUsersRefresh] = useState(false);
    const [newUser, setNewUser] = useState({
        fname: '',
        lname: '',
        email: ''
    });
    const [groupProcessing, setGroupProcessing] = useState(false);

    const {errors} = usePage().props;
    const {data, setData, post, processing} = useForm({
        id: 0,
        first_name: "",
        last_name: "",
        email: "",
    });

    const {get} = useForm();

    const handleUserEdit = (e, row) => {
        setData({
            id: row[0],
            first_name: row[1],
            last_name: row[2],
            email: row[3],
        });
        setUserModelShow(true);
    };

    const handleUserDisable = (e, row) => {
        e.preventDefault();

        Inertia.get(
            route(`policy-management.users-and-groups.users.disable`, row[0]),
            {
                data_scope: appDataScope,
            },
            {
                preserveScroll: true,
                onSuccess: (data) => {
                    setAjaxData([]);
                },
            }
        );
    };

    const handleUserDelete = (e, row) => {
        e.preventDefault();
        AlertBox(
            {
                title: "Are you sure?",
                text: "You will not be able to recover this user!",
                showCancelButton: true,
                confirmButtonColor: "#ff0000",
                confirmButtonText: "Yes, delete it!",
                icon: "warning",
                iconColor:'#ff0000',
            },
            function (result) {
                if (result.isConfirmed) {
                    Inertia.post(
                        route(
                            "policy-management.users-and-groups.users.delete-user",
                            row[0]
                        ),
                        {
                            data_scope: appDataScope,
                        },
                        {
                            preserveScroll: true,
                            onSuccess: (data) => {
                                setAjaxData([]);
                                setGroupsRefresh(true);
                            },
                        }
                    );
                }
            }
        );
    };

    const handleUserActive = (e, row) => {
        e.preventDefault();

        Inertia.get(
            route("policy-management.users-and-groups.users.activate", row[0]),
            {
                data_scope: appDataScope,
            },
            {
                preserveScroll: true,
                onSuccess: (data) => {
                    setAjaxData([]);
                },
            }
        );
    };

    const editGroup = async (e, row) => {
        e.preventDefault();
        const editGroupId = row[5];
        axiosFetch
            .get(
                route("policy-management.users-and-groups.groups.edit", editGroupId)
            )
            .then((res) => {
                setTitle("Update Group");
                setGroupName(res.data.group.name);
                setGroupId(res.data.group.id);
                var users = res.data.users;
                var editUserData = [];
                users.forEach((element) => {
                    const data = {
                        user_first_name: element.first_name,
                        user_last_name: element.last_name,
                        user_email: element.email,
                    };
                    editUserData.push(data);
                });
                var finalData = {
                    groupsData: editUserData,
                };
                setGroupUsers(finalData);
                setAddGroupModelShow(true);
                addExistingUserModalRef.current.checkSelectedUsers();
            });
    };

    const deleteGroup = (e, row) => {
        e.preventDefault();

        AlertBox(
            {
                title: "Are you sure ?",
                text: "You will not be able to retreive group!",
                showCancelButton: true,
                confirmButtonColor: "#ff0000",
                confirmButtonText: "Yes, delete it!",
                icon: "warning",
                iconColor:'#ff0000',
            },
            function (confirmed) {
                if (confirmed.value && confirmed.value == true) {
                    Inertia.delete(
                        route(
                            "policy-management.users-and-groups.groups.delete",
                            row[5]
                        ),
                        {
                            data: {
                                data_scope: appDataScope,
                            },
                            preserveScroll: true,
                            onSuccess: (data) => {
                                setAjaxDataGroup([]);
                            },
                        }
                    );
                }
            }
        );
    };

    const newGroupModel = () => {
        setTitle("Add New Group");
        if (groupId != 0) {
            //Reset user data
            var finalData = {
                groupsData: [],
            };
            setGroupUsers(finalData);
        }
        setGroupId(0);
        setGroupName("");
        setAddGroupModelShow(true);
    };

    function editUserSubmit(event) {
        event.preventDefault();
        Inertia.post(
            route("policy-management.users-and-groups.users.update", data.id),
            {
                ...data,
                data_scope: appDataScope,
            },
            {
                preserveScroll: true,
                onSuccess: (data) => {
                    setAjaxData([]);
                    setUserModelShow(false);
                },
            }
        );
    }

    const addUserToGroup = (e) => {
        e.preventDefault();
        const first_name = e.target[0].value;
        const last_name = e.target[1].value;
        const email = e.target[2].value;
        var errors = {};
        var isError = 0;
        if (last_name === "" || last_name.length < 2) {
            errors = {
                ...errors,
                lname: "Last name length must be of 2 characters",
            };
            isError = 1;
            setGroupsUserAddError({errors});
        }
        if (first_name === "" || first_name.length < 2) {
            errors = {
                ...errors,
                fname: "First name length must be of 2 characters",
            };
            isError = 1;
            setGroupsUserAddError({errors});
        }
        if (
            /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(
                email
            )
        ) {
            // email unique validation
            const index = groupUsers.groupsData.findIndex(
                (item) => item.user_email === email
            );
            if (index != null && index != undefined && index != -1) {
                errors = {
                    ...errors,
                    email: "User is already part of this group",
                };
                isError = 1;
            }
            setGroupsUserAddError({errors});
            var currentData = groupUsers.groupsData;
            if (isError == 0) {
                var data = {
                    user_first_name: first_name,
                    user_last_name: last_name,
                    user_email: email,
                };
                currentData.push(data);
                var finalData = {
                    groupsData: currentData,
                };
                setGroupUsers(finalData);
                e.target[0].value = "";
                e.target[1].value = "";
                e.target[2].value = "";
                setNewUser({fname: "", lname: "", email: ""});
            }
        } else {
            isError = 1;
            errors = {
                ...errors,
                email: "Enter a valid email address",
            };
            setGroupsUserAddError({errors});
        }

    };

    //BULK IMPORT USERS
    const [bulkCSVErrors, setBulkCSVErrors] = useState("");

    const hiddenFileInput = React.useRef(null);

    const bulkImportUsers = () => {
        hiddenFileInput.current.click();
    };

    const bulkImportChange = (event) => {
        setBulkCSVErrors("");
        const file = event.target.files[0];
        const reader = new FileReader();
        reader.onload = function (e) {
            processCSVData(e.target.result);
        };
        reader.readAsText(file);
        event.target.value = null; //Clearing the uploaded file
    };

    const processCSVData = (dataString) => {
        const dataStringLines = dataString.split(/\r\n|\n/);
        const headers = dataStringLines[0]
            .replaceAll('"', "")
            .split(/,(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/);

        //CSV Header Validation
        if (headers == "") {
            setBulkCSVErrors("CSV file is empty");
            return false;
        } else {
            if (
                headers[0] != "first_name" ||
                headers[1] != "last_name" ||
                headers[2] != "email"
            ) {
                setBulkCSVErrors("CSV Header format is invalid");
                return false;
            }
        }

        //CSV Data Validation
        if (!dataStringLines[1]) {
            setBulkCSVErrors("CSV Data is Empty");
            return false;
        }

        const list = [];
        for (let i = 1; i < dataStringLines.length; i++) {
            const row = dataStringLines[i].split(
                /,(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/
            );
            if (headers && row.length == headers.length) {
                const obj = {};
                for (let j = 0; j < headers.length; j++) {
                    let d = row[j];
                    if (d.length > 0) {
                        if (d[0] == '"') d = d.substring(1, d.length - 1);
                        if (d[d.length - 1] == '"')
                            d = d.substring(d.length - 2, 1);
                    }
                    if (headers[j]) {
                        obj[headers[j]] = d;
                    }
                }
                // remove the blank rows
                if (Object.values(obj).filter((x) => x).length > 0) {
                    list.push(obj);
                }
            }
        }
        var currentData = groupUsers.groupsData;
        list.map((item) => {
            var data = {
                user_first_name: item.first_name,
                user_last_name: item.last_name,
                user_email: item.email,
            };
            const index = groupUsers.groupsData.findIndex(
                (item) => item.user_email === data.user_email
            );
            if (index != null && index != undefined && index != -1) {
                setBulkCSVErrors("A user in the CSV is already listed below.");
            } else {
                currentData.push(data);
                var finalData = {
                    groupsData: currentData,
                };
                setGroupUsers(finalData);
            }
        });
    };

    const downloadCSVTemplate = async () => {
        try {
            let response = await axiosFetch({
                url: route(
                    "policy-management.users-and-groups.users.download-csv-template"
                ),
                method: "GET",
                responseType: "blob", // Important
            });

            fileDownload(response.data, "user-template.csv");
        } catch (error) {
            console.log(error);
        }
    };

    const removeThis = (rowData) => {
        const index = groupUsers.groupsData.findIndex(
            (item) => item.user_email === rowData.user_email
        );
        if (index != null && index != undefined) {
            groupUsers.groupsData.splice(index, 1);
        }
        var finalData = {
            groupsData: groupUsers.groupsData,
        };
        setGroupUsers(finalData);
    };

    const saveGroup = () => {
        setGroupProcessing(true);

        if (groupName.length == 0) {
            setGroupNameError("Please provide a group name");
        } else if (groupName.length <= 2) {
            setGroupNameError(
                "Group name length must be greater than 2 character"
            );
        } else if (groupName.length > 2) {
            setGroupNameError("");
            // proceed futher for saving group data
            var postData = {
                name: groupName,
                users: groupUsers,
                data_scope: appDataScope,
            };
            Inertia.post('users-and-groups/groups/store', postData, {
                preserveState: true,
                onSuccess: () => {
                    setAjaxDataGroup([]);
                    handleAddGroupModalClose();
                    setUsersRefresh(true);
                },
                onError: (res) => {
                    if(res.usersRequired){
                        setUsersRequiredError(res.usersRequired);
                        setShowUserRequiredError(true);
                    }
                }
            });
        }
        setGroupProcessing(false);
    }

    const updateGroup = () => {
        if (groupName.length == 0) {
            setGroupNameError("Please provide a group name");
        } else if (groupName.length <= 2) {
            setGroupNameError(
                "Group name length must be greater than 2 character"
            );
        } else if (groupName.length > 2) {
            setGroupNameError("");
            // proceed futher for saving group data
            var postData = {
                id: groupId,
                name: groupName,
                users: groupUsers,
                data_scope: appDataScope,
            };

            Inertia.post(
                route(
                    "policy-management.users-and-groups.groups.update",
                    groupId
                ),
                postData,
                {
                    preserveState: true,
                    onSuccess: (data) => {
                        setAjaxDataGroup([]);
                        setAddGroupModelShow(false);
                        setUsersRefresh(true);
                    }
                }
            );
        }
    };

    const breadcumbsData = {
        title: "Users & Groups - Policy Management",
        breadcumbs: [
            {
                title: "Policy Management",
                href: "campaigns",
            },
            {
                title: "Users & Groups",
                href: "",
            },
        ],
    };

    const fetchURL = "policy-management/users-and-groups/groups/get-json-data";
    const columns = [
        {
            accessor: "0",
            label: "Name",
            priorityLevel: 1,
            position: 1,
            minWidth: 150,
        },
        {
            accessor: "1",
            label: "Status",
            priorityLevel: 1,
            position: 2,
            minWidth: 150,
        },
        {
            accessor: "2",
            label: "No. of Members",
            priorityLevel: 1,
            position: 3,
            minWidth: 50,
        },
        {
            accessor: "3",
            label: "Date Created",
            priorityLevel: 1,
            position: 4,
            minWidth: 150,
        },
        {
            accessor: "4",
            label: "Last Updated",
            priorityLevel: 2,
            position: 5,
            minWidth: 150,
        },
        {
            accessor: "5",
            label: "Action",
            priorityLevel: 0,
            position: 13,
            minWidth: 150,
            sortable: false,
            CustomComponent: ({row}) => {
                return (
                    <Dropdown align="end" className='d-inline-block'>
                        <Dropdown.Toggle
                            as="a"
                            bsPrefix="card-drop arrow-none cursor-pointer"
                        >
                            <i className="mdi mdi-dots-horizontal m-0 text-muted h3" />
                        </Dropdown.Toggle>
                        <Dropdown.Menu className="dropdown-menu-end">
                            <Link
                                className="dropdown-item d-flex align-items-center"
                                onClick={(e) => editGroup(e, row)}
                            >
                                <i className="mdi mdi-square-edit-outline font-18 me-1"/> Edit
                            </Link>
                            <Link
                                className="dropdown-item d-flex align-items-center"
                                onClick={(e) => deleteGroup(e, row)}
                            >
                                <i className="mdi mdi-delete-outline font-18 me-1"/> Delete
                            </Link>
                        </Dropdown.Menu>
                    </Dropdown>
                    // <Fragment>
                    //     <div className="btn-group">
                    //         <a
                    //             className="edit-group-action btn btn-info btn-xs waves-effect waves-light text-white"
                    //             onClick={(e) => editGroup(e, row)}
                    //         >
                    //             <i className="fe-edit"></i>
                    //         </a>
                    //         <a
                    //             className="btn btn-danger btn-xs waves-effect waves-light deletem text-white"
                    //             onClick={(e) => deleteGroup(e, row)}
                    //         >
                    //             <i className="fe-trash-2"></i>
                    //         </a>
                    //     </div>
                    // </Fragment>
                );
            },
        },
    ];

    const fetchURLUsers = "policy-management/users-and-groups/users/get-data";
    const columnsUsers = [
        {
            accessor: "1",
            label: "First Name",
            priorityLevel: 1,
            position: 1,
            minWidth: 150,
        },
        {
            accessor: "2",
            label: "Last Name",
            priorityLevel: 1,
            position: 2,
            minWidth: 150,
        },
        {
            accessor: "3",
            label: "Email",
            priorityLevel: 1,
            position: 3,
            minWidth: 50,
        },
        {
            accessor: "4",
            label: "Groups",
            priorityLevel: 1,
            position: 4,
            minWidth: 150,
        },
        {
            accessor: "5",
            label: "Status",
            priorityLevel: 2,
            position: 5,
            minWidth: 150,
        },
        {
            accessor: "6",
            label: "Date Created",
            priorityLevel: 2,
            position: 6,
            minWidth: 150,
        },
        {
            accessor: "7",
            label: "Last Updated",
            priorityLevel: 2,
            position: 6,
            minWidth: 150,
        },
        {
            accessor: "action",
            label: "Action",
            priorityLevel: 0,
            position: 13,
            minWidth: 150,
            sortable: false,
            CustomComponent: ({row}) => {
                return (
                    <Fragment>
                        <Dropdown className="btn-group">
                            <Dropdown.Toggle
                                variant="secondary"
                                className="table-action-btn arrow-none btn btn-light btn-sm"
                                aria-expanded="false"
                            >
                                <i className="mdi mdi-dots-horizontal"></i>
                            </Dropdown.Toggle>
                            {row[8] == "active" ? (
                                <Dropdown.Menu className="dropdown-menu-end">
                                    <Dropdown.Item
                                        href="#"
                                        onClick={(e) => handleUserEdit(e, row)}
                                    >
                                        <i className="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>
                                        Edit User
                                    </Dropdown.Item>
                                    <Dropdown.Item
                                        href="#"
                                        onClick={(e) =>
                                            handleUserDelete(e, row)
                                        }
                                    >
                                        <i className="mdi mdi-delete-forever me-2 text-muted font-18 vertical-middle"></i>
                                        Delete
                                    </Dropdown.Item>
                                </Dropdown.Menu>
                            ) : (
                                <Dropdown.Menu className="dropdown-menu-end">
                                    <Dropdown.Item
                                        href="#"
                                        onClick={(e) => handleUserEdit(e, row)}
                                    >
                                        <i className="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>
                                        Edit User
                                    </Dropdown.Item>
                                    <Dropdown.Item
                                        href="#"
                                        onClick={(e) =>
                                            handleUserDelete(e, row)
                                        }
                                    >
                                        <i className="mdi mdi-delete-forever me-2 text-muted font-18 vertical-middle"></i>
                                        Delete
                                    </Dropdown.Item>
                                    <Dropdown.Item
                                        href="#"
                                        onClick={(e) =>
                                            handleUserActive(e, row)
                                        }
                                    >
                                        <i className="mdi mdi-account-check me-2 text-muted font-18 vertical-middle"></i>
                                        Active
                                    </Dropdown.Item>
                                </Dropdown.Menu>
                            )}
                        </Dropdown>
                    </Fragment>
                );
            },
        },
    ];

    const addUserColumns = [
        {
            accessor: "user_first_name",
            label: "First Name",
            priorityLevel: 1,
            position: 1,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "user_last_name",
            label: "Last Name",
            priorityLevel: 1,
            position: 2,
            minWidth: 150,
            sortable: false,
        },
        {
            accessor: "user_email",
            label: "Email",
            priorityLevel: 1,
            position: 3,
            minWidth: 50,
            sortable: false,
        },
        {
            accessor: "",
            label: "Action",
            priorityLevel: 2,
            position: 7,
            minWidth: 150,
            width: 300,
            sortable: false,
            CustomComponent: ({row}) => {
                return (
                    <Fragment>
                        <span
                            className="btn btn-danger btn-xs waves-effect waves-light delete"
                            onClick={() => removeThis(row)}
                        >
                            <i className="fe-trash-2"></i>
                        </span>
                    </Fragment>
                );
            },
        },
    ];

    const handleAddGroupModalClose = () => {
        setBulkCSVErrors("");
        setGroupsUserAddError("");
        setGroupNameError("");
        setShowUserRequiredError(false);
        setGroupUsers({groupsData: []});
        addExistingUserModalRef.current.clearAllStates();
        setAddGroupModelShow(false);
        if (errors.name)
            errors.name = '';
    }

    const validateEmail = (email) => {
        if (
            /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(
                email
            )
        ) {
            return false;
        } else {
            return true;
        }
    };

    const updateGroupName = (e) => {
        setGroupName(e.target.value);
        setGroupNameError('');
        if (errors.name)
            errors.name = '';
    }

    useDidMountEffect(() => {
        setGroupsRefresh(!groupsRefresh);
        setUsersRefresh(!usersRefresh);
    }, [appDataScope]);

    return (
        <AppLayout>
            <Fragment>
                <ContentLoader show={showContentLoader}>
                    <BreadcumbsComponent data={breadcumbsData}/>
                    {Object.keys(errors).length < 1 && <FlashMessages/>}
                    <div className='row'>
                        <div className='col'>
                            <div className='card'>
                                <div className='card-body'>
                                    <div className="row">
                                        <div className="col-12">
                                            <button
                                                type="button"
                                                id="add-new-group-btn"
                                                className="btn btn-sm btn-primary waves-effect waves-light float-end"
                                                onClick={newGroupModel}
                                            >
                                                <i className="mdi mdi-plus-circle"/> New Group
                                            </button>
                                        </div>
                                        <div
                                            className="col-12"
                                            id="users-and-groups-tabs-section"
                                        >
                                            <Tabs
                                                activeKey={activeKey}
                                                onSelect={(key) => setActiveKey(key)}
                                                className="mb-3"
                                            >
                                                <Tab eventKey="groups" title="Groups">
                                                    <DataTable
                                                        search
                                                        columns={columns}
                                                        fetchURL={fetchURL}
                                                        search
                                                        ajaxData={ajaxDataGroup}
                                                        refresh={groupsRefresh}
                                                    />
                                                </Tab>
                                                <Tab eventKey="users" title="Users">
                                                    <div id="user-datatable">
                                                        <DataTable
                                                            search
                                                            columns={columnsUsers}
                                                            fetchURL={fetchURLUsers}
                                                            ajaxData={ajaxData}
                                                            refresh={usersRefresh}
                                                        />
                                                    </div>
                                                </Tab>
                                            </Tabs>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <Modal
                        show={addGroupModelShow}
                        onHide={handleAddGroupModalClose}
                        dialogClassName="modal-90w"
                        aria-labelledby="example-custom-modal-styling-title"
                        size="lg"
                    >
                        <Modal.Header className='px-3 pt-3 pb-0' closeButton>
                            <Modal.Title className='my-0' id="example-custom-modal-styling-title">
                                {title}
                            </Modal.Title>
                        </Modal.Header>
                        <Modal.Body className='p-3'>
                            <div className="row">
                            <Alert variant="danger" show={showUserRequiredError} onClose={() => setShowUserRequiredError(false)} dismissible>
                                <strong>{usersRequiredError}</strong>
                            </Alert>
                                <div className="col-md-12">
                                    <div className="mb-3">
                                        <label htmlFor="group-name" className="form-label">Name <span
                                            className="required text-danger">*</span></label>
                                        <input type="text" className="form-control" name="name" id="group-name"
                                               value={groupName} onChange={(e) => {
                                            updateGroupName(e)
                                        }} placeholder="Group name"/>
                                        <input type="text" className="form-control" name="id" id="group-id"
                                               value={groupId} onChange={(e) => {
                                            setGroupId(e.target.value)
                                        }} placeholder="Group id" hidden/>
                                        {(groupNameError) &&
                                            <span className="invalid-feedback d-block">{groupNameError}</span>}
                                        {(errors.name) &&
                                            <span className="invalid-feedback d-block">{errors.name}</span>}
                                    </div>
                                </div>
                            </div>
                            <div className="row mb-3 mt-1">
                                <div></div>
                                <div className="col-12 styleAegis">
                                    {bulkCSVErrors && (
                                        <span className="invalid-feedback d-block">
                                            {bulkCSVErrors}
                                        </span>
                                    )}
                                    <input
                                        type="file"
                                        ref={hiddenFileInput}
                                        onChange={bulkImportChange}
                                        name="users-bulk-import"
                                        className="d-none"
                                        id="users-bulk-import"
                                    />
                                    <button
                                        type="button"
                                        onClick={bulkImportUsers}
                                        id="users-bulk-import-btn"
                                        className="btn btn-danger width-xl waves-effect waves-light ms-1"
                                    >
                                        Bulk Import Users
                                    </button>
                                    <button
                                        type="button"
                                        onClick={downloadCSVTemplate}
                                        className="btn btn-outline-secondary width-xl waves-effect waves-light ms-1 mt-2 mt-md-0"
                                    >
                                        Download CSV Template
                                    </button>
                                    <button
                                        type="button"
                                        id="add-existing-users-to-group-btn"
                                        className="btn btn-outline-secondary width-xl waves-effect waves-light ms-1 mt-2 mt-lg-0"
                                        onClick={() =>
                                            addExistingUserModalRef.current.addExistingUser()
                                        }
                                    >
                                        Add Existing Users
                                    </button>
                                    <button
                                        type="button"
                                        id="import-ldap-users-to-group-btn"
                                        className="btn btn-outline-secondary width-xl waves-effect waves-light ms-1 mt-2 mt-lg-0"
                                    >
                                        Import LDAP Users
                                    </button>
                                </div>
                            </div>
                            <form
                                id="add-user-form"
                                className="absolute-error-form"
                                onSubmit={addUserToGroup}
                            >
                                <div className="row style-container__user">
                                    <div className="col-md-3">
                                        <div className="mb-3">
                                            <input
                                                type="text"
                                                className="form-control"
                                                name="first_name"
                                                placeholder="First Name"
                                                value={newUser.fname}
                                                onChange={(e) =>
                                                    setNewUser({
                                                        ...newUser,
                                                        fname: e.target
                                                            .value,
                                                    })
                                                }
                                            />
                                            {groupsUserAddError.errors &&
                                            (newUser.fname === "" ||
                                                newUser.fname.length <
                                                2) ? (
                                                <span className="invalid-feedback d-block">
                                                    {
                                                        groupsUserAddError
                                                            .errors.fname
                                                    }
                                                </span>
                                            ) : (
                                                ""
                                            )}
                                        </div>
                                    </div>
                                    <div className="col-md-3">
                                        <div className="mb-3">
                                            <input
                                                type="text"
                                                className="form-control"
                                                name="last_name"
                                                placeholder="Last Name"
                                                value={newUser.lname}
                                                onChange={(e) =>
                                                    setNewUser({
                                                        ...newUser,
                                                        lname: e.target
                                                            .value,
                                                    })
                                                }
                                            />
                                            {groupsUserAddError.errors &&
                                            (newUser.lname === "" ||
                                                newUser.lname.length <
                                                2) ? (
                                                <span className="invalid-feedback d-block">
                                                    {
                                                        groupsUserAddError
                                                            .errors.lname
                                                    }
                                                </span>
                                            ) : (
                                                ""
                                            )}
                                        </div>
                                    </div>
                                    <div className="col-md-4">
                                        <div className="mb-3">
                                            <input
                                                type="text"
                                                className="form-control"
                                                name="email"
                                                placeholder="Email"
                                                value={newUser.email}
                                                onChange={(e) =>
                                                    setNewUser({
                                                        ...newUser,
                                                        email: e.target
                                                            .value,
                                                    })
                                                }
                                            />
                                            {groupsUserAddError.errors &&
                                                <span className="invalid-feedback d-block">
                                                    {
                                                        groupsUserAddError
                                                            .errors.email
                                                    }
                                                </span>
                                            }
                                        </div>
                                    </div>
                                    <div className="col-md-1">
                                        <div className="mb-3">
                                            <button
                                                type="submit"
                                                className="btn btn-danger waves-effect waves-light"
                                            >
                                                Add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div className=" table-container">
                                <DataTable
                                    columns={addUserColumns}
                                    offlineData={groupUsers.groupsData}
                                    triggeredLength={
                                        groupUsers.groupsData.length
                                    }
                                    search
                                />
                            </div>
                        </Modal.Body>
                        <Modal.Footer className='px-3 pt-0 pb-3'>
                            <button type="button" className="btn btn-secondary waves-effect" data-dismiss="modal"
                                    onClick={handleAddGroupModalClose}>Close
                            </button>

                            {!groupProcessing ?
                                <button type="button" className="btn btn-primary waves-effect waves-light"
                                        id="submit-group-btn"
                                        onClick={() => groupId == 0 ? saveGroup() : updateGroup()}>Save
                                    Changes</button> :
                                <Button variant="primary" disabled>
                                    <Spinner animation="border" size="sm"/> Updating...</Button>
                            }
                        </Modal.Footer>
                    </Modal>

                    {/* Modal for add existing user */}
                    <AddExistingUserModal
                        ref={addExistingUserModalRef}
                        groupUsers={groupUsers}
                        actionFunction={(userData) => {
                            setGroupUsers(userData);
                        }}
                    />

                    {/* Modal for user edit form */}
                    <Modal
                        show={userModelShow}
                        onHide={() => {
                            setUserModelShow(false);
                        }}
                        dialogClassName="modal-90w"
                        aria-labelledby="example-custom-modal-styling-title"
                        size="lg"
                    >
                        <Modal.Header className='px-3 pt-3 pb-0' closeButton>
                            <Modal.Title className='my-0' id="example-custom-modal-styling-title">
                                Edit User
                            </Modal.Title>
                        </Modal.Header>
                        <form
                            id="update-users-form"
                            onSubmit={editUserSubmit}
                        >
                            <Modal.Body className='p-3'>
                                <div className="row">
                                    <div className="col-md-6">
                                        <div className="mb-3">
                                            <label
                                                htmlFor="first-name"
                                                className="form-label"
                                            >
                                                First Name{" "}
                                            </label>
                                            <input
                                                type="text"
                                                className="form-control"
                                                name="first_name"
                                                id="first-name"
                                                value={data.first_name}
                                                onChange={(e) => {
                                                    setData(
                                                        "first_name",
                                                        e.target.value
                                                    );
                                                }}
                                                placeholder="First name"
                                            />
                                            {errors.formErrors ? (
                                                <label className="invalid-feedback d-block">
                                                    {
                                                        errors.formErrors
                                                            .first_name
                                                    }
                                                </label>
                                            ) : (
                                                ""
                                            )}
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="mb-3">
                                            <label
                                                htmlFor="last-name"
                                                className="form-label"
                                            >
                                                Last Name
                                            </label>
                                            <input
                                                type="text"
                                                className="form-control"
                                                name="last_name"
                                                id="last-name"
                                                value={data.last_name}
                                                onChange={(e) => {
                                                    setData(
                                                        "last_name",
                                                        e.target.value
                                                    );
                                                }}
                                                placeholder="Last name"
                                            />
                                            {errors.formErrors ? (
                                                <label className="invalid-feedback d-block">
                                                    {
                                                        errors.formErrors
                                                            .last_name
                                                    }
                                                </label>
                                            ) : (
                                                ""
                                            )}
                                        </div>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-12">
                                        <div className="mb-0">
                                            <label
                                                htmlFor="email"
                                                className="form-label"
                                            >
                                                Email{" "}
                                            </label>
                                            <input
                                                type="text"
                                                className="form-control"
                                                name="email"
                                                id="email"
                                                value={data.email}
                                                onChange={(e) => {
                                                    setData(
                                                        "email",
                                                        e.target.value
                                                    );
                                                }}
                                                placeholder="Email"
                                            />
                                            {errors.formErrors ? (
                                                <label className="invalid-feedback d-block">
                                                    {errors.formErrors.email}
                                                </label>
                                            ) : (
                                                ""
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </Modal.Body>
                            <Modal.Footer className='px-3 pt-0 pb-3'>
                                <button
                                    type="button"
                                    className="btn btn-secondary waves-effect"
                                    onClick={() => setUserModelShow(false)}
                                >
                                    Close
                                </button>
                                {!processing ? (
                                    <button
                                        type="submit"
                                        className="btn btn-primary waves-effect waves-light"
                                    >
                                        Save Changes
                                    </button>
                                ) : (
                                    <Button variant="primary" disabled>
                                        <Spinner
                                            animation="border"
                                            size="sm"
                                        />{" "}
                                        Updating...
                                    </Button>
                                )}
                            </Modal.Footer>
                        </form>
                    </Modal>
                </ContentLoader>
            </Fragment>
        </AppLayout>
    );
}

export default UsersAndGroups;