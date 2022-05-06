import React, { Fragment, useEffect, useState } from "react";
import { useSelector, useDispatch } from "react-redux";
import CampaignActionOption from "./CampaignActionOption";
import { fetchCampaignList } from "../../../../store/actions/policy-management/campaigns";
import { Link } from "@inertiajs/inertia-react";
import moment from "moment-timezone";

function CampaignItem(props) {
    const { searchQuery, campaignTypeFilter } = props;
    const appDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope.value
    );
    const { campaigns } = useSelector(
        (state) => state.policyManagement.campaignReducer
    );
    const dispatch = useDispatch();

    /* On appDataScope, searchQuery, campaignTypeFilter  change and on initial render as well */
    useEffect(() => {
        loadCampaigns();
    }, [appDataScope, searchQuery, campaignTypeFilter]);

    /* Loads campaigns */
    const loadCampaigns = () => {
        dispatch(
            fetchCampaignList({
                campaign_name: searchQuery,
                campaign_status: campaignTypeFilter,
                data_scope: appDataScope,
            })
        );
    };

    const renderCampaingItems = (campaign, index) => {
        let {
            name,
            status,
            status_badge,
            start_date,
            due_date,
            acknowledgments,
        } = campaign;
        return (
            <div className="col-lg-4 col-sm-6" key={index}>
                <div className="card">
                    <div className="card-body project-box project-div">
                        <CampaignActionOption
                            appDataScope={appDataScope}
                            campaign={campaign}
                            searchQuery={searchQuery}
                            campaignTypeFilter={campaignTypeFilter}
                        ></CampaignActionOption>
                        {/* end dropdown */}
                        <Link
                            className="text-dark"
                            href={route(
                                "policy-management.campaigns.show",
                                campaign.id
                            )}
                        >
                            {/* Title*/}
                            <h4 className="mt-0">{decodeHTMLEntity(name)}</h4>
                            <p className="mt-3 clearfix">
                                <b># policies: {campaign.policies}</b>
                                <span
                                    className={`badge ${status_badge} float-end`}
                                >
                                    {status}
                                </span>
                            </p>
                            {/* Task info*/}
                            <p className="mb-1 row campaign-card-date">
                                <span className="col-12 mb-2 text-nowrap d-inline-block ">
                                    <b>Start Date: &nbsp;</b>{" "}
                                    <span className="text-muted">
                                        {moment(start_date).format('DD-MM-YYYY hh:mm A')}
                                    </span>
                                </span>
                                <span className="col-12 mb-2 text-nowrap d-inline-block">
                                    <b>Due Date: &nbsp;</b>{" "}
                                    <span className="text-muted">
                                        {moment(due_date).format('DD-MM-YYYY hh:mm A')}{" "}
                                    </span>
                                </span>
                            </p>
                            {/* Progress*/}
                            <p className="mb-2 fw-bold">
                                Acknowledgement <span className="float-end" />
                            </p>
                            <div
                                className="progress mb-1"
                                style={{ height: 7 }}
                            >
                                <div
                                    className="progress-bar"
                                    role="progressbar"
                                    aria-valuenow
                                    aria-valuemin={0}
                                    aria-valuemax={100}
                                    style={{ width: `${acknowledgments}%` }}
                                ></div>
                                {/* /.progress-bar .progress-bar-danger */}
                            </div>
                            {/* /.progress .no-rounded */}
                        </Link>
                    </div>{" "}
                    {/* end card box*/}
                </div>
            </div>
        );
    };

    return (
        <Fragment>
            {campaigns.map((campaign, index) => {
                return renderCampaingItems(campaign, index);
            })}
        </Fragment>
    );
}

export default CampaignItem;
