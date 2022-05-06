import React from "react";
import { useForm } from "react-hook-form";
import AuthLayout from "../../layouts/auth-layout/AuthLayout";
import { useForm as useInertiaForm } from "@inertiajs/inertia-react";
import LoadingButton from "../../common/loading-button/LoadingButton";
import Logo from "../../layouts/auth-layout/components/Logo";

export default function ResetPassword(props) {
    document.title = "Reset Password";

    const { token } = props;
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
        token: token,
        email: "",
        password: "",
        password_confirmation: "",
    });

    function submit() {
        post(route("admin-reset-password"));
    }

    return (
        <AuthLayout>
            <div className="row justify-content-center">
                <div className="col-md-8 col-lg-6 col-xl-4">
                    <div className="card bg-pattern">
                        <div className="card-body p-4">
                            {/* <!-- LOGO DISPLAY NAME -->*/}
                            <Logo></Logo>

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

                                <div
                                    id="password-group"
                                    className="position-relative mb-3"
                                >
                                    <label
                                        className="form-label"
                                        htmlFor="password"
                                    >
                                        Password{" "}
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        {...register("password", {
                                            required: true,
                                            pattern:
                                                /^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/,
                                        })}
                                        type="password"
                                        className={`form-control ${
                                            (errors.password ||
                                                apiErrors.password) &&
                                            "msg border-error"
                                        }`}
                                        id="password"
                                        onChange={(e) =>
                                            setData("password", e.target.value)
                                        }
                                        autoComplete="new-password"
                                        placeholder="Enter your password"
                                    />
                                    <span className="error-msg msg">
                                        {errors.password &&
                                            errors.password.type ===
                                                "required" && (
                                                <div className="invalid-feedback d-block">
                                                    The password is required
                                                </div>
                                            )}
                                        {errors.password &&
                                            errors.password.type ===
                                                "pattern" && (
                                                <div className="invalid-feedback d-block">
                                                    Password must contain:
                                                    <ul
                                                        style={{
                                                            paddingLeft:
                                                                "1.5rem",
                                                        }}
                                                    >
                                                        <li>
                                                            {" "}
                                                            a minimum of 8
                                                            characters and{" "}
                                                        </li>
                                                        <li>
                                                            {" "}
                                                            a minimum of 1 lower
                                                            case letter and{" "}
                                                        </li>
                                                        <li>
                                                            {" "}
                                                            a minimum of 1 upper
                                                            case letter and{" "}
                                                        </li>
                                                        <li>
                                                            {" "}
                                                            a minimum of 1
                                                            special character
                                                            and{" "}
                                                        </li>
                                                        <li>
                                                            {" "}
                                                            a minimum of 1
                                                            numeric character{" "}
                                                        </li>
                                                    </ul>
                                                </div>
                                            )}
                                        {apiErrors.password && (
                                            <div
                                                className="invalid-feedback d-block"
                                                dangerouslySetInnerHTML={{
                                                    __html: apiErrors.password,
                                                }}
                                            ></div>
                                        )}
                                    </span>
                                </div>
                                <div
                                    id="password-group"
                                    className="position-relative mb-3"
                                >
                                    <label
                                        className="form-label"
                                        htmlFor="password_confirmation"
                                    >
                                        Confirm Password{" "}
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        {...register("password_confirmation", {
                                            required: true,
                                            validate: {
                                                confirm: (value) =>
                                                    value === password.value,
                                            },
                                        })}
                                        type="password"
                                        className="form-control"
                                        id="password_confirmation"
                                        placeholder="Enter your email"
                                        label="password_confirmation"
                                        onChange={(e) =>
                                            setData(
                                                "password_confirmation",
                                                e.target.value
                                            )
                                        }
                                        placeholder="Re-enter your password"
                                    />
                                    <span className="error-msg msg">
                                        {errors.password_confirmation &&
                                            errors.password_confirmation
                                                .type === "required" && (
                                                <div className="invalid-feedback d-block">
                                                    The password confirmation is
                                                    required
                                                </div>
                                            )}
                                        {errors.password_confirmation &&
                                            errors.password_confirmation.type ==
                                                "confirm" && (
                                                <div className="invalid-feedback d-block">
                                                    The password and confirm
                                                    password must be equal
                                                </div>
                                            )}
                                        {apiErrors.password_confirmation && (
                                            <div className="invalid-feedback d-block">
                                                {
                                                    apiErrors.password_confirmation
                                                }
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
                                        Change Password
                                    </LoadingButton>
                                </div>
                            </form>
                        </div>
                        {/* <!-- end card-body --> */}
                    </div>
                    {/* <!-- end card --> */}
                </div>
                {/* <!-- end col --> */}
            </div>
            {/* <!-- end row --> */}
        </AuthLayout>
    );
}
