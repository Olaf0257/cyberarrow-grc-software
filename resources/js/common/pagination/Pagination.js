import React, { Fragment,useEffect,useState } from 'react';

function Pagination(props) {
    const [links, setProgressPercent] = useState({})

    useEffect(async() => {
        if(props.links){
            setProgressPercent(props.links);
       }
    }, [props]);

    const linkClicked = (e) => {
        props.actionFunction(e);
    }

    return (
        <Fragment>
            {
                links.length > 3 ? 
                    <div className="risks-pagination-wp pagination-rounded mt-4">
                        <nav>
                            <ul className="pagination">
                                {
                                    links.map(function(link,index){
                                        return <li key={index} className={link.active?"page-item  active":"page-item"} aria-disabled={link.url == null}>
                                            {
                                            link.url == null ? link.label == "&laquo; Previous" ? <span className="page-link" aria-hidden="true">‹</span>:<span className="page-link" aria-hidden="true">›</span>
                                            :<span  onClick={linkClicked} className={link.active?"page-link page-link-hover active":"page-link"} rel={link.label} aria-label={link.label} data-link={link.url} >
                                                {(link.label == "&laquo; Previous" || link.label == "Next &raquo;")?link.label == "&laquo; Previous"?"‹":"›":link.label}
                                            </span>
                                            }
                                        </li> 
                                    })
                                }
                            </ul>
                        </nav>
                    </div>
                :""
            }
                
        </Fragment>
    );
}

export default Pagination;