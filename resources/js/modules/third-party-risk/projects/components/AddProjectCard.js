import React, {useState} from 'react';

import AddProjectModal from "./AddProjectModal";

const AddProjectCard = ({hidden = false, reload}) => {
    const [show, setShow] = useState(false);

    const handleClose = () => setShow(false);
    const handleShow = () => setShow(true);

    if (hidden) return <></>;
    return (
        <>
            <AddProjectModal reload={reload} handleClose={handleClose} show={show}/>
            <div className="col-lg-4 col-sm-6">
                <div className="card project__box d-flex justify-content-center align-items-center" onClick={handleShow}>
                    <i style={{fontSize: '4rem', color: '#323b43'}} className="mdi mdi-plus"/>
                </div>
            </div>
        </>
    )
};

export default AddProjectCard;
