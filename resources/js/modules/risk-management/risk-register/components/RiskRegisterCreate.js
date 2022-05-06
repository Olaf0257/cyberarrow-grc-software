import React, {Fragment, useState, useEffect, useRef} from 'react';
import {useForm, Controller} from "react-hook-form";
import {useParams} from "react-router-dom";
import {useSelector} from 'react-redux'
import Select from "react-select";
import Slider from 'rc-slider';
import Alert from 'react-bootstrap/Alert'
import {Inertia} from '@inertiajs/inertia';
import {usePage} from '@inertiajs/inertia-react'
import {Link} from '@inertiajs/inertia-react';

import AppLayout from '../../../../layouts/app-layout/AppLayout';
import LoadingButton from '../../../../common/loading-button/LoadingButton';

import '../styles/style.scss'
import '../styles/create-style.css'
import 'rc-slider/assets/index.css';
import FlashMessages from "../../../../common/FlashMessages";
import Breadcrumb from '../../../../common/breadcumb/Breadcumb';

function RiskRegisterCreate(props) {
    const [risk, setRisk] = useState([])
    const [id, setId] = useState()
    const [categorySelectOptions, setCategorySelectOptions] = useState([])
    const [risksAffectedPropertiesSelectOptions, setRisksAffectedPropertiesSelectOptions] = useState([])
    const [sliderLikelihoodsSliderValues, setSliderLikelihoodsSliderValues] = useState([])
    const [sliderImpactSliderValues, setSliderImpactSliderValues] = useState([])
    const [matrixScores, setMatrixScores] = useState([])
    const [matrixScoreLevels, setMatrixScoreLevels] = useState([])
    const [isFormSubmitting, setIsFormSubmitting] = useState(false)
    const {errors: formErrors} = usePage().props;

    const {control, register, handleSubmit, watch, formState: {errors}, reset} = useForm({
        defaultValues: {}
    });

    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value)

    useEffect(() => {
        loadInitialData(props.data, props.id, props.risk);
    }, [appDataScope]);

    const loadInitialData = (data, id, edit_risk) => {
        //category options
        let category_select_options = [];
        for (let i = 0; i < data.riskCategories.length; i++) {
            const value = data.riskCategories[i];
            category_select_options.push({value: value.id, label: value.name});
        }
        setCategorySelectOptions(category_select_options);
        // category options ends

        // making options object for affected propertiesselect
        let affected_prop_select_options = [];
        let common_options = [];
        let other_options = [];
        for (let i = 0; i < data.risksAffectedProperties.common.length; i++) {
            const value = data.risksAffectedProperties.common[i];
            common_options.push({value: value, label: value});
        }
        for (let i = 0; i < data.risksAffectedProperties.other.length; i++) {
            const value = data.risksAffectedProperties.other[i];
            other_options.push({value: value, label: value});
        }
        affected_prop_select_options.push({label: 'Common', options: common_options})
        affected_prop_select_options.push({label: 'Other', options: other_options})
        setRisksAffectedPropertiesSelectOptions(affected_prop_select_options);
        // end making options object for select

        // likelihood slider values
        let sliderLikelihoods = [];
        let likelihoods = data.riskMatrixLikelihoods;
        for (const key in likelihoods) {
            if (Object.hasOwnProperty.call(likelihoods, key)) {
                const likelihood = likelihoods[key];
                sliderLikelihoods.push(likelihood.name);
            }
        }
        setSliderLikelihoodsSliderValues(sliderLikelihoods);
        // likelihood slider values

        // impact slider values
        let sliderImpact = [];
        let impact = data.riskMatrixImpacts;
        for (const key in impact) {
            if (Object.hasOwnProperty.call(impact, key)) {
                const impacts = impact[key];
                sliderImpact.push(impacts.name);
            }
        }
        setSliderImpactSliderValues(sliderImpact);
        // impact slider values end

        setMatrixScores(data.riskMatrixScores);
        setMatrixScoreLevels(data.riskScoreActiveLevelType);

        // edit value form changes
        if (id) {
            setId(id);
            edit_risk.impact = edit_risk.impact - 1;
            edit_risk.likelihood = edit_risk.likelihood - 1;
            setRisk(edit_risk);
            let values = edit_risk.affected_properties.split(",");
            let affected_properties_values = [];
            for (var key in values) {
                affected_properties_values.push({value: values[key], label: values[key]});
            }
            let defaults = {
                riskNameRequired: edit_risk.name,
                riskDiscriptionRequired: edit_risk.risk_description,
                treatmentRequired: edit_risk.treatment,
                category: {value: edit_risk.category.id, label: edit_risk.category.name},
                risk_treatment: {value: edit_risk.treatment_options, label: edit_risk.treatment_options},
                likelihood: edit_risk.likelihood,
                impact: edit_risk.impact,
                riskAffectedFunctionRequired: edit_risk.affected_functions_or_assets,
                affected_properties: affected_properties_values
            }
            reset(defaults)
        } else {
            let risk = [];
            risk.impact = 0;
            risk.likelihood = 0
            setRisk(risk);
        }
    }

    const handleSliderChange = (type, value) => {
        if (value >= 0) {
            if (type == "impact")
                risk.impact = value;
            else
                risk.likelihood = value;

            setRisk(risk);

            const riskScores = [].concat.apply([], matrixScores)
            const maxScore = Math.max.apply(Math, riskScores.map(function (o) {
                return o.score;
            }))
            const scoreLevels = matrixScoreLevels
            /* Finding the risk score matching the likelihood and impact */
            let targetScore = riskScores.find(score => {
                return (score.likelihood_index == risk.likelihood && score.impact_index == risk.impact)
            })
            const riskScore = targetScore.score

            /* finding the color for the score*/
            let scoreLevel = scoreLevels.levels.find((level, key) => {
                let index = parseInt(key)
                let lastIndex = scoreLevels.levels.length - 1
                let startScore = (index == 0) ? 1 : (scoreLevels.levels[index - 1]['max_score'] + 1)
                let endScore = (index == lastIndex) ? maxScore : level['max_score']

                /* Giving matrix cell color if it falls within the range */
                return riskScore >= startScore && riskScore <= endScore
            })
            document.getElementById('risk_inherent_score_1').innerHTML = riskScore;
            document.getElementById('risk_inherent_level_1').innerHTML = scoreLevel.name;
            document.getElementById('risk_inherent_level_1').style.color = scoreLevel.color;

            document.getElementById('risk_residual_score_1').innerHTML = riskScore;
            document.getElementById('risk_residual_level_1').innerHTML = scoreLevel.name;
            document.getElementById('risk_residual_level_1').style.color = scoreLevel.color;
        }

    }

    const onSubmit = (data) => {
        setIsFormSubmitting(true);
        var values = data.affected_properties.map(function (value) {
            return value.value;
        });
        let post_data = {
            risk_name: data.riskNameRequired,
            risk_description: data.riskDiscriptionRequired,
            treatment: data.treatmentRequired,
            category: data.category.value,
            treatment_options: data.risk_treatment.value,
            likelihood: data.likelihood,
            impact: data.impact,
            affected_functions_or_assets: data.riskAffectedFunctionRequired,
            affected_properties: values,
            data_scope: appDataScope
        };
        let url = '';
        if (id)
            url = route('risks.register.risks-update', [id])
        else
            url = route('risks.register.risks-store');
        Inertia.post(url, post_data, {
            onFinish: () => setIsFormSubmitting(false)
        });
    };

    const dataScopeRef = useRef(appDataScope);
    useEffect(() => {
        if(dataScopeRef.current !== appDataScope && id){
            Inertia.get(route('risks.register.index'));
        }
    }, [appDataScope]);

    const breadcumbsData = {
        "title": "Risk Register",
        "breadcumbs": [
            {
                "title": "Risk Management",
                "href": route('risks.dashboard.index')
            },
            {
                "title": "Risk Register",
                "href": route('risks.register.index')
            },
            {
                "title": id?"Edit Risk":"Add Risk",
                "href": "#"
            }
        ]
      }

    return (
        <AppLayout>
            <div id="risk-register-create-div">
                {/* <!-- breadcrumbs --> */}
                <Breadcrumb data={breadcumbsData}></Breadcrumb>
                <FlashMessages/>
                {/* <!-- end of breadcrumbs --> */}
                <div className="row">
                    <div className="col-xl-12">
                        <div className='card'>
                            <div className="card-body project-box">
                                <div className="top__head-text d-flex justify-content-between pb-2">
                                    <h4>{id ? 'Edit' : 'Add'} Risk</h4>
                                    <Link href={route('risks.manual.setup')}
                                className="btn btn-primary float-end">Manual Import</Link>
                                </div>
                                {/* <!-- form starts here --> */}
                                <form onSubmit={handleSubmit(onSubmit)} style={{'display': 'block'}}>
                                    <div className="row mb-3">
                                        <label htmlFor="riskname"
                                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Risk Name<span
                                            className="required text-danger ms-1">*</span></label>
                                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                            <input type="text" name="risk_name" className="form-control" id="riskname"
                                                placeholder="Enter risk name here" {...register("riskNameRequired", {required: true})} />
                                            {formErrors.risk_name && (
                                                <div className="invalid-feedback d-block">
                                                    {formErrors.risk_name}
                                                </div>
                                            )}
                                            {errors.riskNameRequired && (
                                                <div className="invalid-feedback d-block">
                                                    This field is required
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    <div className="row mb-3">
                                        <label htmlFor="description"
                                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Description<span
                                            className="required text-danger ms-1">*</span></label>
                                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                            <textarea className="form-control" name="risk_description" id="description"
                                                    rows="5"
                                                    placeholder="Enter description here" {...register("riskDiscriptionRequired", {required: true})}></textarea>
                                            <div className="invalid-feedback d-block">
                                                {errors.riskDiscriptionRequired ? <span>This field is required</span> :
                                                    <span> &nbsp; </span>}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="mb-3 row">
                                        <label htmlFor="treatment"
                                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Treatment<span
                                            className="required text-danger ms-1">*</span></label>
                                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                            <textarea className="form-control" name="treatment" id="treatment" rows="5"
                                                    placeholder="Enter treatment here" {...register("treatmentRequired", {required: true})}></textarea>
                                            <div className="invalid-feedback d-block">
                                                {errors.treatmentRequired ? <span>This field is required</span> :
                                                    <span> &nbsp; </span>}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="mb-3 row edit-risk__category category-overflow__xaxis">
                                        <label htmlFor="category"
                                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Category<span
                                            className="required text-danger ms-1">*</span></label>
                                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                            <Controller
                                                name="category"
                                                control={control}
                                                rules={{required: true}}
                                                render={({field}) =>
                                                    <Select className="react-select" classNamePrefix="react-select"
                                                            options={categorySelectOptions} {...field} />
                                                }
                                            />
                                            <div className="invalid-feedback d-block">
                                                {errors.category ? <span>This field is required</span> :
                                                    <span> &nbsp; </span>}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="mb-3 edit-risk__affected-properties row">
                                        <label htmlFor="aff-property"
                                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Affected
                                            property(ies)<span className="required text-danger ms-1">*</span></label>
                                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                            <Controller
                                                name="affected_properties"
                                                control={control}
                                                rules={{required: true}}
                                                render={({field}) =>
                                                    <Select className="react-select" classNamePrefix="react-select"
                                                            options={risksAffectedPropertiesSelectOptions}
                                                            closeMenuOnSelect={false}
                                                            isMulti {...field} />
                                                }
                                            />
                                            <div className="invalid-feedback d-block">
                                                {errors.affected_properties ? <span>This field is required</span> :
                                                    <span> &nbsp; </span>}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="mb-3 edit-risk__risk-treatment row">
                                        <label htmlFor="risk-treatment"
                                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Risk Treatment<span
                                            className="required text-danger ms-1">*</span></label>
                                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                            <Controller
                                                name="risk_treatment"
                                                control={control}
                                                rules={{required: true}}
                                                render={({field}) =>
                                                    <Select className="react-select" classNamePrefix="react-select"
                                                            options={[
                                                                {value: 'Mitigate', label: 'Mitigate'},
                                                                {value: 'Accept', label: 'Accept'}
                                                            ]}
                                                            {...field}
                                                    />
                                                }
                                            />

                                            <div className="invalid-feedback d-block">
                                                {errors.risk_treatment ? <span>This field is required</span> :
                                                    <span> &nbsp; </span>}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="mb-3 row">
                                        <label htmlFor="affected-function-asset"
                                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Affected
                                            function/asset: </label>
                                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                            <input type="text" className="form-control" name="affected_functions_or_assets"
                                                id="affected-function-asset"
                                                placeholder="Enter affected function / asset"  {...register("riskAffectedFunctionRequired", {required: true})} />
                                            <div className="invalid-feedback d-block">
                                                {errors.riskAffectedFunctionRequired ? <span>This field is required</span> :
                                                    <span> &nbsp; </span>}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="mb-3 row">
                                        <label htmlFor="likelihood"
                                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Likelihood</label>
                                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                            {sliderLikelihoodsSliderValues.length > 0 ?
                                                <Controller
                                                    name="likelihood"
                                                    control={control}
                                                    rules={{required: true}}
                                                    render={({field}) =>
                                                        <Slider defaultValue={0}
                                                                marks={sliderLikelihoodsSliderValues}
                                                                max={sliderLikelihoodsSliderValues.length - 1}
                                                                tooltipVisible={false}
                                                                {...field}
                                                                onChange={e => {
                                                                    field.onChange(e);
                                                                    handleSliderChange('likelihood', e);
                                                                }}
                                                        />
                                                    }
                                                />
                                                : ''
                                            }
                                            <div className="invalid-feedback d-block" style={{marginTop: '2.3rem'}}>
                                                {errors.likelihood ? <span>This field is required</span> :
                                                    <span> &nbsp; </span>}
                                            </div>
                                        </div>
                                    </div>
                                    <div className="mb-3 row">
                                        <label htmlFor="impact"
                                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Impact</label>
                                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                            {sliderImpactSliderValues.length > 0 ?

                                                <Controller
                                                    name="impact"
                                                    control={control}
                                                    rules={{required: true}}
                                                    render={({field}) =>
                                                        <Slider defaultValue={0}
                                                                marks={sliderImpactSliderValues}
                                                                max={sliderImpactSliderValues.length - 1}
                                                                tooltipVisible={false}
                                                                {...field}
                                                                onChange={e => {
                                                                    field.onChange(e);
                                                                    handleSliderChange('impact', e);
                                                                }}
                                                        />
                                                    }
                                                />
                                                : ''
                                            }
                                            <div className="invalid-feedback d-block" style={{marginTop: '2.3rem'}}>
                                                {errors.impact ? <span>This field is required</span> :
                                                    <span> &nbsp; </span>}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="mb-3 row">
                                        <label htmlFor="risk__score"
                                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Inherent Risk
                                            Score</label>
                                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                            <h4 className="pt-1">
                                                <span className="inherent-risk-score-wp"
                                                    id="risk_inherent_score_1">{risk.id ? risk.inherent_score : 1}</span>
                                                <span id="risk_inherent_level_1"
                                                    className=" font-xs ms-2 inherent-risk-level-wp risk-score-tag"
                                                    style={{color: risk.id ? risk.InherentRiskScoreLevel.color ? risk.InherentRiskScoreLevel.color : `rgb(125, 230, 79)` : `rgb(125, 230, 79)`}}>
                                                        {risk.id ? risk.InherentRiskScoreLevel.name : 'Low'}
                                                    </span>
                                            </h4>
                                        </div>
                                    </div>

                                    <div className="mb-3 row">
                                        <label htmlFor="risk__score"
                                            className="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Residual Risk
                                            Score</label>
                                        <div className="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                            <h4 className="pt-1">
                                                <span className="residual-risk-score-wp"
                                                    id="risk_residual_score_1">{risk.id ? risk.residual_score : 1}</span>
                                                <span id="risk_residual_level_1"
                                                    className=" font-xs ms-2 residual-risk-level-wp risk-score-tag"
                                                    style={{color: risk.id ? risk.ResidualRiskScoreLevel.color ? risk.InherentRiskScoreLevel.color : `rgb(125, 230, 79)` : `rgb(125, 230, 79)`}}>
                                                        {risk.id ? risk.ResidualRiskScoreLevel.name : 'Low'}
                                                    </span>
                                            </h4>
                                        </div>
                                    </div>

                                    <div className="save-button d-flex justify-content-end">
                                        {id &&
                                        <Link className="btn btn-danger back-btn width-lg" style={{marginRight: "5px"}}
                                            href={`${appBaseURL}/risks/risks-register/` + risk.id + `/show`}>Back</Link>

                                        }
                                        <LoadingButton className="btn btn-primary width-xl waves-effect waves-light"
                                                    onClick={() => {
                                                        handleSubmit(onSubmit)
                                                    }} loading={isFormSubmitting}>Save</LoadingButton>
                                        {/* <input type="submit" className="btn btn-primary width-xl secondary-bg-color" value="Save" /> */}
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

export default RiskRegisterCreate;