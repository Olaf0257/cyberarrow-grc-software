import React, {Fragment} from 'react';
import Chart from 'react-apexcharts'

function EmailSentChart(props) {
    const {emailSentSuccess, totalEmailSent} = props
    const totalEmailSentPercentage = (emailSentSuccess && totalEmailSent) ?  emailSentSuccess * 100 / totalEmailSent : 0;
    const options = {
        colors: ["rgb(178, 221, 76)"],

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
                            return emailSentSuccess
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
            <h4 className="header-title text-center mb-3">Email Sent</h4>
            <Chart options={options} series={[totalEmailSentPercentage]} type="radialBar" width="100%" height="150"></Chart>
        </Fragment>
    );
}

export default EmailSentChart;
