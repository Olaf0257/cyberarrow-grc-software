import React, {
    Fragment,
    useEffect,
    forwardRef,
    useImperativeHandle,
} from "react";
import { useForm } from "react-hook-form";
import { usePage } from "@inertiajs/inertia-react";
import { Inertia } from "@inertiajs/inertia";
import { useStateIfMounted } from "use-state-if-mounted";
import { useSelector } from "react-redux";

function CreateProjectForm(props, ref) {
    const { project, setFormSubmitting, updateWizardCurrentStep } = props;
    const [standards, setStandards] = useStateIfMounted([]);
    const { assignedControls } = usePage().props;
    const {
        register,
        reset,
        formState: { errors },
        trigger,
        setValue,
        getValues,
    } = useForm({
        mode: "onChange",
        // reValidateMode: 'onChange'
    });
    const appDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope.value
    );

    useEffect(async () => {
        let httpResponse = await axiosFetch.get(
            "administration/compliance-template/list"
        );
        let resData = httpResponse.data;

        if (resData.success) {
            let data = resData.data;

            /* SETTING THE STANDARDS*/
            setStandards(data);
        }
    }, []);

    /* Setting the form field value on load*/
    useEffect(() => {
        reset({
            name: project.name,
            description: project.description,
            standard_id: project.standard_id,
        });
    }, [project]);

    /* Custome form validation rule*/
    const nameIsUnique = async (name) => {
        // return false
        let httpRes = await axiosFetch.get(
            route("compliance.projects.check-project-name-taken", project.id),
            {
                params: {
                    name: name,
                },
            }
        );

        let res = httpRes.data;
        return res;
    };

    /* checking from validity*/
    const isFormValid = async () => {
        return await trigger(["name", "description", "standard_id"]);
    };

    // The component instance will be extended
    // with whatever you return from the callback passed
    // as the second argument
    useImperativeHandle(ref, () => ({
        isFormValid,
        handleFormSubmit,
    }));

    const onSubmit = async (data) => {
        let submitURL = project.id
            ? route("compliance-projects-update", project.id)
            : route("compliance-projects-store");

        /* Setting form submitting loader */
        setFormSubmitting(true);

        data["data_scope"] = appDataScope;

        Inertia.post(submitURL, data, {
            onError: (page) => {
                updateWizardCurrentStep(1);
            },
        });
    };

    const handleFormSubmit = async () => {
        const formData = getValues();

        /* Submitting form data */
        onSubmit(formData);
    };

    /* Triggers on standard update */
    const handleStandardChange = (e) => {
        reset({
            ...getValues(),
            standard_id: e.target.value,
        });
    };

    return (
        <Fragment>
            <form className="needs-validation" noValidate>
                <div className="row mb-3">
                    <label
                        className="col-md-3 form-label col-form-label"
                        htmlFor="name"
                    >
                        Project Name{" "}
                        <span className="required text-danger">*</span>
                    </label>
                    <div className="col-md-9">
                        <input
                            type="text"
                            {...register("name", {
                                required: true,
                                maxLength: 190,
                                validate: {
                                    asyncValidate: nameIsUnique,
                                },
                            })}
                            className="form-control"
                            id="project-name"
                            name="name"
                            placeholder="Project Name"
                            tabIndex={1}
                        />
                        {errors.name && errors.name.type === "required" && (
                            <div className="invalid-feedback d-block">
                                The Project Name field is required
                            </div>
                        )}
                        {errors.name &&
                            errors.name.type === "asyncValidate" && (
                                <div className="invalid-feedback d-block">
                                    The Project Name already taken
                                </div>
                            )}
                        {errors.name && errors.name.type === "maxLength" && (
                            <div className="invalid-feedback d-block">
                                The Project Name may not be greater than 190
                                characters
                            </div>
                        )}
                    </div>
                </div>
                <div className="row mb-3">
                    <label
                        className="col-md-3 form-label col-form-label"
                        htmlFor="description"
                    >
                        {" "}
                        Description{" "}
                        <span className="required text-danger">*</span>
                    </label>
                    <div className="col-md-9">
                        <textarea
                            {...register("description", { required: true })}
                            name="description"
                            id="description"
                            className="form-control"
                            cols={30}
                            rows={5}
                            placeholder="Description"
                            tabIndex={2}
                        />
                        {errors.description &&
                            errors.description.type === "required" && (
                                <div className="invalid-feedback d-block">
                                    The Description field is required
                                </div>
                            )}
                    </div>
                </div>
                <div className="row mb-3">
                    <label
                        className="col-md-3 form-label col-form-label"
                        htmlFor="standard"
                    >
                        Standard <span className="required text-danger">*</span>
                    </label>
                    <div className="col-md-9">
                        <select
                            {...register("standard_id", { required: true })}
                            value={getValues("standard_id")}
                            onChange={handleStandardChange}
                            name="standard_id"
                            id="standard"
                            className="form-control cursor-pointer"
                            tabIndex={3}
                            disabled={assignedControls > 0}
                        >
                            <option value="">Choose Standard</option>
                            {standards.map((standard, index) => {
                                return (
                                    <option value={standard.id} key={index}>
                                        {standard.name}
                                    </option>
                                );
                            })}
                        </select>
                        {errors.standard_id &&
                            errors.standard_id.type === "required" && (
                                <div className="invalid-feedback d-block">
                                    The Standard field is required
                                </div>
                            )}
                    </div>
                </div>
            </form>
        </Fragment>
    );
}

export default forwardRef(CreateProjectForm);
