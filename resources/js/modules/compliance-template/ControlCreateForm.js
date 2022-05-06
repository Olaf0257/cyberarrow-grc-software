import React, { useEffect, useState } from "react";
import { Link, usePage } from "@inertiajs/inertia-react";
import { useForm, Controller } from "react-hook-form";
import Select from "react-select";
import { Inertia } from "@inertiajs/inertia";
import "./controls.scss";

export default function ControlCreateForm(props) {
    const [isFormSubmitting, setIsFormSubmitting] = useState(false);
    const propsData = usePage().props;
    const standardControl = propsData.control;
    const id = standardControl ? standardControl.id : null;
    const standard = propsData.standard;
    const idSeparators = propsData.idSeparators;
    const apiErrors = propsData.errors;

    const {
        register,
        formState: { errors },
        control,
        handleSubmit,
        getValues,
        reset,
    } = useForm({
        mode: "onChange",
    });

    useEffect(() => {
        let defaultValues = {
            name: id ? standardControl.name : "",
            description: id ? standardControl.description : "",
            primary_id: id ? standardControl.primary_id : "",
            sub_id: id ? standardControl.sub_id : "",
            id_separator: id ? standardControl.id_separator : ".",
        };
        reset(defaultValues);
    }, []);

    const onSubmit = (data) => {
        setIsFormSubmitting(true);
        const formData = getValues();
        if (id) {
            Inertia.post(
                route("compliance-template-update-controls", [
                    standardControl.standard.id,
                    id,
                ]),
                formData
            );
        } else {
            Inertia.post(
                route(
                    "compliance-template-store-controls",
                    standardControl.standard.id
                ),
                formData
            );
        }
        setIsFormSubmitting(false);
    };

    return (
        <div className={standardControl.id ? "col-xl-12" : "col-xl-6"}>
            <div className="table-left">
                <h4>
                    {standardControl.id ? "Update" : "Create a New"} Control
                </h4>
                <h5 className="mb-3 sub-header">
                    Fields with <span className="text-danger">*</span> are
                    required.
                </h5>

                <form onSubmit={handleSubmit(onSubmit)} method="post">
                    <div className="mb-3">
                        <label className="form-label" htmlFor="name">
                            {" "}
                            Name <span>*</span>
                        </label>
                        <input
                            type="text"
                            {...register("name", {
                                required: true,
                                maxLength: 190,
                            })}
                            className="form-control"
                            aria-describedby="emailHelp"
                            id="name"
                            name="name"
                            placeholder="Enter name"
                            tabIndex={1}
                        />
                        <div className="invalid-feedback d-block">
                            {errors.name && errors.name.type === "required" && (
                                <div className="invalid-feedback d-block">
                                    The Name field is required
                                </div>
                            )}
                            {apiErrors.name && (
                                <div className="invalid-feedback d-block">
                                    {apiErrors.name}
                                </div>
                            )}
                        </div>
                    </div>

                    <div className="mb-3">
                        <label className="form-label" htmlFor="description">
                            {" "}
                            Description <span>*</span>
                        </label>
                        <textarea
                            {...register("description", {
                                required: true,
                                maxLength: 190,
                            })}
                            className="form-control"
                            name="description"
                            id="description"
                            rows="5"
                            cols="10"
                            placeholder="Enter description..."
                            tabIndex={2}
                        ></textarea>
                        <div className="invalid-feedback d-block">
                            {errors.description &&
                                errors.description.type === "required" && (
                                    <div className="invalid-feedback d-block">
                                        The Description field is required
                                    </div>
                                )}
                            {apiErrors.description && (
                                <div className="invalid-feedback d-block">
                                    {apiErrors.description}
                                </div>
                            )}
                        </div>
                    </div>

                    <div className="mb-3">
                        <label className="form-label" htmlFor="primary_id">
                            {" "}
                            Primary ID <span>*</span>
                        </label>
                        <input
                            type="text"
                            {...register("primary_id", {
                                required: true,
                                maxLength: 190,
                            })}
                            className="form-control"
                            id="primary_id"
                            name="primary_id"
                            placeholder="Enter Primary ID"
                            tabIndex={3}
                        />
                        <div className="invalid-feedback d-block">
                            {errors.primary_id &&
                                errors.primary_id.type === "required" && (
                                    <div className="invalid-feedback d-block">
                                        The Primary ID field is required
                                    </div>
                                )}
                            {apiErrors.primary_id && (
                                <div className="invalid-feedback d-block">
                                    {apiErrors.primary_id}
                                </div>
                            )}
                        </div>
                    </div>

                    <div className="mb-3">
                        <label className="form-label" htmlFor="sub_id">
                            {" "}
                            Sub ID <span>*</span>
                        </label>
                        <input
                            type="text"
                            {...register("sub_id", {
                                required: true,
                                maxLength: 190,
                            })}
                            className="form-control"
                            id="sub_id"
                            name="sub_id"
                            placeholder="Enter Sub ID"
                            tabIndex={4}
                        />
                        <div className="invalid-feedback d-block">
                            {errors.sub_id &&
                                errors.sub_id.type === "required" && (
                                    <div className="invalid-feedback d-block">
                                        The Sub ID field is required
                                    </div>
                                )}
                            {apiErrors.sub_id && (
                                <div className="invalid-feedback d-block">
                                    {apiErrors.sub_id}
                                </div>
                            )}
                        </div>
                    </div>

                    <div className="mb-3 id-sep">
                        <label htmlFor="id_separator" className="id_sep-label">
                            {" "}
                            ID Separator{" "}
                        </label>
                        <Controller
                            control={control}
                            name="id_separator"
                            rules={{ required: true }}
                            render={({ field: { onChange, value, ref } }) => (
                                <Select
                                    inputRef={ref}
                                    onChange={(val) => onChange(val.value)}
                                    options={idSeparators}
                                    className="react-select"
                                    classNamePrefix="react-select"
                                    defaultValue={
                                        id
                                            ? {
                                                  label: idSeparators.filter(
                                                      (item) => {
                                                          return (
                                                              item.value ==
                                                              standardControl.id_separator
                                                          );
                                                      }
                                                  )[0].label,
                                                  value: standardControl.id_separator,
                                              }
                                            : {
                                                  label: idSeparators[0].label,
                                                  value: idSeparators[0].value,
                                              }
                                    }
                                />
                            )}
                        />

                        <div className="invalid-feedback d-block">
                            {/* @if ($errors->has('id_separator'))
                                {{ $errors->first('id_separator') }}
                                @endif */}
                            {errors.id_separator &&
                                errors.id_separator.type === "required" && (
                                    <div className="invalid-feedback d-block">
                                        The ID Separator field is required
                                    </div>
                                )}
                        </div>
                    </div>

                    <div className="{{ $control->id ? 'd-flex justify-content-end edit-control-btn' : '' }}">
                        <button type="submit" className="btn btn-primary me-2">
                            {standardControl.id ? "Update" : "Create"}
                        </button>
                        <Link
                            href={route("compliance-template-view-controls", [
                                standardControl.standard.id,
                            ])}
                        >
                            <button type="button" className="btn btn-danger">
                                Back to List
                            </button>
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    );
}
