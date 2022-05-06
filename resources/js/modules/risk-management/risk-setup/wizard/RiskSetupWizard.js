import React, { Fragment, useEffect, useState } from "react";
import { useSelector, useDispatch } from "react-redux";
import "./riskwizard.css";
import BreadcumbsComponent from "../../../../common/breadcumb/Breadcumb";
import RiskStandardSelection from "./components/RiskStandardSelection";
import ProgressBarComponent from "./components/ProgressBarComponent";
import AppLayout from "../../../../layouts/app-layout/AppLayout";
import { useDidMountEffect } from "../../../../custom-hooks";
import YourselfApproachSection from "./components/YourselfApproachSection";
import AutomatedApproachSection from "./components/AutomatedApproachSection";
import ApproachTab from "./components/ApproachTab";
import { showToastMessage } from "../../../../utils/toast-message";

function RiskSetupWizard(props) {
  const [selectedStandard, setSelectedStandard] = useState({});
  const [selectedApproach, setSelectedApproach] = useState(null);
  const [projectExist, setProjectExist] = useState(false);
  const [wizardCurrentTab, setWizardCurrentTab] = useState("standard");
  const [reachedWizardTab, setReachedWizardTab] = useState(1);
  const appDataScope = useSelector(
    (state) => state.appDataScope.selectedDataScope.value
  );
  const dispatch = useDispatch();

  useDidMountEffect(async () => {
    document.title = "Risk Wizard";
    setWizardCurrentTab("standard")
    setReachedWizardTab(1);
  }, [appDataScope]);

  const selectedStandardItem = (e) => {
    setSelectedStandard(e.target.value);
  };

  const handleStandard = (e) => {
    try {
      var url =
        "risks/wizard/check-compliance-projects-exists?standard=" +
        selectedStandard +
        "&data_scope=" +
        appDataScope;
      axiosFetch.get(url).then((res) => {
        const response = res.data;

        if (response.exists == false) {
          setProjectExist(false);
        } else if (response.exists == true) {
          setProjectExist(true);
        }
      });
    } catch (error) {
      console.log("Response error");
    }
  };

  const riskStandardsData = [
    {
      standardName: "ISO/IEC 27002:2013",
      description:
        "ISO/IEC 27002:2013 is an international information security standard which includes internationally accepted security controls. This standard is applicable to organizations of any type and size. The risk assessment will be based on the controls stated in ISO/IEC 27002:2013.",
      value: "ISO/IEC 27002:2013",
    },
    {
      standardName: "ISR V2",
      description:
        "ISR V2 is a local information security standard developed specifically for government entities in the emirate of Dubai in the UAE. The standard is also applicable to any type of organization. The risk assessment will be based on the controls stated in ISR V2.",
      value: "ISR V2",
    },
    {
      standardName: "SAMA Cyber Security Framework",
      description:
        "SAMA Cybersecurity Framework is a local information security standard developed specifically for financial institutions regulated by SAMA in the Kingdom of Saudi Arabia. The standard is also applicable to any type of financial institution. The risk assessment will be based on the controls stated in SAMA Cybersecurity Framework.",
      value: "SAMA Cyber Security Framework",
    },
    {
      standardName: "NCA ECC-1:2018",
      description:
        "NCA Essential Cybersecurity Controls is a local information security standard developed specifically for government entities in the Kingdom of Saudi Arabia. The standard is also applicable to any type of organization. The risk assessment will be based on the controls stated in NCA ECCâ€“1:2018.",
      value: "NCA ECC-1:2018",
    },
    {
      standardName: "NCA CSCC-1:2019",
      description:
        "NCA Critical Systems Cybersecurity Controls is a local information security standard developed specifically for critical systems within national organizations in the Kingdom of Saudi Arabia. The standard is also applicable to any type of organization with critical systems. The risk assessment will be based on the controls stated in NCA CSCCâ€“1:2019.",
      value: "NCA CSCC-1:2019",
    },
    {
      standardName: "UAE IA",
      description:
        "UAE IA is an information security standard developed specifically for government entities in the UAE. The standard is also applicable to any type of organization. The risk assessment will be based on the controls stated in UAE IA.",
      value: "UAE IA",
    },
  ];

  const breadcumbsData = {
    title: "Risk Wizard",
    breadcumbs: [
      {
        title: "Risk Management",
        href: `${appBaseURL}/risks/dashboard`,
      },
      {
        title: "Risk Setup",
        href: "/risks/setup",
      },
      {
        title: "Risk Wizard",
        href: "",
      },
    ],
  };

  const onError = () => {
    if (props.errors.length > 0) {
      showToastMessage('Risk updated successfully!', 'success');
    }
  };

  const isActiveTab = (tab) => {
    return tab == wizardCurrentTab;
  };

  const goToNextTab = (tab) => {
    if (tab === "approach") {
      handleStandard();
      setWizardCurrentTab(tab);
    } else {
      setWizardCurrentTab(tab);
    }
    if (reachedWizardTab >= 3) return;

    setReachedWizardTab(reachedWizardTab + 1);
  };

  const goToTab = (tab) => {
    setWizardCurrentTab(tab);
  };

  const handleTabClick = (tab, tabIndex) => {
    return reachedWizardTab < tabIndex ? "" : goToTab(tab);
  };

  return (
    <AppLayout>
      <Fragment>
        <BreadcumbsComponent data={breadcumbsData} />
        {props.errors && onError()}
        <div className="row" id="mainContainerRiskSetupWizard">
          <div className="col-xl-12" id="risk-setup-wizard-page">
            <div className="card">
              <div className="card-body project-box">
                <ul className="nav nav-pills navtab-bg nav-justified risk-setup-nav-wp">
                  <li className="nav-item">
                    <a
                      href="#"
                      id="standard"
                      onClick={() => {
                        goToTab("standard");
                      }}
                      data-toggle="tab"
                      aria-expanded="false"
                      className={`nav-link ${
                        isActiveTab("standard") ? "active" : ""
                      }`}
                    >
                      Choose Standard
                    </a>
                  </li>
                  <li className="nav-item">
                    <a
                      href="#"
                      id="approach"
                      onClick={() => {
                        handleTabClick("approach", 2)
                      }}
                      data-toggle="tab"
                      aria-expanded="true"
                      className={`nav-link ${
                        isActiveTab("approach") ? "active" : ""
                      }`}
                    >
                      Approach
                    </a>
                  </li>
                  <li className="nav-item">
                    <a
                      href="#"
                      id="import"
                      onClick={() => {
                        handleTabClick("import", 3)
                      }}
                      data-toggle="tab"
                      aria-expanded="false"
                      className={`nav-link ${
                        isActiveTab("import") ? "active" : ""
                      }`}
                    >
                      Import
                    </a>
                  </li>
                </ul>

                <div className="tab-content">
                  <ProgressBarComponent reachedWizardTab={reachedWizardTab} />
                  <div
                    className={`tab-pane ${
                      isActiveTab("standard") ? "active" : ""
                    }`}
                    id="standard-tab"
                  >
                    <div className="row">
                      <RiskStandardSelection
                        inputName={"risk-setup-standard"}
                        selectStandard={selectedStandardItem}
                        riskStandards={riskStandardsData}
                        currentSelected={selectedStandard}
                      />
                      <button
                        className="btn btn-primary go-to-next-step-btn clearfix mt-2 me-2 float-end d-flex ms-auto"
                        onClick={() => {
                          goToNextTab("approach");
                        }}
                        id="nextBtn"
                        disabled={selectedStandard.length > 2 ? false : true}
                        data-current-tab="1"
                      >
                        Next
                      </button>
                    </div>
                  </div>
                  <div
                    className={`tab-pane ${
                      isActiveTab("approach") ? "active" : ""
                    }`}
                    id="approach-tab"
                  >
                    <div className="row">
                      <ApproachTab
                        setSelectedApproach={setSelectedApproach}
                        currentSelected={selectedApproach}
                        projectExist={projectExist}
                      />
                      <button
                        className="btn btn-primary go-to-next-step-btn clearfix mt-2 me-2 float-end d-flex ms-auto"
                        onClick={() => {
                          goToNextTab("import");
                        }}
                        id="secondNextButton"
                        disabled={selectedApproach != null ? false : true}
                        data-current-tab="2"
                      >
                        Next
                      </button>
                    </div>
                  </div>
                  <div
                    className={`tab-pane ${
                      isActiveTab("import") ? "active" : ""
                    }`}
                    id="import-tab"
                  >
                    {selectedApproach == "Yourself" ? (
                      <YourselfApproachSection
                        wizardCurrentTab={wizardCurrentTab}
                        selectedApproach={selectedApproach}
                        selectedStandard={selectedStandard}
                        projectExist={projectExist}
                      />
                    ) : (
                      <AutomatedApproachSection
                        selectedApproach={selectedApproach}
                        selectedStandard={selectedStandard}
                        projectExist={projectExist}
                        wizardCurrentTab={wizardCurrentTab}
                      />
                    )}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </Fragment>
    </AppLayout>
  );
}

export default RiskSetupWizard;