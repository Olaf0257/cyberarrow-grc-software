import React, { useEffect } from 'react';
import './style/auth-layout.scss';
import '../styles/nprogress.css';
import { usePage } from '@inertiajs/inertia-react';
import { appendScript } from '../../common/script/AppendRemoveScript';

export default function AuthLayout(props) {
    const { APP_URL } = usePage().props;
    
    //Adding Login Page Classes from Body Tag
    document.body.classList.add('authentication-bg', 'authentication-bg-pattern');
    // hiding messenger on login page
    if(typeof Intercom!== "undefined"){
        Intercom('update', {"hide_default_launcher": true});
    }
    useEffect(() => {
        let particleJs_file=APP_URL + 'assets/libs/particlejs/particle.min.js';
        let initiateParticleScript=APP_URL + 'assets/js/initializeParticle.js';

        appendScript(particleJs_file);
        setTimeout(() => {
            appendScript(initiateParticleScript);
        },500);
    });

    return (
        <div id="authLayout">
            <div className="account-pages mt-5 mb-5">
                <div className="container">
                    {props.children}
                </div>
            </div>

            <footer className="footer footer-alt">
                Copyright &copy; {(new Date().getFullYear())}<a href="#" className="text-white-50"> EBDAA ITC CO LLC All rights reserved.</a>
            </footer>
        </div>
    );
}