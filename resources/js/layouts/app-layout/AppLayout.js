import React, { Fragment, useState, useEffect } from 'react';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-react';
import PageLoader from '../../common/page-loader/PageLoader';
import ReportGenerateLoader from '../../common/report-generate-loader/ReportGenerateLoader';
import { useDispatch } from 'react-redux';
import { fetchDataScopeDropdownTreeData } from '../../store/actions/data-scope-dropdown';
import Header from './header/Header';
import './style/ladda.min.css'
import './style/layout.css'
import '../styles/nprogress.css';
import {ToastContainer} from "react-toastify";
import 'react-toastify/dist/ReactToastify.min.css';
import RiskGeneratorLoader from "../../common/risk-generate-loader/RiskGeneratorLoader";


function AppLayout(props) {
    const dispatch = useDispatch()


    /* fetching data scope tree dropdown data */
    useEffect(async() => {
        await dispatch(fetchDataScopeDropdownTreeData())
    }, [])

    //Clearing Login Page Classes from Body Tag
    document.body.classList.remove('authentication-bg', 'authentication-bg-pattern');

    const { globalSetting } = usePage().props;
    
    // intercom messanger 
    const intercom=usePage().props;

    if(intercom.authUser && intercom.intercom_app_id){
        window.intercomSettings = {
          api_base: "https://api-iam.intercom.io",
          app_id: intercom.intercom_app_id,
          user_id:  intercom.intercom_user_id,
          user_hash : intercom.intercom_hash,
          name: intercom.authUser.full_name, // Full name
          email: intercom.authUser.email, // Email
          company: {
            id: intercom.organization.id+intercom.organization.name,
            name: intercom.organization.name,
            website:intercom.APP_URL
          }
        };
        (function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/b8bmaz21';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(document.readyState==='complete'){l();}else if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
    }

    // WHEN USER IDLE, SESSION TIMEOUT
    useEffect(() => {
        if (globalSetting.session_timeout) {
            const interval = setInterval(() => {
                axiosFetch.post(route('session.ajax.check')).then(response => {
                    let res = response.data

                    if (res.success) {
                        clearInterval(interval);

                        // Redirecting to pages lock screen
                        Inertia.post(route('pages-lock-screen'), {
                            email: res.user.email,
                            fullName: res.user.full_name,
                            loggedInWithSSO: res.user.is_sso_auth
                        })
                    }
                })
            }, 10000);
            return () => clearInterval(interval);
        }
    }, [globalSetting])

    return (
        <Fragment>

            {/* Pre-loader */}
            <PageLoader></PageLoader>
            {/* End Preloader*/}

            {/* NAVBAR */}
            <Header></Header>
            {/* END NAVBAR */}

            {/* WRAPPER */}
            <div className="wrapper">
                <div className="container-fluid">
                    <div id="content-section-wp">
                        {/* MAIN CONTENT */}
                        {props.children}
                        {/* END MAIN CONTENT */}
                    </div>
                    {/* END content-section-wp */}
                    <div className="clearfix" >
                    </div>
                </div>{/* container fluid */}
            </div>
            <ReportGenerateLoader></ReportGenerateLoader>
            <RiskGeneratorLoader></RiskGeneratorLoader>
            {/*/.wrapper */}

            <ToastContainer style={{zIndex:11111111111}}/>
        </Fragment>
    );
}

export default AppLayout;
