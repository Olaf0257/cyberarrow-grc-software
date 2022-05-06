import React, { Fragment } from "react";
import AppLayout from "../../../layouts/app-layout/AppLayout";
import CampaignTimeline from "./components/CampaignTimeline";
import CampaignUsersTable from "./components/CampaignUsersTable";
import EmailSentChart from "./components/EmailSentChart";
import PoliciesAcknowledgedChart from "./components/PolicyAcknowledgementChart";
import PolicyCompletionChart from "./components/PolicyCompletionChart";
import Dropdown from "react-bootstrap/Dropdown";
import "./campaign-show-page.scss";
import Breadcrumb from "../../../common/breadcumb/Breadcumb";
import fileDownload from "js-file-download";
import { useDispatch, useSelector } from "react-redux";
import FlashAlert from "../../../common/flash-alert/FlashAlert";
import { Inertia } from "@inertiajs/inertia";
import { useDidMountEffect } from "../../../custom-hooks";
import moment from "moment-timezone";

function CampaignShowPage(props) {
    const {
        campaign,
        campaignTimeline,
        totalEmailSent,
        emailSentSuccess,
        completedAcknowledgements,
        totalAcknowledgements,
        completedAcknowledgementsPercentage,
    } = props;
    const appDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope.value
    );
    const dispatch = useDispatch();
    const breadcumbsData = {
        title: "Campaign - Policy Management",
        breadcumbs: [
            {
                title: "Campaigns",
                href: route("policy-management.campaigns"),
            },
            {
                title: "Campaign details",
                href: "#",
            },
        ],
    };

    useDidMountEffect(() => {
        Inertia.visit(route("policy-management.campaigns"));
    }, [appDataScope]);

    const generatePdfReport = async (campaign) => {
        /* showing report generate loader */
        dispatch({ type: "reportGenerateLoader/show" });

        try {
            let response = await axiosFetch({
                url: route(
                    "policy-management.campaigns.export-pdf",
                    campaign.id
                ),
                method: "GET",
                responseType: "blob", // Important
            });

            fileDownload(response.data, "campaign-dashboard-report.pdf");

            /* hiding report generate loader */
            dispatch({ type: "reportGenerateLoader/hide" });
        } catch (error) {
            /* hiding report generate loader */
            dispatch({ type: "reportGenerateLoader/hide" });
        }
    };

    const generateCsvReport = async (campaign) => {
        /* showing report generate loader */
        dispatch({ type: "reportGenerateLoader/show" });

        try {
            let response = await axiosFetch({
                url: route(
                    "policy-management.campaigns.export-csv",
                    campaign.id
                ),
                method: "GET",
                responseType: "blob", // Important
            });

            fileDownload(response.data, "campaign-users-status.csv");

            /* hiding report generate loader */
            dispatch({ type: "reportGenerateLoader/hide" });
        } catch (error) {
            /* hiding report generate loader */
            dispatch({ type: "reportGenerateLoader/hide" });
        }
    };

    return (
        <Fragment>
            <AppLayout>
                <div id="campaign-show-page">
                    <Breadcrumb data={breadcumbsData}></Breadcrumb>

                    <FlashAlert
                        message={props.flash.success}
                        showAlert={() => (props.flash.success ? true : false)}
                        variant="success"
                    />

                    <div className="row">
                        <div className="col-12">
                            <div className="card">
                                <div className="card-body campaign-brief-details">
                                    <div className="campaign-brief-details-inner">
                                        <div className="clearfix">
                                            <Dropdown className="float-end cursor-pointer">
                                                <Dropdown.Toggle
                                                    className="btn btn-primary theme-bg-secondary"
                                                    variant="success"
                                                    id="dropdown-basic"
                                                >
                                                    Export
                                                </Dropdown.Toggle>

                                                <Dropdown.Menu className="dropdown-menu-end">
                                                    <Dropdown.Item
                                                        onClick={() => {
                                                            generatePdfReport(
                                                                campaign
                                                            );
                                                        }}
                                                    >
                                                        PDF
                                                    </Dropdown.Item>
                                                    <Dropdown.Item
                                                        onClick={() => {
                                                            generateCsvReport(
                                                                campaign
                                                            );
                                                        }}
                                                    >
                                                        CSV
                                                    </Dropdown.Item>
                                                </Dropdown.Menu>
                                            </Dropdown>
                                        </div>
                                        <h3>
                                            Result for{" "}
                                            {decodeHTMLEntity(campaign.name)}
                                        </h3>
                                        <ul className="list-group campaign-info-list list-group-flush mt-2 campaign-card-date">
                                            <li className="list-group-item border-0 ps-0">
                                                <strong>Start Date: </strong>
                                                <span className="text-muted">
                                                    {moment(campaign.launch_date).format('DD-MM-YYYY hh:mm A')}
                                                </span>
                                            </li>
                                            <li className="list-group-item border-0 ps-0">
                                                <strong>Due date: </strong>
                                                <span className="text-muted">
                                                {moment(campaign.due_date).format('DD-MM-YYYY hh:mm A')}
                                                </span>
                                            </li>
                                            <li className="list-group-item border-0 ps-0">
                                                <strong>Group(s): </strong>
                                                {campaign.groups.map(
                                                    (group) => {
                                                        return (
                                                            <span
                                                                key={group.id}
                                                                className="badge bg-soft-info text-info"
                                                            >
                                                                {group.name}
                                                            </span>
                                                        );
                                                    }
                                                )}
                                            </li>
                                            <li className="list-group-item border-0 ps-0">
                                                <strong>Policy(ies): </strong>
                                                {campaign.policies.map(
                                                    (policy) => {
                                                        return (
                                                            <span
                                                                key={policy.id}
                                                                className="badge bg-soft-info text-info"
                                                            >
                                                                {decodeHTMLEntity(
                                                                    policy.display_name
                                                                )}
                                                            </span>
                                                        );
                                                    }
                                                )}
                                            </li>
                                            <li className="list-group-item border-0 ps-0">
                                                <strong>Auto-enroll: </strong>
                                                <span className="text-muted">
                                                    {_.startCase(
                                                        _.toLower(
                                                            campaign.auto_enroll_users
                                                        )
                                                    )}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                    {/* Campaign timeline */}
                                    <CampaignTimeline
                                        timelineItems={campaignTimeline}
                                    ></CampaignTimeline>

                                    <div className="row">
                                        <div className="col-lg-8 campaign-brief-details-graph offset-lg-2">
                                            <div className="row">
                                                <div className="col-lg-4">
                                                    <div className="card shadow-none">
                                                        <div className="card-body">
                                                            <EmailSentChart
                                                                totalEmailSent={
                                                                    totalEmailSent
                                                                }
                                                                emailSentSuccess={
                                                                    emailSentSuccess
                                                                }
                                                            ></EmailSentChart>
                                                        </div>{" "}
                                                    </div>
                                                </div>
                                                <div className="col-lg-4">
                                                    <div className="card shadow-none">
                                                        <div className="card-body">
                                                            <PoliciesAcknowledgedChart
                                                                completedAcknowledgements={
                                                                    completedAcknowledgements
                                                                }
                                                                totalAcknowledgements={
                                                                    totalAcknowledgements
                                                                }
                                                            ></PoliciesAcknowledgedChart>
                                                        </div>{" "}
                                                    </div>
                                                </div>
                                                <div className="col-lg-4">
                                                    <div className="card shadow-none">
                                                        <div className="card-body">
                                                            <PolicyCompletionChart
                                                                completedAcknowledgementPercentage={
                                                                    completedAcknowledgementsPercentage
                                                                }
                                                            ></PolicyCompletionChart>
                                                        </div>{" "}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>{" "}
                                        {/* end col*/}
                                    </div>
                                    {/* End row */}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="row">
                        <div className="col-xl-12">
                            <CampaignUsersTable
                                campaign={props.campaign}
                            ></CampaignUsersTable>
                        </div>
                        {/* end col */}
                    </div>
                </div>
                {/* end of campaign show page */}
            </AppLayout>
        </Fragment>
    );
}

export default CampaignShowPage;
