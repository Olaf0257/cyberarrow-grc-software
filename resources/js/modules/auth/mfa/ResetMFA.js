import React, { Fragment, useEffect } from 'react';
import { useForm as useInertiaForm } from '@inertiajs/inertia-react';
import LoadingButton from '../../../common/loading-button/LoadingButton';

export default function ResetMFA(props) {
    const { email } = props
    const { setData, post, processing } = useInertiaForm({
        email: '',
    })

    useEffect(() => {
        setData('email', email)
    }, [])

    function submit(e) {
        e.preventDefault()
        post(route('send-mfa-reset-link'))
    }

    return (
        <Fragment>
            <div className="row mt-3">
                <div className="col-12 text-center">
                    {/* <!--Form that requests to reset your MFA   --> */}
                    <form onSubmit={submit} id="reset-mfa-form">
                        {/* <a className="text-white-50 ms-1" id="send-reset-mfa-link" value="Reset Your MFA" disabled={processing}>Reset Your MFA</a> */}
                        <LoadingButton
                            id="resetpw-btn"
                            className="btn btn-primary w-100"
                            type="submit"
                            loading={processing}
                            disabled={processing}
                        >
                            Reset Your MFA
                        </LoadingButton>
                    </form>
                </div>
            </div>
        </Fragment>
    );
}
