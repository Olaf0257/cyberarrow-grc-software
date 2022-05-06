import React, {Fragment} from 'react';
import { useSelector } from 'react-redux';

function PageLoader(props) {
    const pageLoader = useSelector(state => state.PageLoader)
    return (
        <Fragment>
        <div id="page-loader">
            <div id="loader-status">
                <div className="spinner" />
                <p className="text-center">Loading...</p>
            </div>
        </div>
        </Fragment>
    );
}

export default PageLoader;
