import React from 'react'
import CampaignPolicyAcknowledgement from '../CampaignPolicyAcknowledgement'

const AcknowledgedPage = (props) => {
    const {user} = props
    return (
        <CampaignPolicyAcknowledgement>
            <div className="col-12 text-center">
            <h5 className="card-title">
                Hi {user.first_name} {user.last_name},
            </h5>
            <div className="card-text">
                <p className="text-center h4 mb-3">This link is not valid anymore, you can safely close the page. </p>
            </div>
            </div>
        </CampaignPolicyAcknowledgement>
    )
}

export default AcknowledgedPage
