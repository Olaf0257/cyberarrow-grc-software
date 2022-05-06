import React, { Fragment } from 'react';
import styles from './LoadingButton.module.css';

function LoadingButton(props) {
    const { loading = false, className, onClick, type = 'submit' } = props

    return (
        <Fragment>
            <button className={`${className} ${loading ? styles.expandRight : ''} ${styles.loadingButton}`} type={type} onClick={onClick} disabled={loading}>
                {props.children}
                {loading &&
                    <span className={styles.laddaSpinner}>
                        <img className={styles.customSpinner} height="25px"></img>
                    </span>
                }
            </button>
        </Fragment>
    );
}

export default LoadingButton;
