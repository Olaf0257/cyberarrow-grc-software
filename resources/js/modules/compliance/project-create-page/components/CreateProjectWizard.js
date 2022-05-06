import React, { Fragment, useEffect, useState, useRef } from "react";
import LaddaButton, { S, EXPAND_RIGHT } from "react-ladda";
import CreateProjectForm from "./CreateProjectForm";
import { useStateIfMounted } from "use-state-if-mounted";

function CreateProjectWizard(props) {
    const { project, assignedControls, errors } = props;
    const [wizardProgressBar, setWizardProgressBar] = useState(50);
    const [wizardSteps, setWizardSteps] = useState(2);
    const [formSubmitting, setFormSubmitting] = useState(false);
    const [currentStep, setCurrentStep] = useStateIfMounted(1);
    const createProjectFormRef = useRef();

    // go to wizard's next fase
    const goToNextStep = async (nextStep) => {
        var aValue = localStorage.getItem("data-scope");
        let isFormValid = await createProjectFormRef.current.isFormValid();

        if (!isFormValid) {
            return;
        }

        setCurrentStep(nextStep);
    };
    const updateCurrentStep = (step) => {
        setCurrentStep(step);
    };

    /* updating the progress bar */
    useEffect(() => {
        let progressPer = (currentStep / wizardSteps) * 100;

        setWizardProgressBar(progressPer);
    }, [currentStep]);

    return (
        <Fragment>
            <div id="progressbarwizard">
                <ul className="nav nav-pills bg-light nav-justified form-wizard-header mb-3">
                    <li className="nav-item">
                        <a
                            href="#account-2"
                            onClick={() => {
                                updateCurrentStep(1);
                            }}
                            id="first-tab"
                            className={`nav-link rounded-0 pt-2 pb-2 mb-0 ${
                                currentStep == 1 ? "active" : ""
                            } `}
                        >
                            <i className="mdi mdi-information-outline me-1" />
                            <span className="d-none d-sm-inline">
                                Project Details
                            </span>
                        </a>
                    </li>
                    <li className="nav-item">
                        <a
                            href="#finish-2"
                            onClick={() => {
                                updateCurrentStep(2);
                            }}
                            id="finish"
                            className={`nav-link rounded-0 pt-2 pb-2 mb-0 ${
                                currentStep == 2 ? "active" : ""
                            } ${currentStep != 2 ? "disabled" : ""}`}
                        >
                            <i className="mdi mdi-checkbox-marked-circle-outline me-1" />
                            <span className="d-none d-sm-inline">Finish</span>
                        </a>
                    </li>
                </ul>
                <div className="tab-content b-0 mb-0">
                    <div
                        id="bar"
                        className="progress mb-3"
                        style={{ height: 7 }}
                    >
                        <div
                            className="bar progress-bar progress-bar-striped progress-bar-animated secondary-bg-color"
                            style={{ width: `${wizardProgressBar}%` }}
                        />
                    </div>
                    <div
                        className={`tab-pane ${
                            currentStep == 1 ? "active" : ""
                        }`}
                        id="account-2"
                    >
                        <div className="row">
                            <div className="col-12">
                                <CreateProjectForm
                                    updateWizardCurrentStep={updateCurrentStep}
                                    ref={createProjectFormRef}
                                    project={project}
                                    setFormSubmitting={setFormSubmitting}
                                ></CreateProjectForm>

                                <ul className="list-inline mb-0 wizard">
                                    <li className="next list-inline-item float-end">
                                        <a
                                            onClick={() => {
                                                goToNextStep(currentStep + 1);
                                            }}
                                            id="next"
                                            className="btn btn-primary cursor-pointer"
                                            tabIndex={4}
                                        >
                                            Next
                                        </a>
                                    </li>
                                </ul>
                            </div>{" "}
                            {/* end col */}
                        </div>{" "}
                        {/* end row */}
                    </div>
                    <div
                        className={`tab-pane ${
                            currentStep == 2 ? "active" : ""
                        } ${currentStep != 2 ? "disabled" : ""}`}
                        id="finish-2"
                    >
                        <div className="row">
                            <div className="col-12">
                                <div className="text-center">
                                    <h2 className="mt-0">
                                        <i className="mdi mdi-check-all" />
                                    </h2>
                                    <h2 className="mt-0 mb-3">Thank you !</h2>
                                    <LaddaButton
                                        type="button"
                                        loading={formSubmitting}
                                        className="btn btn-primary"
                                        onClick={() => {
                                            createProjectFormRef.current.handleFormSubmit();
                                        }}
                                        data-size={S}
                                        data-style={EXPAND_RIGHT}
                                        data-spinner-size={30}
                                        data-spinner-color="#ddd"
                                        data-spinner-lines={12}
                                    >
                                        {project.id ? "Update" : "Launch"}{" "}
                                        Project
                                    </LaddaButton>
                                </div>
                            </div>{" "}
                            {/* end col */}
                        </div>{" "}
                        {/* end row */}
                    </div>
                </div>{" "}
                {/* tab-content */}
            </div>{" "}
            {/* end #progressbarwizard*/}
        </Fragment>
    );
}

export default CreateProjectWizard;
