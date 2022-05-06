import React, { Fragment } from "react";
import Dropdown from "react-bootstrap/Dropdown";
import {
    deleteCampaigns,
    fetchCampaignList,
} from "../../../../store/actions/policy-management/campaigns";
import { useDispatch } from "react-redux";
import { duplicateCampaigns } from "../../../../store/actions/policy-management/campaigns";

function CampaignActionOption(props) {
    const { campaign, searchQuery, campaignTypeFilter, appDataScope } = props;
    const dispatch = useDispatch();

    const handleDelete = () => {
        AlertBox(
            {
                title: "Are you sure?",
                text: "You will not be able to reactivate this campaign!",
                showCancelButton: true,
                confirmButtonColor: "#ff0000",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false,
                icon:'warning',
                iconColor:'#ff0000'
            },
            async function (confirmed) {
                if (confirmed.value && confirmed.value == true) {
                    let { payload } = await dispatch(
                        deleteCampaigns(campaign.id)
                    );

                    /* when deleted successfully */
                    if (payload.success) {
                        /* render campaigns */
                        AlertBox({
                            title: "Deleted!",
                            text: "The campaign was deleted successfully'",
                            confirmButtonColor: "#b2dd4c",
                            icon:'success',
                        });
                        dispatch(
                            fetchCampaignList({
                                campaign_name: searchQuery,
                                campaign_status: campaignTypeFilter,
                                data_scope: appDataScope,
                            })
                        );
                    }
                }

            }
        );
    };

    const handleCampaignDuplicate = async () => {
        await dispatch(
            duplicateCampaigns({
                campaignId: campaign.id,
                params: {
                    data_scope: appDataScope,
                },
            })
        );
    };

    return (
        <Fragment>
            <Dropdown className="float-end cursor-pointer">
                <Dropdown.Toggle as="a">
                    <i className="mdi mdi-dots-horizontal m-0 text-muted h3" />
                </Dropdown.Toggle>

                <Dropdown.Menu className="dropdown-menu-end">
                    <Dropdown.Item
                        eventKey="1"
                        onClick={handleCampaignDuplicate}
                        className="d-flex align-items-center"
                    >
                    <i className="mdi mdi-content-copy font-14 me-1" /> Duplicate
                    </Dropdown.Item>
                    <Dropdown.Item eventKey="2" onClick={handleDelete} className="d-flex align-items-center">
                    <i className="mdi mdi-delete-outline font-18 me-1" /> Delete
                    </Dropdown.Item>
                </Dropdown.Menu>
            </Dropdown>
        </Fragment>
    );
}

export default CampaignActionOption;
