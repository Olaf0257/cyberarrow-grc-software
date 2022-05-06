import { useEffect, useState } from "react";
import CampaignPolicyAcknowledgement from "../CampaignPolicyAcknowledgement";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-react";
import Tab from "react-bootstrap/Tab";
import Nav from "react-bootstrap/Nav";
import "./show-page.scss";
import LoadingButton from "../../../../common/loading-button/LoadingButton";
import { Document, Page, pdfjs } from 'react-pdf';
import pdfjsWorker from "pdfjs-dist/build/pdf.worker.entry";
import { Link } from "react-router-dom";

pdfjs.GlobalWorkerOptions.workerSrc = pdfjsWorker;

const ShowPage = (props) => {
  const { tenancy_enabled,file_driver } = usePage().props;
  const {
    campaignAcknowledgmentUserToken: {
      user,
      campaign,
      token: acknowledgmentUserToken,
    },
    campaignAcknowledgments,
    errors,
  } = props;
  const [newUrl,setNewUrl]=useState([]);
  const [activeTab, setActiveTab] = useState(campaignAcknowledgments[0]?.id);
  const [checkedPolicies, setCheckedPolicies] = useState([]);
  const [isFormSubmitting, setIsFormSubmitting] = useState(false);

  const [numPages, setNumPages] = useState(null);
  const [pageNumber, setPageNumber] = useState(1);

  const [newCampaignAcknowledgments,setNewCampaignAcknowledgments]=useState(campaignAcknowledgments);

  function onDocumentLoadSuccess({ numPages }) {
    setNumPages(numPages);
  }

  const handleTabNavClick =async (tabId,index) => {
    if(file_driver =="s3"){
        await getNewPolicyFileUrl(acknowledgmentUserToken,index);
    }
    setActiveTab(tabId);
  };

  const handleNextBtnClick =async (index) => {
    if(file_driver =="s3"){
      await getNewPolicyFileUrl(acknowledgmentUserToken,index);
    }
    setActiveTab(activeTab + 1);
  };

  const handlePrevBtnClick =async (index) => {
    if(file_driver =="s3"){
      await getNewPolicyFileUrl(acknowledgmentUserToken,index);
    }
    setActiveTab(activeTab - 1);
  };

  const getNewPolicyFileUrl = async (token,index) =>{
    let httpRes = await axiosFetch.get(route('policy-management.campaigns.acknowledgement.new_policy_url'),{params:{token:token}});
    campaignAcknowledgments[index].policy.path=httpRes.data[index].policy.path;
    setNewCampaignAcknowledgments(campaignAcknowledgments);
  }

  const docsurl = (url) => {
    return 'https://docs.google.com/gview?embedded=true&url='+url;
  };

  const renderAsideLinks = () => {
    return newCampaignAcknowledgments.map((campaignAcknowledgment, index) => {
      return (
        <Nav.Item key={_.uniqueId()}>
          <Nav.Link
            eventKey={campaignAcknowledgment.id}
            className={`policy-list list-group-item list-group-item-action`}
            onClick={() => {
              handleTabNavClick(campaignAcknowledgment.id,index);
            }}
          >
            {decodeHTMLEntity(campaignAcknowledgment.policy.display_name)}
          </Nav.Link>
        </Nav.Item>
      );
      // return (<a
      //     eventKey={campaignAcknowledgment.id}
      //     className={`policy-list list-group-item list-group-item-action ${(index == 0) ? 'active' : ''}`} data-toggle="list" href="#list-{{ $index }}"
      //     role="tab" aria-controls="home">
      //         { decodeHTMLEntity(campaignAcknowledgment.policy.display_name)}
      // </a>)
    });
  };

  /* For file policy review section rendered */
  const renderFilePreviewSection = (ext, policyPath, index) => {
    
    return (
      <div>
        {/* if the  file type cant be displayed*/}
        {ext == "pdf" ? (
          // <object data={file_driver =="s3"?policyPath:asset(`/${policyPath}`)} width="100%" height={500}>
          //    <p>Your web browser doesn't have a PDF plugin.
          //     Instead you can <a href={file_driver =="s3"?policyPath:asset(`/${policyPath}`)}>click here to
          //     download the PDF file.</a></p>
          // </object>
          <div className="shadow" id="react-pdf-policy">
            <Document file={file_driver =="s3"?policyPath:asset(`/${policyPath}`)} onLoadSuccess={onDocumentLoadSuccess}>
              <Page pageNumber={pageNumber}/>
            </Document>
            
            {numPages &&
            <>
            <p className="d-flex align-items-center justify-content-center">
              Page {pageNumber} of {numPages}
            </p>
            <nav aria-label="pdf-pagination" className="d-flex align-items-center justify-content-center">
              <ul className="pagination pagination-sm">
                <li className={`page-item ${pageNumber === 1 ? 'disabled' : ''}`}><span className="page-link cursor-pointer" onClick={() => setPageNumber(pageNumber - 1)}>Previous</span></li>
                <li className={`page-item ${pageNumber === numPages ? 'disabled' : ''}`}><span className="page-link cursor-pointer" onClick={() => setPageNumber(pageNumber + 1)}>Next</span></li>
              </ul>
            </nav>
            </>
          }
          </div>
        ) : (
          <embed src={file_driver =="s3"?policyPath:asset(`/${policyPath}`)} width="100%" height={500}/> 
          )}
      </div>
    );
  };

  const handlePolicyChecked = (value) => {
    if(checkedPolicies.includes(value)){
      checkedPolicies.pop(value);
    }
    else{
      checkedPolicies.push(value);
    }
    setCheckedPolicies(checkedPolicies);
    console.log(checkedPolicies);
    // setCheckedPolicies(_.xor(checkedPolicies, [e.target.defaultValue]));
  };
  
  const enableSubmitButton= ()=>{
      document.getElementsByClassName('custom-save-button')[0].classList.remove('expandRight');
      document.getElementsByClassName('custom-save-button')[0].disabled = false
      document.getElementsByClassName('custom-spinner-image')[0].style.display = 'none';
  }

  const disableSubmitButton= ()=>{
    document.getElementsByClassName('custom-save-button')[0].classList.add('expandRight');
    document.getElementsByClassName('custom-save-button')[0].disabled = true
    document.getElementsByClassName('custom-spinner-image')[0].style.display = 'block';
  }

  /* Handling the form submit */
  const handleSubmit = (event) => {
    event.preventDefault();
    /* Starting loading button */
    // setIsFormSubmitting(true);
    disableSubmitButton();
    const formData = new FormData();

    /**/
    Object.keys(checkedPolicies).forEach((key) =>
      formData.append("agreed_policy[]", checkedPolicies[key])
    );
    formData.append(
      "campaign_acknowledgment_user_token",
      acknowledgmentUserToken
    );

    /* */
    Inertia.post(
      route("policy-management.campaigns.acknowledgement.confirm"),
      formData,
      {
        onSuccess: (page) => {
          /* Starting loading button */
          // setIsFormSubmitting(false);
          enableSubmitButton()

        },
        onError: (errors) => {
          /* Starting loading button */
          // setIsFormSubmitting(false);
          enableSubmitButton()
        },
      }
    );
  };

  const renderTabContents = () => {
    return newCampaignAcknowledgments.map((campaignAcknowledgment, index) => {
      let isFirstLoop = index == 0;
      let isLastLoop = index == campaignAcknowledgments.length - 1;
      return (
        <Tab.Pane key={_.uniqueId()} eventKey={campaignAcknowledgment.id}>
          <div className="card-text">
            {campaignAcknowledgment.policy.type == "doculink" && (
              <>
                <p>
                  This Policy is a doculink. Please follow the url below to see
                  the policy, and confirm that you acknowledge the policy after
                  viewing
                </p>
                <a href={campaignAcknowledgment.policy.path} target="_blank">
                  {campaignAcknowledgment.policy.path}
                </a>
              </>
            )}
            {/*file preview section */}
            {campaignAcknowledgment.policy.type != "doculink" &&
              renderFilePreviewSection(
                campaignAcknowledgment.policy.ext,
                campaignAcknowledgment.policy.path,
                index
              )}
            <div className="col-12 mt-3 text-center">
              <p>
                {" "}
                I understand that if i have any questions, at any time, i will
                consult with my immediate supervisor or my Human Resource Staff
                members.
              </p>
              <div className="form-check d-flex justify-content-center">
                <input
                  type="checkbox"
                  name="agreed_policy[]"
                  // defaultValue={checkedPolicies.includes(
                  //     campaignAcknowledgment.token
                  //   )?1:0}
                  className="form-check-input me-1 cursor-pointer"
                  id={`checkmeout_${index}`}
                  onChange={(e) => {
                    handlePolicyChecked(campaignAcknowledgment.token);
                  }}
                  defaultChecked={checkedPolicies.includes(
                    campaignAcknowledgment.token
                  )}
                />
                <label
                  className="form-check-label"
                  htmlFor={`checkmeout_${index}`}
                >
                  I have read and understood the above policy.
                </label>
              </div>

                {errors.agreed_policy && (
                  <div className="invalid-feedback d-block">{errors.agreed_policy}</div>
                )}
            </div>
            {/* next and prev button section */}
            <div className="row mt-5 " id="button_div">
              <div className="col-12 text-center clearfix">
                {!isFirstLoop && (
                  <button
                    type="button"
                    className="ms-1 btn btn-primary btnPrevious"
                    onClick={() => {
                      handlePrevBtnClick(index);
                    }}
                  >
                    Previous
                  </button>
                )}
                {!isLastLoop && (
                  <button
                    type="button"
                    className="ms-1 btn btn-primary btnNext"
                    onClick={()=>{handleNextBtnClick(index)}}
                  >
                    Next
                  </button>
                )}
                {isLastLoop && (
                  <button className="ms-1 btn btn-primary custom-save-button"
                                      onClick={(e) => handleSubmit(e)}>
                                  Submit
                    <span className='custom-save-spinner'>
                      <img className='custom-spinner-image' style={{display: 'none'}} height="25px"></img>
                    </span>
                  </button>
                )}
              </div>
            </div>
          </div>
        </Tab.Pane>
      );
    });
  };

  return (
    <CampaignPolicyAcknowledgement>
      <div className="row" id="campaign-policy-acknowledgement-show-page">
        <div className="col-12 m-30 title-heading text-center">
          <h5 className="card-title">
            Hi {decodeHTMLEntity(user.first_name)}&nbsp;
            {decodeHTMLEntity(user.last_name)},
          </h5>
          <p>
            You have been enrolled in the{" "}
            <strong>{decodeHTMLEntity(campaign.name)}</strong> policy management
            campaign. Please read the policy(ies) below and acknowledge the
            following policy(ies).
          </p>
        </div>
        <Tab.Container id="left-tabs-example" activeKey={activeTab}>
          {/* aside links */}
          <div className="col-12 col-sm-4 text-center">
            <Nav variant="pills" className="flex-column">
              {renderAsideLinks()}
            </Nav>
          </div>
          <div className="col-12 col-sm-8">
            {/* <form
              // action="{{ route('policy-management.campaigns.acknowledgement.confirm') }}"
              // method="post"
              // onSubmit={handleSubmit}
            > */}
              <input
                type="hidden"
                name="campaign_acknowledgment_user_token"
                defaultValue={acknowledgmentUserToken}
              />
              <Tab.Content>{renderTabContents()}</Tab.Content>
            {/* </form> */}
          </div>
        </Tab.Container>
      </div>
    </CampaignPolicyAcknowledgement>
  );
};

export default ShowPage;
