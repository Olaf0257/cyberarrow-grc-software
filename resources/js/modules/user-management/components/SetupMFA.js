import React, { Fragment, useEffect, useState } from 'react';
import { Button, Modal } from 'react-bootstrap';

function SetupMFA(props) {

    const [QRCode, setQRCode] = useState();
    const [secretToken, setSecretToken] = useState();

    /* Fetch data on component load */
    useEffect(async () => {
        setQRCode(props.qrcode);
        setSecretToken(props.secrettoken);
    }, []);

    const setupMFA = async () => {
        let response = await axiosFetch.get('/mfa/setup-mfa')

        if (response.status === 200) {
            let data = response.data;
            setQRCode(data.data.as_qr_code);
            setSecretToken(data.data.as_string);
        }
        setModalShow(true)
    }

    return (
        <Modal
            {...props}
            size="lg"
            aria-labelledby="contained-modal-title-vcenter"
            centered
        >
            <Modal.Header className='px-3 pt-3 pb-0' closeButton>
                {/* <Modal.Title className="my-0" id="contained-modal-title-vcenter">
                    Setup MFA for your Account
                </Modal.Title> */}
                <div className="top-text">
                    <h4>Setup MFA for your Account</h4>
                    <div className="instruction__box my-2">
                        <p className="text-white">In order to use Multi Factor Authentication, you will need to install an authenticator application such as 'Google Authenticator'.</p>
                    </div>
                    <h5 className="text-dark">Secret Token:
                        <span
                            className="text-muted token"
                            id="secret-token-wp">
                            {secretToken}
                        </span>
                    </h5>
                </div>
            </Modal.Header>
            <Modal.Body className='p-3'>
                <div className="qrcode-box">
                    <div className="row">
                        <div className="col-xl-5 col-lg-5 col-md-5 col-sm-6 col-12">
                            <div dangerouslySetInnerHTML={{
                                __html: QRCode
                            }} className="qcode-left" id="mfa-qrcode-wp">
                            </div>
                        </div>

                        <div className="col-xl-7 col-lg-7 col-md-7 col-sm-6 col-12">
                            <div className="qrcode-right">
                                <p className="text-dark qrcode-right-text pt-1">Scan the barcode or type out the token to  add the token to the authenticator.</p>
                                <p className="text-dark qrcode-right-text">A new token will be generated everytime  you refresh or disable/enable MFA.</p>
                                <p className="text-dark mb-2 qrcode-right-text">Please enter the first code that shows in  the authenticator.</p>

                                <form action="{{ route('confirm-mfa') }}" method="Post" id="set-up-mfa">
                                    @csrf
                                    <div className="row mb-3">
                                        <div className="col-lg-7 col-md-7 col-sm-9">
                                            <input type="text" name="2fa_code" id="2fa_code" className="2fa__code form-control @if($errors->first('2fa_code')) is-invalid @endif" placeholder="123456" />
                                        </div>
                                    </div>

                                    <button type="submit" className="btn btn-primary enable__btn mt-1">Enable Secure MFA Login</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </Modal.Body>
            <Modal.Footer className='px-3 pt-0 pb-3'>
                <Button onClick={props.onHide}>Close</Button>
            </Modal.Footer>
        </Modal >
    );
}

export default SetupMFA;
