import React, { Fragment, useEffect, useState } from "react";
import RiskItemSection from "./RiskItemSection";
import Accordion from "react-bootstrap/Accordion";
import { useSelector } from "react-redux";

function RiskByCategorySelection(props) {
    const [riskCategories, setRiskCategories] = useState([]);
    const [filteredData, setFilteredData] = useState([]);
    const [risksAffectedProperties, setRisksAffectedProperties] = useState([]);
    const [
        risksAffectedPropertiesSelectOptions,
        setRisksAffectedPropertiesSelectOptions,
    ] = useState([]);
    const [riskMatrixLikelihoods, setRiskMatrixLikelihoods] = useState([]);
    const [riskMatrixImpacts, setRiskMatrixImpacts] = useState([]);
    const [riskMatrixScores, setRiskMatrixScores] = useState([]);
    const [riskScoreActiveLevelType, setRiskScoreActiveLevelType] = useState(
        []
    );
    const [Loading, setLoading] = useState([]);
    const [riskFilterSecond, setRiskFilterSecond] = useState([]);

    const appDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope.value
    );

    /* Fetch risk register data on component load */
    useEffect(async () => {
        setLoading(true);
        loadRiskCategories();
    }, [appDataScope]);

    useEffect(async () => {
        // search risk filter
        if (props.risk_filter.type) {
            const newData = { ...riskCategories };
            let data_filtered = [];
            for (const [key, value] of Object.entries(newData)) {
                let filtered_risk = value.register_risks;
                if (props.risk_filter.search_value.length > 2) {
                    filtered_risk = filtered_risk.filter(function (risk) {
                        // changing both param to lowercase check if name inculdes fiter key
                        return risk.name
                            .toLowerCase()
                            .includes(
                                props.risk_filter.search_value.toLowerCase()
                            );
                    });
                }
                if (props.risk_filter.checkbox_value) {
                    filtered_risk = filtered_risk.filter(function (risk) {
                        // changing both param to lowercase check if name inculdes fiter key
                        return risk.is_updated == 0;
                    });
                }
                if (filtered_risk.length > 0) {
                    data_filtered[key] = Object.assign({}, value);
                    data_filtered[key].register_risks = filtered_risk;
                }
            }
            data_filtered = data_filtered.filter(function (element) {
                return element !== undefined;
            });
            setFilteredData(data_filtered);
        } else {
            setFilteredData(riskCategories);
        }
        // search risk filter end
    }, [props]);

    const loadRiskCategories = async () => {
        let response = await axiosFetch.get("risks/risks-register-react", {
            params: {
                data_scope: appDataScope,
            },
        });

        let resData = response.data;
        if (resData.success) {
            setLoading(false);
            let data = resData.data;
            setRiskCategories(data.riskCategories);
            setFilteredData(data.riskCategories);
            setRisksAffectedProperties(data.risksAffectedProperties);
            setRiskMatrixLikelihoods(data.riskMatrixLikelihoods);
            setRiskMatrixImpacts(data.riskMatrixImpacts);
            setRiskMatrixScores(data.riskMatrixScores);
            setRiskScoreActiveLevelType(data.riskScoreActiveLevelType);

            // making options object for select
            let affected_prop_select_options = [];
            let common_options = [];
            let other_options = [];
            for (
                let i = 0;
                i < data.risksAffectedProperties.common.length;
                i++
            ) {
                const value = data.risksAffectedProperties.common[i];
                common_options.push({ value: value, label: value });
            }
            for (
                let i = 0;
                i < data.risksAffectedProperties.other.length;
                i++
            ) {
                const value = data.risksAffectedProperties.other[i];
                other_options.push({ value: value, label: value });
            }
            affected_prop_select_options.push({
                label: "Common",
                options: common_options,
            });
            affected_prop_select_options.push({
                label: "Other",
                options: other_options,
            });
            setRisksAffectedPropertiesSelectOptions(
                affected_prop_select_options
            );
            // end making options object for select
        }
    };

    const handleSecondSearchChange = (category_id) => (event) => {
        setRiskFilterSecond({
            value: event.target.value,
            category_id: category_id,
        });
    };

    const handleDeleteRender = (child_data, category_id) => {
        if (child_data.length > 0) {
            const category = (element) =>
                element.id > child_data[0].category_id;
            let index = filteredData.findIndex(category);
            filteredData[index].register_risks = child_data;
            let new_filtered_data = filteredData.filter(
                (value) => Object.keys(value).length !== 0
            );
            setFilteredData(new_filtered_data);
        } else {
            const category = (element) => element.id > category_id;
            let index = filteredData.findIndex(category);
            delete filteredData[index];
            let new_filtered_data = filteredData.filter(
                (value) => Object.keys(value).length !== 0
            );
            setFilteredData(new_filtered_data);
        }
    };

    const toggleCategoryAcoordionActive = (index) => (event) => {
        filteredData[index].accordionActive =
            !filteredData[index].accordionActive;
        setFilteredData(filteredData);
        setLoading(!Loading);
    };

    return (
        <Fragment>
            {filteredData.map(function (category, index) {
                return (
                    <Fragment key={index}>
                        <Accordion defaultActiveKey="0">
                            <div className="risk__one riskbox d-flex align-items-center">
                                <Accordion.Toggle
                                    as="div"
                                    variant="div"
                                    onClick={toggleCategoryAcoordionActive(
                                        index
                                    )}
                                    eventKey={"risk-category-wp_" + category.id}
                                >
                                    <div className="icon-box d-flex align-items-center">
                                        <a
                                            data-toggle="collapse"
                                            href={"#risk-" + category.id}
                                            data-id={category.id}
                                            aria-expanded="false"
                                            aria-controls="collapseExample"
                                            className="expandable-icon-wp risk-category"
                                        >
                                            <i
                                                className={
                                                    category.accordionActive
                                                        ? "icon fas fa-chevron-down expand-icon-w"
                                                        : "icon fas fa-chevron-right expand-icon-w"
                                                }
                                            />
                                            <h5 className="ms-2 risk-register-title">
                                                {decodeHTMLEntity(
                                                    category.name
                                                )}
                                            </h5>
                                        </a>
                                    </div>
                                </Accordion.Toggle>
                                <div className="items__num ms-auto pt-3">
                                    <p>
                                        {
                                            category.register_risks.filter(
                                                function (el) {
                                                    return el;
                                                }
                                            ).length
                                        }{" "}
                                        item(s)
                                        <sup
                                            id={
                                                "un-updated-risks-" +
                                                category.id
                                            }
                                        >
                                            <span className="alert-pill badge bg-danger rounded-pill">
                                                {
                                                    category.register_risks.filter(
                                                        function (el) {
                                                            return (
                                                                el.is_updated ==
                                                                0
                                                            );
                                                        }
                                                    ).length
                                                }
                                            </span>
                                        </sup>
                                    </p>
                                </div>
                            </div>
                            {/* display on toggle */}
                            <Accordion.Collapse
                                eventKey={"risk-category-wp_" + category.id}
                            >
                                <div className="risk__one-descrip">
                                    <div className="top__text d-flex p-2">
                                        <h5>Search Risk Items</h5>
                                        <div className="searchbox animated zoomIn ms-auto">
                                            <form method="get">
                                                <input
                                                    type="text"
                                                    placeholder="Search by Risk Name"
                                                    name="risk_name_search_within_category_query"
                                                    onKeyUp={handleSecondSearchChange(
                                                        category.id
                                                    )}
                                                    className="search"
                                                    data-category-id={
                                                        category.id
                                                    }
                                                />
                                                <i className="fas fa-search" />
                                            </form>
                                        </div>
                                    </div>
                                    {/* risk table */}
                                    <div className="risk__table border mb-1">
                                        <Accordion defaultActiveKey="0">
                                            {/* risk item selection component*/}
                                            <table className="table risk-register-table dt-responsive">
                                                <thead className="table-light">
                                                    <tr>
                                                        <th className="risk__id-width">
                                                            {" "}
                                                            Risk ID{" "}
                                                        </th>
                                                        <th> Risk Name </th>
                                                        <th className="hide-on-sm hide-on-xs">
                                                            {" "}
                                                            Control{" "}
                                                        </th>
                                                        <th className="hide-on-xs">
                                                            Likelihood
                                                        </th>
                                                        <th className="hide-on-xs hide-on-sm">
                                                            {" "}
                                                            Impact
                                                        </th>
                                                        <th className="hide-on-xs hide-on-sm">
                                                            {" "}
                                                            Inherent Score{" "}
                                                        </th>
                                                        <th className="hide-on-sm hide-on-xs">
                                                            {" "}
                                                            Residual Score{" "}
                                                        </th>
                                                        <th> Action </th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    id={
                                                        "risk-items-wp-" +
                                                        category.id
                                                    }
                                                >
                                                    <RiskItemSection
                                                        handleDeleteRender={
                                                            handleDeleteRender
                                                        }
                                                        risks={
                                                            category.register_risks
                                                        }
                                                        category={category}
                                                        risk_affected_properties={
                                                            risksAffectedProperties
                                                        }
                                                        risk_affected_properties_select_options={
                                                            risksAffectedPropertiesSelectOptions
                                                        }
                                                        risk_matrix_likelihoods={
                                                            riskMatrixLikelihoods
                                                        }
                                                        risk_matrix_impacts={
                                                            riskMatrixImpacts
                                                        }
                                                        risk_matrix_scores={
                                                            riskMatrixScores
                                                        }
                                                        risk_score_active_level_type={
                                                            riskScoreActiveLevelType
                                                        }
                                                        filter={
                                                            riskFilterSecond
                                                        }
                                                    ></RiskItemSection>
                                                </tbody>
                                            </table>
                                        </Accordion>
                                    </div>
                                    {/* risk table ends */}
                                </div>
                            </Accordion.Collapse>
                            {/* display on toggle ends */}
                        </Accordion>
                    </Fragment>
                );
            })}
            {/*bottom box */}
            {riskCategories.length == 0 && !Loading ? (
                <p className="empty-data-section"> No records found</p>
            ) : (
                ""
            )}
        </Fragment>
    );
}

export default RiskByCategorySelection;
