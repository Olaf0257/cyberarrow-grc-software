import React from 'react';

import {usePage} from '@inertiajs/inertia-react';
import Alert from "react-bootstrap/Alert";

const DismissibleAlert = ({variant, message, show, onClose}) => {
    return (
        <Alert variant={variant} show={show} onClose={onClose} dismissible>
            <strong>{message}</strong>
        </Alert>
    )
};

const FlashMessages = () => {
    const {flash,errors} = usePage().props;

    const [show, setShow] = React.useState(false);
    React.useEffect(() => {
        setShow(true);
    }, [flash, errors]);

    if(flash.success) return <DismissibleAlert show={show} onClose={() => setShow(false)} variant="success" message={flash.success}  />
    if(flash.error) return <DismissibleAlert show={show} onClose={() => setShow(false)} variant="danger" message={flash.error} />
    if(flash.warning) return <DismissibleAlert show={show} onClose={() => setShow(false)} variant="warning" message={flash.warning} />
    if(flash.info) return <DismissibleAlert show={show} onClose={() => setShow(false)} variant="info" message={flash.info} />
    if(flash.csv_upload_error) return <DismissibleAlert show={show} onClose={() => setShow(false)} variant="danger" message={flash.csv_upload_error} />
    if(flash.exception) return <DismissibleAlert show={show} onClose={() => setShow(false)} variant="danger" message={flash.exception} />

    if(Object.keys(errors).length >0) return <DismissibleAlert show={show} onClose={() => setShow(false)} variant="danger" message="Please check the form for errors" />

    return <></>;
};

export default FlashMessages;
