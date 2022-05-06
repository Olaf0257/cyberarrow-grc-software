import React, { useState } from "react";

import Slider from "rc-slider";
import Select from "react-select";

import { useForm, usePage } from "@inertiajs/inertia-react";
import LoadingButton from "../../../../common/loading-button/LoadingButton";

import 'rc-slider/assets/index.css';
import {showToastMessage} from "../../../../utils/toast-message";

const RiskItemDetails = ({
    risk,
    removeRiskFromTable,
    primaryFilters,
    onUpdateCategoryRisksCount,
    handleUpdateRiskStatus,
}) => {
    const [status, setStatus] = useState(risk.status);
    const [inherentScore, setInherentScore] = useState(risk.inherent_score);
    const [residualScore, setResidualScore] = useState(risk.residual_score);
    const [inherentRiskScoreLevel, setInherentRiskScoreLevel] = useState(
        risk.InherentRiskScoreLevel
    );
    const [residualRiskScoreLevel, setResidualRiskScoreLevel] = useState(
        risk.ResidualRiskScoreLevel
    );

    const {
        risksAffectedProperties,
        riskMatrixImpacts,
        riskMatrixLikelihoods,
        riskMatrixScores,
        riskScoreActiveLevelType,
    } = usePage().props;
    const risksAffectedPropertiesSelectOptions = [
        {
            label: "Common",
            options: risksAffectedProperties.common.map((c) => ({
                label: c,
                value: c,
            })),
        },
        {
            label: "Other",
            options: risksAffectedProperties.other.map((o) => ({
                label: o,
                value: o,
            })),
        },
    ];

    const { processing, data, errors, setData, post } = useForm({
        treatment_options: risk.treatment_options,
        affected_functions_or_assets: risk.affected_functions_or_assets,
        affected_properties: risk.affected_properties,
        impact: risk.impact - 1,
        likelihood: risk.likelihood - 1,
    });

    const handleSubmit = () => post(route('risks.register.risks-update-react', risk.id), {
        preserveScroll: true,
        onSuccess: () => {
            // display toast message
            showToastMessage('Risk updated successfully!','success');
            // if we only filtered by incomplete, after save the risk should be removed
            // since we only need the incomplete ones
            if (primaryFilters.only_incomplete) return removeRiskFromTable(risk.id);
            // we're not filtering by incomplete, so now if it's incomplete, we should offset the
            // incomplete *only* by 1
            if (!risk.is_complete) {
                onUpdateCategoryRisksCount(risk.category_id, 0, 1);
                handleUpdateRiskStatus(risk.id, 1);
            }
            setStatus(data.treatment_options === 'Accept' ? 'Close' : 'Open');
        }
    });

    const handleAffectedPropertiesSelect = (values) =>
        setData(
            "affected_properties",
            [...values.map((v) => v.value)].join(",")
        );
    const handleSliderChange = (type) => (value) => {
        setData(type, value);
        const scores = riskMatrixScores.flat();
        const maxScore = Math.max.apply(
            Math,
            scores.map((s) => s.score)
        );
        const { score: riskScore } = scores.find(
            (s) =>
                s.likelihood_index ===
                    (type === "likelihood" ? value : data.likelihood) &&
                s.impact_index === (type === "impact" ? value : data.impact)
        );
        const scoreLevel = riskScoreActiveLevelType.levels.find(
            (level, key) => {
                let index = parseInt(key);
                let lastIndex = riskScoreActiveLevelType.levels.length - 1;
                let startScore =
                    index === 0
                        ? 1
                        : riskScoreActiveLevelType.levels[index - 1][
                              "max_score"
                          ] + 1;
                let endScore =
                    index === lastIndex ? maxScore : level["max_score"];

                /* Giving matrix cell color if it falls within the range */
                return riskScore >= startScore && riskScore <= endScore;
            }
        );

        setInherentRiskScoreLevel({
            ...inherentRiskScoreLevel,
            color: scoreLevel.color,
            name: scoreLevel.name,
        });
        setResidualRiskScoreLevel({
            ...residualRiskScoreLevel,
            color: scoreLevel.color,
            name: scoreLevel.name,
        });
        setInherentScore(riskScore);
        setResidualScore(riskScore);
    };

    return (
        <div className="risk-item-expand p-3 m-2 border">
            <div className="row">
                <div className="col-xl-12 col-lg-12 col-md-12 col-sm-11 col-10">
                    <div className="expanded__box-description">
                        <h4 className="">Description:</h4>
                        <p className="m-0 p-0">{risk.risk_description}</p>
                    </div>
                </div>

                <div className="col-xl-8 col-lg-8 col-md-8 col-sm-6 col-10">
                    <div className="slider-div py-3">
                        <div className="slider__1">
                            <span>
                                <h5 className="m-0 p-0">Likelihood:</h5>
                            </span>
                            <Slider
                                defaultValue={data.likelihood}
                                marks={riskMatrixLikelihoods}
                                max={riskMatrixLikelihoods.length - 1}
                                onChange={handleSliderChange("likelihood")}
                                tooltipVisible={false}
                            />
                        </div>

                        <div
                            className="slider__2 pt-2"
                            style={{ marginTop: "15px" }}
                        >
                            <h5 className="m-0">Impact:</h5>
                            <Slider
                                defaultValue={data.impact}
                                marks={riskMatrixImpacts}
                                max={riskMatrixImpacts.length - 1}
                                onChange={handleSliderChange("impact")}
                                tooltipVisible={false}
                            />
                        </div>
                    </div>

                    <div className="mb-3">
                        <label htmlFor="affected-props" className="text-dark">
                            Affected property(ies):
                        </label>
                        <Select
                            className="react-select"
                            classNamePrefix="react-select"
                            isMulti
                            defaultValue={data.affected_properties
                                .split(",")
                                .map((p) => ({ label: p, value: p }))}
                            options={risksAffectedPropertiesSelectOptions}
                            onChange={handleAffectedPropertiesSelect}
                            closeMenuOnSelect={false}
                        />
                        {errors.affected_properties && (
                            <div className="invalid-feedback d-block">
                                <span>{errors.affected_properties}</span>
                            </div>
                        )}
                    </div>

                    <div className="mb-3">
                        <label
                            htmlFor="risk-treatment"
                            className="text-dark form-label"
                        >
                            Risk Treatment:
                        </label>
                        <select
                            name="treatment_options"
                            className="selectpicker form-control cursor-pointer"
                            value={data.treatment_options}
                            onChange={(e) =>
                                setData("treatment_options", e.target.value)
                            }
                            data-style="btn-light"
                        >
                            <option value="Mitigate">Mitigate</option>
                            <option value="Accept">Accept</option>
                        </select>
                    </div>

                    <div className="mb-3">
                        <label
                            htmlFor="risk-treatment"
                            className="text-dark form-label"
                        >
                            Affected function/asset:
                        </label>
                        <input
                            type="text"
                            className="form-control"
                            name="affected_functions_or_assets"
                            value={data.affected_functions_or_assets}
                            onChange={(e) =>
                                setData(
                                    "affected_functions_or_assets",
                                    e.target.value
                                )
                            }
                        />
                        {errors.affected_functions_or_assets && (
                            <div className="invalid-feedback d-block">
                                <span>
                                    {errors.affected_functions_or_assets}
                                </span>
                            </div>
                        )}
                    </div>
                </div>

                <div className="risk-score-container col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
                    {/* <!-- risk score section --> */}
                    <div className="risk-score">
                        <div className="riskscore mt-2">
                            <h4>
                                Inherent Risk Score:
                                <br />
                                <div className="riskscore-value">
                                    <span>{inherentScore}</span>
                                    <span
                                        className="risk-score-tag ms-2 font-xs"
                                        style={{
                                            color: inherentRiskScoreLevel.color,
                                        }}
                                    >
                                        {inherentRiskScoreLevel.name}
                                    </span>
                                </div>
                            </h4>
                        </div>

                        <div className="res-riskscore mt-3">
                            <h4>
                                Residual Risk Score:
                                <br />
                                <div className="riskscore-value">
                                    <span>{residualScore}</span>

                                    <span
                                        className="risk-score-tag ms-2 font-xs"
                                        style={{
                                            color: residualRiskScoreLevel.color,
                                        }}
                                    >
                                        {residualRiskScoreLevel.name}
                                    </span>
                                </div>
                            </h4>
                        </div>
                        {/* <!-- risk status --> */}
                        <div className="mt-3">
                            <h4>
                                Status:
                                {status === "Close" ? (
                                    <span
                                        className="risk-score-tag low ms-2 font-xs"
                                        id="risk_status"
                                    >
                                        Closed
                                    </span>
                                ) : (
                                    <span
                                        className="risk-score-tag extreme ms-2 font-xs"
                                        id="risk_status"
                                    >
                                        Open
                                    </span>
                                )}
                            </h4>
                        </div>
                    </div>
                </div>

                <div className="col-12">
                    <LoadingButton
                        className="btn btn-primary waves-effect waves-light"
                        onClick={handleSubmit}
                        loading={processing}
                    >
                        Save
                    </LoadingButton>
                </div>
            </div>
        </div>
    );
};

export default RiskItemDetails;
