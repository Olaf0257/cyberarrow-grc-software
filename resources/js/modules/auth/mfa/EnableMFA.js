import React from 'react';
import AuthLayout from '../../../layouts/auth-layout/AuthLayout';
import { Link, useForm as useInertiaForm } from '@inertiajs/inertia-react';
import Logo from '../../../layouts/auth-layout/components/Logo';

export default function EnableMFA(props) {
    document.title = "Enable Multi Factor Authentication";

    return (
        <AuthLayout>
            <div className="row justify-content-center">
                <div className="col-md-8 col-lg-6 col-xl-5">
                    <div className="card bg-pattern">
                        <div className="card-body p-4">
                            {/* <!-- LOGO DISPLAY NAME -->*/}
                            <Logo></Logo>

                            <h4 className="text-center pb-2">Two factor authentication required</h4>
                            <p className="text-center">To proceed, you need to enable Two Factor Authentication.</p>
                            <Link href={route('2fa.setup')}>
                                <button type="submit" className="btn btn-primary w-100">Enable</button>
                            </Link>
                        </div>
                        {/* <!-- end card-body --> */}
                    </div>
                    {/* <!-- end card --> */}

                </div>
                {/* <!-- end col --> */}
            </div>
            {/* <!-- end row --> */}
        </AuthLayout>
    );
}
