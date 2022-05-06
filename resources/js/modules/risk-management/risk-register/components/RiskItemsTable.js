import React, { useEffect } from "react";
import { Accordion, Pagination } from "react-bootstrap";

import RiskItemToggle from "./RiskItemToggle";
import RiskItemDetails from "./RiskItemDetails";

import { paginate } from "../../../../utils/pagination";

const RiskItemsTable = ({
    risks,
    onPaginate,
    pagination,
    onDelete,
    removeRiskFromTable,
    primaryFilters,
    onUpdateCategoryRisksCount,
    handleUpdateRiskStatus,
}) => {
    const { current_page, total } = pagination;
    const start = (current_page - 1) * 5;
    return (
        <div>
            <div className="risk__table border mb-1">
                <Accordion>
                    <table className="table risk-register-table dt-responsive">
                        <thead className="table-light">
                            <tr>
                                <th className="risk__id-width">Risk ID</th>
                                <th>Risk Name</th>
                                <th className="hide-on-sm hide-on-xs">
                                    Control
                                </th>
                                <th className="hide-on-xs">Likelihood</th>
                                <th className="hide-on-xs hide-on-sm">
                                    Impact
                                </th>
                                <th className="hide-on-xs hide-on-sm">
                                    Inherent Score
                                </th>
                                <th className="hide-on-sm hide-on-xs">
                                    Residual Score
                                </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {risks.map((risk, index) => {
                                const eventKey = `risk_${risk.id}`;
                                const position = index + start + 1;
                                return (
                                    <React.Fragment key={risk.id}>
                                        <RiskItemToggle
                                            position={position}
                                            eventKey={eventKey}
                                            risk={risk}
                                            onDelete={onDelete}
                                        />
                                        <tr>
                                            <td
                                                className="p-2"
                                                colSpan="7"
                                                width="100%"
                                            >
                                                <Accordion.Collapse
                                                    eventKey={eventKey}
                                                >
                                                    <RiskItemDetails
                                                        removeRiskFromTable={
                                                            removeRiskFromTable
                                                        }
                                                        handleUpdateRiskStatus={
                                                            handleUpdateRiskStatus
                                                        }
                                                        onUpdateCategoryRisksCount={
                                                            onUpdateCategoryRisksCount
                                                        }
                                                        primaryFilters={
                                                            primaryFilters
                                                        }
                                                        risk={risk}
                                                    />
                                                </Accordion.Collapse>
                                            </td>
                                        </tr>
                                    </React.Fragment>
                                );
                            })}
                        </tbody>
                    </table>
                </Accordion>
                {total > 1 && (
                    <Pagination className="pt-2 justify-content-center pagination-rounded">
                        {
                            <Pagination.Prev
                                onClick={() => onPaginate(current_page - 1)}
                                className="paginate_button previous"
                                disabled={current_page === 1}
                                children={
                                    <i className="fas fa-chevron-left"></i>
                                }
                            />
                        }
                        {paginate(current_page, total).map((page, i) =>
                            page !== "..." ? (
                                <Pagination.Item
                                    key={i}
                                    active={current_page === page}
                                    onClick={() => onPaginate(page)}
                                >
                                    {page}
                                </Pagination.Item>
                            ) : (
                                <Pagination.Ellipsis key={i} />
                            )
                        )}
                        {
                            <Pagination.Next
                                onClick={() => onPaginate(current_page + 1)}
                                className="paginate_button next"
                                disabled={current_page === total}
                                children={
                                    <i className="fas fa-chevron-right"></i>
                                }
                            />
                        }
                    </Pagination>
                )}
            </div>
        </div>
    );
};

export default RiskItemsTable;
