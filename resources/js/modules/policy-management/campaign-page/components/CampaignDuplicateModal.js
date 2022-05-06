import React, { Fragment, useEffect, useState, useRef } from "react";
import Modal from "react-bootstrap/Modal";
import { useDispatch, useSelector } from "react-redux";
import LoadingButton from "../../../../common/loading-button/LoadingButton";
import CampaignDuplicateForm from "./CampaignDuplicateForm";

function CampaignDuplicateModal(props) {
  const { campaignTypeFilter, searchQuery, policies, groups } = props;
  const [campaign, setCampaign] = useState({});
  const { showModal, campaign: campaignData } = useSelector(
    (state) => state.policyManagement.campaignDuplicateReducer
  );
  const dispatch = useDispatch();
  const duplicateCampaignFormRef = useRef(null);
  const [isFormSubmitting, setIsFormSubmitting] = useState(false);

  useEffect(() => {
    setCampaign(campaignData);
  }, [campaignData]);
  const closeModal = () => {
    dispatch({ type: `campaigns/duplicateCampaignModal/close` });
  };

  return (
    <Fragment>
      <Modal
        show={showModal}
        onHide={() => closeModal()}
        size="lg"
        aria-labelledby="example-custom-modal-styling-title"
      >
        <Modal.Header className="px-3 pt-3 pb-0" closeButton>
          <Modal.Title className="my-0" id="example-custom-modal-styling-title">
            Duplicate Campaign
          </Modal.Title>
        </Modal.Header>
        <Modal.Body className="p-3">
          <CampaignDuplicateForm
            ref={duplicateCampaignFormRef}
            campaignTypeFilter={campaignTypeFilter}
            searchQuery={searchQuery}
            setIsFormSubmitting={setIsFormSubmitting}
            policies={policies}
            groups={groups}
          />
        </Modal.Body>
        <Modal.Footer className="px-3 pt-0 pb-3">
          <button
            type="button"
            onClick={() => {
              closeModal();
            }}
            className="btn btn-secondary waves-effect"
            data-dismiss="modal"
          >
            Close
          </button>
          <LoadingButton
            className="btn btn-primary waves-effect waves-light"
            onClick={() => {
              duplicateCampaignFormRef.current.handleSubmitCampaignDuplicate();
            }}
            loading={isFormSubmitting}
          >
            Launch Campaign
          </LoadingButton>
        </Modal.Footer>
      </Modal>
    </Fragment>
  );
}

export default CampaignDuplicateModal;
