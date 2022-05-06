import React, {Fragment} from 'react';
import Chart from 'react-apexcharts'

function PolicyCompletionChart(props) {
    const {completedAcknowledgementPercentage} = props
    const options = {
        colors: ["#28a745"],
        plotOptions: {
            radialBar: {
                hollow: {
                    margin: 10,
                    size: "60%"
                },
                dataLabels: {
                    name: {
                        show: false
                    },
                    value: {
                        show: true,
                        fontSize: '14px',
                        offsetX: 0,
                        offsetY: 0,
                        formatter: function (val) {
                            return `${ Math.round(completedAcknowledgementPercentage) } %`
                        }
                    },
                }
            }
        },

        stroke: {
            lineCap: "round",
        },
        labels: [""]
    };
    return (
        <Fragment>
             <h4 className="header-title text-center mb-3">Completion</h4>
            <Chart options={options} series={[Math.round(completedAcknowledgementPercentage)]} type="radialBar" width="100%" height="150"></Chart>
        </Fragment>
    );
}

export default PolicyCompletionChart;
