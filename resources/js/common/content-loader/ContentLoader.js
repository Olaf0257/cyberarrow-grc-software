import React, {Fragment, useEffect} from 'react';
import styles  from './content-loader.module.scss';

function ContentLoader(props) {
    const {show} = props

    return (
        <Fragment>
        <div className={`${styles.contentLoadingWrapper}`}>
            <div className={`${styles.overlay} ${show ? styles.show : styles.hide}` }>
            </div>
            <div className={`p-2  align-items-center content-loading-el ${styles.contentLoaderEl} ${show ? styles.show : styles.hide}`}>
                <div className={`spinner ${styles.spinner}`}></div>
                <p className="text-center m-0 px-2">Loading...</p>
            </div>
            {props.children}
        </div>
        </Fragment>
    );
}

export default ContentLoader;
