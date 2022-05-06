import React, {useEffect, useState} from 'react';
import { useSelector } from 'react-redux';
import {Link, useForm, usePage} from "@inertiajs/inertia-react";
import Select from "react-select";
import Datetime from "react-datetime";

import "react-datetime/css/react-datetime.css";
import moment from "moment/moment";
import LoadingButton from '../../../../../common/loading-button/LoadingButton';

const DetailsTab = ({ getTaskStatusClass }) => {
    const { projectControl, frequencies, meta, nextReviewDate, project } =
        usePage().props;
    const [contributors, setContributors] = useState({});
    const appDataScope = useSelector(state => state.appDataScope.selectedDataScope.value);
    const {errors, data, setData, post, processing} = useForm({
        responsible: projectControl.responsible,
        approver: projectControl.approver,
        deadline: projectControl.deadline ?? moment().format('YYYY-MM-DD'),
        frequency: projectControl.frequency ?? 'One-Time',
        data_scope: appDataScope
    });

    const disabledColor = {
        background: "#eee",
        color: "#444",
    };

    const getSelectedOption = (key) => {
        const index = Object.keys(contributors).find(
            (c) => contributors[c] === data[key]
        );
        if (index === undefined) return null;
        return {
            value: contributors[index],
            label: index,
        };
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (meta.update_allowed)
            post(
                route("compliance.project.controls.update", [
                    project.id,
                    projectControl.id,
                ])
            );
    };

    useEffect(() => {
        axiosFetch.get(route('common.contributors'),{params:{editable:projectControl.is_editable}})
            .then(res => {
                setContributors(res.data);
            });
    }, []);

    return (
        <div className="tab-padding pb-0" id="">
            <form
                className="form-horizontal absolute-error-form"
                onSubmit={handleSubmit}
                id="control-detail-form"
                method="POST"
            >
                <div className="row mb-3">
                    <label
                        htmlFor="id"
                        className="col-3 form-label col-form-label"
                    >
                        Control ID{" "}
                    </label>
                    <div className="col-9">
                        <input
                            type="text"
                            className="form-control"
                            defaultValue={projectControl.controlId}
                            disabled
                            style={{ ...disabledColor }}
                        />
                    </div>
                </div>

                <div className="row mb-3">
                    <label
                        htmlFor="name"
                        className="col-3 form-label col-form-label"
                    >
                        Name
                    </label>
                    <div className="col-9">
                        <input
                            type="text"
                            className="form-control"
                            defaultValue={decodeHTMLEntity(projectControl.name)}
                            disabled
                            style={{ ...disabledColor }}
                        />
                    </div>
                </div>

                <div className="row mb-3">
                    <label
                        htmlFor="description"
                        className="col-3 form-label col-form-label"
                    >
                        Description
                    </label>
                    <div className="col-9">
                        <textarea
                            value={decodeHTMLEntity(projectControl.description)}
                            className="form-control overflow-auto"
                            cols="50"
                            rows="5"
                            disabled
                            style={{ ...disabledColor }}
                        />
                    </div>
                </div>

                <div className="row mb-3">
                    <label
                        htmlFor="control-status"
                        className="col-3 form-label col-form-label"
                    >
                        Status
                    </label>
                    <div className="col-9">
                        <input
                            type="text"
                            className={`form-control ${getTaskStatusClass(
                                projectControl.status
                            )}`}
                            value={projectControl.status}
                            readOnly
                        />
                    </div>
                </div>

                <div className="row mb-3">
                    <label
                        htmlFor="responsible"
                        className="col-3 col-form-label"
                    >
                        Responsible
                        <span className="required text-danger ms-1">*</span>
                    </label>
                    <div className="col-9">
                        <Select
                            className="react-select"
                            classNamePrefix="react-select"
                            value={getSelectedOption("responsible")}
                            options={Object.keys(contributors).map((c) => ({
                                value: contributors[c],
                                label: c,
                            }))}
                            onChange={({ value }) =>
                                setData("responsible", value)
                            }
                            isDisabled={meta.disabled}
                            styles={{
                                control: (provided) =>
                                    meta.disabled
                                        ? { ...provided, ...disabledColor }
                                        : provided,
                            }}
                        />
                        {errors.responsible && (
                            <div className="invalid-feedback d-block">
                                {errors.responsible}
                            </div>
                        )}
                    </div>
                </div>

                <div className="row mb-3">
                    <label htmlFor="approver" className="col-3 col-form-label">
                        Approver{" "}
                        <span className="required text-danger ms-1">*</span>
                    </label>
                    <div className="col-9">
                        <Select
                            className="react-select"
                            classNamePrefix="react-select"
                            value={getSelectedOption("approver")}
                            options={Object.keys(contributors).map((c) => ({
                                value: contributors[c],
                                label: c,
                            }))}
                            onChange={({ value }) => setData("approver", value)}
                            isDisabled={meta.disabled}
                            styles={{
                                control: (provided) =>
                                    meta.disabled
                                        ? { ...provided, ...disabledColor }
                                        : provided,
                            }}
                        />
                        {errors.approver && (
                            <div className="invalid-feedback d-block">
                                {errors.approver}
                            </div>
                        )}
                    </div>
                </div>

                <div className="row mb-3">
                    <label
                        htmlFor="control-deadline"
                        className="col-3 form-label col-form-label"
                    >
                        Deadline{" "}
                        <span className="required text-danger ms-1">*</span>
                    </label>
                    <div className="col-9 input-group">
                        <Datetime
                            className={"datetime-responsive-div"}
                            inputProps={{
                                className:
                                    "form-control cursor-pointer border-end-0",
                                disabled: meta.disabled,
                            }}
                            timeFormat={false}
                            dateFormat={"DD-MM-YYYY"}
                            value={moment(data.deadline).format('DD-MM-YYYY')}
                            closeOnSelect
                            onChange={(value) =>
                                setData("deadline", value.format("YYYY-MM-DD"))
                            }
                            isValidDate={(current) =>
                                current.isAfter(moment().subtract(1, "day"))
                            }
                        />
                        <div className="border-start-0">
                            <span className="input-group-text bg-none">
                                <i className="mdi mdi-calendar-outline" />
                            </span>
                        </div>
                        {errors.deadline && (
                            <div className="invalid-feedback d-block">
                                {errors.deadline}
                            </div>
                        )}
                    </div>
                </div>

                {nextReviewDate ? (
                    <div className="row mb-3">
                        <label
                            htmlFor="control-deadline"
                            className="col-3 form-label col-form-label"
                        >
                            Next Review Date
                            <span className="required text-danger ms-1">*</span>
                        </label>
                        <div className="col-9 input-group">
                            <input
                                type="text"
                                className="form-control"
                                defaultValue={nextReviewDate}
                                disabled
                                style={{ ...disabledColor }}
                            />
                        </div>
                    </div>
                ) : null}

                <div className="row mb-3">
                    <label htmlFor="frequency" className="col-3 col-form-label">
                        Frequency{" "}
                        <span className="required text-danger ms-1">*</span>
                    </label>
                    <div className="col-9">
                        <Select
                            className="react-select"
                            classNamePrefix="react-select"
                            value={{
                                value: data.frequency,
                                label: data.frequency,
                            }}
                            options={frequencies.map((f) => ({
                                value: f,
                                label: f,
                            }))}
                            onChange={({ value }) =>
                                setData("frequency", value)
                            }
                            isDisabled={meta.disabled}
                            styles={{
                                control: (provided) =>
                                    meta.disabled
                                        ? { ...provided, ...disabledColor }
                                        : provided,
                            }}
                        />

                        {errors.frequency && (
                            <div className="invalid-feedback d-block">
                                {errors.frequency}
                            </div>
                        )}
                    </div>
                </div>

                <div className="row mb-0">
                    <div className="col d-flex justify-content-end">
                        <Link href={route('compliance-project-show', [project.id,'controls'])} as="button" type="button" className="btn btn-danger" replace>Back</Link>
                        {meta.update_allowed ? (
                        <LoadingButton className="btn btn-primary ms-1 waves-effect waves-light"
                        loading={processing}>Update</LoadingButton>
                        ) : null}
                    </div>
                </div>
            </form>
        </div>
    );
};

export default React.memo(DetailsTab);
