import React, { useEffect, useState } from "react";
import { Link, usePage } from "@inertiajs/inertia-react";
import { useForm } from "react-hook-form";
import { Inertia } from "@inertiajs/inertia";
import ComplianceTemplate from "./ComplianceTemplate";
import LoadingButton from "../../common/loading-button/LoadingButton";
import FlashMessages from "../../common/FlashMessages";

export default function StandardCreate(props) {
    const [isFormSubmitting, setIsFormSubmitting] = useState(false);
    const propsData = usePage().props;
    const standard = propsData.standard;
    const dublicateStandard = propsData.dublicateStandard ?? 0;
    const id = standard ? standard.id : null;
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
        document.title = "Create New Standard";
        if (id) {
            reset({
                name: standard.name,
                version: standard.version,
            });
        }
    }, [standard]);

    const onSubmit = (data) => {
        setIsFormSubmitting(true);
        const formData = getValues();
        formData.dublicateStandard = dublicateStandard;
        if (id) {
            Inertia.post(route("compliance-template-update", id), formData);
        } else {
            Inertia.post(route("compliance-template-store"), formData);
        }
        setIsFormSubmitting(false);
    };

    const breadcumbsData = {
        title: `${id ? "Edit" : "Create"} Standard`,
        breadcumbs: [
            {
                title: "Administration",
                href: "",
            },
            {
                title: "Compliance Template",
                href: route("compliance-template-view"),
            },
            {
                title: "Create",
                href: "",
            },
        ],
    };

    return (
        <ComplianceTemplate breadcumbsData={breadcumbsData}>
            <FlashMessages />
            <div className="row">
                <div className="col-xl-12">
                    <div className="card">
                        <div className="card-body">
                            <h5 className="sub-header">
                                Fields with{" "}
                                <span className="text-danger">*</span> are
                                required.
                            </h5>
                            {/* @php
                    $url = $_SERVER['REQUEST_URI'];
                    $explodeurl = explode('/',$url);
                    $standardID = 0;
                    if(isset($explodeurl[6]))
                    {
                        $standardID =$explodeurl[6];
                    }
                    @endphp */}
                            <form
                                className="absolute-error-form"
                                onSubmit={handleSubmit(onSubmit)}
                                method="post"
                                id="validate-form"
                            >
                                <div className="tab-pane">
                                    <div className="row">
                                        <div className="col-12">
                                            <div className="row mb-3">
                                                <label
                                                    className="col-md-3 form-label col-form-label"
                                                    htmlFor="name"
                                                >
                                                    Name{" "}
                                                    <span className="text-danger">
                                                        *
                                                    </span>
                                                </label>
                                                <div className="col-md-9">
                                                    <input
                                                        type="text"
                                                        {...register("name", {
                                                            required: true,
                                                            maxLength: 190,
                                                        })}
                                                        className="form-control"
                                                        id="name"
                                                        name="name"
                                                        placeholder="Name"
                                                        tabIndex={1}
                                                    />
                                                    <div className="invalid-feedback d-block">
                                                        {errors.name &&
                                                            errors.name.type ===
                                                                "required" && (
                                                                <div className="invalid-feedback d-block">
                                                                    The Name
                                                                    field is
                                                                    required
                                                                </div>
                                                            )}
                                                        {apiErrors.name && (
                                                            <div className="invalid-feedback d-block">
                                                                {apiErrors.name}
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="row mb-3">
                                                <label
                                                    className="col-md-3 form-label col-form-label"
                                                    htmlFor="version"
                                                >
                                                    Version{" "}
                                                    <span className="text-danger">
                                                        *
                                                    </span>
                                                </label>
                                                <div className="col-md-9">
                                                    <input
                                                        type="text"
                                                        {...register(
                                                            "version",
                                                            {
                                                                required: true,
                                                                maxLength: 190,
                                                            }
                                                        )}
                                                        className="form-control"
                                                        id="version"
                                                        name="version"
                                                        placeholder="Version"
                                                        tabIndex={2}
                                                    />
                                                    <div className="invalid-feedback d-block">
                                                        {errors.version &&
                                                            errors.version
                                                                .type ===
                                                                "required" && (
                                                                <div className="invalid-feedback d-block">
                                                                    The Version
                                                                    field is
                                                                    required
                                                                </div>
                                                            )}
                                                    </div>
                                                </div>
                                            </div>

                                            <ul className="list-inline mb-0 wizard">
                                                <li className="next list-inline-item float-end">
                                                    <Link
                                                        href={route(
                                                            "compliance-template-view"
                                                        )}
                                                    >
                                                        <button
                                                            type="button"
                                                            className="btn btn-danger back-btn"
                                                            tabIndex="4"
                                                        >
                                                            Back To List
                                                        </button>
                                                    </Link>
                                                    <LoadingButton
                                                        className="btn btn-primary ms-2"
                                                        type="submit"
                                                        loading={
                                                            isFormSubmitting
                                                        }
                                                        tabIndex="3"
                                                    >
                                                        {id
                                                            ? "Update"
                                                            : "Create"}
                                                    </LoadingButton>
                                                </li>
                                            </ul>
                                        </div>
                                        {/* <!-- end col --> */}
                                    </div>
                                    {/* <!-- end row --> */}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </ComplianceTemplate>
    );
}
