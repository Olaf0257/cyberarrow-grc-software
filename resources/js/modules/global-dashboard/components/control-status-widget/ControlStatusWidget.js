import React, { Fragment, useEffect } from "react";
import styles from "./control-status-widget.module.css";
import feather from "feather-icons";
import { Col, Row } from "react-bootstrap";
import { Card } from "react-bootstrap";
function ControlStatusWidget(props) {
    const {
        allControls,
        notApplicableControls,
        implementedControls,
        underReviewControls,
        notImplementedControls,
    } = props;
    useEffect(() => {
        feather.replace();
    }, []);
    return (
        <Fragment>
            <div className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-5 g-3">
                <div className="col">
                    <Card className="mb-0">
                        <Card.Body className="control-stat widget-1" id="controls-stats-widget">
                            <Row>
                                <Col xs={3} md={2} className="ps-4 ps-sm-2 ps-md-0">
                                    <i data-feather="box" className="text-muted me-1" />
                                </Col>
                                <Col xs={9} md={10}>
                                    <h3 className="mt-0 control-num" id="all-controls"> {allControls} </h3>
                                    <span className="control-text">
                                        All Controls
                                    </span>
                                </Col>
                            </Row>
                        </Card.Body>
                    </Card>
                </div>                
                <div className="col">
                    <Card className="mb-0">
                        <Card.Body className="control-stat widget-1" id="controls-stats-widget">
                            <Row>
                                <Col xs={3} md={2} className="ps-4 ps-sm-2 ps-md-0">
                                    <i data-feather="delete" className="text-muted me-1" />
                                </Col>
                                <Col xs={9} md={10}>
                                    <h3 className="mt-0 control-num" id="not-applicable-controls"> {notApplicableControls} </h3>
                                    <span className="control-text">
                                        Not Applicable
                                    </span>
                                </Col>
                            </Row>
                        </Card.Body>
                    </Card>
                </div>               
                <div className="col">
                    <Card className="mb-0">
                        <Card.Body className="control-stat widget-1" id="controls-stats-widget">
                            <Row>
                                <Col xs={3} md={2} className="ps-4 ps-sm-2 ps-md-0">
                                    <i data-feather="flag" className="text-muted me-1" />
                                </Col>
                                <Col xs={9} md={10}>
                                    <h3 className="mt-0 control-num" id="implemented-controls"> {implementedControls} </h3>
                                    <span className="control-text">
                                        Implemented Controls
                                    </span>
                                </Col>
                            </Row>
                        </Card.Body>
                    </Card>
                </div>           
                <div className="col">
                    <Card className="mb-0">
                        <Card.Body className="control-stat widget-1" id="controls-stats-widget">
                            <Row>
                                <Col xs={3} md={2} className="ps-4 ps-sm-2 ps-md-0">
                                    <i data-feather="star" className="text-muted me-1" />
                                </Col>
                                <Col xs={9} md={10}>
                                    <h3 className="mt-0 control-num" id="under-review-controls"> {underReviewControls} </h3>
                                    <span className="control-text">
                                        Under Review
                                    </span>
                                </Col>
                            </Row>
                        </Card.Body>
                    </Card>
                </div>           
                <div className="col">
                    <Card className="mb-0">
                        <Card.Body className="control-stat widget-1" id="controls-stats-widget">
                            <Row>
                                <Col xs={3} md={2} className="ps-4 ps-sm-2 ps-md-0">
                                    <i data-feather="x-square" className="text-muted me-1" />
                                </Col>
                                <Col xs={9} md={10}>
                                    <h3 className="mt-0 control-num" id="not-implemented-controls"> {notImplementedControls} </h3>
                                    <span className="control-text">
                                        Not Implemented
                                    </span>
                                </Col>
                            </Row>
                        </Card.Body>
                    </Card>
                </div>
            </div>

            {/* old */}
            {/* <div className="col-xl-3 col-lg-6 col-md-6">
                <div className="card">
                    <div
                        className="control-stat card-body p-0"
                        id="controls-stats-widget"
                    >
                        <div className="title">
                            <h4 className="head-title">Control Status</h4>
                        </div>
                        <div
                            className={`control-items ${styles.controlItemsStyle}`}
                        >
                            <div className="total">
                                <i data-feather="box" />
                                <span className="control-text">
                                    All Controls
                                </span>
                                <span
                                    className="float-end control-num"
                                    id="all-controls"
                                >
                                    {allControls}
                                </span>
                            </div>
                            <div className="total py-3">
                                <i data-feather="delete" />
                                <span className="control-text">
                                    Not Applicable
                                </span>
                                <span
                                    className="float-end control-num"
                                    id="not-applicable-controls"
                                >
                                    {notApplicableControls}
                                </span>
                            </div>
                            <div className="total">
                                <i data-feather="flag" />
                                <span className="control-text">
                                    Implemented Controls
                                </span>
                                <span
                                    className="float-end control-num"
                                    id="implemented-controls"
                                >
                                    {implementedControls}
                                </span>
                            </div>
                            <div className="total py-3">
                                <i data-feather="star" />
                                <span className="control-text">
                                    Under Review
                                </span>
                                <span
                                    className="float-end control-num"
                                    id="under-review-controls"
                                >
                                    {underReviewControls}
                                </span>
                            </div>
                            <div className="total">
                                <i data-feather="x-square" />
                                <span className="control-text">
                                    Not Implemented Controls
                                </span>
                                <span
                                    className="float-end control-num"
                                    id="not-implemented-controls"
                                >
                                    {notImplementedControls}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div> */}
        </Fragment>
    );
}

export default ControlStatusWidget;
