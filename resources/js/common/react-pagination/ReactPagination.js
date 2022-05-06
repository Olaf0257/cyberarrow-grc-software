import React, { Fragment, useEffect, useState } from "react";
import ReactPaginate from "react-paginate";
import "./react-pagination.scss";

function ReactPagination(props) {
    const { onChange, totalItemsCount, itemsCountPerPage } = props;
    const [pageCount, setPageCount] = useState(0);

    useEffect(() => {
        setPageCount(Math.ceil(totalItemsCount / itemsCountPerPage));
    }, [totalItemsCount, itemsCountPerPage]);

    useEffect(() => {
    }, [pageCount]);

    return (
        <Fragment>
            <ReactPaginate
                className="react-pagination pagination pagination-rounded justify-content-end"
                nextLabel="&raquo;"
                onPageChange={({ selected }) => {
                    onChange ? onChange(selected + 1) : "";
                }}
                marginPagesDisplayed={1}
                pageCount={pageCount}
                previousLabel="&laquo;"
                pageClassName="page-item"
                pageLinkClassName="page-link"
                previousClassName="page-item"
                previousLinkClassName="page-link"
                nextClassName="page-item"
                nextLinkClassName="page-link"
                breakLabel="..."
                breakClassName="page-item"
                breakLinkClassName="page-link"
                containerClassName="pagination"
                activeClassName="active"
                renderOnZeroPageCount={null}
            />
        </Fragment>
    );
}

export default ReactPagination;
