import React, { useEffect } from 'react';
import { Link } from '@inertiajs/inertia-react'
import {Card, Col, Row} from "react-bootstrap";
import feather from "feather-icons";

function ApprovalWidget(props) {
    const { totalNeedMyApprovalTasks, totalUnderReviewMyTasks } = props;
    useEffect(() => {
        feather.replace();
    }, []);
    return (
        <>
            <Card className="third approval-widget">
                <Card.Body>
                    <h4 className="header-title ">Approvals</h4>
                    <Row className="align-items-center approval">
                        <Col xs={7} className="offset-1 pe-0 d-flex align-items-center">
                            <i data-feather="clock" className="text-muted" style={{width:'25px',height:'25px'}}></i>
                                <span className="mx-2 text-dark fa-2x">
                                    {totalUnderReviewMyTasks}
                                </span>
                            <h5 className="text-muted">Under Review</h5>
                        </Col>
                        <Col xs={4} className="ps-0">
                            <Link
                                href={route('compliance.tasks.under-review')}
                                className="btn btn-light width-sm btn-rounded go-btn"
                                method="get"
                            >
                                Go
                            </Link>
                        </Col>
                    </Row>
                    <hr />
                    <Row className="align-items-center approval">
                        <Col xs={7} className="offset-1 pe-0 d-flex align-items-center">
                            <i data-feather="thumbs-up" className="text-muted" style={{width:'25px',height:'25px'}}></i>
                                <span className="mx-2 text-dark fa-2x">
                                    {totalNeedMyApprovalTasks}
                                </span>
                            <h5 className="text-muted">Require My Approval</h5>
                        </Col>
                        <Col xs={4} className="ps-0">
                            <Link
                                href={route('compliance.tasks.need-my-approval')}
                                className="btn btn-light width-sm btn-rounded go-btn"
                                method="get"
                            >
                                Go
                            </Link>
                        </Col>
                    </Row>
                </Card.Body>
            </Card>
            {/*<div className="card third approval-widget">*/}
            {/*    <div className="card-body p-0">*/}
            {/*        <div className="title">*/}
            {/*            <h4 className="header-title ">Approvals</h4>*/}
            {/*        </div>*/}
            {/*        <div className="approval d-flex justify-content-around py-4"> */}
            {/*            <div className="boxx">*/}
            {/*                <i className="fas fa-clock fa-2x"><span className="mx-2 text-muted">{totalUnderReviewMyTasks}</span></i>*/}
            {/*                <hr />*/}
            {/*                <h5 className="text-muted">Under Review</h5>*/}
            {/*                <hr />*/}
            {/*                <Link*/}
            {/*                    href={route('compliance.tasks.under-review')}*/}
            {/*                    className="btn btn-primary width-sm btn-rounded go-btn"*/}
            {/*                    method="get"*/}
            {/*                >*/}
            {/*                    Go*/}
            {/*                </Link>*/}
            {/*            </div>*/}
            {/*            <div className="boxx second-box">*/}
            {/*                <i className="fas fa-thumbs-up fa-2x"><span className="mx-2 text-muted">{totalNeedMyApprovalTasks}</span></i>*/}
            {/*                <hr />*/}
            {/*                <h5 className="text-muted">Require My Approval</h5>*/}
            {/*                <hr />*/}
            {/*                <Link*/}
            {/*                    href={route('compliance.tasks.need-my-approval')}*/}
            {/*                    className="btn btn-primary width-sm btn-rounded ms-3 go-btn"*/}
            {/*                    method="get"*/}
            {/*                >*/}
            {/*                    Go*/}
            {/*                </Link>*/}
            {/*            </div>*/}
            {/*        </div> */}
            {/*    </div>*/}
            {/*</div>*/}
        </>
    );
}

export default ApprovalWidget;
