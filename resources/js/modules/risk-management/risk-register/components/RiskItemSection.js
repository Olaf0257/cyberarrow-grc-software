import React, { Fragment, useEffect, useState, useRef, createRef } from "react";
import Accordion from "react-bootstrap/Accordion";
import Pagination from "react-bootstrap/Pagination";
import Dropdown from "react-bootstrap/Dropdown";
import Slider from "rc-slider";
import Select from "react-select";
import LoadingButton from "../../../../common/loading-button/LoadingButton";

import "rc-slider/assets/index.css";

// import IonRangeSlider from 'react-ion-slider';
// import { Select , Slider } from 'antd';

function RiskItemSection(props) {
    const [risks, setRisks] = useState([]);
    const [category, setCategory] = useState([]);
    const [sliderLikelihoodsSliderValues, setSliderLikelihoodsSliderValues] =
        useState([]);
    const [sliderImpactSliderValues, setSliderImpactSliderValues] = useState(
        []
    );
    const [risksAffectedProperties, setRisksAffectedProperties] = useState([]);
    const [
        risksAffectedPropertiesSelectOptions,
        setRisksAffectedPropertiesSelectOptions,
    ] = useState([]);
    const [matrixScores, setMatrixScores] = useState([]);
    const [matrixScoreLevels, setMatrixScoreLevels] = useState([]);
    const [paginationItems, setPaginationItems] = useState([]);
    const [isFormSubmitting, setIsFormSubmitting] = useState(false);
    const [isActiveLoading, setIsActiveLoading] = useState(false);
    const [formErrors, setFormErrors] = useState(false);

    // const inherentScoreSpan = useRef();
    const inherentElementsRef = useRef(props.risks.map(() => createRef()));
    const residualElementsRef = useRef(props.risks.map(() => createRef()));

    useEffect(async () => {
        if (props.filter.value && category.id === props.filter.category_id) {
            let filtered_risks = [];
            for (const [key, value] of Object.entries(props.risks)) {
                let search_key = props.filter.value;
                if (value.name.toLowerCase().includes(search_key.toLowerCase()))
                    filtered_risks.push(value);
            }
            setRisks(filtered_risks);
            setCategory(props.category);
            renderPagination(filtered_risks);
        } else {
            setRisks(props.risks);
            setCategory(props.category);
            renderPagination(props.risks);
        }

        if (props.risk_matrix_scores) {
            setMatrixScores(props.risk_matrix_scores);
        }

        if (props.risk_score_active_level_type) {
            setMatrixScoreLevels(props.risk_score_active_level_type);
        }

        // likelihood slider
        if (props.risk_matrix_likelihoods) {
            let sliderLikelihoods = [];
            let likelihoods = props.risk_matrix_likelihoods;
            for (const key in likelihoods) {
                if (Object.hasOwnProperty.call(likelihoods, key)) {
                    const likelihood = likelihoods[key];
                    sliderLikelihoods.push(likelihood.name);
                }
            }
            setSliderLikelihoodsSliderValues(sliderLikelihoods);
        }
        // likelihood slider

        // impact slider
        if (props.risk_matrix_impacts) {
            let sliderImpact = [];
            let impact = props.risk_matrix_impacts;
            for (const key in impact) {
                if (Object.hasOwnProperty.call(impact, key)) {
                    const impacts = impact[key];
                    sliderImpact.push(impacts.name);
                }
            }
            setSliderImpactSliderValues(sliderImpact);
        }
        // impact slider

        if (props.risk_affected_properties) {
            setRisksAffectedProperties(props.risk_affected_properties);
        }
        if (props.risk_affected_properties_select_options) {
            setRisksAffectedPropertiesSelectOptions(
                props.risk_affected_properties_select_options
            );
        }
    }, [props]);

    const handleDeleteParentRender = (data) => {
        props.handleDeleteRender(data, category.id);
    };

    const renderPagination = (data) => {
        let pagination = [];
        let currentPage = 1;
        let risksPerPage = 5;
        let indexOfLastRisk = currentPage * risksPerPage;
        let indexOfFirstRisk = indexOfLastRisk - risksPerPage;
        let currentRisks = data.slice(indexOfFirstRisk, indexOfLastRisk);

        let paginationDiv = [];
        let active = 1;
        for (let i = 1; i <= Math.ceil(data.length / risksPerPage); i++) {
            paginationDiv.push(
                <Pagination.Item
                    key={i}
                    active={i === active}
                    onClick={handlePagitaionClick}
                >
                    {i}
                </Pagination.Item>
            );
        }
        pagination.push({
            currentPage,
            risksPerPage,
            indexOfLastRisk,
            indexOfFirstRisk,
            currentRisks,
            paginationDiv,
        });
        setPaginationItems(pagination);
    };

    const handlePagitaionClick = (event) => {
        let pagination = [];
        let currentPage = 1;
        if (event.target) currentPage = parseInt(event.target.text);
        else currentPage = parseInt(event);
        let risksPerPage = 5;
        let indexOfLastRisk = currentPage * risksPerPage;
        let indexOfFirstRisk = indexOfLastRisk - risksPerPage;
        let currentRisks = risks.slice(indexOfFirstRisk, indexOfLastRisk);

        let paginationDiv = [];
        let active = currentPage;
        for (let i = 1; i <= Math.ceil(risks.length / risksPerPage); i++) {
            paginationDiv.push(
                <Pagination.Item
                    key={i}
                    active={i === active}
                    onClick={handlePagitaionClick}
                >
                    {i}
                </Pagination.Item>
            );
        }
        pagination.push({
            currentPage,
            risksPerPage,
            indexOfLastRisk,
            indexOfFirstRisk,
            currentRisks,
            paginationDiv,
        });
        setPaginationItems(pagination);
    };

    const handleAffectedPropertiesChange = (index) => (value) => {
        var values = value.map(function (value) {
            return value.value;
        });
        if (value.length != 0) risks[index].affected_properties = values.join();
        else risks[index].affected_properties = null;
        setRisks(risks);
    };

    const AffectedPropertiesSelect = (select_props) => {
        let defaulValues = [];
        if (select_props.affected_properties) {
            let values = select_props.affected_properties.split(",");
            for (var key in values) {
                defaulValues.push({ value: values[key], label: values[key] });
            }
        }

        return (
            <Select
                isMulti
                defaultValue={defaulValues}
                onChange={handleAffectedPropertiesChange(select_props.index)}
                options={risksAffectedPropertiesSelectOptions}
            />
        );
    };

    const handleTreatmentOptionchange = (index) => (event) => {
        risks[index].treatment_options = event.target.value;
        setRisks(risks);
    };

    const handleAffectedFunctionsChange = (index) => (event) => {
        risks[index].affected_functions_or_assets = event.target.value;
        setRisks(risks);
    };

    const handleSliderChange = (index, type) => (value) => {
        if (type == "impact") risks[index].impact = value;
        else risks[index].likelihood = value;
        setRisks(risks);

        const riskScores = [].concat.apply([], matrixScores);
        const maxScore = Math.max.apply(
            Math,
            riskScores.map(function (o) {
                return o.score;
            })
        );
        const scoreLevels = matrixScoreLevels;
        /* Finding the risk score matching the likelihood and impact */
        let targetScore = riskScores.find((score) => {
            return (
                score.likelihood_index == risks[index].likelihood &&
                score.impact_index == risks[index].impact
            );
        });
        const riskScore = targetScore.score;

        /* finding the color for the score*/
        let scoreLevel = scoreLevels.levels.find((level, key) => {
            let index = parseInt(key);
            let lastIndex = scoreLevels.levels.length - 1;
            let startScore =
                index == 0 ? 1 : scoreLevels.levels[index - 1]["max_score"] + 1;
            let endScore = index == lastIndex ? maxScore : level["max_score"];

            /* Giving matrix cell color if it falls within the range */
            return riskScore >= startScore && riskScore <= endScore;
        });
        risks[index].InherentRiskScoreLevel.name = scoreLevel.name;
        setRisks(risks);
        inherentElementsRef.current[index].current.querySelectorAll(
            "span"
        )[0].innerHTML = riskScore;
        inherentElementsRef.current[index].current.querySelectorAll(
            "span"
        )[1].style.color = scoreLevel.color;
        inherentElementsRef.current[index].current.querySelectorAll(
            "span"
        )[1].innerHTML = scoreLevel.name;

        residualElementsRef.current[index].current.querySelectorAll(
            "span"
        )[0].innerHTML = riskScore;
        residualElementsRef.current[index].current.querySelectorAll(
            "span"
        )[1].style.color = scoreLevel.color;
        residualElementsRef.current[index].current.querySelectorAll(
            "span"
        )[1].innerHTML = scoreLevel.name;
    };

    const handleRiskRegisterUpdate = (index, risk_id) => {
        try {
            if (handleErrors(index)) {
                setIsFormSubmitting(true);
                var url = "risks/risks-register-react/" + risk_id + "/update";
                let data = {
                    affected_properties: risks[index].affected_properties,
                    treatment_options: risks[index].treatment_options,
                    likelihood: risks[index].likelihood,
                    impact: risks[index].impact,
                    affected_functions_or_assets:
                        risks[index].affected_functions_or_assets,
                };

                let httpRes = axiosFetch.post(url, data).then((res) => {
                    setIsFormSubmitting(false);
                });

                let resData = httpRes.data;
            }
        } catch (error) {}
    };

    const handleErrors = (index) => {
        let errors = [];
        let haserrors = false;
        let data = risks;

        if (!risks[index].affected_properties) {
            data[index].affectedPropertiesRequired = true;
            setRisks(data);
            setFormErrors(!formErrors);
            haserrors = true;
        } else {
            data[index].affectedPropertiesRequired = false;
            setRisks(data);
            setFormErrors(!formErrors);
        }

        if (risks[index].affected_functions_or_assets === "") {
            data[index].affectedFunctionRequired = true;
            setRisks(data);
            setFormErrors(!formErrors);
            haserrors = true;
        } else {
            data[index].affectedFunctionRequired = false;
            setRisks(data);
            setFormErrors(!formErrors);
        }
        return !haserrors;
    };

    const handleRiskRegisterDelete = (index, risk_id) => {
        AlertBox(
            {
                title: "Are you sure that you want to delete the risk?",
                text: "This action is irreversible and any mapped controls will be unmapped.",
                showCancelButton: true,
                confirmButtonColor: "#ff0000",
                confirmButtonText: "Yes, delete it!",
                imageUrl: `${appBaseURL}/assets/images/warning.png`,
                imageWidth: 120,
            },
            function (confirmed) {
                if (confirmed.value && confirmed.value == true) {
                    try {
                        var url = "risks/risks-register/" + risk_id + "/delete";
                        axiosFetch.get(url).then((response) => {
                            if (response.data.status === 200) {
                                const newData = risks;
                                let removeIndex = null;
                                for (const [key, value] of Object.entries(
                                    newData
                                )) {
                                    if (value.id === risk_id) {
                                        removeIndex = key;
                                    }
                                }
                                delete newData[removeIndex];
                                let new_risks = newData.filter(
                                    (value) => Object.keys(value).length !== 0
                                );
                                setRisks(new_risks);
                                handleDeleteParentRender(new_risks);
                                renderPagination(new_risks);
                                AlertBox({
                                    text: response.data.message,
                                    confirmButtonColor: "#b2dd4c",
                                    icon: 'success',
                                });
                            }
                        });
                    } catch (error) {
                        console.log(error);
                    }
                }
            }
        );
    };

    const toggleRiskAccordionActive = (index) => (event) => {
        for (const [key, value] of Object.entries(
            paginationItems[0].currentRisks
        )) {
            if (key != index) {
                paginationItems[0].currentRisks[key].accordionActive = false;
            }
        }
        paginationItems[0].currentRisks[index].accordionActive =
            !paginationItems[0].currentRisks[index].accordionActive;
        setPaginationItems(paginationItems);
        setIsActiveLoading(!isActiveLoading);
    };

    return (
        <Fragment>
            {paginationItems.length > 0 ? (
                paginationItems[0].currentRisks.map(function (risk, index) {
                    return (
                        <Fragment key={index}>
                            {/* first risk item  */}
                            <tr key={index} className="risk-table">
                                <Accordion.Toggle
                                    as="td"
                                    onClick={toggleRiskAccordionActive(index)}
                                    eventKey={"expanded__box_" + risk.id}
                                    style={{ width: "10%" }}
                                >
                                    <span className="icon-sec me-2 expandable-icon-wp">
                                        <a
                                            className="link-primary"
                                            className="risk-single-list"
                                            aria-expanded="false"
                                        >
                                            <i
                                                className={
                                                    risk.accordionActive
                                                        ? "icon fas fa-chevron-down me-2 expand-icon-w"
                                                        : "icon fas fa-chevron-right me-2 expand-icon-w"
                                                }
                                            ></i>
                                            {risk.id}
                                        </a>
                                    </span>
                                </Accordion.Toggle>
                                <td style={{ width: "46%" }}>
                                    <a
                                        href={
                                            `${appBaseURL}/risks/risks-register/` +
                                            risk.id +
                                            `/show`
                                        }
                                    >
                                        {" "}
                                        {decodeHTMLEntity(risk.name)}
                                    </a>
                                </td>
                                <td
                                    style={{ width: "5%" }}
                                    className="hide-on-xs hide-on-sm"
                                >
                                    {risk.mapped_controls.length > 0 ? (
                                        <a
                                            href={
                                                `${appBaseURL}/compliance/projects/` +
                                                risk.mapped_controls[0]
                                                    .project_id +
                                                `/controls/` +
                                                risk.mapped_controls[0].id +
                                                `/show/`
                                            }
                                        >
                                            {" "}
                                            {decodeHTMLEntity(
                                                risk.mapped_controls[0]
                                                    .controlId
                                            )}
                                        </a>
                                    ) : (
                                        "None"
                                    )}
                                </td>

                                <td
                                    style={{ width: "10%" }}
                                    className="hide-on-xs inherent-likelihood-td"
                                >
                                    {risk.likelihood}
                                </td>
                                <td
                                    style={{ width: "5%" }}
                                    className="hide-on-xs hide-on-sm inherent-impact-td"
                                >
                                    {risk.impact}
                                </td>
                                <td
                                    style={{ width: "12%" }}
                                    className="hide-on-xs hide-on-sm inherent-score-td"
                                >
                                    {risk.inherent_score}
                                </td>
                                <td
                                    style={{ width: "12%" }}
                                    className="hide-on-xs hide-on-sm residual-score-td"
                                >
                                    {risk.residual_score}
                                </td>
                                <td>
                                    <Dropdown className="btn-group">
                                        <Dropdown.Toggle
                                            variant="secondary"
                                            className="table-action-btn arrow-none btn btn-light btn-sm"
                                            aria-expanded="false"
                                        >
                                            <i className="mdi mdi-dots-horizontal"></i>
                                        </Dropdown.Toggle>

                                        <Dropdown.Menu className="dropdown-menu-end">
                                            <Dropdown.Item
                                                href="#delete"
                                                onClick={() =>
                                                    handleRiskRegisterDelete(
                                                        index,
                                                        risk.id
                                                    )
                                                }
                                            >
                                                <i className="mdi mdi-delete-forever me-2 text-muted font-18 vertical-middle"></i>
                                                Delete
                                            </Dropdown.Item>
                                        </Dropdown.Menu>
                                    </Dropdown>
                                </td>
                            </tr>
                            {/* risk details  */}
                            <tr>
                                <td colSpan="7" width="100%">
                                    <Accordion.Collapse
                                        eventKey={"expanded__box_" + risk.id}
                                    >
                                        <div className="border risk-item-expand p-2">
                                            <div className="row">
                                                <div className="col-xl-12 col-lg-12 col-md-12 col-sm-11 col-10">
                                                    {/*  description box */}
                                                    <div className="expanded__box-description">
                                                        <h4 className="">
                                                            Description:
                                                        </h4>
                                                        <p className="m-0 p-0">
                                                            {decodeHTMLEntity(
                                                                risk.risk_description
                                                            )}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div className="col-xl-8 col-lg-8 col-md-8 col-sm-6 col-12">
                                                    <div className="slider-div py-3">
                                                        <div className="slider__1">
                                                            <span>
                                                                <h5 className="m-0 p-0">
                                                                    Likelihood:
                                                                </h5>
                                                            </span>
                                                            {sliderLikelihoodsSliderValues.length >
                                                            0 ? (
                                                                <Slider
                                                                    defaultValue={
                                                                        risk.likelihood
                                                                    }
                                                                    marks={
                                                                        sliderLikelihoodsSliderValues
                                                                    }
                                                                    max={
                                                                        sliderLikelihoodsSliderValues.length -
                                                                        1
                                                                    }
                                                                    onChange={handleSliderChange(
                                                                        index,
                                                                        "likelihood"
                                                                    )}
                                                                    tooltipVisible={
                                                                        false
                                                                    }
                                                                />
                                                            ) : (
                                                                ""
                                                            )}
                                                        </div>

                                                        <div
                                                            className="slider__2 pt-2"
                                                            style={{
                                                                marginTop:
                                                                    "15px",
                                                            }}
                                                        >
                                                            <h5 className="m-0">
                                                                Impact:
                                                            </h5>
                                                            {sliderImpactSliderValues.length >
                                                            0 ? (
                                                                <Slider
                                                                    defaultValue={
                                                                        risk.impact
                                                                    }
                                                                    marks={
                                                                        sliderImpactSliderValues
                                                                    }
                                                                    max={
                                                                        sliderImpactSliderValues.length -
                                                                        1
                                                                    }
                                                                    onChange={handleSliderChange(
                                                                        index,
                                                                        "impact"
                                                                    )}
                                                                    tooltipVisible={
                                                                        false
                                                                    }
                                                                />
                                                            ) : (
                                                                ""
                                                            )}
                                                        </div>
                                                    </div>

                                                    <div className="mb-3">
                                                        <label
                                                            htmlFor="affected-props"
                                                            className="text-dark form-label"
                                                        >
                                                            Affected
                                                            property(ies):
                                                        </label>
                                                        <AffectedPropertiesSelect
                                                            affected_properties={
                                                                risk.affected_properties
                                                            }
                                                            index={index}
                                                        ></AffectedPropertiesSelect>
                                                        <div className="invalid-feedback d-block">
                                                            {risk.affectedPropertiesRequired ? (
                                                                <span>
                                                                    This field
                                                                    is required
                                                                </span>
                                                            ) : (
                                                                <span>
                                                                    {" "}
                                                                    &nbsp;{" "}
                                                                </span>
                                                            )}
                                                        </div>
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
                                                            defaultValue={
                                                                risk.treatment_options
                                                            }
                                                            onChange={handleTreatmentOptionchange(
                                                                index
                                                            )}
                                                            data-style="btn-light"
                                                        >
                                                            <option value="Mitigate">
                                                                Mitigate
                                                            </option>
                                                            <option value="Accept">
                                                                Accept
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <div className="mb-3">
                                                        <label
                                                            htmlFor="risk-treatment"
                                                            className="text-dark form-label"
                                                        >
                                                            Affected
                                                            function/asset:
                                                        </label>
                                                        <input
                                                            type="text"
                                                            className="form-control"
                                                            name="affected_functions_or_assets"
                                                            defaultValue={
                                                                risk.affected_functions_or_assets
                                                            }
                                                            onChange={handleAffectedFunctionsChange(
                                                                index
                                                            )}
                                                        />
                                                        <div className="invalid-feedback d-block">
                                                            {risk.affectedFunctionRequired ? (
                                                                <span>
                                                                    This field
                                                                    is required
                                                                </span>
                                                            ) : (
                                                                <span>
                                                                    {" "}
                                                                    &nbsp;{" "}
                                                                </span>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="risk-score-container col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
                                                    {/* <!-- risk score section --> */}
                                                    <div className="risk-score">
                                                        <div className="riskscore mt-2">
                                                            <h4>
                                                                Inherent Risk
                                                                Score:
                                                                <br />
                                                                <div
                                                                    className="riskscore-value"
                                                                    ref={
                                                                        inherentElementsRef
                                                                            .current[
                                                                            index
                                                                        ]
                                                                    }
                                                                >
                                                                    <span
                                                                        id={
                                                                            "risk_inherent_score_" +
                                                                            risk.id
                                                                        }
                                                                    >
                                                                        {
                                                                            risk.inherent_score
                                                                        }
                                                                    </span>
                                                                    <span
                                                                        className="risk-score-tag ms-2 font-xs"
                                                                        id={
                                                                            "risk_inherent_level_" +
                                                                            risk.id
                                                                        }
                                                                        style={{
                                                                            color: risk
                                                                                .InherentRiskScoreLevel
                                                                                .color,
                                                                        }}
                                                                    >
                                                                        {
                                                                            risk
                                                                                .InherentRiskScoreLevel
                                                                                .name
                                                                        }
                                                                    </span>
                                                                </div>
                                                            </h4>
                                                        </div>

                                                        <div className="res-riskscore mt-3">
                                                            <h4>
                                                                Residual Risk
                                                                Score:
                                                                <br />
                                                                <div
                                                                    className="riskscore-value"
                                                                    ref={
                                                                        residualElementsRef
                                                                            .current[
                                                                            index
                                                                        ]
                                                                    }
                                                                >
                                                                    <span
                                                                        id={
                                                                            "risk_residual_score_" +
                                                                            risk.id
                                                                        }
                                                                    >
                                                                        {
                                                                            risk.residual_score
                                                                        }
                                                                    </span>
                                                                    <span
                                                                        id={
                                                                            "risk_residual_level_" +
                                                                            risk.id
                                                                        }
                                                                        className="risk-score-tag ms-2 font-xs"
                                                                        style={{
                                                                            color: risk
                                                                                .ResidualRiskScoreLevel
                                                                                .color,
                                                                        }}
                                                                    >
                                                                        {
                                                                            risk
                                                                                .ResidualRiskScoreLevel
                                                                                .name
                                                                        }
                                                                    </span>
                                                                </div>
                                                            </h4>
                                                        </div>
                                                        {/* <!-- risk status --> */}
                                                        <div className="mt-3">
                                                            <h4>
                                                                Status:
                                                                {risk.status ==
                                                                "Close" ? (
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
                                                    {/* <button className="btn btn-primary" onClick={() =>handleRiskRegisterUpdate(index, risk.id)}>Save</button */}
                                                    <LoadingButton
                                                        className="btn btn-primary waves-effect waves-light"
                                                        onClick={() =>
                                                            handleRiskRegisterUpdate(
                                                                index,
                                                                risk.id
                                                            )
                                                        }
                                                        loading={
                                                            isFormSubmitting
                                                        }
                                                    >
                                                        Save
                                                    </LoadingButton>
                                                </div>
                                            </div>
                                        </div>
                                    </Accordion.Collapse>
                                </td>
                            </tr>
                            {/* risk details ends */}
                        </Fragment>
                    );
                })
            ) : (
                <tr></tr>
            )}
            {paginationItems.length > 0 && risks.length > 5 ? (
                <tr className="risks-pagination-wp">
                    <td colSpan="7">
                        <Pagination>
                            <Pagination.Prev
                                disabled={paginationItems[0].currentPage == 1}
                                onClick={() =>
                                    handlePagitaionClick(
                                        paginationItems[0].currentPage - 1
                                    )
                                }
                            />
                            {paginationItems[0].paginationDiv}
                            <Pagination.Next
                                disabled={
                                    paginationItems[0].currentPage ==
                                    paginationItems[0].paginationDiv.length
                                }
                                onClick={() =>
                                    handlePagitaionClick(
                                        paginationItems[0].currentPage + 1
                                    )
                                }
                            />
                        </Pagination>
                    </td>
                </tr>
            ) : (
                <tr></tr>
            )}
        </Fragment>
    );
}

export default RiskItemSection;
