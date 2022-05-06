import React, { useState, useEffect } from "react";
import ImportRiskAccordion from "./ImportRiskAccordion";
import Pagination from "../../../../../common/pagination/Pagination";
import { useDidMountEffect } from "../../../../../custom-hooks";
import { useDispatch, useSelector } from "react-redux";
import Select from "react-select";
import { Inertia } from "@inertiajs/inertia";
import { useForm, Controller } from "react-hook-form";
import Modal from "react-bootstrap/Modal";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";

const schema = yup
  .object({
    project: yup.number().positive().integer().required(),
  })
  .required();

const YourselfApproachSection = (props) => {
  const { wizardCurrentTab, selectedApproach, selectedStandard, projectExist } =
    props;

  const appDataScope = useSelector(
    (state) => state.appDataScope.selectedDataScope.value
  );
  const dispatch = useDispatch();
  const [categories, setCategories] = useState([]);
  const [currentCategoryIndex, setCurrentCategoryIndex] = useState(null);
  const [riskList, setRiskList] = useState([]);
  const [showMappingModal, setShowMappingModal] = useState(false);
  const [renderedRiskIds, setRenderedRiskIds] = useState([]);
  const [selectedRiskIds, setSelectedRiskIds] = useState([]);
  const [riskSetupSteps, setRiskSetupSteps] = useState(0);
  const [completedSetupStep, setCompletedSetupStep] = useState(0);
  const [projects, setProjects] = useState([]);
  const [riskNameSearchQuery, setRiskNameSearchQuery] = useState({
    getData: false,
    value: "",
  });
  const {
    control,
    getValues,
    trigger,
    formState: { errors },
  } = useForm({
    resolver: yupResolver(schema),
  });

  useDidMountEffect(() => {
    if (riskNameSearchQuery.getData) {
      getCategoryRisks();
    }
  }, [riskNameSearchQuery]);

  useDidMountEffect(() => {
    getCategoryRisks();
  }, [categories]);

  /* Resetting and setting for the first time */
  useDidMountEffect(() => {
    if (wizardCurrentTab === "import") {
      getCategories();
      setCurrentCategoryIndex(0);
    } else {
      setCurrentCategoryIndex(null);
    }
  }, [wizardCurrentTab]);

  useDidMountEffect(async () => {
    if (currentCategoryIndex !== null) {
      getCategoryRisks();
    }
  }, [currentCategoryIndex]);

  useEffect(() => {
  }, [completedSetupStep]);

  const filterRisk = (e) => {
    setRiskNameSearchQuery({
      getData: true,
      value: e.target.value,
    });
  };

  const goToNextCategory = () => {
    if (riskSetupSteps === currentCategoryIndex + 1) return;
    /* This prevent the data request when risk name search input cleared*/
    setRiskNameSearchQuery({
      getData: false,
      value: "",
    });
    let nextStep = currentCategoryIndex + 1;
    setCurrentCategoryIndex(nextStep);

    if (nextStep > completedSetupStep) {
      setCompletedSetupStep(nextStep);
    }

    scrollToTop();
  };

  const goToPrevCategory = () => {
    /* This prevent the data request when risk name search input cleared*/
    setRiskNameSearchQuery({
      getData: false,
      value: "",
    });
    setCurrentCategoryIndex(currentCategoryIndex - 1);

    scrollToTop();
  };

  const generateRiskRegister = () => {

    if (projectExist) {
      setShowMappingModal(true);
    } else {
      AlertBox(
        {
          title: "Are you sure ?",
          text: "All old risk will be deleted",
          showCancelButton: true,
          confirmButtonColor: "#ff0000",
          confirmButtonText: "Yes",
          icon:'warning',
        iconColor:'#ff0000'
        },
        function (confirmed) {
          if (confirmed.value && confirmed.value == true) {
            /* showing risk generate loader */
            dispatch({ type: "riskGenerateLoader/show" });

            finalPost();
          }
        }
      );
    }
  };

  const finalPost = (map = 0, controlMappingProjectValue = 0) => {
    var data = {
      selected_risk_ids: selectedRiskIds,
      data_scope: appDataScope,
      is_map: map,
      control_mapping_project: controlMappingProjectValue,
    };
    Inertia.post("yourself-risks-setup", data, {
      onSuccess: () => {
        dispatch({ type: "riskGenerateLoader/hide" });
      },
      onError: () => {
        dispatch({ type: "riskGenerateLoader/hide" });
      },
    });
    if (props.errors && props.errors[0]) {
      toast.error(props.errors[0], {
        position: "bottom-right",
        autoClose: 5000,
        hideProgressBar: false,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
    }
  };

  const isConfirmTab = () => {
    return riskSetupSteps === currentCategoryIndex + 1;
  };

  const isAllSelected =
    renderedRiskIds.length === 0
      ? false
      : renderedRiskIds.every((v) => selectedRiskIds.includes(v));

  const getCategoryRisks = async (page = 0) => {
    let confirmTab = isConfirmTab();

    let categoryId = confirmTab ? 0 : categories[currentCategoryIndex]["id"];
    let params = {
      standard: selectedStandard,
      is_confirm_tab: confirmTab,
      current_tab_index: 1,
      selected_risk_ids: Object.values(selectedRiskIds),
      risk_name_search_query: riskNameSearchQuery.value,
      category_id: categoryId,
      page: page,
      data_scope: appDataScope,
    };

    let httpRes = await axiosFetch.get(
      route("risks.wizard.get-risk-import-risks-list-section"),
      {
        params: params,
      }
    );
    const response = httpRes.data;

    if (response.success) {
      setRiskList(response);
      let riskData = response && response.risks && response.risks.data;
      setRenderedRiskIds(riskData.map((item) => item.id));
    }
  };

  const getCategories = async () => {
    let httpRes = await axiosFetch.get(
      route("risks.wizard.get-risk-import-setup-page"),
      {
        params: {
          setupApproach: selectedApproach,
          standard: selectedStandard,
          data_scope: appDataScope,
        },
      }
    );

    const response = httpRes.data;

    if (response.success) {
      const { projects, riskCategories } = response;

      let projectList = [{ value: 0, label: "Select Project" }];
      projects.map(function (eachProject, index) {
        projectList.push({ value: eachProject.id, label: eachProject.name });
      });

      setProjects(projectList);
      setCategories(riskCategories);

      if (riskCategories.length > 0) {
        setRiskSetupSteps(riskCategories.length + 1);
      } else {
        setRiskSetupSteps(0);
      }
    }
  };

  const paginationLinkedClickAction = (e) => {
    //e.target.dataset.link <------ to get the clicked link url from pagination
    const url = new URL(e.target.dataset.link);
    const page = url.searchParams.get("page");


    getCategoryRisks(page);

    scrollToTop();
  };

  const onCheckedItem = (id) => {
    let selected = selectedRiskIds;

    let find = selected.indexOf(id);

    if (find > -1) {
      selected.splice(find, 1);
    } else {
      selected.push(id);
    }
    setSelectedRiskIds([...selected]);
  };

  const selectAll = () => {
    if (isAllSelected) {
      let filteredRiskIds = selectedRiskIds.filter(
        (riskId) => !renderedRiskIds.includes(riskId)
      );
      setSelectedRiskIds(filteredRiskIds);
    } else {
      setSelectedRiskIds([...selectedRiskIds, ...renderedRiskIds]);
    }
  };

  const handleCloseMappingModal = () => {
    setShowMappingModal(false);
  };

  const proceedRiskSetupWithMapping = async () => {
    let isValid = await trigger("project");

    if (!isValid) return false;

    AlertBox(
      {
        title: "Are you sure ?",
        text: "All old risk will be deleted",
        showCancelButton: true,
        confirmButtonColor: "#ff0000",
        confirmButtonText: "Yes",
        icon:'warning',
        iconColor:'#ff0000'
      },
      function (confirmed) {
        if (confirmed.value && confirmed.value == true) {
          /* showing risk generate loader */
          handleCloseMappingModal();
          dispatch({ type: "riskGenerateLoader/show" });

          finalPost(1, getValues("project"));
        }
      }
    );
  };

  const proceedRiskSetupWithoutMapping = () => {
    AlertBox(
      {
        title: "Are you sure ?",
        text: "All old risk will be deleted",
        showCancelButton: true,
        confirmButtonColor: "#ff0000",
        confirmButtonText: "Yes",
        icon:'warning',
        iconColor:'#ff0000'
      },
      function (confirmed) {
        if (confirmed.value && confirmed.value == true) {
          /* showing risk generate loader */
          handleCloseMappingModal();
          dispatch({ type: "riskGenerateLoader/show" });

          finalPost();
        }
      }
    );
  };

  const handleSetUpStepClick = (step) => {
    setCurrentCategoryIndex(step);
  };

  const getSetUpStepClassess = (index) => {
    let classes = "nav-link risk-category-tab-nav";

    if (currentCategoryIndex == index) {
      classes = classes + " active";
    }

    if (completedSetupStep < index) {
      classes = classes + " disabled";
    } else {
      classes = classes + " completed";
    }

    return classes;
  };

  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  };

  return (
    <>
      <div id="yourself-apporach">
        <div className="nested__tabs">
          <ul className="nav  circular">
            <div className="liner"></div>
            {categories.length > 0
              ? categories.map(function (eachRCategory, index) {
                  return (
                    <li key={index} className="nav-item">
                      <a
                        href="#"
                        onClick={() => handleSetUpStepClick(index)}
                        className={getSetUpStepClassess(index)}
                        title={eachRCategory.name}
                      >
                        {index < completedSetupStep ? (
                          <span className="round-tabs">
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              width="30px"
                              height="30px"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              strokeWidth="1"
                              strokeLinecap="round"
                              strokeLinejoin="round"
                              className="feather feather-check"
                            >
                              <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                          </span>
                        ) : (
                          <span className="round-tabs">
                            {index + 1}
                            <i data-feather="check"></i>
                          </span>
                        )}
                      </a>
                      <p> {eachRCategory.name}</p>
                    </li>
                  );
                })
              : ""}
            <li className="nav-item">
              <a
                href="#"
                data-current-category-tab="curent"
                className={getSetUpStepClassess(riskSetupSteps - 1)}
                onClick={() => handleSetUpStepClick(riskSetupSteps - 1)}
                title="Comfirm"
              >
                <span className="round-tabs">
                  {riskSetupSteps > 0 ? riskSetupSteps : ""}
                  <i data-feather="check"></i>
                </span>
              </a>
              <p> Confirm</p>
            </li>
          </ul>
        </div>
        <div className="tab-content">
          <div className="top__head mb-2">
            <div className="row ms-4">
              <div className="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                <div className="top__one">
                  <h5 id="risk-category">
                    {isConfirmTab()
                      ? "Confirm"
                      : categories[currentCategoryIndex]
                      ? categories[currentCategoryIndex].name
                      : ""}
                  </h5>
                </div>
              </div>
              <div className="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                <div className="searchbox top__search">
                  <input
                    type="text"
                    placeholder="Search by Risk Name"
                    onChange={filterRisk}
                    name="search_by_risk_name"
                    value={riskNameSearchQuery.value}
                    className="search"
                  />
                  <i className="fas fa-search search-icon"></i>
                </div>
              </div>
              <div className="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                <div className="top__three d-flex me-3">
                  <h5 className="ms-md-auto select-all-text">Select All</h5>
                  <div className="checkbox checkbox-success checkbox4 select_all_checkbox">
                    <input
                      id="select_all_risk_items_checkbox"
                      type="checkbox"
                      onChange={selectAll}
                      checked={isAllSelected}
                    />
                    <label htmlFor="select_all_risk_items_checkbox"></label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <ImportRiskAccordion
            riskList={riskList}
            riskSelectedIds={selectedRiskIds}
            clickCheckAction={onCheckedItem}
          />
          {riskList.risks ? (
            <Pagination
              actionFunction={paginationLinkedClickAction}
              links={riskList.risks.links}
            />
          ) : (
            ""
          )}
          <div className="import-sec-btn mt-3">
            <div className="d-flex">
              {currentCategoryIndex > 0 ? (
                <button
                  className="btn btn-danger btn-back risk-category-back-btn"
                  onClick={goToPrevCategory}
                >
                  Back
                </button>
              ) : (
                ""
              )}
              {isConfirmTab() ? (
                <button
                  className="btn btn-primary ms-auto"
                  onClick={generateRiskRegister}
                >
                  Confirm
                </button>
              ) : (
                <button
                  className="btn btn-primary ms-auto"
                  onClick={goToNextCategory}
                >
                  Next
                </button>
              )}
            </div>
          </div>
        </div>
      </div>

      <Modal
        show={showMappingModal}
        onHide={handleCloseMappingModal}
        size="md"
        aria-labelledby="contained-modal-title-vcenter"
        centered
      >
        <Modal.Header closeButton className="align-items-start">
          <Modal.Title>
            <center>Choose control mapping</center>
            <h4 className="mt-4">
              Use this feature if you want to automatically map the risks to
              existing compliance controls. This is an optional feature.{" "}
            </h4>
          </Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <div className="row">
            <div className="col-sm-12">
              {projects ? (
                <Controller
                  control={control}
                  name="project"
                  render={({ field: { onChange, value, ref } }) => (
                    <Select
                      onChange={(val) => onChange(val.value)}
                      defaultValue={projects[0]}
                      options={projects}
                    />
                  )}
                />
              ) : (
                ""
              )}
              <div className="invalid-feedback d-block" id="automated-setup-error">
                {errors.project?.message}
              </div>
            </div>
          </div>
        </Modal.Body>
        <Modal.Footer>
          <button
            type="button"
            id="proceed-setup-without-mapping"
            className="btn btn-primary btn-left"
            onClick={() => proceedRiskSetupWithoutMapping()}
          >
            Proceed without mapping
          </button>
          <button
            type="button"
            id="proceed-setup-map"
            className="btn btn-primary float-right"
            onClick={() => proceedRiskSetupWithMapping()}
          >
            Map
          </button>
        </Modal.Footer>
      </Modal>
    </>
  );
};

export default YourselfApproachSection;
