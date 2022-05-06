import React, { useState } from "react";
import { useForm } from "react-hook-form";
import AuthLayout from "../../layouts/auth-layout/AuthLayout";
import { Link, useForm as useInertiaForm } from "@inertiajs/inertia-react";
import LoadingButton from "../../common/loading-button/LoadingButton";
import Logo from "../../layouts/auth-layout/components/Logo";

export default function ForgetPassword(props) {
    document.title = "Forget Password";

    const propsData = { props };
    const apiErrors = propsData.props.errors ? propsData.props.errors : null;

    const {
        register,
        formState: { errors },
        handleSubmit,
    } = useForm({
        mode: "onChange",
    });

    const { setData, post, processing } = useInertiaForm({
        email: "",
    });

    function submit() {
        post(route("forget-password-post"));
    }

    return (
        <AuthLayout>
            <div className="row justify-content-center">
                <div className="col-md-8 col-lg-6 col-xl-4">
                    <div className="card bg-pattern">
                        <div className="card-body p-4">
                            {/* <!-- LOGO DISPLAY NAME -->*/}
                            <Logo></Logo>

                            <p className="text-muted mb-4 mt-3">
                                Enter your email address and we'll send you an
                                email with instructions to reset your password.
                            </p>

                            <form
                                onSubmit={handleSubmit(submit)}
                                className="absolute-error-form"
                                id="recoverpw-form"
                            >
                                <div
                                    id="email-group"
                                    className="position-relative mb-3"
                                >
                                    <label
                                        className="form-label"
                                        htmlFor="email"
                                    >
                                        Email address{" "}
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        {...register("email", {
                                            required: true,
                                            maxLength: 190,
                                            pattern:
                                                /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/,
                                        })}
                                        type="text"
                                        className={`form-control ${
                                            errors.email && "border-error"
                                        }`}
                                        name="email"
                                        id="email"
                                        placeholder="Enter your email"
                                        label="Email"
                                        onChange={(e) =>
                                            setData("email", e.target.value)
                                        }
                                        autoComplete="off"
                                        autoFocus
                                    />
                                    <span className="error-msg msg">
                                        {errors.email &&
                                            errors.email.type ===
                                                "required" && (
                                                <div className="invalid-feedback d-block">
                                                    The email address is
                                                    required
                                                </div>
                                            )}
                                        {errors.email &&
                                            errors.email.type === "pattern" && (
                                                <div className="invalid-feedback d-block">
                                                    Please enter a valid email
                                                    address
                                                </div>
                                            )}
                                        {apiErrors.email && (
                                            <div className="invalid-feedback d-block">
                                                {apiErrors.email}
                                            </div>
                                        )}
                                    </span>
                                </div>

                                <div className="mb-0 text-center">
                                    <LoadingButton
                                        id="resetpw-btn"
                                        className="btn btn-primary w-100"
                                        type="submit"
                                        loading={processing}
                                        disabled={processing}
                                    >
                                        Reset Password
                                    </LoadingButton>
                                </div>
                            </form>
                        </div>
                        {/* <!-- end card-body --> */}
                    </div>
                    {/* <!-- end card --> */}

                    <div className="row mt-3">
                        <div className="col-12 text-center">
                            <p className="text-white-50">
                                Back to{" "}
                                <Link
                                    href={route("login")}
                                    className="text-white ms-1"
                                >
                                    <b>Log in</b>
                                </Link>
                            </p>
                        </div>
                        {/* <!-- end col --> */}
                    </div>
                    {/* <!-- end row --> */}
                </div>
                {/* <!-- end col --> */}
            </div>
            {/* <!-- end row --> */}
        </AuthLayout>
    );
}
