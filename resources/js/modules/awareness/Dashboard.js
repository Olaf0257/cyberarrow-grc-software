import React, { useEffect, useState } from "react";
import AppLayout from "../../layouts/app-layout/AppLayout";
import Chart from "react-apexcharts";
import "./styles/style.scss"
// Import Swiper React components
import { Swiper, SwiperSlide } from "swiper/react";

// Import Swiper styles
import "swiper/css";
import "swiper/css/pagination"
// import Swiper core and required modules
import SwiperCore, {
    Pagination, Navigation
} from 'swiper';

import courseImg1 from '../../../../public/assets/images/slider/course-1.png'
import courseImg2 from '../../../../public/assets/images/slider/course-2.png'
import courseImg3 from '../../../../public/assets/images/slider/course-3.jpg'
import courseImg4 from '../../../../public/assets/images/slider/course-4.jpg'


// install Swiper modules
SwiperCore.use([Pagination, Navigation]);


function AwarenessDashboard(props) {

    const courseData = [{
        courseName: 'Physical & Environmental Security',
        endDate: '06-10-2021',
        duration: '8 min',
        score: '0',
        status: 'In Progress'
    },
    {
        courseName: 'Malware Security',
        endDate: '06-10-2021',
        duration: '8 min',
        score: '0',
        status: 'Not Started'
    },
    {
        courseName: 'Classify Your Data',
        endDate: '17-11-2021',
        duration: '8 min',
        score: '0',
        status: 'Not Started'
    },
    {
        courseName: 'Social Media Security',
        endDate: '11-11-2021',
        duration: '8 min',
        score: '100',
        status: 'Complete'
    },
    {
        courseName: 'Mobile Security',
        endDate: '06-10-2021',
        duration: '8 min',
        score: '60',
        status: 'Not Started'
    },
    {
        courseName: 'Keep it Clean',
        endDate: '06-10-2021',
        duration: '8 min',
        score: '0',
        status: 'Complete'
    }]

    const [progressChart, setProgressChart] = useState({});
    const [passFailChart, setPassFailChart] = useState({});
    const [scoreChart, setScoreChart] = useState({});

    useEffect(() => {
        renderCharts();
    }, [])

    const renderCharts = () => {
        //Donut piechart
        let chartData = {}
        chartData.options = {
            chart: {
                zoom: {
                    enabled: true,
                    type: "x",
                    autoScaleYaxis: false,
                    zoomedArea: {
                        fill: {
                            color: "#90CAF9",
                            opacity: 0.4,
                        },
                        stroke: {
                            color: "#0D47A1",
                            opacity: 0.4,
                            width: 1,
                        },
                    },
                },
            },
            colors: ["#cf1110", "#ffc107", "#359f1d"],
            labels: ['Not Started', 'In Progress', 'Completed'],
            legend: {
                position: 'top'
            },
            dataLabels: {
                enabled: false,
            },
            plotOptions: {
                pie: {
                    expandOnClick: true,
                    donut: {
                        size: "50%",
                        background: "transparent",
                    },
                },
            },
            tooltip: {
                enabled: true,
            },
        };
        chartData.series = [5, 30, 50]
        setProgressChart(chartData);

        chartData = {};
        chartData.options = {
            chart: {
                zoom: {
                    enabled: true,
                    type: "x",
                    autoScaleYaxis: false,
                    zoomedArea: {
                        fill: {
                            color: "#90CAF9",
                            opacity: 0.4,
                        },
                        stroke: {
                            color: "#0D47A1",
                            opacity: 0.4,
                            width: 1,
                        },
                    },
                },
            },
            colors: ["#cf1110", "#5bc0de"],
            labels: ['Pass', 'Fail'],
            legend: {
                position: 'top'
            },
            dataLabels: {
                enabled: false,
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        offset: -5
                    }
                }
            },
            tooltip: {
                enabled: true,
            },
        }

        chartData.series = [97, 3]

        setPassFailChart(chartData);

        chartData = {};
        chartData.options = {
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            title: {
                text: 'Score',
                align: 'center',
                floating: false,
                style: {
                    fontSize: '14px',
                    fontWeight: 'normal',
                    fontFamily: undefined,
                },
            },
            plotOptions: {
                bar: {
                    barHeight: '70%',
                    distributed: true,
                    horizontal: true,
                    dataLabels: {
                        position: 'bottom'
                    },
                },
            },
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: {
                    colors: ['#fff']
                },
                formatter: function (val, opt) {
                    return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val
                },
                offsetX: 0,
                dropShadow: {
                    enabled: true
                }
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            xaxis: {
                categories: ['Social Media Security', 'Keep It Clean', 'First Line of Defense'],
                max: 100
            },
            // yaxis: {
            //     max: 100
            // },
            yaxis: {
                labels: {
                    show: false
                }
            },
            grid: {
                show: true,
            },
            fill: {
                opacity: 0.7
            },
            colors: ['#28a944'],
            tooltip: {
                theme: 'dark',
                x: {
                    show: false
                },
                y: {
                    title: {
                        formatter: function () {
                            return ''
                        }
                    }
                }
            },
            legend: {
                show: false
                // position:'top'
            }
        }

        chartData.series = [{
            data: [90, 80, 100]
        }]

        setScoreChart(chartData);
    }



    return (
        <AppLayout>
            <div id="awareness-dashboard-page">
                {/* breadcrumbs */}
                <div className="row">
                    <div className="col-12">
                        <div className="page-title-box">
                            <h4 className="page-title">My Courses</h4>
                        </div>
                    </div>
                </div>
                {/* end of breadcrumbs */}

                <div className="row">
                    <div className="col-12">
                        <Swiper slidesPerView={1} spaceBetween={20} pagination={{
                            "clickable": true
                        }}
                            breakpoints={{
                                1200: {
                                    slidesPerView: 4
                                },
                                992: {
                                    slidesPerView: 3
                                },
                                768: {
                                    slidesPerView: 3
                                },
                                576: {
                                    slidesPerView: 2
                                }

                            }}
                            className="mySwiper">
                            <SwiperSlide>
                                <div className="card">
                                    <img className="card-img-top" src={courseImg1} alt="Card image" />
                                    <div className="card-body">
                                        <p className="text-center mb-0 text-dark">Classify Your Data</p>
                                    </div>
                                    <div className="swiper-img-detail">
                                        <button type="button" className="btn btn-primary">
                                            Go to Course
                                        </button>
                                    </div>
                                </div>
                            </SwiperSlide>
                            <SwiperSlide >
                                <div className="card">
                                    <img className="card-img-top" src={courseImg2} alt="Card image" />
                                    <div className="card-body">
                                        <p className="text-center mb-0 text-dark">Social Media Security</p>
                                    </div>
                                    <div className="swiper-img-detail">
                                        <button type="button" className="btn btn-primary">
                                            Go to Course
                                        </button>
                                    </div>
                                </div>
                            </SwiperSlide>
                            <SwiperSlide >
                                <div className="card">
                                    <img className="card-img-top" src={courseImg3} alt="Card image" />
                                    <div className="card-body">
                                        <p className="text-center mb-0 text-dark">Keep It Clean</p>
                                    </div>
                                    <div className="swiper-img-detail">
                                        <button type="button" className="btn btn-primary">
                                            Go to Course
                                        </button>
                                    </div>
                                </div>
                            </SwiperSlide>
                            <SwiperSlide >
                                <div className="card">
                                    <img className="card-img-top" src={courseImg4} alt="Card image" />
                                    <div className="card-body">
                                        <p className="text-center mb-0 text-dark">First Line of Defense</p>
                                    </div>
                                    <div className="swiper-img-detail">
                                        <button type="button" className="btn btn-primary">
                                            Go to Course
                                        </button>
                                    </div>
                                </div>
                            </SwiperSlide>
                            <SwiperSlide >
                                <div className="card">
                                    <div className="card-body project-box project-div d-flex justify-content-center align-items-center" style={{ minHeight: '15.5rem', fontSize: '5rem', color: '#323b43' }}>
                                        <i className="mdi mdi-plus" />
                                    </div>
                                    <div className="swiper-img-detail">
                                        <button type="button" className="btn btn-primary">
                                            Add New
                                        </button>
                                    </div>
                                </div>
                            </SwiperSlide>

                        </Swiper>
                    </div>
                </div>

                <div className="row">
                    <div className="col-12 col-lg-7">
                        <div className="card-box">
                            <h4 className="header-title mb-3">Enrolled Courses</h4>
                            <div className="table-responsive">
                                <table className="table text-nowrap table-striped m-0">
                                    <thead >
                                        <tr className="text-center">
                                            <th>Course Name</th>
                                            <th>End Date</th>
                                            <th>Duration</th>
                                            <th>Score</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {
                                            (courseData || []).map((course, index) => {
                                                return (
                                                    <tr key={index.toString()} className="text-center">
                                                        <td>
                                                            {course.courseName}
                                                        </td>
                                                        <td>
                                                            {course.endDate}
                                                        </td>
                                                        <td>
                                                            {course.duration}
                                                        </td>
                                                        <td>
                                                            {course.score}
                                                        </td>
                                                        <td >
                                                            {course.status === "In Progress" && <span className='badge bg-warning'>
                                                                {course.status}
                                                            </span>
                                                            }
                                                            {course.status === "Complete" && <span className='badge bg-success'>
                                                                {course.status}
                                                            </span>
                                                            }
                                                            {course.status === "Not Started" && <span className='badge bg-danger'>
                                                                {course.status}
                                                            </span>
                                                            }
                                                        </td>
                                                    </tr>
                                                );
                                            })
                                        }
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div className="card-box">
                            <div className="bar-chart mt-2">
                                <h4 className="header-title">
                                    My Score Chart
                                </h4>
                                {scoreChart.series && (
                                    <Chart
                                        options={
                                            scoreChart.options
                                        }
                                        series={
                                            scoreChart.series
                                        }
                                        type="bar"
                                        height={300}
                                        className="apex-charts"
                                    />
                                )}
                            </div>
                        </div>
                    </div>
                    <div className="col-12 col-lg-5">
                        <div className="donut-pie-chart card-box">
                            <h4 className="header-title">
                                My Progress
                            </h4>
                            {progressChart.series && (
                                <Chart
                                    options={
                                        progressChart.options
                                    }
                                    series={
                                        progressChart.series
                                    }
                                    type="donut"
                                    height={350}
                                    className="apex-charts"
                                />
                            )}

                        </div>

                    </div>
                </div>
            </div>

        </AppLayout>

    );
}

export default AwarenessDashboard;