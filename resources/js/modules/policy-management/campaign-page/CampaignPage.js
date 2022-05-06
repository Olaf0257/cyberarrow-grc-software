import React, { useEffect, useState } from "react";
import AddCampaignBox from "./components/AddCampaignBox";
import CampaignItem from "./components/CampaignItem";
import CampaignDuplicateModal from "./components/CampaignDuplicateModal";
import AppLayout from "../../../layouts/app-layout/AppLayout";
import "./campaign-page.scss";
import Breadcrumb from "../../../common/breadcumb/Breadcumb";
import FlashAlert from "../../../common/flash-alert/FlashAlert";
import CampaignAddModal from "./components/CampaignAddModal";
import ContentLoader from "../../../common/content-loader/ContentLoader";
import FlashMessages from "../../../common/FlashMessages";
import { useSelector, useDispatch } from "react-redux";
import { fetchCampaignCreateData } from "../../../store/actions/policy-management/campaigns";
import { useStateIfMounted } from "use-state-if-mounted";

function CampaignPage(props) {
    const [campaignTypeFilter, setCampaignTypeFilter] = useState("active");
    const [searchQuery, setSearchQuery] = useState("");
    const [policies, setPolicies] = useStateIfMounted([]);
    const [groups, setGroups] = useStateIfMounted([]);
    const dispatch = useDispatch();
    const [showCampaignModal, setShowCampaignModal] = useState(false);
    const { status: campaignListFetchingStatus } = useSelector(
        (state) => state.policyManagement.campaignReducer
    );
    const appDataScope = useSelector(
        (state) => state.appDataScope.selectedDataScope.value
    );

    useEffect(async () => {
        document.title = "Campaigns";
        let { payload } = await dispatch(
            fetchCampaignCreateData({
                data_scope: appDataScope,
            })
        );

        if (payload && payload.success) {
            let { groups, policies } = payload.data;

            setPolicies(policies);
            setGroups(groups);
        }
    }, [appDataScope]);

    const handleSearchQueryChange = (event) => {
        setSearchQuery(event.target.value);
    };

    /* shows the campaign add box only when active tab type is active */
    const renderShowCampaignBox = () => {
        return campaignTypeFilter != "active" ? (
            ""
        ) : (
            <AddCampaignBox
                showCampaignModal={showCampaignModal}
                setShowCampaignModal={setShowCampaignModal}
            ></AddCampaignBox>
        );
    };

    const breadcumbsData = {
        title: "Campaign - Policy Management",
        breadcumbs: [
            {
                title: "Policy Management",
                href: route("policy-management.campaigns"),
            },
            {
                title: "Campaigns",
                href: "#",
            },
        ],
    };

    return (
        <AppLayout>
            <div id="policy-management_campaign-page">
                {/* breadcrumbs */}
                <Breadcrumb data={breadcumbsData}></Breadcrumb>
                {/* end of breadcrumbs */}
                <FlashMessages />
                <div className="row mb-3">
                    <div className="col-md-8">
                        <button
                            type="button"
                            className={`btn btn-primary campaign-status-btn mb-3 mb-md-0 me-1 ${campaignTypeFilter === 'active' ? 'active' : ''}`}
                            onClick={() => {
                                setCampaignTypeFilter("active");
                            }}
                        >
                            Active Campaigns
                        </button>
                        <button
                            type="button"
                            className={`btn btn-primary campaign-status-btn mb-3 mb-md-0 ${campaignTypeFilter === 'archived' ? 'active' : ''}`}
                            onClick={() => {
                                setCampaignTypeFilter("archived");
                            }}
                        >
                            Archived Campaigns
                        </button>
                    </div>
                    <div className="col-md-4 clearfix">
                        <div className="float-end">
                            <div className="ms-md-3 mb-3">
                                <div className="row align-items-center">
                                    <div className="col-12">
                                        <input
                                            type="text"
                                            onChange={handleSearchQueryChange}
                                            name="campaign_name"
                                            className="form-control form-control-sm"
                                            placeholder="Search..."
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {/* campaign list */}
                <ContentLoader
                    show={
                        campaignListFetchingStatus == "pending" ? true : false
                    }
                >
                    <div className="row" id="campaigns-wp">
                        {renderShowCampaignBox()}
                        <CampaignItem
                            campaignTypeFilter={campaignTypeFilter}
                            searchQuery={searchQuery}
                        ></CampaignItem>
                    </div>
                </ContentLoader>

                <CampaignAddModal
                    show={showCampaignModal}
                    setShow={setShowCampaignModal}
                    campaignTypeFilter={campaignTypeFilter}
                    searchQuery={searchQuery}
                    policies={policies}
                    groups={groups}
                />
                <CampaignDuplicateModal
                    campaignTypeFilter={campaignTypeFilter}
                    searchQuery={searchQuery}
                    policies={policies}
                    groups={groups}
                />
            </div>
        </AppLayout>
    );
}

export default CampaignPage;
