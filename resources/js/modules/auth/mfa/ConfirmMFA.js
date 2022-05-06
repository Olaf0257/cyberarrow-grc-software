import React, { useState } from "react";
import { Inertia } from "@inertiajs/inertia";
import { useForm } from "react-hook-form";
import AuthLayout from "../../../layouts/auth-layout/AuthLayout";
import LoadingButton from "../../../common/loading-button/LoadingButton";
import ResetMFA from "./ResetMFA";
import Logo from "../../../layouts/auth-layout/components/Logo";

export default function ConfirmMFA(props) {
    document.title = "Multi Factor Authentication";

    const [isFormSubmitting, setIsFormSubmitting] = useState(false);
    const { email } = props;
    const propsData = { props };
    const apiErrors = propsData.props.errors ? propsData.props.errors : null;
    const errorMessage = propsData.props.flash
        ? propsData.props.flash.error
        : null;
    const {
        register,
        formState: { errors },
        getValues,
        handleSubmit,
    } = useForm({
        mode: "onChange",
    });

    // const { data, setData, post, processing } = useInertiaForm({
    //     two_fa_code: '',
    //     email: '',
    //     password: '',
    //     remember: '',
    // })

    // useEffect(() => {
    //     setData('email', email)
    // }, [])

    function submit() {
        setIsFormSubmitting(true);
        let formData = getValues();
        formData["email"] = email;
        Inertia.post(route("2fa.confirm"), formData, {
            onSuccess: () => {
                setIsFormSubmitting(false);
            },
            onError: () => {
                setIsFormSubmitting(false);
            },
        });
    }

    return (
        <AuthLayout>
            <div className="row justify-content-center">
                <div className="col-md-8 col-lg-6 col-xl-5">
                    <div className="card bg-pattern">
                        <div className="card-body p-4">
                            {/* <!-- LOGO DISPLAY NAME -->*/}
                            <Logo></Logo>

                            <h4 className="text-center">
                                Two factor authentication required
                            </h4>
                            <hr />
                            <p>
                                To log in, open your Authenticator app and fill
                                up the 6-digit code.
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
                                    <input
                                        {...register("two_fa_code", {
                                            required: true,
                                            maxLength: 6,
                                            minLength: 6,
                                        })}
                                        className={`form-control ${
                                            errors.two_fa_code && "border-error"
                                        }`}
                                        placeholder="123456"
                                        autoComplete="off"
                                        autoFocus
                                    />
                                    <span className="error-msg msg">
                                        {errors.two_fa_code &&
                                            errors.two_fa_code.type ===
                                                "required" && (
                                                <div className="invalid-feedback d-block">
                                                    The Code is required
                                                </div>
                                            )}
                                        {errors.two_fa_code &&
                                            (errors.two_fa_code.type ===
                                                "minLength" ||
                                                errors.two_fa_code.type ===
                                                    "maxLength") && (
                                                <div className="invalid-feedback d-block">
                                                    The Code must contain 6
                                                    characters
                                                </div>
                                            )}
                                        {apiErrors.two_fa_code && (
                                            <div className="invalid-feedback d-block">
                                                {apiErrors.two_fa_code}
                                            </div>
                                        )}
                                        {errorMessage && (
                                            <div className="invalid-feedback d-block">
                                                {errorMessage}
                                            </div>
                                        )}
                                    </span>
                                </div>

                                <div className="mb-0 text-center">
                                    <LoadingButton
                                        id="resetpw-btn"
                                        className="btn btn-primary w-100"
                                        type="submit"
                                        loading={isFormSubmitting}
                                        disabled={isFormSubmitting}
                                    >
                                        Log In
                                    </LoadingButton>
                                </div>
                            </form>
                        </div>
                        {/* <!-- end card-body --> */}
                    </div>
                    {/* <!-- end card --> */}

                    <ResetMFA email={email}></ResetMFA>
                </div>
                {/* <!-- end col --> */}
            </div>
            {/* <!-- end row --> */}
        </AuthLayout>
    );
}
