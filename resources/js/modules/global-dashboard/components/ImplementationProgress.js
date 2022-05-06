import React, { Fragment, useEffect, useState } from "react";
import C3Chart from "react-c3js";
import "c3/c3.css";
import {Card} from "react-bootstrap";
import Chart from "react-apexcharts";

function ImplementationProgress(props) {
    const {
        allControls,
        notApplicableControls,
        implementedControls,
        underReviewControls,
        notImplementedControls,
    } = props;

    const [implementationChart, setImplementationChart] = useState({
        options:{},
        series:[]
    });


    /* Component states */
    // const [data, setData] = useState({
    //     columns: [
    //         ["Implemented", 0],
    //         ["Under Review", 0],
    //         ["Not Implemented", 0],
    //     ],
    //     type: "donut",
    // });



    /* // Implementation Progresss Chart percentage calculation*/
    useEffect(() => {
        let implementedControlsPercentage =
            allControls > 0 ? (implementedControls / allControls) * 100 : 0;
        let underReviewControlsPercentage =
            allControls > 0 ? (underReviewControls / allControls) * 100 : 0;
        let notImplementedControlsPercentage =
            allControls > 0 ? (notImplementedControls / allControls) * 100 : 0;

        /* Updating chart data */
        if(allControls){
        //      setData((prevState) => ({
        //     ...prevState,
        //     columns: [
        //         ["Implemented - "+implementedControls, implementedControlsPercentage],
        //         ["Under Review - "+underReviewControls, underReviewControlsPercentage],
        //         ["Not Implemented - "+notImplementedControls, notImplementedControlsPercentage],
        //     ],
        // }));
        renderCharts();
    }
    }, [
        allControls,
        notApplicableControls,
        implementedControls,
        underReviewControls,
        notImplementedControls,
    ]);

    // const size = {
    //     height: 328,
    //     width: 240,
    // };

    // const color = {
    //     pattern: ["#359f1d", "#5bc0de", "#cf1110"],
    // };

    const renderCharts = () => {
        let ipc = {};
        ipc.options = {
            chart: {
                type: 'donut',
            },
            tooltip: {
                enabled: true,
                fillSeriesColor: false,
                theme:false,
                style:{
                    fontSize:'15px'
                }
            },
            stroke: {
                colors: ['#fff'],
            },
            colors:["#359f1d", "#5bc0de", "#cf1110"],
            labels:["Implemented","Under Review","Not Implemented"],
            legend: {
                show: true,
                position: 'bottom',
                formatter: function(seriesName, opts) {
                    return [seriesName, " - ", opts.w.globals.series[opts.seriesIndex]]
                }
            },
            dataLabels: {
                enabled: false,
            },
            states: {
                active: {
                    filter: {
                        type: 'none',
                    }
                }
            },
            plotOptions: {
                pie: {
                    expandOnClick: true,
                    donut: {
                        size: "90%",
                        background: "transparent",
                        labels: {
                            show: true,
                            name: {
                                show: false,
                                fontSize: "25px",
                                color: "black",
                            },
                            value:{
                                fontSize: "2.86rem",
                                color: "#6e6b7b",
                            },
                            total: {
                                show: true,  
                                showAlways:true,
                                // formatter:function(w){
                                //     return allControls
                                // }
                                // label:'Total Risks'
                            },
                        },
                    },
                },
            }
        };
        ipc.series=[implementedControls,underReviewControls,notImplementedControls]
        setImplementationChart(ipc);
    };

    return (
        <Fragment>
            <Card className="h-100">
                <Card.Body className="implementation">
                    <h4 className="head-title mt-0">Implementation Progress</h4>
                    <div className="chart-box">
                        <div id="implementation-progress-chart" dir="ltr">
                        {/* {props.allControls ?  <C3Chart
                                data={data}
                                size={size}
                                color={color}
                                donut={{ width: 10, title: props.allControls }}
                            />: ""} */}
                            {props.allControls ?  
                             <Chart
                               options={implementationChart.options}
                               series={implementationChart.series}
                               type="donut"
                               height={328}
                               />: ""}
                        </div>
                    </div>
                </Card.Body>
            </Card> 
        </Fragment>
    );
}

export default ImplementationProgress;
