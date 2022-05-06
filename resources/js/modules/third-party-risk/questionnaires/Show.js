import React, {useEffect, useState} from "react";

import {usePage} from "@inertiajs/inertia-react";

import Question from "../components/Question";
import ReactPaginate from "react-paginate";
import AuthLayout from "../../../layouts/auth-layout/AuthLayout";
import Logo from "../../../layouts/auth-layout/components/Logo";

import "../style/questionnaires.scss";
import '../../../common/react-pagination/react-pagination.scss';
import {Alert} from "react-bootstrap";

const Show = () => {
    const perPage = 6;
    const [questionsList, setQuestionsList] = useState([]);
    const [page, setPage] = useState(0);
    const {vendor, questions, token, can_respond} = usePage().props;
    const [answers, setAnswers] = useState([]);
    const [processing, setProcessing] = useState(false);
    const [submitted, setSubmitted] = useState(false);
    const [error, setError] = useState(null);
    const pageCount = Math.ceil(questions.length / perPage);
    const myRef = React.useRef(null);


    useEffect(() => {
        setQuestionsList(questions.slice(page * perPage, (page + 1) * perPage));
    }, [page]);

    useEffect(() => {
        setAnswers(questions.map(q => ({question_id: q.id, answer: q.single_answer?.answer ?? null})));
    }, []);

    const handleSetPage = page => {
        setPage(page);
        window.scrollTo({
            behavior: 'smooth',
            top: myRef.current.offsetTop - 30
        });
    }

    const getAnswer = id => answers.find(a => a.question_id === id)?.answer;
    const setAnswer = (id) => (answer) => {
        setAnswers(prevState => prevState.map(a => a.question_id === id ? ({...a, answer}) : a));
    }

    const handleSubmit = () => {
        setProcessing(true);
        setError(null);
        const unansweredQuestionsCount = answers.filter(a => a.answer === null).length;
        if (unansweredQuestionsCount > 0) {
            window.scrollTo({behavior: 'smooth', top: 0});
            setError(`You still have ${unansweredQuestionsCount} unanswered questions!`);
            setProcessing(false);
            return;
        }
        axiosFetch.post(route('third-party-risk.save-questionnaire',), {answers, token})
            .then(() => {
                setProcessing(false);
                setSubmitted(true);
            });
    }

    if (!can_respond) return (
        <AuthLayout>
            <div className="card bg-pattern">
                <Logo/>
                <div className="card-body pb-0">
                    <div className="row" id="questionnaire">
                        <div className="col-12 m-30 title-heading text-center">
                            <h5 className="card-title">Hi {vendor.contact_name},</h5>
                            <p>
                                You cannot respond to this vendor risk questionnaire because you already submitted your
                                answers.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </AuthLayout>
    )
    return (
        <AuthLayout>
            {error ? (
                <Alert variant="danger" onClose={() => setError(null)} dismissible>
                    {error}
                </Alert>
            ) : null}
            <div className="card bg-pattern">
                <Logo/>
                <div className="card-body pb-0">
                    <div className="row" id="questionnaire">
                        <div className="col-12 m-30 title-heading text-center">
                            <h5 className="card-title">Hi {vendor.contact_name},</h5>
                            {submitted ? (
                                <p>Your response has been recorded, Thank you.</p>
                            ) : (
                                <p>
                                    You have been invited to complete this vendor
                                    risk questionnaire. Please read the questions
                                    carefully and provide your answers.
                                </p>
                            )}
                        </div>
                    </div>
                    {!submitted ? (
                        <div ref={myRef}>
                            <ol>
                                {questionsList.map(({text, id}) =>
                                    <Question
                                        id={id}
                                        question={text}
                                        answer={getAnswer(id)}
                                        setAnswer={setAnswer(id)}
                                        key={id}
                                        id={id}
                                    />
                                )}
                            </ol>
                            {/* <div className="mt-3">
                                <ReactPaginate
                                    className="react-pagination pagination pagination-rounded justify-content-center"
                                    nextLabel="&raquo;"
                                    onPageChange={({selected}) => handleSetPage(selected)}
                                    forcePage={page}
                                    marginPagesDisplayed={1}
                                    pageCount={pageCount}
                                    previousLabel="&laquo;"
                                    pageClassName="page-item"
                                    pageLinkClassName="page-link"
                                    previousClassName="page-item d-none"
                                    previousLinkClassName="page-link"
                                    nextClassName="page-item d-none"
                                    nextLinkClassName="page-link"
                                    breakLabel="..."
                                    breakClassName="page-item"
                                    breakLinkClassName="page-link"
                                    containerClassName="pagination"
                                    activeClassName="active"
                                    renderOnZeroPageCount={null}
                                />
                            </div> */}
                            <div className="d-md-flex d-block text-center justify-content-md-between justify-content-center flex-wrap align-items-center mt-3 mb-3">
                                {page > 0 ?
                                    <button className="btn btn-primary"
                                            onClick={() => handleSetPage(page - 1)}>Previous</button> :
                                    <div/>}
                                      <ReactPaginate
                                    className="react-pagination pagination pagination-rounded justify-content-center align-items-center my-2 my-md-0"
                                    nextLabel="&raquo;"
                                    onPageChange={({selected}) => handleSetPage(selected)}
                                    forcePage={page}
                                    marginPagesDisplayed={1}
                                    pageCount={pageCount}
                                    previousLabel="&laquo;"
                                    pageClassName="page-item"
                                    pageLinkClassName="page-link"
                                    previousClassName="page-item d-none"
                                    previousLinkClassName="page-link"
                                    nextClassName="page-item d-none"
                                    nextLinkClassName="page-link"
                                    breakLabel="..."
                                    breakClassName="page-item"
                                    breakLinkClassName="page-link"
                                    containerClassName="pagination"
                                    activeClassName="active"
                                    renderOnZeroPageCount={null}
                                />
                                {page === pageCount - 1 ? (
                                        <button
                                            className="btn btn-primary"
                                            onClick={handleSubmit}
                                            disabled={processing}>Submit</button>
                                    ) :
                                    <button className="btn btn-primary"
                                            onClick={() => handleSetPage(page + 1)}>Next</button>}

                            </div>
                        </div>
                    ) : null}
                </div>
            </div>
        </AuthLayout>
    );
}
export default Show;
