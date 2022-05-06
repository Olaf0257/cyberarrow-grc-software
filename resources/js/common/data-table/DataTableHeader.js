import React, { Fragment } from 'react';
import './data-table.css'

function DataTableHeader(props) {

    const enableSearch = props.enableSearch;

    const onPerPageChange = (e) => {
        props.perPageChangedTo(e.target.value);
    }

    const onSearchChange = (e) => {
        props.searchChangedTo(e.target.value);
    }

    return (
        <Fragment>
            <div className="row">
                <div className="col-sm-12 col-md-6">
                    <div className="dataTables_length" id="custom-datatable_length">
                        <label>Show&nbsp;
                            <select name="custom-datatable_length" onChange={onPerPageChange} aria-controls="custom-datatable" className="form-select form-select-sm cursor-pointer form-control form-control-sm">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select> entries
                        </label>
                    </div>
                </div>
                {enableSearch &&
                    <div className="col-sm-12 col-md-6">
                        <div id="custom-datatable_filter" className="dataTables_filter">
                            <label>
                                Search:
                                <input type="search" onChange={onSearchChange} className="form-control form-control-sm" placeholder="" aria-controls="custom-datatable" />
                            </label>
                        </div>
                    </div>
                }
            </div>

        </Fragment >
    );
}

export default DataTableHeader;
