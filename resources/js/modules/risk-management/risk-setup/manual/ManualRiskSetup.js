import React,{Fragment,useCallback,useEffect,useState} from 'react';
import { useSelector } from 'react-redux';
import BreadcumbsComponent from '../../../../common/breadcumb/Breadcumb'
import './style.scss';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import Spinner from 'react-bootstrap/Spinner';
import {useDropzone} from 'react-dropzone';
import AppLayout from '../../../../layouts/app-layout/AppLayout';
import { useForm } from '@inertiajs/inertia-react';
import FlashMessages from "../../../../common/FlashMessages";
import fileDownload from 'js-file-download';

function ManualRiskSetup(props) {
    const [isUpload,setIsUpload] = useState({});
    const [errorMsg,setErrorMsg] = useState({});
    const [enableUpload,setEnableUpload] = useState({});
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);

    useEffect(async () => {
        document.title = "Manual Import";
        setIsUpload(0);
        setEnableUpload(0);
    }, [appDataScope]);

    const { data, setData, post, progress } = useForm({
        csv_upload: null,
        data_scope: appDataScope,
    });

    const onDrop = useCallback(acceptedFiles => {
        setIsUpload(1);
        var errText = "";
        if(acceptedFiles[0].size <= 10){
            errText = "Error: File size error";
        }else if(acceptedFiles[0].type != "text/csv" && acceptedFiles[0].type != "application/vnd.ms-excel"){
            errText = "Error: Unsupported file type";
        }else{
            setData('csv_upload',acceptedFiles[0]);
            setEnableUpload(1);
        }
        setErrorMsg({error:errText});
      }, []);

    const postData = (e) => {
        //Post using inertia
        post(route('risks.manual.risks-import'));
        setIsUpload(0);
        setEnableUpload(0);
    }

    const {acceptedFiles, getRootProps, getInputProps} = useDropzone({
        accept: 'text/csv,.csv,application/vnd.ms-excel',
        maxFiles:1,
        onDrop
    });

    const files = acceptedFiles.map(file => (
        <div key={file.path}>{file.path} - {file.size} bytes </div>
    ));

    async function downloadSample(url) {
        try {
            let response = await axiosFetch({
                url: route("risks.manual.download-sample"),
                method: 'GET',
                responseType: 'blob', // Important
            })

            fileDownload(response.data, 'risk-setup.csv');
        } catch (error) {
            console.log(error);
        }
    }

    const breadcumbsData = {
            "title":"Manual Import",
            "breadcumbs": [
                {
                    "title": "Risk Management",
                    "href": `${appBaseURL}/risks/dashboard`
                },
                {
                    "title":"Risk Setup",
                    "href":"/risks/setup"
                },
                {
                    "title":"Manual Import",
                    "href":""
                },
            ]
        };

    return (
        <AppLayout>
            <Fragment>
            <BreadcumbsComponent data={breadcumbsData} />
            <div className="row" id="manual-risk-setup">
                <div className="col-xl-12">
                    <div className='card'>
                        <div className="card-body project-box">
                            <div className="manual-import">
                            <Row>
                            <Col lg={6}>
                            <h4>Upload a CSV file to create new risk</h4><br/>
                                { errorMsg.error ? <div className="alert alert-danger alert-block"><button type="button" className="btn-close" data-dismiss="alert" onClick={()=>{errorMsg.error="";}}>×</button><strong>{errorMsg.error}</strong>
                                                </div>:"" }
                                <FlashMessages />
                                <section className="container px-0 pb-2">
                                <div {...getRootProps({className: 'dropzone upload_csv_section'})}>
                                <input {...getInputProps()} />
                                {
                                    isUpload == 1 ?
                                    <div><span className="fe-file icon-custom-size"></span>
                                    {files}</div>
                                : <div><span className="fe-upload-cloud icon-custom-size"></span><div className="dropify-message">Drag and drop a file here or click</div></div>
                                }
                                </div>
                                </section>
                                <div className="csv-buttons py-2">
                                { enableUpload == 0 ?
                                <button className="upload__btn btn btn-primary me-1">Upload Risks</button>
                                :<button onClick={postData} className="upload__btn btn btn-primary me-1">Upload Risks</button> }
                                <a onClick={() => downloadSample()} style={{ color:'white', cursor: 'pointer' }} className="sample__btn btn btn-primary">Download Sample</a>
                                </div>
                            </Col>
                            <Col lg={6}>
                            <div className="csv__contents-box">
                            <Row>
                                <Col><h5 className="text-uppercase text-white">the csv file should have the following:</h5></Col>
                                {props.risksAffectedProperties?"":<Col><Spinner className="float-end" animation="border" variant="light" size="sm" /></Col>}
                            </Row>
                            {props.risksAffectedProperties?
                                <ul>
                                <li>name (required): 191 character limit</li>
                                <li>risk_description (required)</li>
                                <li>affected_properties (required) :
                                    { props.risksAffectedProperties.common.length > 0 &&
                                            <div><b>Common </b>( {props.risksAffectedProperties.common.map(function(value,index){ return props.risksAffectedProperties.common.length-1 == index?value:value+", " })} )</div>
                                    }
                                    { props.risksAffectedProperties.Other.length > 0 &&
                                            <div><b>Other </b>( {props.risksAffectedProperties.Other.map(function(value,index){ return props.risksAffectedProperties.Other.length-1 == index?value:value+", " })} )</div>
                                    }
                                </li>
                                <li>affected_functions_or_assets (required) : 191 character limit</li>
                                <li>treatment (required) </li>
                                <li>category (required) : {
                                        props.riskCategories.map(function(value,index){
                                            return props.riskCategories.length-1 == index ? value.name+" => "+value.order_number:value.name+" => "+value.order_number+", "
                                        })
                                }
                                </li>
                                <li>treatment_options: Mitigate, Accept</li>
                                <li>
                                likelihood (optional) : {
                                        props.riskLikelihoods.map(function(value,index){
                                            return props.riskLikelihoods.length-1 == index ? value.name+" => "+value.id :value.name+" => "+value.id+", ";
                                        })
                                }
                                </li>
                                <li>
                                impact (optional) : {
                                        props.riskImpacts.map(function(value,index){
                                            return props.riskImpacts.length-1 == index ? value.name+" => "+value.id :value.name+" => "+value.id+", ";
                                        })
                                }
                                </li>
                                </ul>
                                :''
                            }
                            </div>
                            </Col>
                            </Row>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Fragment>
        </AppLayout>
    );
}

export default ManualRiskSetup;