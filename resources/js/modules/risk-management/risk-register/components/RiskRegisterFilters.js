import React from 'react';

const RiskRegisterFilters = (props) => {
    const {
        searchTerm,
        onTermChange,
        onlyIncomplete,
        onCheck
    } = props;
    return (
        <div className="middle-box pb-2 d-flex justify-content-between">
            <div className="searchbox top__search">
                {/* <form method="get"> */}
                    <input type="text" value={searchTerm} onChange={onTermChange} placeholder="Search by Risk Name" id="risk-search" name="search_by_risk_name" className="search" /><i className="fas fa-search search-icon" />
                {/* </form> */}
            </div>
            <div className="text__box d-flex display-info">
                <h5 className="pt-md-1 display-info__allign me-1">Display only risks with incomplete information</h5>
                <div className="checkbox checkbox-success mid__checkbox ">
                    <input id="updated-risks-filter" checked={onlyIncomplete} onChange={onCheck} type="checkbox" />
                    <label htmlFor="updated-risks-filter" />
                </div>
            </div>
        </div>
    )
};

export default RiskRegisterFilters;
