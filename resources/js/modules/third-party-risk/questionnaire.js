import React from "react";
import { Button } from "react-bootstrap";
import ReactPagination from "../../common/react-pagination/ReactPagination";
import AuthLayout from "../../layouts/auth-layout/AuthLayout";
import Logo from "../../layouts/auth-layout/components/Logo";
import Question from "./components/Question";
import "./style/style.scss";

export default function Questionnaire(props) {
    return (
        <AuthLayout>
            <div className="card bg-pattern">
                {/* LOGO DISPLAY NAME */}
                <Logo />
                <div className="card-body pb-0">
                    <div className="row" id="questionnaire">
                        <div className="col-12 m-30 title-heading text-center">
                            <h5 className="card-title">Hi Amar Basic,</h5>
                            <p>
                                You have been invited to complete this vendor
                                risk questionnaire. Please read the questions
                                carefully and provide your answers.
                            </p>
                        </div>
                    </div>
                    <ol>
                        <li>
                            <Question question="Does the supplier/vendor follow secure coding principles?" />
                        </li>
                        <li>
                            <Question question="Does the supplier/vendor conduct required testing before deploying the information systems/ applications into production?" />
                        </li>
                        <li>
                            <Question question="Does the supplier/vendor have documented and approved business continuity plans for its critical services?" />
                        </li>
                        <li>
                            <Question question="Does the supplier/vendor have disaster recovery procedures for its IT infrastructure?" />
                        </li>
                        <li>
                            <Question question="Does the supplier/vendor test its business continuity plans or capabilities and taken corrective action when needed?" />
                        </li>
                        <li>
                            <Question question="Does the supplier/vendor have evidence of compliance with relevant laws and regulations?" />
                        </li>
                        {/* <li><Question question="Does the supplier/vendor have a clearly documented procedures for handling customer private and personal employee data in compliance with GDPR and applicable data privacy regulations?" /></li>
                        <li><Question question="Does the supplier/vendor conduct regular audits for checking compliance with information security standards and best practices such as ISO 27001, PCI DSS, etc.?" /></li>
                        <li><Question question="Does the supplier/vendor sign non disclosure agreements with sub contractors, partners, and auditors before providing access to customer data?" /></li>
                        <li><Question question="Does the supplier/vendor monitor the services provided by the sub contractors or partners on behalf of the supplier/vendor to ensure avoiding any service disruption?" /></li>
                        <li><Question question="Does the supplier/vendor evaluate the business continuity capabilities of its sub contractors?" /></li>
                        <li><Question question="Did the supplier/vendor develop and maintain a formal cloud security policy that addresses the local regulatory requirements and requirements of its customers for overall cloud management process?" /></li>
                        <li><Question question="Have the supplier/vendor implemented adequate cloud security controls based on the results of a risk assessment?s" /></li>
                        <li><Question question="Does the supplier/vendor regularly review the security controls implemented on its cloud architecture and make changes where needed?" /></li> */}
                    </ol>
                    <div className="mt-3">
                        <ReactPagination
                            itemsCountPerPage={10}
                            totalItemsCount={50}
                            onChange={() => {}}
                        ></ReactPagination>
                    </div>
                    <div className="d-flex justify-content-end mt-3">
                        <Button>Next</Button>
                    </div>
                </div>
            </div>{" "}
            {/* end card body */}
        </AuthLayout>
    );
}
