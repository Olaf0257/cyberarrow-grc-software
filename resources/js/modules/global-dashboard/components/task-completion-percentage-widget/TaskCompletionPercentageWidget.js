import React, { Fragment, useState, useEffect } from "react";
import {
    CircularInput,
    CircularTrack,
    CircularProgress,
    StyledCircularInput,
    CircularThumb,
    StyledCircularTrack,
} from "react-circular-input";
import styles from "./task-completion-percentage-widget.module.css";
import ReactApexChart from "react-apexcharts";

function TaskCompletionPercentageWidget(props) {
    const propsData = { props };
    const globalSetting = propsData.props.globalSetting;
    const { completedTasksPercent } = props;
    const [value, setValue] = useState(0);

    // custom limits
    const min = 0;
    const max = 1;

    // get value within limits
    const valueWithinLimits = (v) => Math.min(Math.max(v, min), max);

    // custom range
    const range = [0, 100];

    // scaled range value
    const rangeValue = value * (range[1] - range[0]) + range[0];

    useEffect(() => {
        if (typeof completedTasksPercent != "undefined") {
            setValue(completedTasksPercent / 100);
        }
    }, [completedTasksPercent]);

    const chartOption = {
        colors:["359f1d"],
        chart: {
            height: 350,
            type: 'radialBar',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                hollow: {
                    margin: 0,
                    size: '80%',
                    background: '#fff',
                    image: undefined,
                    imageOffsetX: 0,
                    imageOffsetY: 0,
                    position: 'front',
                    // dropShadow: {
                    //     enabled: true,
                    //     top: 3,
                    //     left: 0,
                    //     blur: 4,
                    //     opacity: 0.01
                    // }
                },
                track:{
                    dropShadow: {
                        enabled: false,
                    }
                },
                dataLabels: {
                    show: true,
                    name: {
                        offsetY: -10,
                        show: false,
                        color: '#343a40',
                        fontSize: '17px'
                    },
                    value: {
                        formatter: function (val) {
                            return val + '%';
                        },
                        color: '#6e6b7b',
                        fontSize: '2.86rem',
                        fontWeight:400,
                        show: true,
                    }
                }
            }
        },
        fill: {
            type: 'gradient',
            colors: ['#359f1d'],
            gradient: {
                shade: 'light',
                type: 'horizontal',
                shadeIntensity: 1,
                gradientToColors: ['#359f1d'],
                inverseColors: true,
                opacityFrom: 1,
                opacityTo: 0.7,
                stops: [0, 100]
            }
        },
        stroke: {
            lineCap: 'round',
            width: 1
        },
        labels: ['Task Completion Percentage'],
    }

    const chartSeries = [completedTasksPercent]

    return (
        <Fragment>
            <ReactApexChart options={chartOption} series={chartSeries} type="radialBar" height={310} />
            <h4 className="text-center mb-0 mt-2">Task Completion Percentage</h4>
            {/* <div className="col-xl-3 col-lg-6 col-md-6">
                <div className="card">
                    <div
                        className="task-completion card-body p-0"
                        id="tasks-completion-percentage-widget"
                    >
                        <div className="title">
                            <h4 className="head-title">
                                Task Completion Percentage
                            </h4>
                        </div>
                        <div
                            className="widget-chart text-center py-2"
                            id="task-completion-widget"
                            dir="ltr"
                        >
                            <CircularInput
                                value={valueWithinLimits(value)}
                                className={styles.taskCompletionWidgetInput}
                            >
                                <CircularTrack stroke="#eeeeee" />
                                <CircularProgress
                                    stroke={globalSetting.secondary_color}
                                />
                                <text
                                    x={100}
                                    y={100}
                                    textAnchor="middle"
                                    dy="0.3em"
                                    fontSize="2rem"
                                    fill={globalSetting.secondary_color}
                                    fontWeight="bold"
                                >
                                    {Math.round(rangeValue)}%
                                </text>
                            </CircularInput>
                        </div>
                    </div>
                </div>
            </div> */}
        </Fragment>
    );
}

export default TaskCompletionPercentageWidget;
