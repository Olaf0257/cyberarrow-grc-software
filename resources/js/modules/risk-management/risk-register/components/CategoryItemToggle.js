import React, { useContext } from "react";
import { AccordionContext, useAccordionButton } from "react-bootstrap";

const CategoryItemToggle = ({ eventKey, callback, category }) => {
    const currentEventKey = useContext(AccordionContext);
    const handleOnClick = useAccordionButton(
        eventKey,
        () => callback && callback(eventKey)
    );

    return (
        <div
            className="riskbox d-flex align-items-center"
            onClick={handleOnClick}
        >
            <div className="icon-box d-flex align-items-center cursor-pointer">
                <i
                    className={
                        currentEventKey.activeEventKey === eventKey
                            ? "icon fas fa-chevron-down expand-icon-w"
                            : "icon fas fa-chevron-right expand-icon-w"
                    }
                />
                <h5 className="ms-2 risk-register-title">{category.name}</h5>
            </div>
            <div className="items__num ms-auto pt-3">
                <p>
                    {category.total_risks} item(s)
                    <sup>
                        <span className="alert-pill badge bg-danger rounded-pill">
                            {category.total_incomplete_risks}
                        </span>
                    </sup>
                </p>
            </div>
        </div>
    );
};

export default CategoryItemToggle;
