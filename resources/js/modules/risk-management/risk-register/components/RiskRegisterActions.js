import React, { useState } from "react";

import { InertiaLink } from "@inertiajs/inertia-react";
import { useDispatch, useSelector } from "react-redux";
import fileDownload from "js-file-download";

import NProgress from "nprogress";
import moment from "moment/moment";

const RiskRegisterActions = () => {
    const dispatch = useDispatch();

    const appDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope.value
    );

    const handleExport = () => {
        dispatch({ type: "reportGenerateLoader/show" });
        axiosFetch
            .get(route("risks.register.risks-export"), {
                responseType: "blob",
                params: {
                    data_scope: appDataScope,
                },
            })
            .then((res) => {
                fileDownload(
                    res.data,
                    `Risk Register ${moment().format("DD-MM-YYYY")}.xlsx`
                );
            })
            .finally(() => {
                dispatch({ type: "reportGenerateLoader/hide" });
            });
    };

    return (
        <div className="top__box">
            <div className="top__box-btn mb-2 pb-2">
                <button
                    onClick={handleExport}
                    className="btn btn-primary export__risk-btn"
                >
                    Export Risks
                </button>
                <InertiaLink
                    href={route("risks.register.risks-create")}
                    className="btn btn-primary add__risk-btn mx-md-2"
                >
                    Add New Risks
                </InertiaLink>
            </div>
        </div>
    );
};

export default RiskRegisterActions;
