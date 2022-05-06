import React, {useCallback, useEffect, useState} from "react";
import {usePage} from "@inertiajs/inertia-react";
import {useForm} from "@inertiajs/inertia-react";
import Select from "react-select";
import LoadingButton from "../../common/loading-button/LoadingButton";
import {useDropzone} from "react-dropzone";
import fileDownload from "js-file-download";
import FlashMessages from "../../common/FlashMessages";
import "./controls.scss";
import Alert from "react-bootstrap/Alert";

export default function ControlCreateUploadForm(props) {
    const [errorMsg, setErrorMsg] = useState({});
    const [isUpload, setIsUpload] = useState(false);
    const [enableUpload, setEnableUpload] = useState(0);
    const [show, setShow] = useState(false);
    const [showCsvError, setShowCsvError] = useState(false);
    const propsData = usePage().props;
    let csvUploadErrors = propsData.flash.csv_upload_error
        ? propsData.flash.csv_upload_error
        : null;
    const standardControl = propsData.control;
    const standard = propsData.standard;
    const idSeparators = propsData.idSeparators;

    const {setData, post, processing} = useForm({
        csv_upload: null,
        id_separator: ".",
    });

    useEffect(() => {
        setShow(true);
    }, [errorMsg]);

    useEffect(() => {
        if (propsData.errors.csv_upload) {
            setShowCsvError(true);
        }
    },[propsData.errors]);

    async function downloadSample() {
        try {
            let response = await axiosFetch({
                url: route(
                    "compliance-template-download-template-controls",
                    standard.id
                ),
                method: "GET",
                responseType: "blob", // Important
            });

            fileDownload(response.data, "control.csv");
        } catch (error) {
            console.log(error);
        }
    }

    const onDrop = useCallback((acceptedFiles) => {
        var errText = "";
        if (acceptedFiles.length) {
            if (acceptedFiles[0].size <= 10) {
                errText = "Error: File size error";
            } else if (acceptedFiles[0].type != "text/csv" && acceptedFiles[0].type != "application/vnd.ms-excel") {
                errText = "Error: Unsupported file type";
            } else {
                setData("csv_upload", acceptedFiles[0]);
                setEnableUpload(1);
                setIsUpload(1);
            }
        } else {
            errText = "Error: Unsupported file type";
        }
        setErrorMsg({error: errText});

    }, []);

    const {acceptedFiles, getRootProps, getInputProps} = useDropzone({
        accept: 'text/csv,.csv,application/vnd.ms-excel',
        maxFiles: 1,
        onDrop,
    });

    const files = acceptedFiles.map((file) => (
        <div key={file.path}>
            {file.path} - {file.size} bytes{" "}
        </div>
    ));

    const onUploadSubmit = () => {
        event.preventDefault();
        post(
            route("compliance-template-upload-csv-store-controls", standard.id)
        );
        setIsUpload(0);
        setEnableUpload(0);
    };

    return (
        <div className={standardControl.id ? "d-none" : "col-xl-6"}>

            <form onSubmit={onUploadSubmit}>
                <div className="table-right">
                    <h4>Upload Control CSV</h4>
                    <h5 className="mb-3">
                        {" "}
                        Upload a CSV file to create new controls
                    </h5>

                    {csvUploadErrors && <FlashMessages/>}
                    {errorMsg.error && (
                        <Alert variant="danger" show={show} onClose={() => {
                            setShow(false);
                        }} dismissible>
                            <strong>{errorMsg.error}</strong>
                        </Alert>
                    )}

                    {propsData.errors.csv_upload && (
                        <Alert variant="danger" show={showCsvError} onClose={() => {
                            setShowCsvError(false);
                        }} dismissible>
                            <strong>{propsData.errors.csv_upload}</strong>
                        </Alert>
                    )}

                    <section className="container">
                        <div
                            {...getRootProps({
                                className: "dropzone upload_csv_section",
                            })}
                        >
                            <input {...getInputProps()} />
                            {isUpload === 1 ? (
                                <div>
                                    <span className="fe-file icon-custom-size"></span>
                                    {files}
                                </div>
                            ) : (
                                <div>
                                    <span className="fe-upload-cloud icon-custom-size"></span>
                                    <div className="dropify-message">
                                        Drag and drop a file here or click
                                    </div>
                                </div>
                            )}
                        </div>
                    </section>

                    <div className="mb-3 pt-2">
                        <label htmlFor="" className="form-label">
                            {" "}
                            ID Separator{" "}
                        </label>
                        <Select
                            onChange={(val) =>
                                setData("id_separator", val.value)
                            }
                            options={idSeparators}
                            className="react-select"
                            classNamePrefix="react-select"
                            defaultValue={{
                                label: idSeparators[0].label,
                                value: idSeparators[0].value,
                            }}
                        />
                        <div className="invalid-feedback d-block">
                            {/* {
                                errors.id_separator && errors.id_separator.type === "required" && (
                                    <div className="invalid-feedback d-block">The ID Separator field is required</div>
                                )
                            } */}
                        </div>
                    </div>

                    <LoadingButton
                        className="upload__btn btn btn-primary me-2"
                        type="submit"
                        loading={processing}
                        disabled={!enableUpload}
                    >
                        Upload Controls
                    </LoadingButton>

                    <button
                        type="button"
                        onClick={() => downloadSample()}
                        className="btn sample__dwn-btn btn-primary"
                    >
                        {" "}
                        Download Sample
                    </button>

                    <div className="cv-info">
                        <h5 className="text-uppercase text-white">
                            the csv file should have the following header line:{" "}
                        </h5>
                        <p>primary_id, sub_id, name, description</p>
                        <p>Field size limits for the CSV are: </p>
                        <ul>
                            <li>primary_id: 20 character limit</li>
                            <li>sub_id: 20 character limit</li>
                            <li>name: 255 character limit</li>
                            <li>description: 50,000 character limit</li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    );
}
