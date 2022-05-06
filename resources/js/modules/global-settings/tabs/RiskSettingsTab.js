import React, {useState} from 'react';

import {Inertia} from "@inertiajs/inertia";
import {Popover, OverlayTrigger, Button, Modal} from "react-bootstrap";
import {Link, usePage} from "@inertiajs/inertia-react";
import {useRanger} from "react-ranger";

import LoadingButton from '../../../common/loading-button/LoadingButton';

import '../styles/risk-matrix.css';

const RiskSettingsTab = ({active}) => {
    const {
        riskMatrixAcceptableScore,
        riskMatrixImpacts,
        riskMatrixLikelihoods,
        riskMatrixScores,
        riskScoreLevelTypes,
        APP_URL
    } = usePage().props;

    const [saving, setSaving] = useState(false);
    const [restoring, setRestoring] = useState(false);
    const [edited, setEdited] = useState(false);

    const [impacts, setImpacts] = useState(riskMatrixImpacts);
    const [probability, setProbability] = useState(riskMatrixLikelihoods);
    const [scores, setScores] = useState(riskMatrixScores);

    const [acceptableScore, setAcceptableScore] = useState(riskMatrixAcceptableScore.score);
    //picked by the user
    const [scoreLevel, setScoreLevel] = useState(riskScoreLevelTypes.filter(t => t.is_active === 1)[0]);
    const [rows, setRows] = useState([]);
    const [maxScore, setMaxScore] = useState(0);

    const [showModal, setShowModal] = useState(false);

    const [values, setValues] = React.useState([]);
    // this piece of state will hold the value of the
    // input in all the popovers: scores, impact and probability
    const [popoverInputValue, setPopoverInputValue] = useState('');

    const handleSaveMatrix = () => {
        const formData = new FormData;
        formData.append('risk_matrix_data', JSON.stringify(
            {
                matrix: {
                    likelihoods: probability,
                    impacts,
                    riskScores: scores
                },
                levels: mapHandlersValueToLevels(values)
            }
        ));
        formData.append('risk_acceptable_score', acceptableScore);
        Inertia.post(route('global-settings.risk-matrix.update'), formData, {
            onStart: () => setSaving(true),
            onSuccess: () => {
                setSaving(false);
                setShowModal(false);
                setEdited(false);
            }
        });
    }

    const getRows = () => {
        const width = impacts.length + 1;
        const height = probability.length + 1;

        const rows = [];

        for (let i = 0; i < height; i++) {
            const data = [];
            for (let j = 0; j < width; j++) {
                if (i === 0 && j === 0) {
                    data.push({type: null, value: '', id: -1});
                } else if (j === 0) {
                    data.push({type: 'probability', value: probability[i - 1].name, id: probability[i - 1].id});
                } else if (i === 0) {
                    data.push({type: 'impact', value: impacts[j - 1].name, id: impacts[j - 1].id});
                } else {
                    data.push({type: 'score', value: scores[i - 1][j - 1].score, id: scores[i - 1][j - 1].id});
                }
            }
            rows.push(data);
        }
        setRows(rows.reverse());
    }

    React.useEffect(() => {
        if (scores.length === probability.length && scores[scores.length - 1].length === impacts.length) {
            getRows();
            riskMatrixGetMaxScore();
        }
    }, [scores, impacts, probability]);

    React.useEffect(() => {
        if (active && edited) {
            const removeBeforeEventListener = Inertia.on('before', e => {
                // user is going somewhere, allow save & restore
                if (![route('global-settings.risk-matrix.restore-to-default'), route('global-settings.risk-matrix.update')].includes(e.detail.visit.url.href)) {
                    e.preventDefault();
                    AlertBox({
                        title: 'Are you sure?',
                        text: 'You have unsaved changes in risk matrix that will be lost.',
                        confirmButtonColor: '#f1556c',
                        allowOutsideClick: false,
                        icon: 'warning',
                        iconColor: '#f1556c',
                        showCancelButton: true,
                        confirmButtonText: 'Save',
                        cancelButtonText: 'Leave'
                    }, function (result) {
                        if (result.isConfirmed) {
                            return handleSaveMatrix();
                        }

                        removeBeforeEventListener();
                        Inertia.get(e.detail.visit.url.href);
                    })
                }
            });

            return removeBeforeEventListener;
        }
    }, [edited, active, values]);

    const handleDeleteRow = () => {
        // we remove the top
        if (scores.length - 1 >= 3) {
            const tempProbability = probability;
            tempProbability.pop();
            setProbability(tempProbability);

            const tempScores = scores;
            tempScores.pop();
            setScores(tempScores);

            getRows();
            riskMatrixGetMaxScore();
            setEdited(true);
        }
    }

    const handleDeleteColumn = () => {
        if (scores[0] && scores[0].length - 1 >= 3) {

            const tempScores = scores;
            for (let i = 0; i < tempScores.length; i++) {
                tempScores[i].pop();
            }
            setScores(tempScores);

            const tempImpacts = impacts;
            tempImpacts.pop();
            setImpacts(tempImpacts);

            getRows();
            riskMatrixGetMaxScore();
            setEdited(true);
        }
    }

    const handleAddRow = () => {
        if (popoverInputValue === '') return alert('This field is required');
        setEdited(true);
        setScores([...scores, scores[scores.length - 1].map(s => ({...s, id: null, likelihood_index: null}))]);
        setProbability([...probability, {id: null, name: popoverInputValue}]);
        // close the popover and reset
        setPopoverInputValue('');
        document.body.click();
    }

    const handleAddColumn = () => {
        if (popoverInputValue === '') return alert('This field is required');
        setEdited(true);
        const tempScores = scores;

        for (let i = 0; i < tempScores.length; i++)
            tempScores[i].push({...tempScores[i][tempScores[i].length - 1], id: null, impact_index: null});

        setScores(tempScores);
        setImpacts([...impacts, {id: null, name: popoverInputValue}]);

        // close the popover and reset
        setPopoverInputValue('');
        document.body.click();
    }

    const addRowPopover = (
        <Popover id="popover-add-row" className="popover-matrix">
            <Popover.Body>
                <input type="text" value={popoverInputValue} onChange={e => setPopoverInputValue(e.target.value)}/>
            </Popover.Body>
            <div className="popover-footer">
                <button type="button" className="btn btn-secondary cancel" onClick={() => document.body.click()}>
                    <i className="fas fa-times-circle text-medium"/>
                </button>
                <button type="button" className="btn btn-primary save" onClick={handleAddRow}>
                    <i className="fas fa-check-circle text-medium"/>
                </button>
            </div>
        </Popover>
    );

    const handleEdit = (type, index) => {
        if (popoverInputValue === '') return alert('This field is required');
        if (type === 'score' && (!parseInt(popoverInputValue) || parseInt(popoverInputValue) < 0)) return alert('This field should be greater or equal to 1');

        switch (type) {
            case 'impact':
                const tempImpacts = impacts;
                tempImpacts[index].name = popoverInputValue;
                setImpacts(tempImpacts);
                break;
            case 'probability':
                const tempProbability = probability;
                tempProbability[index].name = popoverInputValue;
                setProbability(tempProbability);
                break;
            case 'score':
                const [i, j] = index;
                const tempScores = scores;
                tempScores[i][j].score = parseInt(popoverInputValue);
                setScores(tempScores);
                break;
        }
        riskMatrixGetMaxScore();
        getRows();
        setEdited(true);
        document.body.click();
    }

    const editPopover = (type, index) => (
        <Popover id="popover-edit-likelihood" className="popover-matrix">
            <Popover.Body>
                <input type="text" value={popoverInputValue} onChange={e => setPopoverInputValue(e.target.value)}/>
            </Popover.Body>
            <div className="popover-footer">
                <button type="button" className="btn btn-secondary cancel" onClick={() => document.body.click()}>
                    <i className="fas fa-times-circle text-medium"/>
                </button>
                <button type="button" className="btn btn-primary save" onClick={() => handleEdit(type, index)}>
                    <i className="fas fa-check-circle text-medium"/>
                </button>
            </div>
        </Popover>
    )

    const addColumnPopover = (
        <Popover id="popover-add-column" className="popover-matrix">
            <Popover.Body>
                <input type="text" value={popoverInputValue} onChange={e => setPopoverInputValue(e.target.value)}/>
            </Popover.Body>
            <div className="popover-footer">
                <button type="button" className="btn btn-secondary cancel" onClick={() => document.body.click()}>
                    <i className="fas fa-times-circle text-medium"/>
                </button>
                <button type="button" className="btn btn-primary save" onClick={handleAddColumn}>
                    <i className="fas fa-check-circle text-medium"/>
                </button>
            </div>
        </Popover>
    );


    const transformHandlers = (values) => {
        if (typeof values[0] === 'object') {
            return values.filter(l => l.max_score !== null).reverse().map((v, i) => v.max_score >= maxScore ? ({
                ...v,
                max_score: maxScore - i - 1
            }) : v).reverse()
        }
        return values.reverse().map((v, i) => v >= maxScore ? maxScore - i - 1 : v).reverse();
    }

    React.useEffect(() => {
        //
        if (maxScore > 0)
            setValues(transformHandlers(scoreLevel.levels.filter(l => l.max_score !== null).map(l => l.max_score)));
    }, [scoreLevel, maxScore]);

    const handleCloseModal = () => setShowModal(false);

    const checkDupsExists = arr => new Set(arr).size !== arr.length;

    const riskMatrixGetMaxScore = () => {
        const riskScores = [];
        /* creating riskScores array*/
        scores.map(function (s) {
            s.map(function (o) {
                riskScores.push(parseInt(o.score))
            })
        })
        const newScore = Math.max.apply(Math, riskScores);
        setMaxScore(newScore);
    }

    const mapHandlersValueToLevels = values => {
        const tempLevels = scoreLevel.levels;
        tempLevels.map((level, i) => level.max_score = values[i] ?? null);
        return tempLevels;
    }

    const handleOnDrag = v => {
        if ((v.join('') === values.join('')) || checkDupsExists(v) || v.includes(0))
            return;

        const handlersValues = transformHandlers(v);
        setScoreLevel({...scoreLevel, levels: [...mapHandlersValueToLevels(handlersValues)]})
        setValues(handlersValues);
        setEdited(true);
    }

    const {getTrackProps, handles, segments} = useRanger({
        values,
        onDrag: handleOnDrag,
        min: 0,
        max: maxScore,
        stepSize: 1,
    });

    const getSliderColor = i => scoreLevel.levels[i] ? scoreLevel.levels[i].color : scoreLevel.levels[scoreLevel.levels.length - 1].color;

    const getScoreColor = score => {
        const transformed = transformHandlers(scoreLevel.levels);
        for (let i = 0; i < transformed.length; i++)
            if (parseInt(score) <= transformed[i].max_score)
                return transformed[i].color;
        return scoreLevel.levels[scoreLevel.levels.length - 1].color;
    }

    const handleEditRiskName = index => {
        if (popoverInputValue === '') return alert('This field is required');
        setScoreLevel({
            ...scoreLevel,
            levels: [
                ...scoreLevel.levels.map((l, i) => i === index ? ({...l, name: popoverInputValue}) : l)
            ]
        });
        document.body.click();
        setEdited(true);
    }

    const editRiskNamePopover = index => (
        <Popover id={`risk_edit_${index}`} className={'d-flex'}>
            <Popover.Body style={{padding: '0.2rem 0.75rem'}}>
                <input className="level-edit-input form-control form-control-sm valid" type={'text'}
                       value={popoverInputValue} onChange={e => setPopoverInputValue(e.target.value)}/>
            </Popover.Body>
            <div className="popover-footer">
                <button type="button" className="btn btn-secondary cancel-btn" onClick={() => document.body.click()}>
                    <i className="fas fa-times-circle text-medium"/>
                </button>
                <button type="button" className="btn btn-primary save-btn" onClick={() => handleEditRiskName(index)}>
                    <i className="fas fa-check-circle text-medium"/>
                </button>
            </div>
        </Popover>
    );

    const getAcceptableRiskScores = (scores) => {
        let allScores = [];
        for (let i = 0; i < scores.length; i++) {
            for (let j = 0; j < scores[0].length; j++)
                allScores.push(scores[i][j].score);
        }
        return [...new Set(allScores)].sort((a, b) => a - b);
    }

    const handleRestoreMatrix = () => {
        Inertia.post(route('global-settings.risk-matrix.restore-to-default'), null, {
            onStart: () => setRestoring(true),
            onSuccess: (page) => {
                setEdited(false);
                setRestoring(false);
                // reset the state
                const {
                    riskMatrixAcceptableScore,
                    riskMatrixImpacts,
                    riskMatrixLikelihoods,
                    riskMatrixScores,
                    riskScoreLevelTypes
                } = page.props;

                setScores(riskMatrixScores);
                setImpacts(riskMatrixImpacts);
                setProbability(riskMatrixLikelihoods);
                setAcceptableScore(riskMatrixAcceptableScore.score);
                setScoreLevel(riskScoreLevelTypes.filter(t => t.is_active === 1)[0]);
            }
        })
    }

    return (
        <div className="row global">
            <Modal show={showModal} onHide={handleCloseModal}>
                <Modal.Header className='px-3 pt-3 pb-0' closeButton>
                    <Modal.Title className="w-100 my-0" as={'h3'}>Save changes</Modal.Title>
                </Modal.Header>
                <Modal.Body className="text-center p-3">
                    <img src={APP_URL + 'assets/images/info1.png'} height="100" width="100" alt="warning"/>
                    <h4 className="py-3">
                        This will only affect the newly created risks, existing risks will keep their old scoring and
                        will have to be updated manually.
                    </h4>
                    <h4 className="pb-3"><b>Make sure to revisit the existing risks.</b></h4>
                    <h4><b>Do you wish to continue?</b></h4>
                </Modal.Body>
                <Modal.Footer className='px-3 pt-0 pb-3'>
                    <Button variant="secondary" onClick={handleCloseModal}>
                        Cancel
                    </Button>
                    <Button variant="primary" id="save-updated-risk-matrix-confirm-btn" disabled={saving}
                            onClick={handleSaveMatrix}>
                        {saving ? 'Saving' : 'Save'}
                    </Button>
                </Modal.Footer>
            </Modal>
            <div className="col-12 clearfix mb-3 px-3">
                <textarea name="risk_matrix_data" className="d-none"></textarea>
                <button type="button" id="save-updated-risk-matrix-btn" className="btn btn-primary float-end"
                        onClick={() => setShowModal(true)}>
                    Save
                </button>

                {/* <button type="button" id="reset-risk-matrix-to-default" className="btn btn-secondary float-end mx-2"
                        disabled={restoring} onClick={handleRestoreMatrix}>
                    {restoring ? 'Restoring' : 'Restore to default'}
                </button> */}

                <LoadingButton
                                       className="btn btn-secondary float-end mx-2"
                                        id="reset-risk-matrix-to-default" 
                                        loading={restoring}
                                        onClick={handleRestoreMatrix}
                                        disabled={restoring}
                                    >
                                        {restoring ? 'Restoring' : 'Restore to default'}
                                    </LoadingButton>

            </div>
            <div className="col-12">
                <div className="row">
                    <div className="col">
                        <div id="risk-matrix-container">
                            <OverlayTrigger rootClose trigger="click" placement="right" overlay={addRowPopover}>
                                <Button variant="link"
                                        className="bg-light risk-matrix-popover risk-matrix-actions column-width"
                                        id="add-matrix-row" onClick={() => setPopoverInputValue('')}><i
                                    className="mdi mdi-plus-circle-outline text-success"/></Button>
                            </OverlayTrigger>
                            <button className="btn bg-light risk-matrix-actions column-width" id="remove-matrix-column"
                                    type="button" onClick={handleDeleteColumn}>
                                <i className="mdi mdi-minus-circle-outline text-danger"/>
                            </button>

                            <button className="btn bg-light risk-matrix-actions column-height" id="remove-matrix-row"
                                    type="button" onClick={handleDeleteRow}>
                                <i className="mdi mdi-minus-circle-outline text-danger"/>
                            </button>

                            <OverlayTrigger rootClose trigger="click" placement="left" overlay={addColumnPopover}>
                                <Button variant="link"
                                        className="bg-light risk-matrix-popover risk-matrix-actions column-height"
                                        id="add-matrix-column" onClick={() => setPopoverInputValue('')}><i
                                    className="mdi mdi-plus-circle-outline text-success"/></Button>
                            </OverlayTrigger>


                            <div className="d-flex align-items-center position:relative;">
                                <div className="table-probability"><h4
                                    style={{transform: 'rotate(-90deg)'}}>Probability</h4></div>
                                <div className="table-scroll-wrapper">
                                    <table className="table table-bordered mb-0" id="risk-matrix">
                                        <tbody>
                                        {rows.map((row, i) => {
                                            return (
                                                <tr key={i}>
                                                    {row.map((cell, j) => {
                                                        if (cell.type === 'score') {
                                                            const index = [probability.length - i - 1, j - 1];
                                                            return (
                                                                <td className="clearfix position-relative"
                                                                    style={{backgroundColor: getScoreColor(cell.value)}}
                                                                    key={j}>
                                                                    <span className="truncate">{cell.value}</span>
                                                                    <OverlayTrigger trigger={'click'} rootClose
                                                                                    placement={'bottom'}
                                                                                    overlay={editPopover(cell.type, index)}>
                                                                        <a className="edit-cell-options risk-matrix-popover"
                                                                           onClick={() => setPopoverInputValue(cell.value)}>
                                                                            <i className="icon-note"/>
                                                                        </a>
                                                                    </OverlayTrigger>
                                                                </td>
                                                            )
                                                        }

                                                        return (
                                                            <td className="clearfix position-relative" key={j}>
                                                                <span className="truncate">{cell.value}</span>
                                                                {cell.value && (
                                                                    <OverlayTrigger trigger={'click'} rootClose
                                                                                    placement={cell.type === 'impact' ? 'top' : 'right'}
                                                                                    overlay={editPopover(cell.type, cell.type === 'impact' ? (j - 1) : (probability.length - i - 1))}>
                                                                        <a className="edit-cell-options risk-matrix-popover"
                                                                           onClick={() => setPopoverInputValue(cell.value)}>
                                                                            <i className="icon-note"/>
                                                                        </a>
                                                                    </OverlayTrigger>
                                                                )}
                                                            </td>
                                                        )
                                                    })}
                                                </tr>
                                            )
                                        })}
                                        </tbody>
                                    </table>
                                </div>
                                <div className="risk-matrix-caption"><h4>Impact</h4></div>
                            </div>
                        </div>
                    </div>
                </div>
                <section className="risk-levels-slider pt-2 pb-5 mt-5 mb-0">
                    <div className="row mt-3 mb-4">
                        <div className="col-md-10 mb-4">
                            <h4>Risk Level Ranges</h4>
                            <p>Define the risk score ranges for each risk level</p>
                        </div>
                        <div className="risk-levels col-md-2 mb-3">
                            <h5>Risk levels</h5>
                            <div className={"mb-3"}>

                                <select
                                    id="risk-levels-switcher"
                                    value={scoreLevel.level}
                                    onChange={e => {
                                        setScoreLevel(riskScoreLevelTypes.filter(t => t.level === parseInt(e.target.value))[0]);
                                        setEdited(true);
                                    }}
                                    className="form-control cursor-pointer"
                                >
                                    {riskScoreLevelTypes.map(t => <option key={t.id}
                                                                          value={t.level}>{t.level}</option>)}
                                </select>
                            </div>
                        </div>
                        <div className="col-md-12 my-4">
                            <div id="risk-level-slider-container">
                                <div
                                    id="risk-matrix-levels"
                                    {...getTrackProps({
                                        style: {
                                            height: '11px',
                                            border: '1px solid #c5c5c5',
                                            borderRadius: '3px'
                                        },
                                    })}
                                >


                                    {/*the colored segments*/}
                                    {segments.map(({getSegmentProps}, i) => {
                                        return (
                                            <div {...getSegmentProps()} style={{
                                                ...getSegmentProps().style,
                                                background: getSliderColor(i),
                                                height: '100%',
                                            }}/>
                                        )
                                    })}

                                    {handles.map(({getHandleProps, value}) => (
                                        <div
                                            className="ui-slider-handle ui-corner-all ui-state-default"
                                            data-value={value}
                                            {...getHandleProps({
                                                style: {
                                                    width: '16px',
                                                    height: '1.4rem',
                                                    borderRadius: '3px',
                                                    background: '#d6d6d6',
                                                    border: '1px solid #d6d6d6',
                                                    zIndex: '1'
                                                },
                                            })}
                                        />
                                    ))}
                                </div>
                                {/* <p className="clearfix">
                                    <span className="float-end max-value-el">Max value: {maxScore}</span>
                                </p> */}
                                <div className="position-relative">
                                    {/*for the bottom pops*/}
                                    {segments.map(({getSegmentProps}, i) => {
                                        const left = parseInt(getSegmentProps().style.left.slice(0, -1));
                                        const width = parseInt(getSegmentProps().style.width.slice(0, -1));
                                        return (
                                            <div
                                                key={i}
                                                style={{
                                                    position: 'absolute',
                                                    left: `${left + Math.floor(width / 2)}%`
                                                }}
                                            >
                                                <Popover id={`pop_${i}`} className="custom-pop no-arrow">
                                                    <Popover.Body>
                                                        {scoreLevel.levels[i]?.name ?? ''}
                                                        <OverlayTrigger rootClose trigger="click" placement="bottom"
                                                                        overlay={editRiskNamePopover(i)}>
                                                            <button type="button" className="btn btn-secondary edit"
                                                                    onClick={() => setPopoverInputValue(scoreLevel.levels[i]?.name ?? '')}>
                                                                <i className="icon-note"/>
                                                            </button>
                                                        </OverlayTrigger>
                                                    </Popover.Body>
                                                </Popover>
                                            </div>
                                        )
                                    })}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="row">
                        <div className="acceptable-risk-scores col-md-2 offset-md-10">
                            <h5>Acceptable risk score</h5>
                            <div className={"mb-3"}>
                                <select
                                    id="acceptable-risk-score-picker"
                                    className={'form-control cursor-pointer'}
                                    value={acceptableScore}
                                    onChange={(e) => {
                                        setAcceptableScore(e.target.value);
                                        setEdited(true);
                                    }}
                                >
                                    {getAcceptableRiskScores(scores).map((score, i) => <option value={score}
                                                                                               key={i}>{score}</option>)}
                                </select>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    )
}

export default RiskSettingsTab;
