import React, {Fragment} from 'react';
import Modal from 'react-bootstrap/Modal'
import { useSelector } from 'react-redux';

function RiskGeneratorLoader(props) {
    const {show} = useSelector(state => state.riskGenerateLoaderReducer)
    return (
        <Fragment>
            <Modal
                id="generateRiskModal"
                show={show}
                aria-labelledby="myCenterModalLabel"
                dialogClassName="modal-dialog-centered"
            >
                <Modal.Body>
                    <div id="animationSandbox" className="p-2 text-center">
                        <div className="spinner-border text-success m-2" role="status" >
                        </div>
                        <p>Hold on your risks are being generated…</p>
                    </div>
                </Modal.Body>
            </Modal>
        </Fragment>
    );
}

export default RiskGeneratorLoader;
