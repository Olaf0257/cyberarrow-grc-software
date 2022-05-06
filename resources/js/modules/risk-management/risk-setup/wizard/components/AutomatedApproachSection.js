import React, { useState, useEffect } from "react";
import Select from "react-select";
import { useDidMountEffect } from "../../../../../custom-hooks";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { useSelector, useDispatch } from "react-redux";
import { Inertia } from "@inertiajs/inertia";

const schema = yup
  .object({
    project: yup.number().positive().integer().required(),
  })
  .required();

const AutomatedApproachSection = (props) => {
  const {
    selectedApproach,
    selectedStandard,
    wizardCurrentTab
  } = props;
  const {
    control,
    getValues,
    trigger,
    formState: { errors },
  } = useForm({
    resolver: yupResolver(schema),
  });

  const appDataScope = useSelector(
    (state) => state.appDataScope.selectedDataScope.value
  );
  const dispatch = useDispatch();
  const [projects, setProjects] = useState([]);

  const getProjects = async () => {
    let httpRes = await axiosFetch.get(
      route("risks.wizard.get-risk-import-setup-page"),
      {
        params: {
          setupApproach: selectedApproach,
          standard: selectedStandard,
        },
      }
    );

    let response = httpRes.data;

    if (response.success) {
      const { projects } = response;
      let projectList = [{ value: 0, label: "Select Project" }];
      projects.map(function (eachProject, index) {
        projectList.push({ value: eachProject.id, label: eachProject.name });
      });

      setProjects(projectList);
    }
  };

  const generateRiskRegister = async () => {
    const isValid = await trigger(["project"]);

    if (!isValid) return;

    const postData = {
      project: getValues("project"),
      riskSetUpStandard: selectedStandard,
      data_scope: appDataScope,
    };

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

          Inertia.post(route("risks.wizard.automated-risk-setup"), postData, {
            onSuccess: () => {
              dispatch({ type: "riskGenerateLoader/hide" });
            },
            onError: () => {
              dispatch({ type: "riskGenerateLoader/hide" });
            },
          });
        }
      }
    );

  };

  useDidMountEffect(() => {
    if (wizardCurrentTab === "import") {
      getProjects();
    }
  }, [wizardCurrentTab]);

  useEffect(() => {
  }, [projects]);

  const handleProjectChange = (val) => {
  };

  return (
    <>
      <div id="approach-automated">
        <p className="text-center">
          Choose the corresponding compliance project to generate your risks.
        </p>
        <div className="form-group row">
          <label
            htmlFor="inputPassword"
            className="col-lg-1 col-md-1 col-sm-1 offset-lg-4 offset-md-4 offset-sm-3 col-form-label"
          >
            Project
          </label>
          <div className="col-lg-3 col-md-3 col-sm-4">
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
            <p className="invalid-feedback d-block">{errors.project?.message}</p>
          </div>
        </div>
        <div className="import-sec-btn d-flex">
          <button
            type="submit"
            className="btn btn-primary btn-generate-risk ms-auto generate-risk-register"
            onClick={generateRiskRegister}
          >
            Generate Risk Register
          </button>
        </div>
      </div>
    </>
  );
};
export default AutomatedApproachSection;
