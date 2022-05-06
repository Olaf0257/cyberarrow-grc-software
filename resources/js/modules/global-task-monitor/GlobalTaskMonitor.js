import React, { Fragment, useEffect, useState, useRef } from 'react';
import { Link, usePage } from '@inertiajs/inertia-react';
import { useSelector } from 'react-redux';
import Select from 'react-select';
import Spinner from 'react-bootstrap/Spinner';
import DataTable from '../../common/data-table/DataTable';
import fileDownload from 'js-file-download';
import './styles/global-task-monitor.scss';
import './styles/filter.scss';

import "flatpickr/dist/themes/light.css";
import Flatpickr from "react-flatpickr";

import AppLayout from '../../layouts/app-layout/AppLayout';
import ProjectFilter from './components/ProjectFilter';
import DepartmentFilter from './components/DepartmentFilter';
import BreadcumbsComponent from "../../common/breadcumb/Breadcumb";
import DepartmentFilterWithoutDataScope from './components/DepartmentFilterWithoutDataScope';
import ProjectFilterWithoutDataScope from './components/ProjectFilterWithoutDataScope';

function GlobalTaskMonitor(props) {
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value)
    const propsData = usePage().props;
    const fetchURL = propsData.taskListURL;
    const [assigneeUsers, setAssigneeUsers] = useState(propsData.assigneeUsers);
    const [approverUsers, setApproverUsers] = useState(propsData.approverUsers);
    const [projects, setProjects] = useState(propsData.complianceProjects);
    const urlSegmentTwo = propsData.urlSegmentTwo;
    const [selectedDepartments, setSelectedDepartments] = useState(propsData.selectedDepartments);
    const [selectedProjects, setSelectedProjects] = useState(propsData.selectedProjects);
    const [selectedStatus, setSelectedStatus] = useState(propsData.selectedStatus);
    const [selectedStage, setSelectedStage] = useState(propsData.selectedStage);
    const [responsibleView, setResponsibleView] = useState(propsData.responsibleView);
    const [approverView, setApproverView] = useState(propsData.approverView);
    const authUser = propsData.authUser;

    const statusOptions = [
        { value: '', label: 'All Statuses' },
        { value: 'active', label: 'Upcoming' },
        { value: 'pass_due', label: 'Past Due' },
        { value: 'due_today', label: 'Due Today' }
    ]

    const stageOptions = [
        { value: '', label: 'All Stages' },
        { value: 'Under Review', label: 'Under Review' },
        { value: 'Implemented', label: 'Implemented' },
        { value: 'Not Implemented', label: 'Not Implemented' },
        { value: 'Rejected', label: 'Rejected' }
    ];

    const projectFilterRef = useRef(null)
    const departmentFilterRef = useRef(null)
    const assigneeUsersSelectRef = useRef()
    const approverUsersSelectRef = useRef()

    //Filters
    const [departments, setDepartments] = useState('');
    const [filterProjects, setFilterProjects] = useState('');
    const [filterDepartments, setFilterDepartments] = useState('');
    const [standard, setStandard] = useState('');
    const [task, setTask] = useState('');
    const [assignedUser, setAssignedUser] = useState('');
    const [approverUser, setApproverUser] = useState('');
    const [completionDate, setCompletionDate] = useState('');
    const [dueDate, setDueDate] = useState('');
    const [status, setStatus] = useState('');
    const [stage, setStage] = useState('');
    const [ajaxData, setAjaxData] = useState([]);

    //Setting Form Values Based on Values From Query Params
    useEffect(() => {
        if (urlSegmentTwo == 'global') {
            //pre-select all the departments in department filter
            departmentFilterRef.current.handleRenderableDataUpdate(() => { })
        }
        if (responsibleView) {
            setAssignedUser(authUser.id)
        }
        if (approverView) {
            setApproverUser(authUser.id)
        }
        if (selectedStatus) {
            setStatus(selectedStatus)
            setSelectedStatus(null)
        }
        if (selectedStage) {
            setStage(selectedStage)
            setSelectedStage(null)
        }
        if (selectedDepartments) {
            setFilterDepartments(selectedDepartments.map((c) => c.value).toString())
        }
        if (selectedProjects) {
            setFilterProjects(selectedProjects.map((c) => c.value).toString())
        }
        if (urlSegmentTwo == 'compliance') {
            //pre-select all the projects for filter
            getAllProjects()
        }
    }, []);

    const getAllProjects = () => {
        try {
            axiosFetch.get(route('common.get-all-projects'))
                .then(res => {
                    setFilterProjects(res.data.projects.map((c) => c.value).toString());
                })
        } catch (error) {
            console.log("Response error");
        }
    }

    useEffect(() => {
        getAssigneeandApproverUsers();
    }, [appDataScope])

    const getAssigneeandApproverUsers = () => {
        try {
            axiosFetch.get(route('common.get-users-by-department'), {
                params: {
                    data_scope: appDataScope
                }
            })
                .then(res => {
                    let tempUsers = res.data;
                    const clone = Object.assign([], tempUsers); //passed by value since objects are passed by reference in js
                    let allAssigneesJson = { value: 0, label: 'All Assignees' }
                    let allApproversJson = { value: 0, label: 'All Approvers' }
                    tempUsers.unshift(allAssigneesJson);
                    setAssigneeUsers(tempUsers);
                    clone.unshift(allApproversJson);
                    setApproverUsers(clone);

                    if (window.location.pathname.split('/')[1] == 'global') { //Global Task Monitor
                        assigneeUsersSelectRef.current.setValue(allAssigneesJson);
                        approverUsersSelectRef.current.setValue(allApproversJson);
                    }
                    // else { // My Task Monitor
                    //     if (window.location.pathname.split('/')[3] == 'need-my-approval') {
                    //         assigneeUsersSelectRef.current.setValue(allAssigneesJson);
                    //     } else {
                    //         approverUsersSelectRef.current.setValue(allApproversJson);
                    //     }
                    // }

                })
        } catch (error) {
            console.log("Response error");
        }
    }

    const handleProjectSelect = (data) => {
        let latestFilterProjects = data.map((c) => c.value).toString()
        setFilterProjects(latestFilterProjects);
    }

    const handleSearch = () => {
        const data = {
            selected_departments: filterDepartments,
            selected_projects: filterProjects,
            standard_name: standard,
            control_name: task,
            responsible_user: assignedUser,
            approver_user: approverUser,
            completion_date: completionDate,
            due_date: dueDate,
            status,
            approval_status: stage
        };
        setAjaxData(data);
    }

    const handleExport = async () => {
        const data = {
            selected_departments: filterDepartments,
            selected_projects: filterProjects,
            standard_name: standard,
            control_name: task,
            responsible_user: assignedUser,
            approver_user: approverUser,
            completion_date: completionDate,
            due_date: dueDate,
            status,
            approval_status: stage
        };
        try {
            let response = await axiosFetch({
                url: route('compliance.tasks.export-data'),
                method: 'POST',
                data: data,
                responseType: 'blob', // Important
            })

            fileDownload(response.data, 'tasks.csv');
        } catch (error) {
            console.log(error);
        }
    }

    const breadcumbsData = {
        "title": `${urlSegmentTwo == 'compliance' ? 'My Task Monitor' : 'Global Task Monitor'}`,
        "breadcumbs": [
            {
                "title": "Compliance",
                "href": ""
            },
            {
                "title": "Dashboard",
                "href": route('compliance-dashboard')
            },
            {
                "title": "Task Monitor",
                "href": ""
            },
        ]
    }

    const columns = [
        { accessor: 'project_name', label: 'Project', priorityLevel: 1, position: 1, minWidth: 150, sortable: false },
        { accessor: 'standard_name', label: 'Standard', priorityLevel: 4, position: 2, minWidth: 250, sortable: false },
        { accessor: 'control', label: 'Control', priorityLevel: 1, position: 3, minWidth: 250, sortable: false },
        { accessor: 'control_description', label: 'Control Description', priorityLevel: 5, position: 4, minWidth: 150, sortable: false },
        { accessor: 'type', label: 'Type', priorityLevel: 1, position: 5, minWidth: 150, sortable: false },
        { accessor: 'assigned', label: 'Assigned', priorityLevel: 1, position: 6, minWidth: 150, sortable: false },
        { accessor: 'approver', label: 'Approver', priorityLevel: 1, position: 7, minWidth: 150, sortable: false },
        { accessor: 'completion_date', label: 'Completion Date', priorityLevel: 3, position: 8, minWidth: 150, sortable: false },
        { accessor: 'due_date', label: 'Due Date', priorityLevel: 2, position: 9, minWidth: 250, sortable: false },
        { accessor: 'task_status', label: 'Status', priorityLevel: 1, position: 10, minWidth: 150, sortable: false },
        { accessor: 'approval_stage', label: 'Approval Stage', priorityLevel: 1, position: 11, minWidth: 150, sortable: false },
        {
            accessor: 'action', label: 'Action', priorityLevel: 0, position: 12, minWidth: 50, sortable: false,
            CustomComponent: ({ row }) => {
                return (
                    <Fragment>
                        <Link
                            href={`${appBaseURL}/compliance/projects/${row.project_id}/controls/${row.id}/show/tasks`}
                            className="btn btn-primary go"
                            method="get">
                            Go
                        </Link>
                    </Fragment>
                );
            },
        },
    ]

    return (
        <AppLayout>
            {/* <!-- page title here --> */}
            <BreadcumbsComponent data={breadcumbsData} />

            <div className="row global-task-monitor filter">
                <div className='col'>
                    <div className='card'>
                        <div className="card-body w-100">
                           <div className='row'>
                            <div className="col-12">
                                    <div className="filter-row d-flex flex-column flex-md-row justify-content-between my-2 p-2 rounded">
                                        <div className="filter-row__wrap d-flex flex-wrap">
                                            <div className="m-1 all-department">
                                                {urlSegmentTwo == 'compliance' ?
                                                    <DepartmentFilterWithoutDataScope ref={departmentFilterRef} /> :
                                                    <DepartmentFilter ref={departmentFilterRef} />
                                                }
                                                {/* <select name="selected_departments[]" class ="filter-input" id="departments-filter" multiple>
                                                    @if($organization)
                                                    <option value="0" {{ in_array(0, $selectedDepartments)?'selected': '' }}>{{ decodeHTMLEntity($organization-> name)}}</option>
                                                    @endif
                                                    @foreach($departments as $department)
                                                    <option value="{{ $department->id }}" {{ in_array($department-> id, $selectedDepartments)?'selected': '' }}>{{ decodeHTMLEntity($department-> name) }}</option>
                                                    @endforeach
                                                    </select> */}
                                            </div>
                                            <div className="all-projects m-1">
                                                {/* {projects.length > 0 ?
                                                    <Select
                                                        options={projects}
                                                        defaultValue={selectedProjects}
                                                        id="projectId"
                                                        onChange={(e) => setFilterProjects(e.map((c) => c.value).toString())}
                                                        isMulti /> :
                                                    <Spinner className="mt-2" animation="border" variant="dark" size="sm" />} */}
                                                {/* <MultiSelect
                                                    options={projects}
                                                    // value={selectedProjects}
                                                    onChange={(e) => setFilterProjects(e.map((c) => c.value).toString())}
                                                    labelledBy="Select"
                                                    className="w-100 project-filter-container"
                                                /> */}
                                                {urlSegmentTwo == 'compliance' ?
                                                    <ProjectFilterWithoutDataScope actionFunction={handleProjectSelect} selectedProjects={selectedProjects} /> :
                                                    <ProjectFilter actionFunction={handleProjectSelect} selectedProjects={selectedProjects} ref={projectFilterRef} />
                                                }
                                            </div>
                                            <div className="m-1 all-standards">
                                                <input onChange={(e) => setStandard(e.target.value)} className="form-control filter-input" name="standard_name" type="text" placeholder="All Standards" />
                                            </div>
                                            <div className="m-1 all-tasks">
                                                <input onChange={(e) => setTask(e.target.value)} className="form-control filter-input" name="control_name" type="text" placeholder="All Tasks" />
                                            </div>
                                            <div className="all-users m-1">
                                                {assigneeUsers.length > 0 ?
                                                    <Select
                                                        ref={assigneeUsersSelectRef}
                                                        defaultValue={
                                                            responsibleView ?
                                                                assigneeUsers.filter(option => authUser.id === option.value) :
                                                                assigneeUsers[0]
                                                        }
                                                        className="react-select"
                                                        classNamePrefix="react-select"
                                                        options={assigneeUsers}
                                                        id="taskContributorId"
                                                        onChange={(e) => setAssignedUser(e.value)}
                                                        isDisabled={responsibleView}
                                                    /> :
                                                    <Spinner className="mt-2" animation="border" variant="dark" size="sm" />
                                                }
                                                {/* <select className="form-control select2-field filter-input" name="responsible_user" {{ $responsibleView? "disabled": '' }}> */}
                                                {/* <option value="">All Users</option> */}
                                                {/* @foreach($taskContributors as $key => $value) */}
                                                {/* <option value="{{ $value }}" {{ $responsibleView?($authUser-> id == $value ? 'selected' : '' ): ''}}> {{ decodeHTMLEntity($key) }}</option> */}
                                                {/* @endforeach */}
                                                {/* </select> */}
                                            </div>
                                            <div className="all-approvers m-1">
                                                {approverUsers.length > 0 ?
                                                    <Select
                                                        ref={approverUsersSelectRef}
                                                        id="taskContributorId"
                                                        className="react-select"
                                                        classNamePrefix="react-select"
                                                        defaultValue={
                                                            approverView ?
                                                                approverUsers.filter(option => authUser.id === option.value) :
                                                                approverUsers[0]
                                                        }
                                                        options={approverUsers}
                                                        onChange={(e) => setApproverUser(e.value)}
                                                        isDisabled={approverView}
                                                    /> :
                                                    <Spinner className="mt-2" animation="border" variant="dark" size="sm" />}
                                                {/* <select className="form-control select2-field filter-input" name="approver_user" {{ $approverView? "disabled": '' }}> */}
                                                {/* <option value="">All Approvers</option> */}
                                                {/* @foreach($taskContributors as $key => $value) */}
                                                {/* <option value="{{ $value }}" {{ $approverView?($authUser-> id == $value ? 'selected' : '' ) : ''}}> {{ decodeHTMLEntity($key) }}</option> */}
                                                {/* @endforeach */}
                                                {/* </select> */}
                                            </div>

                                            <div className="completion-date m-1">
                                                <div className="input-group">
                                                    <Flatpickr
                                                        className="flatpickr-date"
                                                        placeholder='Completion Date'
                                                        onChange={([completion_date]) => {
                                                            setCompletionDate(completion_date.getFullYear() + "-" + (completion_date.getMonth() + 1) + "-" + completion_date.getDate());
                                                        }}
                                                    />
                                                    {/* <input name="completion_date" type="date" className="basic-datepicker form-control pl-1 pr-1 border-right-0 flatpickr-input filter-input" placeholder="Completion Date" /> */}
                                                    <div className="border-start-0">
                                                        <span className="input-group-text bg-none"><i className="mdi mdi-calendar-outline"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="due-date m-1">
                                                <div className="input-group">
                                                    <Flatpickr
                                                        className="flatpickr-date"
                                                        placeholder='Due Date'
                                                        onChange={([due_date]) => {
                                                            setDueDate(due_date.getFullYear() + "-" + (due_date.getMonth() + 1) + "-" + due_date.getDate());
                                                        }}
                                                    />
                                                    {/* <input name="due_date" type="date" className="basic-datepicker form-control pl-1 pr-1 border-right-0 flatpickr-input filter-input" placeholder="Due Date" /> */}
                                                    <div className="border-start-0">
                                                        <span className="input-group-text bg-none"><i className="mdi mdi-calendar-outline"></i></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="all-status m-1">
                                                <Select
                                                    defaultValue={
                                                        selectedStatus ?
                                                            statusOptions.filter(option => option.value == selectedStatus) :
                                                            statusOptions[0]
                                                    }
                                                    className="react-select"
                                                    classNamePrefix="react-select"
                                                    options={statusOptions}
                                                    onChange={(e) => setStatus(e.value)}
                                                    id="statusId"
                                                />
                                                {/* <select class ="form-control select2-field filter-input" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="active" {{ $currentPage == 'all-active'?'selected': ''}}>Upcoming</option>
                                    <option value="pass_due" {{ $currentPage == 'pass-due'?'selected': ''}}>Past Due</option>
                                    <option value="due_today" {{ $currentPage == 'due-today'?'selected': ''}}>Due Today</option>
                                    </select > */}
                                            </div >
                                            <div className="all-stages m-1">
                                                <Select
                                                    defaultValue={
                                                        selectedStage ?
                                                            stageOptions.filter(option => option.value == selectedStage) :
                                                            stageOptions[0]
                                                    }
                                                    className="react-select"
                                                    classNamePrefix="react-select"
                                                    options={stageOptions}
                                                    onChange={(e) => setStage(e.value)}
                                                // id="stageId"
                                                />
                                                {/* <select class ="form-control select2-field filter-input" name="approval_status">
                                    <option value="">All Stages</option>
                                    <option value="Under Review" {{ ($currentPage == 'under-review' || $currentPage == 'need-my-approval')?'selected': ''}}>Under Review</option>
                                    <option value="Implemented">Implemented</option>
                                    <option value="Not Implemented">Not Implemented</option>
                                    <option value="Rejected">Rejected</option>
                                    </select> */}
                                            </div >
                                        </div >
                                        {/* < !--/.filter-row__wrap--> */}
                                        <div className="m-1 text-center text-sm-auto w-10 task-button-wrapper" >
                                            <button className="btn btn-primary" type="button" id="search" onClick={handleSearch}> Search </button>
                                            {/* <form action="/global/tasks/export-data" method="GET"> */}
                                            <button id="export-data-btn" className="btn btn-primary" onClick={handleExport}> Export </button>
                                            {/* </form> */}
                                        </div>
                                    </div>
                                    {/* < !--/.filter-row--> */}
                                </div >
                                <div className="col-12">
                                    {/* <!-- table --> */}
                                    <DataTable columns={columns} fetchURL={fetchURL} ajaxData={ajaxData} refreshOnPageChange />
                                </div>
                           </div>
                        </div>
                    </div>
                </div>
            </div >
        </AppLayout >
    );
}

export default GlobalTaskMonitor;
