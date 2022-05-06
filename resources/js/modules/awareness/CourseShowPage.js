import React, { useEffect, useState } from "react";
import AppLayout from "../../layouts/app-layout/AppLayout";
import courseImg2 from '../../../../public/assets/images/slider/course-2.png'


function CourseShowPage(props) {

    const [courseData, setCourseData] = useState({});

    useEffect(() => {
        setCourseData({
            courseName: 'Social Media Security',
            courseImage: courseImg2,
            description: 'This course provides an overview of the risks while using the latest social media platforms. Through this course you will learn about the security and privacy features available in the most commonly used social media platforms and the best practices to avoid being a victim to attacks on these platforms.',
            duration: '8 min',
            status: 'Completed'
        })
    }, [])

    return (
        <AppLayout>
            <div id="awareness-course-show-page">
                <div className="card mt-4">
                    <div className="card-body">
                        <div className="mb-3">
                            <h4 className="float-start">{courseData.courseName}</h4>
                            <button className="btn btn-secondary float-end">Back</button>
                            <div className="clearfix"></div>
                        </div>
                        <div className="row">
                            <div className="col-lg-3">
                                <img src={courseData.courseImage} alt="courseImage" height={50} className="img-fluid" />
                            </div>
                            <div className="col-lg-6">
                                <p className="my-4 my-lg-0">
                                    {courseData.description}
                                </p>
                            </div>
                            <div className="col-lg-3">
                               
                                <div className="mb-1" style={{maxWidth:'300px'}}>
                                    <i className="far fa-clock me-1"></i>
                                    <b className="me-5">Duration</b>
                                    <b>{courseData.duration}</b>
                                </div>
                                <div style={{maxWidth:'300px'}}>
                                    <i className="fas fa-tachometer-alt me-1"></i>
                                    <b className="me-5">Status</b>
                                    <b>{courseData.status}</b>
                                </div>
                            </div>
                        </div>
                        <div className="row mt-3">
                            <div className="col-12">
                                <form className="text-center">
                                <div className="row text-center justify-content-center">
                                <input id="start" type="checkbox" value="on" name="newattempt" className="me-1" style={{marginTop:'5px'}} />
                                <label for="start">Start a new attempt</label>
                                </div>
                                <button type="submit" className="btn btn-primary"><i className="fas fa-rocket me-1"></i>Launch</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </AppLayout>
    );
}

export default CourseShowPage;