import React from "react";
import CampaignPolicyAcknowledgement from "../CampaignPolicyAcknowledgement";

const CompletedPage = (props) => {
    const { campaignAcknowledgments, user } = props;

    const renderACknowledgedPoliciesSection = () => {
        return campaignAcknowledgments.map((campaignAcknowledgment, index) => {
            return (
                <h5 className="text-center">
                    #{index + 1}
                    {decodeHTMLEntity(
                        campaignAcknowledgment.policy.display_name
                    )}
                </h5>
            );
        });
    };

    return (
        <CampaignPolicyAcknowledgement>
            <div className="row">
                <div className="col-12 text-center">
                    {user && <h5 className="card-title">
                        Hi {`${decodeHTMLEntity(user.first_name)}
                        ${decodeHTMLEntity(user.last_name)}`}
                        ,
                    </h5>}
                    {campaignAcknowledgments && <div className="card-text">
                        <p className="text-center h4 mb-3">
                            Thank you for acknowledging the following
                            policy(ies):
                        </p>
                        {renderACknowledgedPoliciesSection()}
                    </div>}
                </div>
            </div>
        </CampaignPolicyAcknowledgement>
    );
};

export default CompletedPage;
