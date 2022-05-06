import React, { Fragment } from "react";

function AddCampaignBox(props) {
    const { setShowCampaignModal } = props;

    const handleShowAddCampaignModal = (event) => {
        event.preventDefault();

        setShowCampaignModal(true);
    };

    return (
        <Fragment>
            <div className="col-lg-4 col-sm-6">
                <a
                    href=""
                    data-toggle="modal"
                    onClick={handleShowAddCampaignModal}
                >
                    <div className="card">
                        <div
                            className="card-body project-box project-div d-flex justify-content-center align-items-center"
                            style={{
                                minHeight: "15.5rem",
                                fontSize: "4rem",
                                color: "#323b43",
                            }}
                        >
                            <i className="mdi mdi-plus" />
                        </div>{" "}
                        {/* end card box*/}
                    </div>
                </a>
            </div>
        </Fragment>
    );
}

export default AddCampaignBox;
