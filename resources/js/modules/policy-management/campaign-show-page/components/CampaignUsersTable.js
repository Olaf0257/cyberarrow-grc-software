import React, { Fragment, useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import ReactPagination from "../../../../common/react-pagination/ReactPagination";
import { fetchCampaignUserList } from "../../../../store/actions/policy-management/campaigns";
import { useDidMountEffect } from "../../../../custom-hooks";
import { Inertia } from "@inertiajs/inertia";
import LoadingButton from "../../../../common/loading-button/LoadingButton";
import {transformDateTime} from "../../../../utils/date";

function CampaignUsersTable(props) {
  const { campaign } = props;
  const dispatch = useDispatch();
  const appDataScope = useSelector(
    (state) => state.appDataScope.selectedDataScope.value
  );
  const [campaignUsers, setCampaignUsers] = useState([]);
  const [activeKeys, setActiveKeys] = useState([]);
  const [filterByUserName, setFilterByUserName] = useState("");
  const [pageLengthFilter, setPageLengthFilter] = useState(10);
  const [usersTablePagination, setUsersTablePagination] = useState({});
  const [usersTableCurrentPage, setUsersTableCurrentPage] = useState(1);
  const [sendingMailRemainder, setSendingMailRemainder] = useState(false);

  /* Trigger on change search and pageLengthFilter change */
  useDidMountEffect(() => {
    loadCampaignUsers(usersTableCurrentPage);
  }, [filterByUserName, usersTableCurrentPage]);

  useEffect(() => {
    loadCampaignUsers();
  }, [pageLengthFilter]);

  const loadCampaignUsers = async (currentPage = null) => {
    const params = {
      filter_by_user_name: filterByUserName,
      page_length: pageLengthFilter,
      data_scope: appDataScope,
    };

    if (currentPage) {
      params["page"] = currentPage;
    }

    let {
      payload: { campaignUsers },
    } = await dispatch(
      fetchCampaignUserList({
        campaignId: campaign.id,
        params: params,
      })
    );

    setCampaignUsers(campaignUsers.data);
    setUsersTablePagination({
      links: campaignUsers.links,
      per_page: campaignUsers.per_page,
      total: campaignUsers.total,
    });
  };

  const toggleActiveKeys = (key) => {
    let prevActiveKeys = [...activeKeys];

    /* Toggling the value in array*/
    let updatedActiveKeys = _.xor(prevActiveKeys, [key]);

    /* updating the active key state */
    setActiveKeys(updatedActiveKeys);
  };

  const renderUserListSection = (campaignUser) => {
    return (
      <Fragment key={campaignUser.id}>
        <tr>
          <td>
            <span className="icon-sec me-2 expandable-icon-wp cursor-pointer">
              <a
                onClick={() => toggleActiveKeys(campaignUser.id)}
                aria-expanded="false"
                aria-controls="collapseExample"
              >
                <i
                  className={`icon fas expand-icon-w fa-chevron-${
                    activeKeys.includes(campaignUser.id) ? "down" : "right"
                  } me-2`}
                ></i>
              </a>
            </span>
          </td>
          <td>{campaignUser.first_name}</td>
          <td className="hide-on-xs hide-on-sm">{campaignUser.last_name}</td>
          <td className="hide-on-xs">{campaignUser.email}</td>
          <td className="hide-on-xs hide-on-sm">
            <span
              className="badge bg-info text-white"
              style={{
                background: '{{$user->user_acknowledgement_status["color"]}}',
              }}
            >
              {campaignUser.user_acknowledgement_status["status"]}
            </span>
          </td>
        </tr>

        <tr className="user-activities-tr">
          <td className="user-activities" colSpan="7">
            <div
              className={`px-2 pb-0 collapse ${
                activeKeys.includes(campaignUser.id) ? "show" : ""
              }`}
            >
              <h4 className="header-title my-3">Timeline for </h4>
              <ul className="list-group list-group-flush user-activity-lists">
                <li className="list-group-item user-activity-node d-flex align-items-center">
                  <div className="node-icon node-icon-green">
                    <i className="dripicons-rocket"></i>
                  </div>
                  <span className="user-activity-node-title mx-2">
                    Campaign created
                  </span>
                  <span className="col-4 col-sm-4 col-md-3 col-lg-2">
                    {transformDateTime(campaign.created_at)}
                  </span>
                </li>

                {campaignUser.activities.map((activity) => {
                  return (
                    <li
                      key={activity.id}
                      className="list-group-item user-activity-node d-flex align-items-center"
                    >
                      {(() => {
                        switch (activity.type) {
                          case "email-sent":
                            return (
                              <div className="node-icon bg-success">
                                <i className="dripicons-mail"></i>
                              </div>
                            );
                          case "clicked-link":
                            return (
                              <div className="node-icon bg-primary">
                                <i className="ti-hand-point-up"></i>
                              </div>
                            );
                          case "email-sent-error":
                            return (
                              <div className="node-icon bg-danger">
                                <i className="dripicons-cross"></i>
                              </div>
                            );
                          case "policy-acknowledged":
                            return (
                              <div className="node-icon node-icon-green bg-warning">
                                <i className="fas fa-check"></i>
                              </div>
                            );

                          default:
                            return "";
                        }
                      })()}

                      <span className="user-activity-node-title mx-2">
                        {activity.activity}
                      </span>
                      <span className="col-4 col-sm-4 col-md-3 col-xl-2">
                        {transformDateTime(activity.created_at)}
                      </span>
                    </li>
                  );
                })}
              </ul>
            </div>
          </td>
        </tr>
      </Fragment>
    );
  };

  const sendRemainderEmail = () => {
    let URL = route(
      "policy-management.campaigns.send-users-reminder",
      campaign.id
    );
    setSendingMailRemainder(true);

    Inertia.get(URL, {
      onFinish: () => {
        setSendingMailRemainder(false);
      },
    });
  };

  return (
    <Fragment>
      <div className="card">
        <div className="card-body table-container">
          <div className="mb-3 clearfix">
            <h3 className="mb-4">Details</h3>
            {/* chage length of data */}
            <div className="row">
              <div className="col-md-2">
                <div className="custom-limit">
                  <label>
                    <span>Show</span>
                    <select
                      name="user_list_length"
                      onChange={(event) => {
                        setPageLengthFilter(event.target.value);
                      }}
                      className="form-select form-select-sm cursor-pointer form-control form-control-sm"
                    >
                      <option value={10}>10</option>
                      <option value={25}>25</option>
                      <option value={50}>50</option>
                      <option value={100}>100</option>
                    </select>
                    <span>Entries</span>
                  </label>
                </div>
              </div>
              <div className="col-md-10">
                <div className="float-end form-left-mobile">
                  <div className="row align-items-center">
                    <div className="col-12">
                      <input
                        type="text"
                        name="filter_by_user_name"
                        onChange={(e) => {
                          setFilterByUserName(e.target.value);
                        }}
                        className="form-control form-control-sm"
                        placeholder="Search..."
                      />
                    </div>
                  </div>
                </div>
                <LoadingButton
                  className="btn btn-sm btn-primary waves-effect waves-light float-end me-2"
                  type="buttom"
                  onClick={() => {
                    sendRemainderEmail();
                  }}
                  loading={sendingMailRemainder}
                >
                  Send Reminder
                </LoadingButton>
              </div>
            </div>{" "}
            {/* End of row */}
          </div>
          <table className="table table-centered display table-hover w-100">
            <thead>
              <tr>
                <th></th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="campaign-users-wp">
              {campaignUsers.map((campaignUser) => {
                {
                  /* first risk item */
                }
                return renderUserListSection(campaignUser);
              })}

              {/* pagination */}
              <tr>
                <td colSpan="6">
                  <div className="float-end campaign-users-pagination">
                    <ReactPagination
                      itemsCountPerPage={pageLengthFilter}
                      totalItemsCount={usersTablePagination.total}
                      onChange={(page) => {
                        setUsersTableCurrentPage(page);
                      }}
                    ></ReactPagination>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      {/* End of table-container */}
    </Fragment>
  );
}

export default CampaignUsersTable;
