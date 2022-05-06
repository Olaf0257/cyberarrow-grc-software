import { Inertia } from "@inertiajs/inertia";
import React, { useState, useEffect } from "react";
import { useForm } from "react-hook-form";
import AuthLayout from "../../layouts/auth-layout/AuthLayout";
import { Link } from "@inertiajs/inertia-react";
import Logo from "../../layouts/auth-layout/components/Logo";

export default function LoginPage(props) {
    document.title = "Login";
    const propsData = { props };
    const apiErrors = propsData.props.errors ? propsData.props.errors : null;
    const [isSSOConfigured, setIsSSOConfigured] = useState(false);

    const { register, formState: { errors }, handleSubmit, getValues, reset } = useForm({
        mode: 'onSubmit',
    });

    const login = () => {
        const formData = getValues();
        Inertia.post(route('login'), formData, {
            onError: () => {
                reset({
                    ...getValues(),
                    password: ''
                })
            }
        });
    };

    function getSAMLConfiguration() {
        axiosFetch.get(route("getSAMLConfiguration")).then((response) => {
            if (response.status) {
                setIsSSOConfigured(response.data);
            }
        });
    }

    useEffect(() => {
        getSAMLConfiguration();
    }, []);

    return (
        <AuthLayout>
            <div className="row justify-content-center">
                <div className="col-md-8 col-lg-6 col-xl-4">
                    <div className="login__main card bg-pattern">
                        <div className="card-body p-4">
                            {/* <!-- LOGO DISPLAY NAME -->*/}
                            <Logo></Logo>
                            {/* @if ($message = Session::get('exception'))
                            <div className="alert alert-danger alert-block  mt-2">
                                <button type="button" className="btn-close" data-dismiss="alert">×</button>
                                <strong>{{ $message }}</strong>
                            </div>
                            @endif */}

                            {/*
                            @if(Session::has('saml2_error_detail'))
                            @foreach(Session::get('saml2_error_detail') as $error)
                            <p className="error-msg msg">{{ $error }}</p>
                            @endforeach
                            @endif */}

                            <span className="error-msg msg">
                                {apiErrors.email && (
                                    <div className="invalid-feedback d-block">
                                        {apiErrors.email}
                                    </div>
                                )}
                            </span>

                            <span className="error-msg msg">
                                {apiErrors.password && (
                                    <div className="invalid-feedback d-block">
                                        {apiErrors.password}
                                    </div>
                                )}
                            </span>

                            <form
                                onSubmit={handleSubmit(login)}
                                className="absolute-error-form d-block"
                                id="login-form"
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
                                        type="text"
                                        {...register("email", {
                                            required: true,
                                            maxLength: 190,
                                            pattern:
                                                /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/,
                                        })}
                                        className={`form-control ${
                                            errors.email && "border-error"
                                        }`}
                                        name="email"
                                        id="emailaddress"
                                        placeholder="Enter your email"
                                        autoComplete="off"
                                        autoFocus={apiErrors ? true : false}
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
                                    </span>
                                </div>

                                {/* {
                                    apiErrors.password && (
                                        <div className="invalid-feedback d-block">{apiErrors.password}</div>
                                    )
                                } */}

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
                                            maxLength: 190,
                                        })}
                                        className={`form-control ${
                                            errors.password && "border-error"
                                        }`}
                                        name="password"
                                        type="password"
                                        autoComplete="new-password"
                                        id="password"
                                        placeholder="Enter your password"
                                        defaultValue={errors && ''} />
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

                                <div className="position-relative mb-3">
                                    <div className="form-check">
                                        <input
                                            className="form-check-input"
                                            {...register("remember")}
                                            type="checkbox"
                                            name="remember"
                                            id="checkbox-signin"
                                        />
                                        <label
                                            className="form-check-label"
                                            htmlFor="checkbox-signin"
                                        >
                                            Remember me
                                        </label>
                                    </div>
                                </div>

                                <div className="position-relative mb-0 text-center">
                                    <button
                                        onClick={() => void 0}
                                        id="login-btn"
                                        className="btn btn-primary w-100 secondary-bg-color"
                                    >
                                        {" "}
                                        Log In{" "}
                                    </button>
                                    {isSSOConfigured && (
                                        <a
                                            href={route("saml2.login")}
                                            className="btn btn-primary w-100 secondary-bg-color mt-2"
                                        >
                                            {" "}
                                            SSO{" "}
                                        </a>
                                    )}
                                </div>
                            </form>
                        </div>
                        {/* <!-- end card-body --> */}
                    </div>
                    {/* <!-- end card --> */}

                    <div className="row mt-3">
                        <div className="col-12 text-center">
                            <p>
                                {" "}
                                <Link
                                    href={route("forget-password")}
                                    className="text-white-50 ms-1"
                                >
                                    Forgot your password?
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
