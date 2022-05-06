import React, { useContext } from "react";
import {
    AccordionContext,
    useAccordionButton,
    Dropdown,
} from "react-bootstrap";
import { InertiaLink } from "@inertiajs/inertia-react";

const RiskItemToggle = ({ eventKey, callback, risk, onDelete, position }) => {
    const currentEventKey = useContext(AccordionContext);
    const handleOnClick = useAccordionButton(
        eventKey,
        () => callback && callback(eventKey)
    );

    return (
        <tr className="risk-table">
            <td onClick={handleOnClick} style={{ width: "10%" }}>
                <span className="icon-sec me-2 expandable-icon-wp">
                    <a
                        className="link-primary risk-single-list"
                        aria-expanded="false"
                    >
                        <i
                            className={
                                currentEventKey.activeEventKey === eventKey
                                    ? "icon fas fa-chevron-down me-2 expand-icon-w"
                                    : "icon fas fa-chevron-right me-2 expand-icon-w"
                            }
                        />
                        {position}
                    </a>
                </span>
            </td>
            <td style={{ width: "46%" }}>
                <InertiaLink
                    href={
                        `${appBaseURL}/risks/risks-register/` +
                        risk.id +
                        `/show`
                    }
                >
                    {" "}
                    {decodeHTMLEntity(risk.name)}
                </InertiaLink>
            </td>
            <td style={{ width: "5%" }} className="hide-on-xs hide-on-sm">
                {risk.mapped_controls.length > 0 ? (
                    <InertiaLink
                        href={
                            `${appBaseURL}/compliance/projects/` +
                            risk.mapped_controls[0].project_id +
                            `/controls/` +
                            risk.mapped_controls[0].id +
                            `/show/`
                        }
                    >
                        {" "}
                        {decodeHTMLEntity(risk.mapped_controls[0].controlId)}
                    </InertiaLink>
                ) : (
                    "None"
                )}
            </td>
            <td
                style={{ width: "10%" }}
                className="hide-on-xs inherent-likelihood-td"
            >
                {risk.likelihood}
            </td>
            <td
                style={{ width: "5%" }}
                className="hide-on-xs hide-on-sm inherent-impact-td"
            >
                {risk.impact}
            </td>
            <td
                style={{ width: "12%" }}
                className="hide-on-xs hide-on-sm inherent-score-td"
            >
                {risk.inherent_score}
            </td>
            <td
                style={{ width: "12%" }}
                className="hide-on-xs hide-on-sm residual-score-td"
            >
                {risk.residual_score}
            </td>
            <td>
                <Dropdown className="btn-group">
                    <Dropdown.Toggle
                        variant="secondary"
                        className="table-action-btn arrow-none btn btn-light btn-sm"
                        aria-expanded="false"
                    >
                        <i className="mdi mdi-dots-horizontal" />
                    </Dropdown.Toggle>
                    <Dropdown.Menu className="dropdown-menu-end ">
                        <Dropdown.Item
                            href="#delete"
                            onClick={() => onDelete(risk.id)}
                        >
                            <i className="mdi mdi-delete-forever me-2 text-muted font-18 vertical-middle" />
                            Delete
                        </Dropdown.Item>
                    </Dropdown.Menu>
                </Dropdown>
            </td>
        </tr>
    );
};

export default RiskItemToggle;
