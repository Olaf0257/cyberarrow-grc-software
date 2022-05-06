import React, { Fragment } from 'react';
import Pagination from './Pagination';
import './data-table.css';
import ReactPagination from '../../common/react-pagination/ReactPagination';

function DataTableFooter(props) {
    const {data, isOffline} = props;
    let startFrom = 0;
    let startTo = 0;
    let totalCount = 0;
    let perpage = 0;

    if(isOffline){
        totalCount = props.total;
        const activeIndex = props.activeIndex;
        perpage = props.perpage;
        startFrom = totalCount > 0 ? activeIndex == 0?1:activeIndex*perpage+1:0;
        startTo = totalCount > 0 ? (activeIndex == 0 && totalCount <= perpage)?totalCount:((activeIndex+1)*perpage)>totalCount?totalCount:(activeIndex+1)*perpage:0;
    }

    const paginationLinkedClickAction = (e) => {
        props.paginateTo(e);
        if(props.paginateEventTrigger){
            props.paginateEventTrigger(e);
        }
    }

    return (
        <Fragment>
            {
                data &&
                <div className="row">
                    <div className="col-sm-12 col-md-5">
                        {
                            isOffline?
                            <div className="dataTables_info" id="custom-datatable_info" role="status" aria-live="polite">
                            Showing {startFrom} to {startTo} of {totalCount} entries
                            </div>
                            :
                            <div className="dataTables_info" id="custom-datatable_info" role="status" aria-live="polite">
                            Showing {data.from ? data.from : 0} to {data.to ? data.to : 0} of {data.total ? data.total : 0} entries
                            </div>
                        }

                    </div>
                    {
                    isOffline ?
                    <div className="col-sm-12 col-md-7 mt-1">
                        <ReactPagination
                            itemsCountPerPage={perpage}
                            totalItemsCount={totalCount}
                            onChange={(page) => paginationLinkedClickAction(page) }>
                            </ReactPagination>
                    </div>
                        :
                    <div className="col-sm-12 col-md-7">
                    <div className="dataTables_paginate paging_simple_numbers" id="custom-datatable_paginate">
                        <Pagination actionFunction={paginationLinkedClickAction} links={data.links} />
                    </div>
                    </div>
                    }

                </div>
            }
        </Fragment >
    );
}

export default DataTableFooter;
