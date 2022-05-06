import React, { Fragment, useState, useRef } from "react";
import Modal from "react-bootstrap/Modal";
import LoadingButton from "../../../../common/loading-button/LoadingButton";
import AddCampaignForm from "./AddCampaignForm";

function CampaignAddModal(props) {
  const { show, setShow, campaignTypeFilter, searchQuery, policies, groups } =
    props;
  const addCampaignFormRef = useRef(null);
  const [isFormSubmitting, setIsFormSubmitting] = useState(false);

  return (
    <Fragment>
      <Modal
        show={show}
        onHide={() => setShow(false)}
        size="lg"
        aria-labelledby="example-custom-modal-styling-title"
      >
        <Modal.Header className="px-3 pt-3 pb-0" closeButton>
          <Modal.Title className="my-0" id="example-custom-modal-styling-title">
            New Campaign
          </Modal.Title>
        </Modal.Header>
        <Modal.Body className="p-3">
          <AddCampaignForm
            searchQuery={searchQuery}
            campaignTypeFilter={campaignTypeFilter}
            ref={addCampaignFormRef}
            setIsFormSubmitting={setIsFormSubmitting}
            policies={policies}
            groups={groups}
            setShowCampaignAddModal={setShow}
          ></AddCampaignForm>
        </Modal.Body>
        <Modal.Footer className="px-3 pt-0 pb-3">
          <button
            type="button"
            onClick={() => {
              setShow(false);
            }}
            className="btn btn-secondary waves-effect"
            data-dismiss="modal"
          >
            Close
          </button>
          <LoadingButton
            className="btn btn-primary waves-effect waves-light"
            onClick={() => {
              addCampaignFormRef.current.launchCampaign();
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

export default CampaignAddModal;
