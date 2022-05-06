import React, {
  forwardRef,
  Fragment,
  useEffect,
  useImperativeHandle,
  useState,
} from "react";
import { useSelector, useDispatch } from "react-redux";
import Datetime from "react-datetime";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { useForm, Controller } from "react-hook-form";
import TimezoneList from "../../../../utils/timezone-list";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-react";
import Select from "react-select";
import { fetchCampaignList } from "../../../../store/actions/policy-management/campaigns";
import { subDays } from "date-fns";

const schema = yup
  .object({
    name: yup.string().required(),
    policies: yup.array().required().min(1),
    groups: yup.array().required().min(1),
    launch_date: yup.string().required(),
    due_date: yup.string().required(),
    auto_enroll_users: yup.string().required(),
    timezone: yup.string().required(),
  })
  .required();

function CampaignDuplicateForm(props, ref) {
  const { policies, groups } = props;
  const { searchQuery, campaignTypeFilter, setIsFormSubmitting } = props;
  const appDataScope = useSelector(
    (state) => state.appDataScope.selectedDataScope.value
  );
  const [groupOptions, setGroupOptions] = useState([]);
  const [policyOptions, setPolicyOptions] = useState([]);
  const [timezoneOptions, setTimezoneOptions] = useState([]);
  const [defaultTimezone, setDefaultTimezone] = useState('');
  const { campaign } = useSelector(
    (state) => state.policyManagement.campaignDuplicateReducer
  );
  const dispatch = useDispatch();
  const { globalSetting, errors: serverSideValErrors } = usePage().props;

  const {
    reset,
    register,
    trigger,
    getValues,
    control,
    formState: { errors },
    setError,
  } = useForm({
    resolver: yupResolver(schema),
    defaultValues: {
      name: `Copy of - ${campaign.name}`,
    },
    mode: "onChange",
  });

  // The component instance will be extended
  // with whatever you return from the callback passed
  // as the second argument
  useImperativeHandle(ref, () => ({
    handleSubmitCampaignDuplicate,
  }));

  /* Setting groups options */
  useEffect(() => {
    // setPolicyOptions()

    let data = groups.map((group) => {
      return {
        value: group.id,
        label: group.name,
      };
    });

    setGroupOptions(data);
  }, [groups]);

  /* Setting policies options */
  useEffect(() => {
    let data = policies.map((policy) => {
      return {
        value: policy.id,
        label: policy.display_name,
      };
    });

    setPolicyOptions(data);
  }, [policies]);

  /* Setting backend validation errors */
  useEffect(() => {
    for (const key in serverSideValErrors) {
      if (serverSideValErrors.hasOwnProperty(key)) {
        setError(key, {
          message: serverSideValErrors[key],
        });
      }
    }
  }, [serverSideValErrors]);

  /**
   *
   */
  useEffect(() => {
    let data = TimezoneList.map((timezone) => {
      return {
        value: timezone.id,
        label: timezone.text,
      };
    });

    setTimezoneOptions(data);
  }, [TimezoneList]);

  /* Setting the edit form value */
  useEffect(() => {
    let campaignPolicies = campaign.policies.map((item) => item.policy_id);

    reset({
      ...getValues(),
      policies: campaignPolicies,
    });
  }, [campaign.policies]);

  /* Setting Default Timezone */
  useEffect(() => {
    let timezone = TimezoneList.filter((timezone) => timezone.id == globalSetting.timezone)[0]
    if (timezone) {
      setDefaultTimezone({ value: timezone.id, label: timezone.text });
    }
    reset({
      ...getValues(),
      timezone: globalSetting.timezone
    });
  }, []);

  /**
   *
   */
  const handleSubmitCampaignDuplicate = async (event) => {

    let isValid = await trigger();

    /* Returing when invalid */
    if (!isValid) return false;

    try {
      /* submitting data */
      let formData = getValues();
      /* Adding data scope attribute */
      formData["data_scope"] = appDataScope;
      formData["duplicate_campaign_form"] = true;
      setIsFormSubmitting(true);

      Inertia.post(route("policy-management.campaigns.store"), formData, {
        onSuccess: (page) => {
          let {
            props: {
              flash: { data: campaign },
            },
          } = page;

          setIsFormSubmitting(false);

          AlertBox(
            {
              title: "Campaign Scheduled!",
              text: "This campaign has been scheduled for launch!",
              // showCancelButton: true,
              confirmButtonColor: "#b2dd4c",
              confirmButtonText: "OK",
              closeOnConfirm: false,
              icon:'success'
            },
            function (confirmed) {
              if (confirmed.value && confirmed.value == true) {
                // close the modal
                dispatch({ type: `campaigns/duplicateCampaignModal/close` });

                Inertia.visit(
                  route("policy-management.campaigns.show", campaign.id)
                );
              } else {
                // close the modal
                dispatch({ type: `campaigns/duplicateCampaignModal/close` });

                // render campaigns
                dispatch(
                  fetchCampaignList({
                    campaign_name: searchQuery,
                    campaign_status: campaignTypeFilter,
                    data_scope: appDataScope,
                  })
                );
              }
            }
          );
        },
        onFinish: () => {
          setIsFormSubmitting(false);
        },
      });

    } catch (error) {
      setIsFormSubmitting(false);
    }
  };

  return (
    <Fragment>
      <div className="row">
        <div className="col-md-12">
          <div className="mb-3">
            <label htmlFor="name" className="form-label">
              Name <span className="required text-danger">*</span>
            </label>
            <input
              type="text"
              name="name"
              {...register("name")}
              className="form-control"
              id="name"
            />
            <p className="invalid-feedback d-block">{errors.name?.message}</p>
          </div>
        </div>
        <div className="col-md-12">
          <div className="mb-3">
            <label htmlFor="policies" className="form-label">
              Policy(ies) <span className="required text-danger">*</span>
            </label>
            <Controller
              control={control}
              name="policies"
              options={policyOptions}
              render={({ field: { onChange, value, ref } }) => (
                <Select
                  inputRef={ref}
                  className="react-select"
                  classNamePrefix="react-select"
                  onChange={(val) => onChange(val.map((c) => c.value))}
                  options={policyOptions}
                  defaultValue={campaign.policies.map((policy) => {
                    return {
                      value: policy.policy_id,
                      label: policy.display_name,
                    };
                  })}
                  isMulti
                />
              )}
            />
            <p className="invalid-feedback d-block">{errors.policies?.message}</p>
          </div>
        </div>
      </div>
      <div className="row">
        <div className="col-md-4">
          <div className="mb-3">
            <label htmlFor="launch-date_add-form" className="form-label">
              Launch Date <span className="required text-danger">*</span>
            </label>
            <Controller
              control={control}
              name="launch_date"
              defaultValue={new Date()}
              render={({ field }) => (
                <Datetime
                  {...field}
                  dateFormat={'DD/MM/YYYY'}
                  isValidDate={(current) => {
                    return current.isAfter(subDays(new Date(), 1));
                  }}
                />
              )}
            />
            <p className="invalid-feedback d-block">{errors.launch_date?.message}</p>
          </div>
        </div>
        <div className="col-md-4">
          <div className="mb-3">
            <label htmlFor="due-date_add-form" className="form-label">
              Due Date <span className="required text-danger">*</span>
            </label>
            <Controller
              control={control}
              name="due_date"
              render={({ field }) => (
                <Datetime
                  {...field}
                  dateFormat={'DD/MM/YYYY'}
                  isValidDate={(current) => {
                    return current.isAfter(
                      subDays(new Date(getValues("launch_date")), 1)
                    );
                  }}
                />
              )}
            />
            <p className="invalid-feedback d-block">{errors.due_date?.message}</p>
          </div>
        </div>
        <div className="col-md-4">
          <div className="mb-3">
            <label htmlFor="timezone-add-form" className="form-label">
              Time Zone <span className="required text-danger">*</span>
            </label>
            <Controller
              control={control}
              name="timezone"
              render={({ field: { onChange, value, ref } }) => (
                <Select
                  inputRef={ref}
                  className="react-select"
                  classNamePrefix="react-select"
                  onChange={(val) => onChange(val.value)}
                  options={timezoneOptions}
                  defaultValue={
                    {
                      value: TimezoneList.filter((timezone) => timezone.id == globalSetting.timezone)[0].id,
                      label: TimezoneList.filter((timezone) => timezone.id == globalSetting.timezone)[0].text
                    }
                  }
                />
              )}
            />
            <p className="invalid-feedback d-block">{errors.timezone?.message}</p>
          </div>
        </div>
      </div>
      <div className="row">
        <div className="col-md-12">
          <div className="mb-3 no-margin">
            <label htmlFor="group" className="form-label">
              Groups <span className="required text-danger">*</span>
            </label>
            <Controller
              control={control}
              name="groups"
              render={({ field: { onChange, value, ref } }) => (
                <Select
                  inputRef={ref}
                  className="react-select"
                  classNamePrefix="react-select"
                  onChange={(val) => onChange(val.map((c) => c.value))}
                  options={groupOptions}
                  isMulti
                />
              )}
            />
            <p className="invalid-feedback d-block">{errors.groups?.message}</p>
          </div>
        </div>
      </div>
      <div className="row">
        <div className="col-md-12">
          <div className="mb-0 no-margin">
            <label htmlFor="group" className="form-label">
              Auto-enroll future group users{" "}
              <span className="required text-danger">*</span>
            </label>
            <select
              {...register("auto_enroll_users")}
              className="form-control text-center cursor-pointer"
              name="auto_enroll_users"
            >
              <option value="yes">Yes</option>
              <option value="no">No</option>
            </select>
            <p className="invalid-feedback d-block">
              {errors.auto_enroll_users?.message}
            </p>
          </div>
        </div>
      </div>
    </Fragment>
  );
}

export default forwardRef(CampaignDuplicateForm);
