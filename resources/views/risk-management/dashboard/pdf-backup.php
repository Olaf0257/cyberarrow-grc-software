




    // RISK BY SEVERITY AND CLOSED STATUS STARTS HERE


    var riskByClosedStatusChart = new Chart(document.getElementById("risk-by-closed-status-chart"), {
        type: 'doughnut',
        data:  {
            datasets: [
                {
                    data: ["{{ $totalClosedLowRisks }}", 0, 0, 0, "{{ $totalClosedRisks - $totalClosedLowRisks }}"],
                    backgroundColor: [
                        "#92D050",
                        "#FF5",
                        "#FFC000",
                        "#FF0000",
                        "#F5F5F5"
                    ]
                },
                {
                    data: [0, "{{ $totalClosedModerateRisks }}", 0, 0, "{{ $totalClosedRisks - $totalClosedModerateRisks }}"],
                    backgroundColor: [
                        "",
                        "#FF5",
                        "#F5F5F5"
                    ]
                },
                {
                    data: [0, 0, "{{ $totalClosedHighRisks }}", 0, "{{ $totalClosedRisks - $totalClosedHighRisks }}"],
                    backgroundColor: [
                        "",
                        "",
                        "#FFC000"
                    ]
                },
                {
                    data: [0, 0, 0, "{{ $totalClosedExtremeRisks }}", "{{ $totalClosedRisks - $totalClosedExtremeRisks }}"],
                    backgroundColor: [
                        "",
                        "",
                        "",
                        "#FF0000"
                    ]
                }
            ],
            labels: [
                "Low",
                "Moderate",
                "High",
                "Extreme",
                ""
            ]
        },
        options: {
            title : {
                display: false
            },
            legend: {
                display: false
            },
            borderColor: "#f00",
            elements: {
                center: {
                    text: "{{ $totalClosedRisks }} ( Closed )",
                    fontSize: 2.5,
                    textY: 1.9
                }
            },
            plugins: {
                // Change options for ALL labels of THIS CHART
                datalabels: false,
                labels: {
                    render: function (args) {
                        if(args.label){
                            return args.label +' '+ args.value;
                        }  
                    },
                    arc: true,
                    fontColor: '#000',
                    fontSize: 24,
                    position: 'center',
                },

            }
        }
    });



    //RISK BY SEVERITY AND CLOSED STATUS ENDS HERE




    
    // Risk by cateogries chart script Ends here


