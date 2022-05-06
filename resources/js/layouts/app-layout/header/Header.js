import React, { Fragment, useState } from "react";
import ProfileDropDown from "./components/profile-dropdown/ProfileDropDown";
import DataScopeDropdown from "./components/data-scope-dropdown/DataScopeDropdown";
import { Link, usePage } from "@inertiajs/inertia-react";
import NavigarionMenu from "./components/navigation-menu/NavigarionMenu";
import "./header.scss";

function Header(props) {
    const { authUserRoles, globalSetting, APP_URL , file_driver} = usePage().props;
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

    let logoRedirectRoute = "#";
    if (authUserRoles.includes("Global Admin")) {
        logoRedirectRoute = route("global.dashboard");
    } else {
        if (authUserRoles.includes("Contributor")) {
            logoRedirectRoute = route("compliance-dashboard");
        }
    }

    /* Handle mobile menu toggle */
    const handleMobileMenuToggle = () => {
        setIsMobileMenuOpen(!isMobileMenuOpen);
    };

    return (
        <Fragment>
            {/* Navigation Bar*/}
            <header id="topnav" className="primary-bg-color">
                {/* Topbar Start */}
                <div className="navbar-custom">
                    <div className="container-fluid">
                        <ul className="list-unstyled topnav-menu float-end mb-0">
                            <li className="dropdown notification-list">
                                {/* Mobile menu toggle*/}
                                <a
                                    className={`navbar-toggle nav-link ${
                                        isMobileMenuOpen ? "open" : ""
                                    }`}
                                    onClick={() => {
                                        handleMobileMenuToggle();
                                    }}
                                >
                                    <div className="lines">
                                        <span />
                                        <span />
                                        <span />
                                    </div>
                                </a>
                                {/* End mobile menu toggle*/}
                            </li>
                            <ProfileDropDown></ProfileDropDown>
                        </ul>

                        {/* data-scope-dropdown */}
                        <DataScopeDropdown id="TopDataScopeDropdown"></DataScopeDropdown>
                        {/* LOGO */}

                        <div className="logo-box">
                            <Link
                                href={logoRedirectRoute}
                                className="logo text-center"
                            >
                                <span className="logo">
                                    {file_driver =="s3"?
                                        <img
                                            src={globalSetting.company_logo==="assets/images/ebdaa-Logo.png"? APP_URL + globalSetting.company_logo: globalSetting.company_logo }
                                            alt="Company Logo"
                                            width={70}
                                        /> 
                                        :
                                        <img
                                            src={globalSetting.company_logo==="assets/images/ebdaa-Logo.png"? APP_URL + globalSetting.company_logo: asset(globalSetting.company_logo) }
                                            alt="Company Logo"
                                            width={70}
                                        />
                                    }
                                    <span className="logo-lg-text-light secondary-text-color">
                                        {decodeHTMLEntity(
                                            globalSetting.display_name
                                        )}
                                    </span>
                                </span>
                            </Link>
                        </div>
                    </div>
                    {/* end of container-fluid */}
                </div>
                {/* end Topbar */}
                <div className="topbar-menu">
                    <div className="container">
                        <NavigarionMenu
                            authUserRoles={authUserRoles}
                            isMobileMenuOpen={isMobileMenuOpen}
                        ></NavigarionMenu>
                        <div className="clearfix" />
                    </div>
                </div>
                {/* end navbar-custom */}
            </header>
        </Fragment>
    );
}

export default Header;
