import React, {useState, useEffect} from 'react';

import {usePage} from '@inertiajs/inertia-react';

import {Tab, Tabs} from "react-bootstrap";
import GlobalSettingsHeader from "./components/GlobalSettingsHeader";
import AppLayout from "../../layouts/app-layout/AppLayout";

// Tabs
import AccountOverviewTab from "./tabs/AccountOverviewTab";
import GlobalSettingsTab from "./tabs/GlobalSettingsTab";
import SMTPSettingsTab from "./tabs/SMTPSettingsTab";
import LDAPSettingsTab from "./tabs/LDAPSettingsTab";
import SAMLSettingsTab from "./tabs/SAMLSettingsTab";
import OrganizationsTab from "./tabs/OrganizationsTab";
import RiskSettingsTab from "./tabs/RiskSettingsTab";

import './styles/style.css';
import FlashMessages from "../../common/FlashMessages";

const GlobalSettings = () => {
    const props = usePage().props;
    const [activeKey, setActiveKey] = useState('accountOverview');

    const {globalSetting, timezones, sessionExpiryTimes, mailSettings, ldapSettings, activeTab , tenancy_enabled, license_enabled} = props;

    useEffect(() => {
        document.title = "Global Settings";
        if(!license_enabled)
            setActiveKey('globalSettings');
        if(activeTab)
            return setActiveKey(activeTab);
    }, [activeTab]);

    return (
        <AppLayout>
            <GlobalSettingsHeader />
            <FlashMessages />
            <div className="row card">
                <div className="col-xl-12 card-body">
                    <div className="tabs-menu-down">
                        <Tabs activeKey={activeKey}
                              onSelect={(k) => setActiveKey(k)} className='mb-3'>
                            {license_enabled && 
                                <Tab eventKey="accountOverview" title="Account Overview">
                                    <AccountOverviewTab />
                                </Tab>
                            }
                            <Tab eventKey="globalSettings" title="Global Settings">
                                <GlobalSettingsTab globalSetting={globalSetting} timezones={timezones} sessionExpiryTimes={sessionExpiryTimes} />
                            </Tab>
                                {/* {!tenancy_enabled &&  */}
                                    <Tab eventKey="smtpSettings" title="SMTP Settings" >
                                        <SMTPSettingsTab mailSettings={mailSettings} />
                                    </Tab>
                                {/* } */}
                                {!tenancy_enabled && 
                                    <Tab eventKey="ldapSettings" title="LDAP Settings">
                                        <LDAPSettingsTab ldapSettings={ldapSettings} />
                                    </Tab>
                                }  
                                {/* {!tenancy_enabled &&  */}
                                    <Tab eventKey="samlSettings" title="SAML">
                                        <SAMLSettingsTab />  
                                    </Tab>
                                {/* } */}
                            <Tab eventKey="organizations" title="Organizations">
                                <OrganizationsTab />
                            </Tab>

                            <Tab eventKey="riskSettings" title="Risk Settings">
                                <RiskSettingsTab active={activeKey === 'riskSettings'} />
                            </Tab>
                        </Tabs>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
};

export default GlobalSettings;
