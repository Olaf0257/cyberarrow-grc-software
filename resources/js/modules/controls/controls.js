import React, { Fragment,useEffect,useState,useRef } from 'react';
import BreadcumbsComponent from '../../common/breadcumb/Breadcumb';
import './controls.scss';
import Select from 'react-select';
import Spinner from 'react-bootstrap/Spinner';
import DataTable from '../../common/data-table/DataTable';
import Dropdown from 'react-bootstrap/Dropdown';
import { Link } from "@inertiajs/inertia-react";
import AppLayout from '../../layouts/app-layout/AppLayout';
import {useSelector} from "react-redux";
import {Modal} from "react-bootstrap";
import fileDownload from "js-file-download";

var defaultProductsData = [];
defaultProductsData.push({ value: 0, label: "Select Project" });

function Controls(props) {
    const [projects, setProjects] = useState({});
    const [controls, setControls] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [textEvedenceHeading, setTextEvedenceHeading] = useState('');
    const [textEvedenceBody, setTextEvedenceBody] = useState('');
    const [standardId, setStandardId] = useState('');
    const [projectId, setProjectId] = useState('');
    const [userId, setUserId] = useState('');
    const [controlIdValue, setControlID] = useState('');
    // const [taskContributors, setTaskContributor] = useState({});
    const projectSelectRef = useRef();
    const userSelectRef = useRef();

    const [ajaxData, setAjaxData] = useState([]);
    const [allStandards, setAllStandards] = useState(null);

    useEffect(() => {
        document.title = "Implemented Controls";
        setProjects(defaultProductsData);
    }, []);

    const appDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope.value
    );
    const fetchURL = "compliance/implemented-controls/data";

    const handleStandardChange = (e) => {
        setStandardId(e.value);
        getProjects(e.value);
        projectSelectRef.current.clearValue();
    };

    const handleProjectChange = (e) => {
        if (e != null) {
            setProjectId(e.value);
        }
    };

    const handleUserChange = (e) => {
        if (e != null) {
            setUserId(e.value);
        }
    };

    const searchData = () => {
        const data = {
            control_name: controls ? controls : "",
            controlID: controlIdValue ? controlIdValue : "",
            responsible_user: userId ? userId : "",
            standard_id: standardId ? standardId : "",
            project_id: projectId ? projectId : "",
        };
        setAjaxData(data);
    };

    const getProjects = (id) => {
        try {
            axiosFetch
                .get("compliance/tasks/get-projects-by-standards", {
                    params: {
                        standardId: id,
                        data_scope: appDataScope,
                    },
                })
                .then((res) => {
                    const response = res.data;
                    defaultProductsData = [];
                    defaultProductsData.push({
                        value: 0,
                        label: "Select Project",
                    });
                    response.map(function (each, index) {
                        defaultProductsData.push({
                            value: each.id,
                            label: each.name,
                        });
                    });
                    setProjects(defaultProductsData);
                });
        } catch (error) {
            console.log("Response error");
        }
    };

    const handleControlId = (e) => {
        setControlID(e.target.value);
    };

    const handleControlName = (e) => {
        setControls(e.target.value);
    };

    const resetValues = () => {
        setProjectId("");
        setStandardId("");
        setControlID("");
        setControls("");
        setUserId("");
        if (userSelectRef.current !== undefined) {
            userSelectRef.current.clearValue();
        }
    };

    const showTextEvidenceModal = (row) =>{
        setTextEvedenceHeading(row.name);
        setTextEvedenceBody(row.text_evidence);
        setShowModal(true);
    }

    const downloadEvidence = async (url) => {
        try {
            let { data, headers } = await axiosFetch.get(url,
                {
                    responseType: "blob", // Important
                });
            let disposition = headers["content-disposition"];
            if (disposition && disposition.indexOf('attachment') !== -1) {
                if(disposition.includes('UTF')){ // if it's a zip file
                    // var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    var filenameRegex = /filename\*?=([^']*'')?([^;]*)/;
                    var matches = filenameRegex.exec(disposition);
                    // console.log('MATCHES', matches);
                    if (matches != null && matches[2]) {
                        let fileName = matches[2].replace(/['"]/g, '');
                        fileDownload(data, fileName);
                    }
                } 
                else { // if it's a normal file
                    let fileNameStr = disposition.split("filename=")[1];
                    let fileName = fileNameStr.substring(1, fileNameStr.length - 1);
                    fileDownload(data, fileName);
                }
            } else {
                AlertBox({
                    text: 'File Not Found',
                    confirmButtonColor: '#f1556c',
                    icon: 'error',
                });
            }
        } catch (error) {
            console.log(error);
        }
    };

    useEffect(() => {
        // reset the values
        resetValues();
        getControlsData();
        setAjaxData({});
    }, [appDataScope]);

    const columns = [
        { accessor: '0', label: 'Standard', priorityLevel: 1, position: 1, minWidth: 150,sortable:false },
        { accessor: '1', label: 'Project', priorityLevel: 1, position: 2, minWidth: 150,sortable:false },
        { accessor: '2', label: 'Control ID', priorityLevel: 1, position: 3, minWidth: 50, sortable:false},
        { accessor: '3', label: 'Control Name', priorityLevel: 1, position: 4, minWidth: 150, sortable:false,
            CustomComponent: ({ row }) => {
                if(row[7]){
                    return (
                        <Fragment>
                            <Link href={row[3].url}>
                                {decodeHTMLEntity(row[3].name)}
                            </Link>
                        </Fragment>
                    )
                }
            }
        },
        { accessor: '4', label: 'Control Description', priorityLevel: 2, position: 5, minWidth: 150, sortable:false, CustomComponent: ({row}) => <span>{decodeHTMLEntity(row[4])}</span>},
        { accessor: '5', label: 'Last Uploaded', priorityLevel: 2, position: 6, minWidth: 150, sortable:false},
        { accessor: '6', label: 'Responsible', priorityLevel: 2, position: 6, minWidth: 50,sortable:false },
        { accessor: '7', label: 'Action', priorityLevel: 2, position: 6, minWidth: 50,sortable:false, 
            CustomComponent: ({ row }) => {
                if(row[7]){
                    return (
                    <Fragment>
                        {row[7][0].map((textevidence, index) => {
                            return (
                                <a key={index} className='btn btn-secondary btn-xs waves-effect waves-light' title='View Text' style={{ marginRight: '2px' }} href={undefined} onClick={(e) => { e.preventDefault(); showTextEvidenceModal(textevidence); }}>
                                    <i className='fe-type' style={{ fontSize: '12px', color: 'white' }}></i>
                                </a>
                            );
                        })}
                        {row[7][1].map((linkevidence, index) => {
                            return (
                                <a key={index} className='btn btn-secondary btn-xs waves-effect waves-light' target="__blank" title='Go To Link' style={{ marginRight: '2px' }} href={linkevidence.path} >
                                    <i className='fe-link' style={{ fontSize: '12px', color: 'white' }}></i>
                                </a>
                            );
                        })}
                        {row[7][2].map((controlEvidence, index) => {
                            return (
                                <a key={index} className='btn btn-secondary btn-xs waves-effect waves-light' target="__blank" title='View Control' style={{ marginRight: '2px' }} href={controlEvidence} >
                                    <i className='fe-eye' style={{ fontSize: '12px', color: 'white' }}></i>
                                </a>
                            );
                        })}
                        {row[7][3].map((documentEvidence, index) => {
                            return (
                                <a key={index} className='btn btn-secondary btn-xs waves-effect waves-light' title='Download Document' style={{ marginRight: '2px' }} onClick={() => downloadEvidence(documentEvidence)} >
                                    <i className='fe-download' style={{ fontSize: '12px', color: 'white' }}></i>
                                </a>
                            );
                        })}
                    </Fragment>
                    );
                }
                else{
                    return '';
                }
            },
        },
    ]

    const breadcumbsData = {
        title: "Controls",
        breadcumbs: [
            {
                title: "Controls",
                href: "/compliance/implemented-controls",
            },
        ],
    };

    const getControlsData = () => {
        setAllStandards(null);
        setProjects([{ value: 0, label: "Select Project" }]);
        if (projectSelectRef.current !== undefined) {
            projectSelectRef.current.clearValue();
        }

        axiosFetch
            .get(route("compliance.implemented-controls-data"), {
                params: {
                    data_scope: appDataScope,
                },
            })
            .then((res) => {
                const { allContributors, managedStandards } = res.data;
                setAllStandards(managedStandards);
            });
    };

    return (
        <Fragment>
           <AppLayout>
            <div id="implemented_controls_page">
                <BreadcumbsComponent data={breadcumbsData} />
                    <div className="row">
                        <div className="col">
                            <div className="card">
                            <div className="card-body w-100">
                                <div className="col-12  top-control mb-2">
                                    <div className="filter-row d-flex flex-column flex-sm-row justify-content-between my-2 p-2 rounded">
                                        <div className="filter-row__wrap d-flex flex-wrap">
                                            <div className="all-standards m-1">
                                            {allStandards ? <Select className="react-select" classNamePrefix="react-select" defaultValue={allStandards[0]} options={allStandards} onChange={handleStandardChange}/>:<Spinner className="mt-2" animation="border" variant="dark" size="sm" /> }
                                            </div>
                                            <div className="all-standards m-1">
                                            {projects.length > 0 ? <Select className="react-select" classNamePrefix="react-select" ref={projectSelectRef} options={projects} isDisabled={projects.length == 1} onChange={handleProjectChange} /> : ""}
                                            </div>
                                        <div className="m-1 all-controlID"><input className="form-control filter-input" name="controlID" type="text" placeholder="Control ID" value={controlIdValue} onChange={handleControlId} /></div>
                                        <div className="m-1 all-controlName"><input className="form-control filter-input" name="control_name" type="text" placeholder="Control Name" value={controls} onChange={handleControlName} /></div>
                                        <div className="all-users m-1">
                                            {props.taskContributors ? <Select className="react-select" classNamePrefix="react-select"  ref={userSelectRef} options={props.taskContributors} onChange={handleUserChange} />:<Spinner className="mt-2" animation="border" variant="dark" size="sm" /> }
                                        </div>
                                        </div>
                                        <div className="m-1 text-center text-sm-auto">
                                        <button className="btn btn-primary btn-block" type="button" id="search" onClick={searchData}> Search </button>
                                        </div>
                                        </div>
                                        <div>
                                    </div>
                                </div>
                                <DataTable
                                        columns={columns}
                                        fetchURL={fetchURL}
                                        ajaxData={ajaxData}
                                    />
                            </div>
                            </div>
                        </div>
                </div>
                        <Modal show={showModal} onHide={()=>setShowModal(false)} size={'xl'} centered>
                            <Modal.Header className='px-3 pt-3 pb-0' closeButton>
                                <Modal.Title className='my-0'>{textEvedenceHeading}</Modal.Title>
                            </Modal.Header>
                            <Modal.Body className='p-3'>{textEvedenceBody}</Modal.Body>
                            <Modal.Footer className='px-3 pt-0 pb-3'>
                                <button className="btn btn-secondary text-white" onClick={()=>setShowModal(false)}>
                                    Close
                                </button>
                            </Modal.Footer>
                        </Modal>
            </div>
        </AppLayout>
        </Fragment>
    );
}

export default Controls;
