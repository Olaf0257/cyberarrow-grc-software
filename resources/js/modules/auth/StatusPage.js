import React from 'react';
import AuthLayout from '../../layouts/auth-layout/AuthLayout';
import { Link } from '@inertiajs/inertia-react';
import Logo from '../../layouts/auth-layout/components/Logo';

export default function StatusPage(props) {
    const { data } = props;
    document.title = data.pageTitle ? data.pageTitle : '';

    return (
        <AuthLayout>
            <div className="row justify-content-center">
                <div className="col-md-8 col-lg-6 col-xl-4">
                    <div className="card bg-pattern">

                        <div className="card-body p-4">
                            {/* <!-- LOGO DISPLAY NAME -->*/}
                            <Logo></Logo>

                            <div className="mt-3 text-center">
                                <svg version="1.1" xmlnsx="&ns_extend;" xmlnsi="&ns_ai;" xmlnsgraph="&ns_graphs;"
                                    xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 98 98"
                                    style={{ 'height': '120px' }} xmlSpace="preserve">
                                    <g iextraneous="self">
                                        <circle id="XMLID_50_" className="st0" cx="49" cy="49" r="49" />
                                        <g id="XMLID_4_">
                                            <path id="XMLID_49_" className="st1" d="M77.3,42.7V77c0,0.6-0.4,1-1,1H21.7c-0.5,0-1-0.5-1-1V42.7c0-0.3,0.1-0.6,0.4-0.8l27.3-21.7
                                        c0.3-0.3,0.8-0.3,1.2,0l27.3,21.7C77.1,42.1,77.3,42.4,77.3,42.7z"/>
                                            <path id="XMLID_48_" className="st2" d="M66.5,69.5h-35c-1.1,0-2-0.9-2-2V26.8c0-1.1,0.9-2,2-2h35c1.1,0,2,0.9,2,2v40.7
                                        C68.5,68.6,67.6,69.5,66.5,69.5z"/>
                                            <path id="XMLID_47_" className="st1" d="M62.9,33.4H47.2c-0.5,0-0.9-0.4-0.9-0.9v-0.2c0-0.5,0.4-0.9,0.9-0.9h15.7
                                        c0.5,0,0.9,0.4,0.9,0.9v0.2C63.8,33,63.4,33.4,62.9,33.4z"/>
                                            <path id="XMLID_46_" className="st1" d="M62.9,40.3H47.2c-0.5,0-0.9-0.4-0.9-0.9v-0.2c0-0.5,0.4-0.9,0.9-0.9h15.7
                                        c0.5,0,0.9,0.4,0.9,0.9v0.2C63.8,39.9,63.4,40.3,62.9,40.3z"/>
                                            <path id="XMLID_45_" className="st1" d="M62.9,47.2H47.2c-0.5,0-0.9-0.4-0.9-0.9v-0.2c0-0.5,0.4-0.9,0.9-0.9h15.7
                                        c0.5,0,0.9,0.4,0.9,0.9v0.2C63.8,46.8,63.4,47.2,62.9,47.2z"/>
                                            <path id="XMLID_44_" className="st1" d="M62.9,54.1H47.2c-0.5,0-0.9-0.4-0.9-0.9v-0.2c0-0.5,0.4-0.9,0.9-0.9h15.7
                                        c0.5,0,0.9,0.4,0.9,0.9v0.2C63.8,53.7,63.4,54.1,62.9,54.1z"/>
                                            <path id="XMLID_43_" className="st2" d="M41.6,40.1h-5.8c-0.6,0-1-0.4-1-1v-6.7c0-0.6,0.4-1,1-1h5.8c0.6,0,1,0.4,1,1v6.7
                                        C42.6,39.7,42.2,40.1,41.6,40.1z"/>
                                            <path id="XMLID_42_" className="st2" d="M41.6,54.2h-5.8c-0.6,0-1-0.4-1-1v-6.7c0-0.6,0.4-1,1-1h5.8c0.6,0,1,0.4,1,1v6.7
                                        C42.6,53.8,42.2,54.2,41.6,54.2z"/>
                                            <path id="XMLID_41_" className="st1" d="M23.4,46.2l25,17.8c0.3,0.2,0.7,0.2,1.1,0l26.8-19.8l-3.3,30.9H27.7L23.4,46.2z" />
                                            <path id="XMLID_40_" className="st3" d="M74.9,45.2L49.5,63.5c-0.3,0.2-0.7,0.2-1.1,0L23.2,45.2" />
                                        </g>
                                    </g>
                                </svg>

                                {data.title && <h3>{data.title}</h3>}
                                {data.header && <h4>{data.header}</h4>}
                                {data.message &&
                                    <p className="text-muted font-14 mt-2">{data.message}</p>
                                }
                                {data.actionLink && data.actionTitle &&
                                    <Link href={data.actionLink} className="btn btn-primary w-100 secondary-bg-color mt-3">{data.actionTitle}</Link>
                                }
                            </div>

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
