import React, { Fragment, useState } from "react";
import { Link } from "@inertiajs/inertia-react";
import { Inertia } from "@inertiajs/inertia";
import { useWindowDimensions } from "../../../../../custom-hooks";

function NavigarionMenu(props) {
    const { authUserRoles, isMobileMenuOpen } = props;
    const windowDimensions = useWindowDimensions();
    const [openedDropdownList, setOpenedDropdownList] = useState([]);

    /* Generation style based on the window dimensions */
    const getStyle = () => {
        let style = {};

        if (windowDimensions.width < 992) {
            style = {
                transition: ".5s",
                height: isMobileMenuOpen ? 400 : 0,
            };
        } else {
            style = {
                height: "auto",
            };
        }

        return style;
    };

    /* Redirecting to */
    const handleSubmenuDropdownClick = (route) => {
        if (windowDimensions.width < 992) {
            let prevOpenedDropdownList = [...openedDropdownList];

            /* Toggling the value in array*/
            let updatedOpenedDropdownList = _.xor(prevOpenedDropdownList, [
                route,
            ]);

            /* updating the active key state */
            setOpenedDropdownList(updatedOpenedDropdownList);
        } else {
            /* for desktop size screen */
            Inertia.visit(route);
        }
    };

    /* Returns open class when provided route is opened*/
    const checkIfThisOpenedDropdown = (route) => {
        return openedDropdownList.includes(route) ? "open" : "";
    };

    return (
        <Fragment>
            <div id="navigation" style={getStyle()}>
                <ul className="navigation-menu">
                    {authUserRoles.includes("Global Admin") && (
                        <li>
                            <Link href={route("global.dashboard")}>
                                <i className="mdi mdi-earth font-16"/>
                                Global Dashboards
                            </Link>
                        </li>
                    )}

                    {(authUserRoles.includes("Global Admin") ||
                        authUserRoles.includes("Compliance Administrator") ||
                        authUserRoles.includes("Contributor")) && (
                        <li
                            className={`has-submenu ${checkIfThisOpenedDropdown(
                                route("compliance-dashboard")
                            )}`}
                        >
                            <a
                                href="#"
                                onClick={() => {
                                    handleSubmenuDropdownClick(
                                        route("compliance-dashboard")
                                    );
                                }}
                                className="first-child-redirect"
                            >
                                {" "}
                                <i className="fe-shield"/>
                                Compliance
                                <div className="arrow-down"/>
                            </a>
                            <ul
                                className={`submenu ${checkIfThisOpenedDropdown(
                                    route("compliance-dashboard")
                                )}`}
                            >
                                <li>
                                    <Link href={route("compliance-dashboard")}>
                                        My Dashboard
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href={route("compliance-projects-view")}
                                    >
                                        Projects
                                    </Link>
                                </li>
                            </ul>
                        </li>
                    )}

                    {(authUserRoles.includes("Global Admin") ||
                        authUserRoles.includes("Policy Administrator")) && (
                        <li
                            className={`has-submenu ${checkIfThisOpenedDropdown(
                                route("policy-management.campaigns")
                            )}`}
                        >
                            <a
                                href="#"
                                onClick={() => {
                                    handleSubmenuDropdownClick(
                                        route("policy-management.campaigns")
                                    );
                                }}
                                className="first-child-redirect"
                            >
                                {" "}
                                <i className="fe-layout"/>
                                Policy Management <div className="arrow-down"/>
                            </a>
                            <ul
                                className={`submenu ${checkIfThisOpenedDropdown(
                                    route("policy-management.campaigns")
                                )}`}
                            >
                                <li>
                                    <Link
                                        href={route(
                                            "policy-management.campaigns"
                                        )}
                                    >
                                        Campaigns
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href={route(
                                            "policy-management.policies"
                                        )}
                                    >
                                        Policies
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href={route(
                                            "policy-management.users-and-groups"
                                        )}
                                    >
                                        Users &amp; Groups
                                    </Link>
                                </li>
                            </ul>
                        </li>
                    )}

                    {(authUserRoles.includes("Global Admin") ||
                        authUserRoles.includes("Risk Administrator")) && (
                        <li
                            className={`has-submenu ${checkIfThisOpenedDropdown(
                                route("risks.dashboard.index")
                            )}`}
                        >
                            <a
                                href="#"
                                onClick={() => {
                                    handleSubmenuDropdownClick(
                                        route("risks.dashboard.index")
                                    );
                                }}
                                className="first-child-redirect"
                            >
                                {" "}
                                <i className="fe-alert-triangle"/>
                                Risk Management <div className="arrow-down"/>
                            </a>
                            <ul
                                className={`submenu ${checkIfThisOpenedDropdown(
                                    route("risks.dashboard.index")
                                )}`}
                            >
                                <li>
                                    <Link href={route("risks.dashboard.index")}>
                                        Dashboard
                                    </Link>
                                </li>
                                <li>
                                    <Link href={route("risks.register.index")}>
                                        Risk Register
                                    </Link>
                                </li>
                                <li>
                                    <Link href={route("risks.setup")}>
                                        Risk Setup
                                    </Link>
                                </li>
                            </ul>
                        </li>
                    )}

                    {authUserRoles.some(role => ['Global Admin', 'Third Party Risk Administrator'].includes(role)) ? (
                        <li className={`has-submenu ${checkIfThisOpenedDropdown(route('third-party-risk.dashboard'))}`}>
                            <a
                                href="#"
                                onClick={() => {
                                    handleSubmenuDropdownClick(route('third-party-risk.dashboard'))
                                }}
                                className="first-child-redirect"
                            >
                                {" "}
                                <i className="mdi mdi-shield-account-outline font-16"/>
                                Third Party Risk <div className="arrow-down"/>
                            </a>
                            <ul className={`submenu ${checkIfThisOpenedDropdown(route('third-party-risk.dashboard'))}`}>
                                <li>
                                    <Link href={route('third-party-risk.dashboard')}>
                                        Dashboard
                                    </Link>
                                </li>
                                <li>
                                    <Link href={route('third-party-risk.projects.index')}>
                                        Projects
                                    </Link>
                                </li>
                                <li>
                                    <Link href={route('third-party-risk.questionnaires.index')}>
                                        Questionnaires
                                    </Link>
                                </li>
                                <li>
                                    <Link href={route('third-party-risk.vendors.index')}>
                                        Vendors
                                    </Link>
                                </li>
                            </ul>
                        </li>
                    ) : null}

                        {(authUserRoles.includes("Global Admin")) && (
                        <li>
                            <Link
                                href={route('asset-management.index')}
                            >
                                {" "}
                                <i className="fe-database"/>
                                Asset Management
                            </Link>
                        </li>
                    )}

                    {(authUserRoles.includes("Global Admin") ||
                        authUserRoles.includes("Compliance Administrator") ||
                        authUserRoles.includes("Auditor") ||
                        authUserRoles.includes("Contributor")) && (
                        <li
                          className={`has-submenu last-elements ${checkIfThisOpenedDropdown(
                              route("compliance.implemented-controls")
                        )}`}>
                             <a
                                onClick={() => {
                                    handleSubmenuDropdownClick(
                                        route("compliance.implemented-controls")
                                    );
                                }}
                                className="first-child-redirect"
                                href="#"
                            >
                                <i className="fe-sliders"/>
                                Controls <div className="arrow-down"/>
                            </a>
                            <ul
                                className={`submenu ${checkIfThisOpenedDropdown(
                                    route("compliance.implemented-controls")
                                )}`}
                            >
                                {(authUserRoles.includes("Global Admin") &&
                                <li>
                                    <Link href={route("kpi.index")}>
                                        KPI Dashboard
                                    </Link>
                                </li> )}
                                <li>
                                <Link
                                href={route("compliance.implemented-controls")}
                            >
                                {/* {" "} */}
                                {/* <i className="fe-sliders"/> */}
                                Controls
                            </Link>
                                </li>
                            </ul>
                           
                        </li>
                    )}

                    {authUserRoles.includes("Global Admin") && (
                        <li
                            className={`has-submenu last-elements ${checkIfThisOpenedDropdown(
                                route("global-settings")
                            )}`}
                        >
                            <a
                                onClick={() => {
                                    handleSubmenuDropdownClick(
                                        route("global-settings")
                                    );
                                }}
                                className="first-child-redirect"
                                href="#"
                            >
                                <i className="fe-settings"/>
                                Administration <div className="arrow-down"/>
                            </a>
                            <ul
                                className={`submenu ${checkIfThisOpenedDropdown(
                                    route("global-settings")
                                )}`}
                            >
                                <li>
                                    <Link href={route("global-settings")}>
                                        Global Settings
                                    </Link>
                                </li>
                                {(authUserRoles.includes("Global Admin") &&
                                <li>
                                    <Link
                                        href={route("integrations.index")}
                                    >
                                        Integrations
                                    </Link>
                                </li> )}
                                <li className="has-submenu">
                                    <Link
                                        href={route(
                                            "admin-user-management-view"
                                        )}
                                    >
                                        User Management
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href={route("compliance-template-view")}
                                    >
                                        Compliance Templates
                                    </Link>
                                </li>
                            </ul>
                        </li>
                    )}
                </ul>
            </div>
        </Fragment>
    );
}

export default NavigarionMenu;
