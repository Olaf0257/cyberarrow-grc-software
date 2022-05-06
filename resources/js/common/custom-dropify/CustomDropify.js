import React, {useState, useCallback, useEffect} from 'react';
import {useDropzone} from "react-dropzone";
import PropTypes from 'prop-types';

import './styles/style.css';

const FileIcon = ({extension}) => {
    return (
        <div className="position-relative">
            <svg xmlns="http://www.w3.org/2000/svg" width="76" height="76" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" strokeWidth="1" strokeLinecap="round" strokeLinejoin="round"
                 className="feather feather-file">
                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>
                <polyline points="13 2 13 9 20 9"/>
            </svg>
            <span className="position-absolute file-ext">{extension}</span>
        </div>
    )
}

const UploadIcon = () => {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
             stroke="#cfcfcf" strokeWidth="1" strokeLinecap="round" strokeLinejoin="round"
             className="feather feather-upload-cloud">
            <polyline points="16 16 12 12 8 16"/>
            <line x1="12" y1="12" x2="12" y2="21"/>
            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
            <polyline points="16 16 12 12 8 16"/>
        </svg>
    );
}

const CustomDropify = (props) => {
    const {maxSize, accept, file, onSelect, defaultPreview} = props;

    const [preview, setPreview] = useState(null);
    const [fileObj, setFileObj] = useState(null);

    const handleDrop = useCallback((acceptedFiles) => {
        const file = acceptedFiles[0];
        onSelect(file);
    }, []);

    const {getInputProps, getRootProps} = useDropzone({
        onDrop: handleDrop,
        maxSize,
        maxFiles: 1,
        accept
    });

    const setDefaultPreview = () => {
        let filename='';
        // for aws images
        if(defaultPreview.includes('X-Amz-Signature')){
            filename = defaultPreview.split('/').reverse()[0];
            filename=filename.split('?')[0];
        }
        else{
            filename = defaultPreview.split('/').reverse()[0];
        }
        const extension = filename.split('.').reverse()[0];
        const isImage = ['jpg', 'gif', 'jpeg', 'ico', 'webp', 'png'].includes(extension);
        setFileObj({
            filename,
            extension,
            hasPreview: isImage
        });
        isImage ? setPreview(defaultPreview) : setPreview(null);
    };

    const handleRemove = e => {
        e.stopPropagation();
        onSelect(null);
    }

    useEffect(() => {
        if (file) {
            const isImage = file.type.includes('image');
            setFileObj({
                filename: file.name,
                extension: file.name.split('.').reverse()[0],
                hasPreview: isImage
            })
            isImage ? setPreview(URL.createObjectURL(file)) : setPreview(null);
            return;
        }

        setFileObj(null);
        setPreview(null);

        if (!file && defaultPreview) setDefaultPreview();
    }, [file]);

    return (
        <div className="custom_dropper_wrapper" {...getRootProps()}>
            <input {...getInputProps()} />
            <div className="custom_dropper_body">
                {fileObj ? (
                    <>
                        {fileObj.hasPreview ?
                            <img src={preview} alt={fileObj.filename} className="custom_dropper_preview"/> : (
                                <h4>
                                    <FileIcon extension={fileObj.extension}/>
                                </h4>
                            )}
                    </>
                ) : (
                    <>
                        <h4>
                            <UploadIcon/>
                        </h4>
                        <p>Drag and drop a file here or click</p>
                    </>
                )}
            </div>

            {fileObj ? (
                <div className="custom_dropper_fader">
                    <h4>{fileObj.filename}</h4>
                    <p>Drag and drop a file here or click</p>
                    {file && <button className="custom_dropper_remove_btn" onClick={handleRemove}>Remove</button>}
                </div>
            ) : null}

        </div>
    )
}

CustomDropify.propTypes = {
    maxSize: PropTypes.number,
    accept: PropTypes.string,
    onSelect: PropTypes.func.isRequired
}

export default CustomDropify;
