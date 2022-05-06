import React, {
  forwardRef,
  Fragment,
  useEffect,
  useImperativeHandle,
  useState,
} from "react";
import Select from "react-select";
import Datetime from "react-datetime";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-react";
import { useSelector, useDispatch } from "react-redux";
import "react-datetime/css/react-datetime.css";
import TimezoneList from "../../../../utils/timezone-list";
import { fetchCampaignList } from "../../../../store/actions/policy-management/campaigns";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import { subDays } from "date-fns";
import * as yup from "yup";
import moment from "moment-timezone";

const schema = yup
  .object({
    name: yup.string().required('Name is a required field'),
    policies: yup.array().required('Policies is a required field').min(1),
    groups: yup.array().required('Groups is a required field').min(1),
    launch_date: yup.string().required('Launch date is a required field'),
    due_date: yup.string().required('Due date is a required field'),
    auto_enroll_users: yup.string().required(),
    timezone: yup.string().required(),
  })
  .required();

function AddCampaignForm(props, ref) {
  const { policies, groups } = props;
  const {
    setIsFormSubmitting,
    campaignTypeFilter,
    searchQuery,
    setShowCampaignAddModal,
  } = props;
  const appDataScope = useSelector(
    (state) => state.appDataScope.selectedDataScope.value
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
    reValidateMode: "onChange",
    mode: "onChange",
  });

  /* component states definations starts */
  const [policyOptions, setPolicyOptions] = useState([]);
  const [groupOptions, setGroupOptions] = useState([]);
  const [timezoneOptions, setTimezoneOptions] = useState([]);
  const [defaultTimezone, setDefaultTimezone] = useState('');

  /* component states definations ends */

  // The component instance will be extended
  // with whatever you return from the callback passed
  // as the second argument
  useImperativeHandle(ref, () => ({
    launchCampaign,
  }));

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

  /* Setting policies options */
  useEffect(() => {
    let data = policies.map((policy) => {
      return {
        value: policy.id,
        label: decodeHTMLEntity(policy.display_name),
      };
    });

    setPolicyOptions(data);
  }, [policies]);

  /* Setting groups options */
  useEffect(() => {
    let data = groups.map((group) => {
      return {
        value: group.id,
        label: group.name,
      };
    });
    setGroupOptions(data);
  }, [groups]);

  useEffect(() => {
    let data = TimezoneList.map((timezone) => {
      return {
        value: timezone.id,
        label: timezone.text,
      };
    });

    setTimezoneOptions(data);

  }, [TimezoneList]);

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

  const launchCampaign = async () => {
    try {
      let isValid = await trigger();

      /* Returing when invalid */
      if (!isValid) return false;

      /* submitting data */
      let formData = getValues();
      /* Adding data scope attribute */
      formData["data_scope"] = appDataScope;

      const format = 'YYYY-MM-DD HH:mm:ss';

      const launchDate =  moment.utc(formData.launch_date).tz(globalSetting.timezone).format(format);
      formData.launch_date = moment.tz(launchDate, formData.timezone).utc().format(format);

      const dueDate =  moment.utc(formData.due_date).tz(globalSetting.timezone).format(format);
      formData.due_date = moment.tz(dueDate, formData.timezone).utc().format(format);

      setIsFormSubmitting(true);

      Inertia.post(route("policy-management.campaigns.store"), formData, {
        onSuccess: (page) => {
          let {
            props: {
              flash: { data: campaign },
            },
          } = page;

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
                Inertia.visit(
                  route("policy-management.campaigns.show", campaign.id)
                );
              } else {
                setShowCampaignAddModal(false);
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
    } catch (error) { }
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
              className="form-control"
              {...register("name")}
              id="name"
              placeholder=""
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
                  className="react-select"
                  classNamePrefix="react-select"
                  inputRef={ref}
                  onChange={(val) => onChange(val.map((c) => c.value))}
                  options={policyOptions}
                  isMulti
                />
              )}
            />
            <p className="invalid-feedback d-block">{errors.policies?.message}</p>
          </div>
        </div>
      </div>
      {/* end of row */}
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
                  displayTimeZone={globalSetting.timezone}
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
                  displayTimeZone={globalSetting.timezone}
                  isValidDate={(current) => {
                    return current.isAfter(subDays(new Date(getValues("launch_date")), 1));
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
                  className="react-select"
                  classNamePrefix="react-select"
                  inputRef={ref}
                  onChange={(val) => onChange(val.value)}
                  options={timezoneOptions}
                  defaultValue={
                    //defaultTimezone   <- This is not working, if fixed, replicate on campaignduplicateform as well
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
                  className="react-select"
                  classNamePrefix="react-select"
                  inputRef={ref}
                  onChange={(val) => {
                    onChange(val.map((c) => c.value));
                  }}
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
              <option className="cursor-pointer" value="yes">
                Yes
              </option>
              <option className="cursor-pointer" value="no">
                No
              </option>
            </select>
          </div>
          <p className="invalid-feedback d-block">
            {errors.auto_enroll_users?.message}
          </p>
        </div>
      </div>
    </Fragment>
  );
}

export default forwardRef(AddCampaignForm);
