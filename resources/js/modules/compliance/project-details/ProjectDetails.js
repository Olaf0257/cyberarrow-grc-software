import React, {Fragment, useEffect, useState, useRef} from 'react';
import {useDispatch, useSelector} from "react-redux";
import AppLayout from '../../../layouts/app-layout/AppLayout';
import Breadcrumb from '../../../common/breadcumb/Breadcumb';
import ContentLoader from '../../../common/content-loader/ContentLoader';
import Tabs from 'react-bootstrap/Tabs';
import Tab from 'react-bootstrap/Tab';
import './style.scss';
import Chart from 'react-apexcharts';
import DataTable from '../../../common/data-table/DataTable';
import Select from 'react-select';
import Flatpickr from "react-flatpickr";
import "flatpickr/dist/themes/light.css";
import feather from "feather-icons";
import {Inertia} from '@inertiajs/inertia';
import FlashMessages from '../../../common/FlashMessages';
import {Link} from '@inertiajs/inertia-react'
import fileDownload from "js-file-download";
import moment from 'moment/moment';


function ProjectDetails(props) {
    const [controlsDataNew, setControlsDataNew] = useState([]);
    const [controlsAdmins, setControlsAdmins] = useState([]);
    const [frequencySelectOptions, setfrequencySelectOptions] = useState([]);
    const [errorRows, setErrorRows] = useState([]);
    const [errorDeallineRows, setErrorDeallineRows] = useState([]);
    const [invalidDeadlines, setInvalidDeadlines] = useState([]);

    const [fetchUrl, setFetchUrl] = useState("/compliance/projects/" + props.project.id + "/controls-json");

    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);

    const is_control_disabled = useState(props.control_disabled);
    const dataScopeRef = useRef(appDataScope);
    const dispatch = useDispatch();

    useEffect(() => {
        document.title = "Project Details";
        if (dataScopeRef.current !== appDataScope) {
            Inertia.get(route("compliance-projects-view"));
        }
        feather.replace();
    }, [appDataScope]);

    const breadcumbsData = {
        "title": "Project Details",
        "breadcumbs": [
            {
                "title": "Compliance",
                "href": route('compliance-dashboard')
            },
            {
                "title": "Projects",
                "href": route('compliance-projects-view')
            },
            {
                "title": "Details",
                "href": "#"
            },
        ]
    }

    useEffect(async () => {
        let response = await axiosFetch.get(route('common.get-users-by-department'), {
            params: {
                data_scope: appDataScope
            }
        });
        let resData = response.data;

        setControlsAdmins(resData);

        let options = [
            {value: 'One-Time', label: 'One-Time'},
            {value: 'Monthly', label: 'Monthly'},
            {value: 'Every 3 Months', label: 'Every 3 Months'},
            {value: 'Bi-Annually', label: 'Bi-Annually'},
            {value: 'Annually', label: 'Annually'}
        ];
        setfrequencySelectOptions(options);

    }, [appDataScope]);

    const getSelectDefalultValue = (value) => {
        if (value) {
            var selected = controlsAdmins.filter(function (el) {
                return el.value == value
            });
            return selected;
        } else {
            return null;
        }
    }

    const handleChangeTab = (tabKey) => {
        window.history.pushState('','',route('compliance-project-show', [props.project.id,tabKey.toLowerCase()]))
    }

    const checkValidDate = (date) =>{
        var today = new Date();
        const offset = today.getTimezoneOffset()
        today = new Date(today.getTime() - (offset * 60 * 1000)) 
        const today_date = today.toISOString().split('T')[0]
        if(date<today_date){
            return false;
        }
        else{
            return true;
        }
    }
    const setDeadlineDefault = (row) => {
        var today = new Date();
        const offset = today.getTimezoneOffset()
        today = new Date(today.getTime() - (offset * 60 * 1000))
        const deadline_date = today.toISOString().split('T')[0]

        // if (row.deadline) {
        //     if (row.deadline < deadline_date) {
        //         var deadline_element = 'deadline_' + row.id;
        //         var invalid_deadlines = invalidDeadlines;
        //         invalid_deadlines.push(row);
        //     } else {
        //         return row.deadline;
        //     }
        // } else {
        //     return deadline_date;
        // }

    }

    const getFrequencySelectDefalultValue = (value) => {
        if (value) {
            var selected = frequencySelectOptions.filter(function (el) {
                return el.value == value
            });
            return selected;
        } else {
            return null;
        }
    }

    const checkIfSameApproverResponsible = (row) => {
        var exist_rows = errorRows.filter(function (el) {
            return el == row.id
        });

        var element = 'responsible_' + row.id;
        var element2 = 'approver_' + row.id;
        if (!row.responsible && !row.approver) {
            document.getElementById(element).style.display = 'none';
            document.getElementById(element2).style.display = 'none';
            if (exist_rows.length > 0) {
                errorRows.pop(row.id);
            }
            return;
        } else {
            if (exist_rows.length == 0) {
                if (row.responsible === row.approver) {
                    document.getElementById(element2).textContent = "Responsible Approver can't be same";
                    document.getElementById(element).textContent = "Responsible Approver can't be same";
                    document.getElementById(element).style.display = 'block';
                    document.getElementById(element2).style.display = 'block';
                    errorRows.push(row.id);
                    return;
                } else {
                    checkIfResponsibleApproverRequired(row, element, element2);
                }
            } else {
                if (row.responsible === row.approver) {
                    document.getElementById(element2).textContent = "Responsible Approver can't be same";
                    document.getElementById(element).textContent = "Responsible Approver can't be same";
                    document.getElementById(element).style.display = 'block';
                    document.getElementById(element2).style.display = 'block';
                    errorRows.push(row.id);
                    return;
                }
                if (row.responsible != row.approver) {
                    document.getElementById(element).style.display = 'none';
                    document.getElementById(element2).style.display = 'none';
                    errorRows.pop(row.id);
                }
                checkIfResponsibleApproverRequired(row, element, element2);
            }
        }

    }

    const checkIfResponsibleApproverRequired = (row, element, element2) => {
        if (!row.approver) {
            document.getElementById(element2).textContent = "You must select any one approver !";
            document.getElementById(element2).style.display = 'block';
            errorRows.push(row.id);
            return;
        } else {
            document.getElementById(element2).style.display = 'none';
            errorRows.pop(row.id);
        }
        if (!row.responsible) {
            document.getElementById(element).textContent = "You must select any one responsible !";
            document.getElementById(element).style.display = 'block';
            errorRows.push(row.id);
            return;
        } else {
            document.getElementById(element).style.display = 'none';
            errorRows.pop(row.id);
        }
    }

    const handleApplicableChange = (e, row) => {
        var value = e.target.checked ? 1 : 0;
        row.applicable = value;
        var class_name = '';
        var row_status = row.status;
        if(!row.applicable){
            class_name = 'badge task-status-purple w-60';
            row_status = 'Not Applicable';
        }else if(row.status==='Not Implemented'){
            class_name='badge task-status-red w-60';
        }
        else if(row.status==='Implemented'){
            class_name='badge task-status-green w-60';
        }
        else if(row.status==='Rejected'){
            class_name='badge task-status-orange w-60';
        }
        else{
            class_name='badge task-status-blue w-60';
        }

        var status = document.getElementById("task-status"+row.id);
        status.removeAttribute('class');
        status.setAttribute('class',class_name);
        status.textContent = row_status;
        // if(!row.applicable){
        //     status.parentElement.parentElement.setAttribute('class','row-background-color');
        // }else{
        //     status.parentElement.parentElement.removeAttribute('class');
        // }
        updateControlsDataNew(value, row, 'applicable');

        var selectArray = ['responsible','approver','frequency'];
        if (value) {
            selectArray.forEach((name) => {
                document.getElementById(`${name}-select-${row.id}`).classList.remove('d-none');
                document.getElementById(`${name}-select-disabled-${row.id}`).classList.add('d-none');
            });

            //deadline
            document.getElementById(`deadline_enabled_${row.id}`).classList.remove('d-none');
            document.getElementById(`deadline_disabled_${row.id}`).classList.add('d-none');

            //control name
            document.getElementById(`control_link_name_${row.id}`).classList.remove('d-none');
            document.getElementById(`control_name_${row.id}`).classList.add('d-none');

            //control detail
            document.getElementById(`control_detail_btn_${row.id}`).classList.remove('d-none');
        }else{
            selectArray.forEach((name) => {
                document.getElementById(`${name}-select-disabled-${row.id}`).classList.remove('d-none');
                document.getElementById(`${name}-select-${row.id}`).classList.add('d-none');
            });

            //deadline
            document.getElementById(`deadline_disabled_${row.id}`).classList.remove('d-none');
            document.getElementById(`deadline_enabled_${row.id}`).classList.add('d-none');

            // control link name
            document.getElementById(`control_link_name_${row.id}`).classList.add('d-none');
            document.getElementById(`control_name_${row.id}`).classList.remove('d-none');

            //control detail
            document.getElementById(`control_detail_btn_${row.id}`).classList.add('d-none');
        }

    }

    const handleResponsibleChange = (e, row) => {
        if (e) {
            var value = e.value;
            row.responsible = value;
            checkIfSameApproverResponsible(row);
            updateControlsDataNew(value, row, 'responsible');
        } else {
            row.responsible = null;
            checkIfSameApproverResponsible(row);
            updateControlsDataNew(null, row, 'responsible');
        }
    }

    const handleFrequencyChange = (e, row) => {
        var value = e.value;
        row.frequency = value;
        updateControlsDataNew(value, row, 'frequency');
    }

    const handleApproverChange = (e, row) => {
        if (e) {
            var value = e.value;
            row.approver = value;
            checkIfSameApproverResponsible(row);
            updateControlsDataNew(value, row, 'approver');
        } else {
            row.approver = null;
            checkIfSameApproverResponsible(row);
            updateControlsDataNew(null, row, 'approver');
        }
    }

    const handleDateChange = (value, row) => {
        var element='deadline_'+row.id;
        const offset = value.getTimezoneOffset()
        value = new Date(value.getTime() - (offset * 60 * 1000))
        const deadline_date = value.toISOString().split('T')[0]
        if(checkValidDate(deadline_date)){
            row.deadline = deadline_date;
            updateControlsDataNew(deadline_date, row, 'deadline');
            document.getElementById(element).style.display = 'none';
           
            errorDeallineRows.pop(row.id);
        }
        else{
            var exist_rows = errorDeallineRows.filter(function (el) {
                return el == row.id
            });
            if(exist_rows.length === 0 ){
                document.getElementById(element).style.display = 'block';
                errorDeallineRows.push(row.id);
            }
        }
    }

    const updateControlsDataNew = (value, row, type) => {
        var new_controls_data = controlsDataNew;
        var exist = controlsDataNew.filter(function (el) {
            return el.id == row.id
        });
        if (exist.length == 0) {
            var new_controls_data = controlsDataNew;
            new_controls_data.push(row);
            setControlsDataNew(new_controls_data);
        } else {
            Object.keys(controlsDataNew).forEach(function (key) {
                if (controlsDataNew[key].id === row.id) {
                    switch (type) {
                        case 'applicable':
                            controlsDataNew[key].applicable = value;
                            break;
                        case 'responsible':
                            controlsDataNew[key].responsible = value;
                            break;
                        case 'approver':
                            controlsDataNew[key].approver = value;
                            break;
                        case 'deadline':
                            controlsDataNew[key].deadline = value;
                            break;
                        case 'frequency':
                            controlsDataNew[key].frequency = value;
                            break;
                        default:
                    }
                }
            });
            setControlsDataNew(controlsDataNew);
        }
    }

    const handleReadyDeadline = () => {
    }
    const disableSaveButton = (value) => {
        var elements = [0, 1];
        elements.forEach(element => {
            if (value) {
                document.getElementsByClassName('custom-save-button')[element].classList.add('expandRight');
                document.getElementsByClassName('custom-save-button')[element].disabled = true
                document.getElementsByClassName('custom-spinner-image')[element].style.display = 'block';
            } else {
                document.getElementsByClassName('custom-save-button')[element].classList.remove('expandRight');
                document.getElementsByClassName('custom-save-button')[element].disabled = false
                document.getElementsByClassName('custom-spinner-image')[element].style.display = 'none';
            }
        });

    }

    const saveAndProceed = () => {
        if (errorRows.length === 0 && errorDeallineRows.length === 0) {
            disableSaveButton(true);
            var controls = controlsDataNew;
            axiosFetch.post(
                route('compliance-project-controls-update-all-json', props.project.id),
                {'controls': controls}
            )
                .then(function (response) {
                    disableSaveButton(false);
                    if(document.getElementsByClassName('page-link').length){
                        var last_page_link_element = document.getElementsByClassName('page-link').length - 2;
                        const is_last_page = document.getElementsByClassName('page-link')[last_page_link_element].classList.contains('active');
                        if (is_last_page) {
                            AlertBox({
                                title: "Control assignment updated successfully!",
                                showCancelButton: false,
                                confirmButtonColor: '#b2dd4c',
                                confirmButtonText: 'OK',
                                icon: 'success',
                            })
                        } else {
                            AlertBox({
                                title: "Control assignment updated successfully!",
                                text: "Do you want to continue to the next page?",
                                showCancelButton: true,
                                confirmButtonColor: '#b2dd4c',
                                confirmButtonText: 'Yes',
                                cancelButtonText: "No",
                                icon: 'success',
                            }, function (confirmed) {
                                if (confirmed.value && confirmed.value == true) {
                                    var next_page_link_element = document.getElementsByClassName('page-link').length - 1;
                                    document.getElementsByClassName('page-link')[next_page_link_element].click()
                                } else {
                                    document.getElementsByClassName('page-link page-link-hover active')[0].click();
                                }
                            })
                        }
                    } else {
                        AlertBox({
                            title: "Control assignment updated successfully!",
                            showCancelButton: false,
                            confirmButtonColor: '#b2dd4c',
                            confirmButtonText: 'OK',
                            icon: 'success',
                        })
                    }
                })
                .catch(function (response) {
                    //handle error
                    disableSaveButton(false);
                });
        }

    }
    const columns = [
        {
            accessor: 'applicable', label: 'Applicable', priorityLevel: 1, position: 1, minWidth: 150, sortable: false,
            CustomComponent: ({row}) => {
                return (
                    <Fragment>
                        <div className="checkbox checkbox-success cursor-pointer ">
                            <input
                                id={"applicable-checkbox" + row.id}
                                type="checkbox"
                                disabled={!row.is_editable || is_control_disabled[0]}
                                defaultChecked={row.applicable}
                                onChange={(e) => handleApplicableChange(e, row)}/>
                            <label htmlFor={"applicable-checkbox" + row.id}></label>
                        </div>
                    </Fragment>
                );
            },
        },
        {accessor: 'controlId', label: 'Control ID', priorityLevel: 1, position: 2, minWidth: 150, sortable: false,},
        {
            accessor: 'name', label: 'Name', priorityLevel: 1, position: 3, minWidth: 150, sortable: false,
            CustomComponent: ({row}) => {
                return (
                    <Fragment>
                            <Link className={!row.applicable?'d-none':'control-name-column'} id={`control_link_name_${row.id}`} href={route('compliance-project-control-show', [row.project_id, row.id, 'tasks'])} >{decodeHTMLEntity(row.name)}</Link>
                            <span className={row.applicable?'d-none':'control-name-column'} id={`control_name_${row.id}`}>{decodeHTMLEntity(row.name)}</span>
                    </Fragment>
                );
            },
        },

        { accessor: 'description', label: 'Control Description', priorityLevel: 2, position: 3, minWidth: 400,sortable:false, CustomComponent: ({row}) => <span>{decodeHTMLEntity(row.description)}</span>},
        { accessor: 'status', label: 'Status', priorityLevel: 1, position: 4, minWidth: 100,sortable:false,
        CustomComponent: ({ row }) => {
                    var class_name='';
                    var row_status =row.status;

                    if(!row.applicable){
                        class_name = 'badge task-status-purple w-60';
                        row_status = 'Not Applicable';
                    }else if(row.status==='Not Implemented'){
                        class_name='badge task-status-red w-60';
                    }
                    else if(row.status==='Implemented'){
                        class_name='badge task-status-green w-60';
                    }
                    else if(row.status==='Rejected'){
                        class_name='badge task-status-orange w-60';
                    }
                    else{
                        class_name='badge task-status-blue w-60';
                    }

                    return (
                        <Fragment>
                            <span id={"task-status"+row.id} className={class_name}>{row_status}</span>
                        </Fragment>
                    );
            },
        },
        {
            accessor: 'responsible',
            label: 'Responsible',
            priorityLevel: 1,
            position: 4,
            minWidth: 200,
            sortable: false,
            CustomComponent: ({row}) => {
                return (
                    <Fragment>
                        <p className="tootip bg-danger row-input-error" id={"responsible_" + row.id}
                           style={{display: 'none'}}>Responsible &amp; Approver can't be same</p>
                        <Select
                            className={`react-select ${(!row.is_editable || is_control_disabled[0] || !row.applicable) ? 'd-none' : ''}`}
                            id={`responsible-select-${row.id}`}
                            classNamePrefix="react-select"
                            onChange={(e) => handleResponsibleChange(e, row)}
                            defaultValue={getSelectDefalultValue(row.responsible)}
                            options={controlsAdmins}
                            isClearable={true}
                            isSearchable={true}
                            name="responsible"
                        />
                        <Select
                            className={`react-select cursor-pointer ${(!row.is_editable || is_control_disabled[0] || !row.applicable) ? '' : 'd-none'}`}
                            id={`responsible-select-disabled-${row.id}`}
                            classNamePrefix="react-select"
                            onChange={(e) => handleResponsibleChange(e, row)}
                            defaultValue={getSelectDefalultValue(row.responsible)}
                            isDisabled={true}
                            options={controlsAdmins}
                            isClearable
                            isSearchable={true}
                            name="responsible"
                        />
                    </Fragment>
                );
            },
        },
        {
            accessor: 'approver', label: 'Approver', priorityLevel: 1, position: 5, minWidth: 200, sortable: false,
            CustomComponent: ({row}) => {
                return (
                    <Fragment>
                        {/* {validationMessage.responsible[row[0].applicableValue] != null && <div className="bg-danger row-input-error mb-2">{validationMessage.responsible[row[0].applicableValue]}</div>} */}
                        <p className="tootip bg-danger row-input-error" id={"approver_" + row.id}
                           style={{display: 'none'}}>Responsible &amp; Approver can't be same</p>
                        <Select
                            className={`react-select ${(!row.is_editable || is_control_disabled[0] || !row.applicable) ? 'd-none' : ''}`}
                            id={`approver-select-${row.id}`}
                            classNamePrefix="react-select"
                            onChange={(e) => handleApproverChange(e, row)}
                            defaultValue={getSelectDefalultValue(row.approver)}
                            options={controlsAdmins}
                            isClearable={true}
                            isSearchable={true}
                            name="approver"
                        />
                        <Select
                            className={`react-select ${(!row.is_editable || is_control_disabled[0] || !row.applicable) ? '' : 'd-none'}`}
                            id={`approver-select-disabled-${row.id}`}
                            classNamePrefix="react-select"
                            onChange={(e) => handleApproverChange(e, row)}
                            defaultValue={getSelectDefalultValue(row.approver)}
                            isDisabled={true}
                            options={controlsAdmins}
                            isClearable={true}
                            isSearchable={true}
                            name="approver"
                        />
                    </Fragment>
                );
            },
        },
        {
            accessor: 'deadline', label: 'Deadline', priorityLevel: 1, position: 6, minWidth: 200, sortable: false,
            CustomComponent: ({row}) => {
                const options = {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    altFormat: 'd-m-Y',
                    altInput: true
                    // minDate: "today"
                };
                return (
                    <Fragment>
                        <div className="input-group">
                            <p className="tootip bg-danger row-input-error" id={"deadline_" + row.id}
                            style={{display: 'none',marginTop:'-8%'}}>The date cannot be a past date</p>
                            <Flatpickr
                                className={`form-control flatpickr-date deadline-picker ${(!row.is_editable || is_control_disabled[0] || !row.applicable) ? 'd-none' : ''}`}
                                options={options}
                                id={'deadline_enabled_' + row.id}
                                defaultValue={row.deadline ? row.deadline : "today"}
                                onChange={([deadline]) => {
                                    handleDateChange(deadline, row);
                                }}
                            />
                            <Flatpickr
                                className={`form-control flatpickr-date deadline-picker ${(!row.is_editable || is_control_disabled[0] || !row.applicable) ? '' : 'd-none'}`}
                                options={options}
                                id={'deadline_disabled_' + row.id}
                                disabled={true}
                                defaultValue={row.deadline ? row.deadline : "today"}
                                onChange={([deadline]) => {
                                    handleDateChange(deadline, row);
                                }}
                            />
                            <div className="border-start-0">
                                <span className="input-group-text bg-none"><i className="mdi mdi-calendar-outline"></i></span>
                            </div>
                        </div>
                    </Fragment>
                );
            },
        },
        {
            accessor: 'frequency', label: 'Frequency', priorityLevel: 1, position: 7, minWidth: 100, sortable: false,
            CustomComponent: ({row}) => {
                return (
                    <Fragment>
                        <Select
                            className={`react-select ${(!row.is_editable || is_control_disabled[0] || !row.applicable) ? 'd-none' : ''}`}
                            id={`frequency-select-${row.id}`}
                            classNamePrefix="react-select"
                            onChange={(e) => handleFrequencyChange(e, row)}
                            defaultValue={getFrequencySelectDefalultValue(row.frequency) ? getFrequencySelectDefalultValue(row.frequency) : {
                                value: 'One-Time',
                                label: 'One-Time'
                            }}
                            options={frequencySelectOptions}
                        />
                        <Select
                            className={`react-select ${(!row.is_editable || is_control_disabled[0] || !row.applicable) ? '' : 'd-none'}`}
                            id={`frequency-select-disabled-${row.id}`}
                            classNamePrefix="react-select"
                            onChange={(e) => handleFrequencyChange(e, row)}
                            defaultValue={getFrequencySelectDefalultValue(row.frequency) ? getFrequencySelectDefalultValue(row.frequency) : {
                                value: 'One-Time',
                                label: 'One-Time'
                            }}
                            options={frequencySelectOptions}
                            isDisabled={true}
                        />
                    </Fragment>
                );
            },
        },
        {
            accessor: 'id_separator', label: '', priorityLevel: 2, position: 8, minWidth: 150, sortable: false,
            CustomComponent: ({row}) => {
                return (
                    <Fragment>
                    <Link id={`control_detail_btn_${row.id}`} href={route('compliance-project-control-show', [row.project_id, row.id])}
                                  className={`btn btn-sm btn-primary ${!row.applicable?'d-none':''}`}>Details</Link>
                    </Fragment>
                );
            },
        },
    ];

    const pieData = {
        series: [props.data.implemented, props.data.underReview, props.data.notImplemented],
        options: {
            chart: {
                type: 'donut',
            },
            tooltip: {
                enabled: true,
                fillSeriesColor: false,
                theme:false,
                style:{
                    fontSize:'15px'
                }
            },
            colors: ["#359f1d", "#5bc0de", "#cf1110"],
            labels: ["Implemented", "Under Review", "Not Implemented"],            
            legend: {
                show: true,
                position: 'bottom',
                formatter: function(seriesName, opts) {
                    return [seriesName, " - ", opts.w.globals.series[opts.seriesIndex]]
                }
            },
            dataLabels: {
                enabled: false,
            },
            states: {
                active: {
                    filter: {
                        type: 'none',
                    }
                }
            },
            plotOptions: {
                pie: {
                    expandOnClick: true,
                    donut: {
                        size: "90%",
                        background: "transparent",
                        labels: {
                            show: true,
                            name: {
                                show: false,
                                fontSize: "25px",
                                color: "black",
                            },
                            value:{
                                fontSize: "35px",
                                color: "#6e6b7b",
                            },
                            total: {
                                show: true,  
                                showAlways:true,
                            },
                        },
                    }
                }
            },
        },
    }

    const handleExportProject = () => {
        dispatch({type: "reportGenerateLoader/show"});
        axiosFetch.get(route('compliance.projects.export',props.project.id), {
            responseType: 'blob',
            params: {
                data_scope: appDataScope
            }
        })
        .then((res) => {
            fileDownload(res.data, `Compliance Project ${moment().format('DD-MM-YYYY')}.xlsx`);
        })
        .finally(() => {
            dispatch({type: "reportGenerateLoader/hide"});
        })
    }

    return (
        <AppLayout>
            <ContentLoader show={false}>
                <div id="compliance-project-details-page">
                    {/* breadcrumbs */}
                    <Breadcrumb data={breadcumbsData}></Breadcrumb>
                    <FlashMessages/>
                    {/* end of breadcrumbs */}
                    <div className="row card" id="projects-details">
                        <div className="col-lg-12 card-body" id="project-details-tab-show">
                        <button onClick={handleExportProject} className="btn btn-primary export__risk-btn float-end"
                                >Export</button>
                            <Tabs defaultActiveKey="Details" className="mb-3">
                                <Tab eventKey="Details" title="Details">
                                    <h5 className="mt-0">
                                        {props.project.name} ( Standard: {props.project.standard} )
                                    </h5>
                                    <p className="mb-0">{props.project.description}</p>
                                </Tab>
                                <Tab eventKey="Controls" title="Controls">
                                    {
                                        !is_control_disabled[0] &&
                                        <div className="save-button d-flex justify-content-end mb-3">
                                            <button className="btn btn-primary custom-save-button"
                                                    onClick={() => saveAndProceed()}>
                                                Save
                                                <span className='custom-save-spinner'>
                                    <img className='custom-spinner-image' style={{display: 'none'}} height="25px"></img>
                                </span>
                                            </button>
                                        </div>
                                    }

                                    <DataTable
                                        columns={columns}
                                        fetchURL={fetchUrl}
                                        search
                                        hideHeader={false}
                                        refreshOnPageChange
                                    />
                                    {
                                        !is_control_disabled[0] &&
                                        <div className="save-button d-flex justify-content-end mt-3">
                                            <button className="btn btn-primary custom-save-button"
                                                    onClick={() => saveAndProceed()}>
                                                Save
                                                <span className='custom-save-spinner'>
                                    <img className='custom-spinner-image' style={{display: 'none'}} height="25px"></img>
                                </span>
                                            </button>
                                        </div>
                                    }
                                </Tab>
                            </Tabs>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-lg-12">
                            <h5 className="page-title mb-3 mt-4 fw-bold">Overview</h5>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-lg-6">
                            <div className="card h-100">
                                <div className="card-body">
                                    <h4 className="header-title">Control Status</h4>
                                    <hr/>
                                    <table id="control-status-table" className="table no-bordered">
                                        <tbody>
                                        <tr>
                                            <td><i data-feather="box" className="text-muted me-2" />Total Controls:</td>
                                            <td className='text-dark'><strong>{props.data.total}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><i data-feather="delete" className="text-muted me-2" />Not Applicable:</td>
                                            <td className='text-dark'><strong>{props.data.notApplicable}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><i data-feather="flag" className="text-muted me-2" />Implemented Controls:</td>
                                            <td className='text-dark'><strong>{props.data.implemented}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><i data-feather="star" className="text-muted me-2" />Under Review:</td>
                                            <td className='text-dark'><strong>{props.data.underReview}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><i data-feather="x-square" className="text-muted me-2" />Not Implemented Controls:</td>
                                            <td className='text-dark'><strong>{props.data.notImplementedcontrols}</strong></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="card h-100">
                                <div className="card-body">
                                    <h4 className="header-title">Implementation Progress</h4>
                                    <hr/>
                                    <div id="chart" className="offset-2 cursor-pointer"
                                         style={{height: "300px", width: "400px"}}>
                                        <Chart 
                                            options={pieData.options} 
                                            series={pieData.series} 
                                            type="donut"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </ContentLoader>
        </AppLayout>
    );
}

export default ProjectDetails;