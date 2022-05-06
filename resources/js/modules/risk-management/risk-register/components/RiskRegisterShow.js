import React, { Fragment, useState, useEffect, useRef } from "react";
import { useParams } from "react-router-dom";
import AppLayout from "../../../../layouts/app-layout/AppLayout";
import { useSelector } from "react-redux";
import Modal from "react-bootstrap/Modal";
import Button from "react-bootstrap/Button";
import DataTable from "../../../../common/data-table/DataTable";
import Select from "react-select";
import Switch from "rc-switch";
import "rc-switch/assets/index.css";
import { Link, useForm, usePage } from "@inertiajs/inertia-react";
import axios from "axios";
import Breadcrumb from "../../../../common/breadcumb/Breadcumb";

// import 'react-switchery-component/react-switchery-component.css';
import "../styles/show-style.css";
import FlashMessages from "../../../../common/FlashMessages";
import { Inertia } from "@inertiajs/inertia";
import Swal from 'sweetalert2'
import withReactContent from 'sweetalert2-react-content'

const ManualAssignmentModal = ({ show, onClose, dataScope }) => {
  const { authUserRoles, risk } = usePage().props;
  const [contributorObject, setContributorObject] = useState({});
  const { data, setData, errors, post, processing } = useForm({
    owner: risk.owner_id,
    custodian: risk.custodian_id,
  });

  useEffect(() => {
    axiosFetch
      .get(route("risks.register.risks-contributors"), {
        params: {
          data_scope: dataScope,
        },
      })
      .then((res) => {
        setContributorObject(res.data);
      });
  }, []);

  const allowedRoles = [
    "Global Admin",
    "Compliance Administrator",
    "Risk Administrator",
    "Contributors",
  ];
  const disabled = !authUserRoles.some((role) => allowedRoles.includes(role));

  const contributors = [
    ...Object.keys(contributorObject).map((name) => ({
      label: decodeHTMLEntity(name),
      value: contributorObject[name],
    })),
  ];

  const handleSubmit = (e) => {
    e.preventDefault();
    post(route("risks.register.risks-manual-assign", risk.id), {
      preserveScroll: true,
      onSuccess: () => {
        onClose();
      },
    });
  };

  return (
    <Modal show={show} onHide={onClose} size={"xl"} centered>
      <Modal.Header className="px-3 pt-3 pb-0" closeButton>
        <Modal.Title className="my-0">Manual assignment</Modal.Title>
      </Modal.Header>
      <form onSubmit={handleSubmit}>
        <Modal.Body className="p-3">
          <div className="row">
            <div className="col-md-6">
              <div className="mb-3">
                <label htmlFor="" className="form-label">
                  Risk Owner
                  <span className="required text-danger">*</span>
                </label>
                <Select
                  className="react-select"
                  classNamePrefix="react-select"
                  options={contributors}
                  isDisabled={disabled}
                  defaultValue={contributors.find(
                    (c) => c.value === data.owner
                  )}
                  onChange={(e) => setData("owner", e.value)}
                />
                {errors.owner && (
                  <div className="invalid-feedback d-block">{errors.owner}</div>
                )}
              </div>
            </div>
            <div className="col-md-6">
              <div className="mb-3">
                <label htmlFor="" className="form-label">
                  Risk Custodian
                  <span className="required text-danger">*</span>
                </label>
                <Select
                  className="react-select"
                  classNamePrefix="react-select"
                  options={contributors}
                  isDisabled={disabled}
                  defaultValue={contributors.find(
                    (c) => c.value === data.custodian
                  )}
                  onChange={(e) => setData("custodian", e.value)}
                />
                {errors.custodian && (
                  <div className="invalid-feedback d-block">
                    {errors.custodian}
                  </div>
                )}
              </div>
            </div>
          </div>
        </Modal.Body>
        <Modal.Footer className="px-3 pb-3 pt-0">
          <Button variant="secondary" onClick={onClose}>
            Close
          </Button>
          <Button variant="primary" disabled={processing} type={"submit"}>
            {processing ? "Saving" : "Save"}
          </Button>
        </Modal.Footer>
      </form>
    </Modal>
  );
};

function RiskRegisterShow(props) {
  // let { id } = props.id;
  const [risk, setRisk] = useState([]);
  const [show, setShow] = useState(false);
  const [refreshMappedControlDataTable, setRefreshMappedControlDataTable] =
    useState(false);
  const [mappedControlsUrl, setMappedControlsUrl] = useState(false);
  const [manualAssignmentShow, setManualAssignmentShow] = useState(false);
  const appDataScope = useSelector(
    (state) => state.appDataScope.selectedDataScope.value
  );

  // map controls filter controls
  const [standards, setStandards] = useState([]);
  const [projects, setProjects] = useState([]);
  const [standardId, setStandardId] = useState([]);
  const [projectId, setProjectId] = useState([]);
  const [ajaxData, setAjaxData] = useState([]);
  const [fetchUrl, setFetchUrl] = useState(false);
  const projectSelectRef = useRef();
  const mappedSwitch = useRef();

  const MySwal = withReactContent(Swal);

  /* getting the risk show data */
  useEffect(async () => {
    if (props.id) {
      setFetchUrl(
        `risks/${props.id}/get-risk-mapping-compliance-project-controls`
      );
      setMappedControlsUrl(
        `risks/${props.id}/get-risk-mapped-compliance-controls`
      );
      try {
        let httpRes = await axiosFetch.get(
          `risks/risks-register-react/${props.id}/show`
        );
        let res = httpRes.data;
        if (res.success) {
          const { risk, allComplianceStandards } = res.data;
          setRisk(risk);
        }
      } catch (error) {}
      let response = await axiosFetch.get("risks/get-filter-options", {
        params: {
          data_scope: appDataScope,
        },
      });
      setStandards(response.data.data.managedStandards);
    }
  }, [appDataScope, show]);

  const columns = [
    {
      accessor: "project_name",
      label: "Project",
      priorityLevel: 1,
      position: 1,
      minWidth: 150,
      sortable: false,
    },
    {
      accessor: "standard",
      label: "Standard",
      priorityLevel: 1,
      position: 2,
      minWidth: 150,
      sortable: false,
    },
    {
      accessor: "control_id",
      label: "Control ID",
      priorityLevel: 1,
      position: 3,
      minWidth: 50,
      sortable: false,
    },
    {
      accessor: "control_name",
      label: "Control Name",
      priorityLevel: 1,
      position: 4,
      minWidth: 150,
      sortable: false,
    },
    {
      accessor: "desc",
      label: "Control Description",
      priorityLevel: 2,
      position: 5,
      minWidth: 150,
      sortable: false,
    },
    {
      accessor: "status",
      label: "Status",
      priorityLevel: 2,
      position: 5,
      minWidth: 150,
    },
    {
      accessor: "is_mapped",
      label: "Is mapped",
      priorityLevel: 2,
      position: 7,
      minWidth: 50,
      width: 50,
      sortable: false,
      CustomComponent: ({ row }) => {
        return (
          <Switch
            id={`swtch${row.id}`}
            className="switch-class"
            ref={mappedSwitch}
            onChange={async (val) => {
              MySwal.fire({
                title: '',
                text: 'Updating...',
                didOpen: () => {
                  MySwal.showLoading();
                },
                loaderHtml: `<div id="animationSandbox" class="text-center">
                    <div class="spinner-border text-success" role="status" >
                    </div>
                </div>`,
                willOpen: function(ele) {
                  var elems = ele.querySelectorAll(".swal2-loader");

                  [].forEach.call(elems, function(el) {
                      el.classList.remove("swal2-loader");
                  });
                }
              });

              let post_data = {
                risk_id: props.id,
                control_id: row.id,
              };
              let url = route("risks.register.map-risk-controls");
              await axios.post(url, post_data);
              const data = {
                standard_filter: standardId ? standardId : "",
                project_filter: projectId ? projectId : "",
              };
              setAjaxData(data);

              //Close loader
              MySwal.close();

              /* Refreshing the mapped control data table */
              setRefreshMappedControlDataTable((prevState) => !prevState);
            }}
            options={{
              color: "#b2dd4c",
            }}
            checked={row.is_mapped}
          />
        );
      },
    },
  ];

  const mapped_columns = [
    {
      accessor: "0",
      label: "Control ID",
      priorityLevel: 1,
      position: 1,
      minWidth: 150,
      sortable: false,
    },
    {
      accessor: "1",
      label: "Project",
      priorityLevel: 1,
      position: 2,
      minWidth: 150,
      sortable: false,
    },
    {
      accessor: "2",
      label: "Name",
      priorityLevel: 1,
      position: 3,
      minWidth: 50,
      sortable: false,
    },
    {
      accessor: "3",
      label: "Description",
      priorityLevel: 1,
      position: 4,
      minWidth: 150,
      sortable: false,
    },
    {
      accessor: "4",
      label: "Frequency",
      priorityLevel: 2,
      position: 5,
      minWidth: 150,
      sortable: false,
    },
    {
      accessor: "5",
      label: "Responsible",
      priorityLevel: 2,
      position: 5,
      minWidth: 150,
      sortable: false,
    },
    {
      accessor: "6",
      label: "Approver",
      priorityLevel: 2,
      position: 6,
      minWidth: 150,
      sortable: false,
    },
  ];

  const searchData = () => {
    const data = {
      standard_filter: standardId ? standardId : "",
      project_filter: projectId ? projectId : "",
    };
    setAjaxData(data);
  };

  const handleStandardChange = (e) => {
    setStandardId(e.value);
    setProjectId(null);
    getProjects(e.value);
    projectSelectRef.current.clearValue();
  };

  const handleProjectChange = (e) => {
    if (e != null) {
      setProjectId(e.value);
    }
  };

  const getProjects = (id) => {
    try {
      var url = "risks/get-filter-options?standardId=" + id;
      axiosFetch
        .get(url, {
          params: {
            data_scope: appDataScope,
          },
        })
        .then((res) => {
          setProjects(res.data.data.projects);
        });
    } catch (error) {
      console.log("Response error");
    }
  };

  const dataScopeRef = useRef(appDataScope);
  useEffect(() => {
    if (dataScopeRef.current !== appDataScope) {
      Inertia.get(route("risks.register.index"));
    }
  }, [appDataScope]);

  const breadcumbsData = {
    "title": "Risk Register",
    "breadcumbs": [
        {
            "title": "Risk Management",
            "href": route('risks.dashboard.index')
        },
        {
            "title": "Risk Register",
            "href": route('risks.register.index')
        },
        {
            "title": "View Risk",
            "href": "#"
        }
    ]
  }
  const handleClose = () => setShow(false);
  const handleShow = () => setShow(true);
  return (
    <AppLayout>
      <ManualAssignmentModal
        show={manualAssignmentShow}
        onClose={() => setManualAssignmentShow(false)}
        dataScope={appDataScope}
      />
      {/* <!-- breadcrumbs --> */}
      <Breadcrumb data={breadcumbsData}></Breadcrumb>
      {/* <!-- end of breadcrumbs --> */}
      <FlashMessages />
      {/* <!-- View Risk section --> */}
      <div className="row" id="risk-overview-section">
        <div className="col-xl-12">
          <div className="card">
            <div className="card-body project-box">
              <div className="risk__detail">
                <div className="top__btn d-flex align-items-center mb-3">
                  <div className="top__head-text">
                    <h4>View Risk</h4>
                  </div>
                  <span className="update__btn ms-auto">
                    <Link
                      className="btn btn-danger back-btn"
                      style={{ marginRight: "5px" }}
                      href={`${appBaseURL}/risks/dashboard`}
                    >
                      Back
                    </Link>
                    <Link
                      className="btn btn-primary"
                      href={
                        `${appBaseURL}/risks/risks-register/` +
                        risk.id +
                        `/edit`
                      }
                    >
                      Edit
                    </Link>
                  </span>
                </div>

                <div className="row border py-3">
                  <div className="col-lg-8 col-md-12">
                    <div className="description">
                      <div>
                        <h5 className="head__text">Description:</h5>
                        <span className="sub__text">
                          {decodeHTMLEntity(risk.risk_description)}
                        </span>
                      </div>

                      <div className="my-2">
                        <h5 className="head__text">Treatment:</h5>
                        <span className="sub__text">
                          {decodeHTMLEntity(risk.treatment)}
                        </span>
                      </div>

                      <div>
                        <h5 className="head__text">Affected property(ies):</h5>
                        <span>{risk.affected_properties}</span>
                      </div>

                      <div className="my-2">
                        <h5 className="head__text">Affected function/asset:</h5>
                        <span>
                          {decodeHTMLEntity(risk.affected_functions_or_assets)}{" "}
                        </span>
                      </div>

                      <div>
                        <h5 className="head__text">Risk Treatment:</h5>
                        <span>{risk.treatment_options}</span>
                      </div>
                    </div>
                  </div>

                  <div className="col-lg-4 col-md-12">
                    <div className="category">
                      <div className="mb-2">
                        <h5 className="head__text">Category:</h5>
                        <span className="sub__text">
                          {risk.category
                            ? decodeHTMLEntity(risk.category.name)
                            : ""}{" "}
                        </span>
                      </div>

                      <div>
                        <h5 className="head__text">Likelihood:</h5>
                        <span>{risk.likelihood}</span>
                      </div>

                      <div className="my-2">
                        <h5 className="head__text">Impact:</h5>
                        <span>{risk.impact}</span>
                      </div>

                      <div>
                        <h5 className="head__text">Inherent Risk Score:</h5>
                        <span>
                          {risk.inherent_score}
                          <span
                            className="risk-score-tag  ms-2"
                            style={{
                              color: risk.InherentRiskScoreLevel
                                ? risk.InherentRiskScoreLevel.color
                                : "white",
                            }}
                          >
                            {risk.InherentRiskScoreLevel
                              ? risk.InherentRiskScoreLevel.name
                              : ""}
                          </span>
                        </span>
                      </div>

                      <div className="my-2">
                        <h5 className="head__text">Residual Risk Score:</h5>
                        <span>
                          {risk.residual_score}
                          <span
                            className="risk-score-tag  ms-2"
                            style={{
                              color: risk.ResidualRiskScoreLevel
                                ? risk.ResidualRiskScoreLevel.color
                                : "white",
                            }}
                          >
                            {risk.ResidualRiskScoreLevel
                              ? risk.ResidualRiskScoreLevel.name
                              : ""}
                          </span>
                        </span>
                      </div>

                      <div>
                        <h5 className="head__text">Status:</h5>
                        {risk.status === "Close" ? (
                          <span className="risk-score-tag low ">Closed</span>
                        ) : (
                          <span className="risk-score-tag extreme">Open</span>
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      {/* <!-- View Risk section ends --> */}

      {/* <!-- Controls --> */}
      <div className="row">
        <div className="col-xl-12">
          <div className="card">
            <div className="card-body">
              <div className="controls__table">
                <div className="top__head-text">
                  <h4>Controls</h4>
                </div>
                <div className="control__btn mb-2 d-inline-block d-md-flex justify-content-end ">
                  <button
                    onClick={() => setManualAssignmentShow(true)}
                    className="btn btn-primary width-lg mapping-btn ms-2"
                  >
                    Manual assignment
                  </button>
                  {risk.treatment_options === "Mitigate" ? (
                    <Button
                      className="btn btn-primary width-lg mapping-btn ms-2"
                      onClick={handleShow}
                    >
                      Edit Control Mapping(s)
                    </Button>
                  ) : (
                    <a className="btn btn-primary width-lg mapping-btn ms-2 disabled">
                      Edit Control Mapping(s)
                    </a>
                  )}
                </div>

                {/* RISK CONTROL MAPPING MODAL */}
                <Modal
                  show={show}
                  onHide={handleClose}
                  centered={true}
                  size="xl"
                >
                  <Modal.Header className="px-3 pt-3 pb-0" closeButton>
                    <Modal.Title className="my-0">Control Mapping</Modal.Title>
                  </Modal.Header>
                  <div className="row linking-existing-controls-modal__filters d-flex justify-content-md-end map-controls-div">
                    <div className="col-md-5 mx-1 mb-3">
                      {standards && (
                        <Select
                          className="react-select"
                          classNamePrefix="react-select"
                          defaultValue={standards[0]}
                          options={standards}
                          onChange={handleStandardChange}
                        />
                      )}
                    </div>
                    <div className="col-md-5 mx-1 mb-3">
                      <Select
                        className="react-select"
                        classNamePrefix="react-select"
                        ref={projectSelectRef}
                        options={projects}
                        isDisabled={projects.length == 0}
                        onChange={handleProjectChange}
                      />
                    </div>
                    <div className="col-md-2 d-flex justify-content-end ms-1 mb-3">
                      <button
                        name="search"
                        className="btn btn-primary"
                        onClick={searchData}
                      >
                        Search
                      </button>
                    </div>
                  </div>
                  <Modal.Body className="p-3">
                    <DataTable
                      columns={columns}
                      fetchURL={fetchUrl}
                      ajaxData={ajaxData}
                      search
                    />
                  </Modal.Body>
                  <Modal.Footer className="px-3 pt-0 pb-3"></Modal.Footer>
                </Modal>
                {/* RISK CONTROL MAPPING MODAL END*/}

                {/* <!-- table --> */}
                {mappedControlsUrl && (
                  <DataTable
                    columns={mapped_columns}
                    fetchURL={mappedControlsUrl}
                    loaderHeight="535px"
                  />
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}

export default RiskRegisterShow;
