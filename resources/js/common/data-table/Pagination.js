import React, { Fragment, useEffect, useState } from 'react';

function Pagination(props) {
    const [links, setLinks] = useState({})

    useEffect(async () => {
        if (props.links) {
            setLinks(props.links);
        }
    }, [props]);

    const linkClicked = (e) => {
        props.actionFunction(e);
    }

    return (
        <Fragment>
            {
                links.length > 3 ?
                    <ul className="pagination pagination-rounded">
                        {
                            links.map(function (link, index) {
                                return <li key={index} className={link.active ? "page-item  active" : "page-item"} aria-disabled={link.url == null}>
                                    {

                                            link.label == "&laquo; Previous" ?
                                            link.url == null ?
                                            <a href={undefined} style={{ cursor: 'pointer' }} className="page-link previous" aria-hidden="true">
                                                {"<"}
                                            </a>
                                            : <a href={undefined} style={{ cursor: 'pointer' }} onClick={linkClicked}  className="page-link previous" rel={link.label} aria-label={link.label} data-link={link.url} >
                                           {"<"}
                                         </a>
                                            : link.label == "..." ? <a style={{ cursor: 'pointer' }} className="page-link" aria-hidden="true">...</a>
                                            :  link.label == "Next &raquo;" ?
                                            link.url == null ?
                                            <a href={undefined} style={{ cursor: 'pointer' }} className="page-link next" aria-hidden="true">
                                                {">"}
                                            </a>
                                            : <a  href={undefined} style={{ cursor: 'pointer' }} onClick={linkClicked}  className="page-link next" rel={link.label} aria-label={link.label} data-link={link.url} >
                                           {">"}
                                         </a>
                                            : <a href={undefined} style={{ cursor: 'pointer' }} onClick={linkClicked} className={link.active ? "page-link page-link-hover active" : "page-link"} rel={link.label} aria-label={link.label} data-link={link.url} >
                                                {(link.label == "&laquo; Previous" || link.label == "Next &raquo;") ? link.label == "&laquo; Previous" ? "‹" : "›" : link.label}
                                            </a>
                                    }

                                </li>
                            })
                        }
                        {/* {
                            links.map(function (link, index) {
                                return <li key={index} className={link.active ? "page-item  active" : "page-item"} aria-disabled={link.url == null}>
                                    {
                                        link.url == null ? link.label == "&laquo; Previous" ? <a style={{ cursor: 'pointer' }} className="page-link previous" aria-hidden="true"> <i className='fas fa-chevron-left'></i></a> : link.label == "..." ? <a style={{ cursor: 'pointer' }} className="page-link" aria-hidden="true">...</a> : <a style={{ cursor: 'pointer' }} className="page-link next" aria-hidden="true"> <i className='fas fa-chevron-right'></i></a>
                                            :
                                            link.label == "&laquo; Previous" ?
                                                <a style={{ cursor: 'pointer' }} onClick={linkClicked} className={link.active ? "page-link page-link-hover active previous" : "page-link previous"} rel={link.label} aria-label={link.label} data-link={link.url} >
                                                     <i className='fas fa-chevron-left'></i>
                                                </a>
                                                : link.label == "Next &raquo;" ?
                                                    <a style={{ cursor: 'pointer' }} onClick={linkClicked} className={link.active ? "page-link page-link-hover active next" : "page-link next"} rel={link.label} aria-label={link.label} data-link={link.url} >
                                                         <i className='fas fa-chevron-right'></i>
                                                    </a>
                                                    :
                                                    <a style={{ cursor: 'pointer' }} onClick={linkClicked} className={link.active ? "page-link page-link-hover active" : "page-link"} rel={link.label} aria-label={link.label} data-link={link.url} >
                                                        {link.label}
                                                    </a>
                                    }
                                </li>
                            })
                        } */}
                    </ul>
                    : ""
            }


            {/* <ul className="pagination pagination-rounded">
                        <li className="paginate_button page-item previous disabled" id="custom-datatable_previous">
                            <a href="#" aria-controls="custom-datatable" data-dt-idx="0" tabIndex="0" className="page-link">
                                <i className="mdi mdi-chevron-left"></i>
                            </a>
                        </li>

                        <li className="paginate_button page-item active">
                            <a href="#" aria-controls="custom-datatable" data-dt-idx="1" tabIndex="0" className="page-link">1</a>
                        </li>
                        <li className="paginate_button page-item ">
                            <a href="#" aria-controls="custom-datatable" data-dt-idx="2" tabIndex="0" className="page-link">2</a>
                        </li>

                        <li className="paginate_button page-item next" id="custom-datatable_next">
                            <a href="#" aria-controls="custom-datatable" data-dt-idx="3" tabIndex="0" className="page-link">
                                <i className="mdi mdi-chevron-right"></i>
                            </a>
                        </li>
                    </ul> */}

        </Fragment>


    );
}

export default Pagination;
