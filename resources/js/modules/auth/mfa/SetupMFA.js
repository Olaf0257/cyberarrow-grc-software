import React, { useState, useEffect } from "react";
import { useForm } from "react-hook-form";
import AuthLayout from "../../../layouts/auth-layout/AuthLayout";
import { Inertia } from "@inertiajs/inertia";

import "./setup-mfa.scss";

export default function SetupMFA(props) {
  document.title = "Setup Multi Factor Authentication";

  const { QRCode, secretToken } = props;

  const propsData = { props };
  const [apiMFAErrorMessages, setApiMFAErrorMessages] = useState("");
  const apiErrors = propsData.props.errors ? propsData.props.errors : null;
  const errorMessage = propsData.props.flash
    ? propsData.props.flash.error
    : null;
  const {
    register,
    formState: { errors },
    handleSubmit,
    getValues,
  } = useForm({
    mode: "onChange",
  });

  // const { data, setData, post, processing } = useInertiaForm({
  //     two_fa_code: '',
  //     email: '',
  //     password: '',
  //     remember: '',
  // })

  // Submit Enable MFA Login
  const onEnableMFALogin = (data) => {
    const formData = getValues();
    axiosFetch
      .post(route("validate-mfa-code"), formData)
      .then((res) => {
        if (res.data) {
          Inertia.post(route("confirm-mfa"), formData);
        } else {
          setApiMFAErrorMessages("The Code is invalid or expired!");
        }
      })
      .catch(function (e) {
        AlertBox({
          text: res.data,
          confirmButtonColor: "#b2dd4c",
          icon: 'success',
        });
      });
  };

  return (
    <AuthLayout>
      <div className="row" id="setupMFA">
        <div className="col-xl-12">
          <div className="card">
            <div className="card-body project-box">
              <div className="top-text">
                <h4>Setup MFA for your Account</h4>
                <div className="instruction__box my-2">
                  <p className="text-white">
                    In order to use Multi Factor Authentication, you will need
                    to install an authenticator application such as 'Google
                    Authenticator'.
                  </p>
                </div>
                <h5 className="text-dark my-3">
                  Secret Token:
                  <span className="text-muted token" id="secret-token-wp">
                    {secretToken}
                  </span>
                </h5>
              </div>

              <div className="qrcode-box">
                <div className="row">
                  <div className="col-xl-5 col-lg-5 col-md-5 col-sm-6 col-12">
                    <div
                      dangerouslySetInnerHTML={{
                        __html: QRCode,
                      }}
                      className="qcode-left"
                      id="mfa-qrcode-wp"
                    ></div>
                  </div>

                  <div className="col-xl-7 col-lg-7 col-md-7 col-sm-6 col-12">
                    <div className="qrcode-right">
                      <p className="text-dark qrcode-right-text pt-1">
                        Scan the barcode or type out the token to add the token
                        to the authenticator.
                      </p>
                      <p className="text-dark qrcode-right-text">
                        A new token will be generated everytime you refresh or
                        disable/enable MFA.
                      </p>
                      <p className="text-dark mb-2 qrcode-right-text">
                        Please enter the first code that shows in the
                        authenticator.
                      </p>

                      <form
                        onSubmit={handleSubmit(onEnableMFALogin)}
                        id="set-up-mfa"
                      >
                        <div className="row mb-3">
                          <div className="col-lg-7 col-md-7 col-sm-9">
                            <input
                              type="text"
                              {...register("two_factor_code", {
                                required: true,
                                maxLength: 6,
                                minLength: 6,
                              })}
                              className="2fa__code form-control"
                              id="two_factor_code"
                              name="two_factor_code"
                              placeholder="123456"
                            />
                            <input
                              type="hidden"
                              {...register("during_login", {
                                value: true,
                              })}
                            />
                            {errors.two_factor_code &&
                              errors.two_factor_code.type === "required" && (
                                <div className="invalid-feedback d-block">
                                  The Code field is required
                                </div>
                              )}
                            {errors.two_factor_code &&
                              errors.two_factor_code.type === "maxLength" && (
                                <div className="invalid-feedback d-block">
                                  The Code may not be greater than 6 characters
                                </div>
                              )}
                            {errors.two_factor_code &&
                              errors.two_factor_code.type === "minLength" && (
                                <div className="invalid-feedback d-block">
                                  The Code may not be less than 6 characters
                                </div>
                              )}
                            {apiMFAErrorMessages && (
                              <div className="invalid-feedback d-block">
                                {apiMFAErrorMessages}
                              </div>
                            )}
                            {/* <input type="text" name="2fa_code" id="2fa_code" className="2fa__code form-control @if($errors->first('2fa_code')) is-invalid @endif" placeholder="123456" /> */}
                          </div>
                        </div>

                        <button
                          type="submit"
                          className="btn btn-primary enable__btn mt-1"
                        >
                          Enable Secure MFA Login
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {/* <!-- end card --> */}
        </div>
        {/* <!-- end col --> */}
      </div>
      {/* <!-- end row --> */}
    </AuthLayout>
  );
}
