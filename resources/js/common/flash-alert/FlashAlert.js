import React, {Fragment, useEffect, useState} from 'react';
import Alert from 'react-bootstrap/Alert'

function FlashAlert(props) {
    const {message, variant, showAlert} = props
    const [show, setShow] = useState(true);

    /*Triggers on showAlert update*/
    useEffect(() => {
        setShow(showAlert)
    }, [showAlert])

    return (
        <Fragment>
        {show && <Alert variant={variant} onClose={() => setShow(false)} dismissible>
            <strong>
                {message}
            </strong>
        </Alert>}
        </Fragment>
    );
}

export default FlashAlert;
