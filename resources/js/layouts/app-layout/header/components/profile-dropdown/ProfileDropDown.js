import React, { Fragment } from "react";
import Dropdown from "react-bootstrap/Dropdown";
import { Link, usePage } from "@inertiajs/inertia-react";
import { Inertia } from "@inertiajs/inertia";

function ProfileDropDown(props) {
    const { authUser } = usePage().props;
    const handleLogout = () => {
        let logoutURL = authUser.is_sso_auth
            ? route("saml2.logout")
            : route("admin-logout");

        Inertia.visit(logoutURL, {
            onSuccess: (page) => {
                /* Removing data scope */
                localStorage.removeItem("data-scope");
            },
        });
    };

    return (
        <Fragment>
            <Dropdown
                as="li"
                bsPrefix="dropdown notification-list user-logout-dropdown-wp"
            >
                <Dropdown.Toggle
                    as="a"
                    id="dropdown-custom-components"
                    bsPrefix="nav-link dropdown-toggle nav-user me-0 waves-effect"
                >
                    <span className="avatar">
                        {decodeHTMLEntity(authUser.avatar)}
                    </span>
                    <span className="pro-user-name ms-1">
                        {_.truncate(decodeHTMLEntity(authUser.full_name), {
                            length: 16,
                            omission: "...",
                        })}
                        <i className="mdi mdi-chevron-down" />
                    </span>
                </Dropdown.Toggle>

                <Dropdown.Menu bsPrefix="dropdown-menu dropdown-menu-end profile-dropdown">
                    {/* item*/}
                    <Link
                        href={route("admin-user-management-edit", authUser.id)}
                        className="dropdown-item notify-item"
                    >
                        <i className="fe-user" />
                        <span>My Account</span>
                    </Link>
                    <div className="dropdown-divider" />
                    {/* item*/}
                    <a
                        onClick={() => {
                            handleLogout();
                        }}
                        className="dropdown-item notify-item cursor-pointer"
                    >
                        <i className="fe-log-out" />
                        <span>Logout</span>
                    </a>
                </Dropdown.Menu>
            </Dropdown>
        </Fragment>
    );
}

export default ProfileDropDown;
