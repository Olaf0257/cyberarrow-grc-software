import { Inertia } from "@inertiajs/inertia";
import React, { useState, useEffect } from "react";
import { useForm } from "react-hook-form";
import AuthLayout from "../../layouts/auth-layout/AuthLayout";
import { Link } from "@inertiajs/inertia-react";
import Logo from "../../layouts/auth-layout/components/Logo";

export default function SessionOutPage(props) {
    document.title = "Session Timeout";
    const { globalSetting } = props;
    const propsData = { props };
    const apiErrors = propsData.props.errors ? propsData.props.errors : null;

    const [email, setEmail] = useState(propsData.props.email);
    const [fullName, setFullName] = useState(propsData.props.fullName);
    const [loggedInWithSSO, setLoggedInWithSSO] = useState(
        propsData.props.loggedInWithSSO
    );

    useEffect(() => {
        // saving data to localstorage
        if (email) {
            localStorage.setItem("email", email);
        }
        if (fullName) {
            localStorage.setItem("fullName", fullName);
        }
        localStorage.setItem("loggedInWithSSO", loggedInWithSSO);

        // getting data from localstorage
        var localStorageEmail = localStorage.getItem("email");
        var localStorageUsername = localStorage.getItem("fullName");
        if (localStorageEmail) {
            setEmail(localStorageEmail);
        }
        if (localStorageUsername) {
            setFullName(localStorageUsername);
        }
        setLoggedInWithSSO(localStorage.getItem("loggedInWithSSO"));
    }, []);

    const {
        register,
        formState: { errors },
        handleSubmit,
        getValues,
    } = useForm({
        mode: "onSubmit",
    });

    const login = () => {
        const formData = getValues();
        formData["email"] = email;
        Inertia.post(route("login"), formData);
    };

    return (
        <AuthLayout>
            <div className="row justify-content-center">
                <div className="col-md-8 col-lg-6 col-xl-4">
                    <div className="login__main card bg-pattern">
                        <div className="card-body p-4">
                            {/* <!-- LOGO DISPLAY NAME -->*/}
                            <Logo></Logo>

                            <div className="text-center w-75 m-auto">
                                <h4 className="text-dark-50 text-center mt-3">
                                    Hi! {fullName}
                                </h4>
                                <p className="text-muted mb-4">
                                    Your session timed out due to inactivity.
                                    <br />
                                    {loggedInWithSSO == "yes"
                                        ? "Please click on the button below to regain access."
                                        : "Please enter your password to regain access."}
                                </p>
                            </div>

                            <span className="error-msg msg">
                                {apiErrors.email && (
                                    <div className="invalid-feedback d-block">
                                        {apiErrors.email}
                                    </div>
                                )}
                            </span>

                            {loggedInWithSSO == "yes" ? (
                                <a
                                    href={route("saml2.login")}
                                    className="btn btn-primary w-100 secondary-bg-color"
                                >
                                    {" "}
                                    SSO{" "}
                                </a>
                            ) : (
                                <form
                                    onSubmit={handleSubmit(login)}
                                    className="absolute-error-form"
                                    id="login-form"
                                >
                                    {apiErrors.password && (
                                        <div className="invalid-feedback d-block">
                                            {apiErrors.password}
                                        </div>
                                    )}
                                    <div
                                        id="password-group"
                                        className="position-relative mb-3"
                                    >
                                        <label
                                            className=" form-label"
                                            htmlFor="password"
                                        >
                                            Password{" "}
                                            <span className="text-danger">
                                                *
                                            </span>
                                        </label>
                                        <input
                                            {...register("password", {
                                                required: true,
                                                maxLength: 190,
                                            })}
                                            className={`form-control ${
                                                errors.password &&
                                                "border-error"
                                            }`}
                                            name="password"
                                            type="password"
                                            autoComplete="new-password"
                                            id="password"
                                            placeholder="Enter your password"
                                            autoFocus
                                        />
                                        <span className="error-msg msg">
                                            {errors.password &&
                                                errors.password.type ===
                                                    "required" && (
                                                    <div className="invalid-feedback d-block">
                                                        The password field is
                                                        required
                                                    </div>
                                                )}
                                        </span>
                                    </div>

                                    <div className="position-relative mb-0 text-center">
                                        <button
                                            type="submit"
                                            id="login-btn"
                                            className="btn btn-primary w-100 secondary-bg-color"
                                        >
                                            {" "}
                                            Log In{" "}
                                        </button>
                                    </div>
                                </form>
                            )}
                        </div>
                        {/* <!-- end card-body --> */}
                    </div>
                    {/* <!-- end card --> */}

                    <div className="row mt-3">
                        <div className="col-12 text-center">
                            <p>
                                Not you? Return to{" "}
                                <Link
                                    href={route("login")}
                                    className="text-white ms-1"
                                >
                                    Log In
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
