const RiskScorePicker = function(config) {
    this.data = config.data




    this.createRiskScoreMatrix = function (riskScorePickerEl) {
       const tableEl = document.createElement('table')
       const riskScorePickerInput = riskScorePickerEl.querySelector('.risk-score-picker-input')

       const riskScores = this.data.riskScores

        /* Creating row */
       for (const key in riskScores) {
           if (Object.hasOwnProperty.call(riskScores, key)) {
               const riskScoreChunk = riskScores[key];

               let tr = document.createElement('tr')

                /* Creating td */
                for (const key in riskScoreChunk) {
                    if (Object.hasOwnProperty.call(riskScoreChunk, key)) {
                        const riskScore = riskScoreChunk[key];
                        
                        /* Creating td */
                        let td = document.createElement('td')
                         /* Find color for matrix cell*/
                        let riskScoreLevel = this.getRiskScoreLevelByScore(riskScore.score)
                        /* Giving td color*/
                        let riskScoreColor = riskScoreLevel.color
                        td.style.backgroundColor = riskScoreColor

                        /* adding data attributes */
                        td.dataset.color = riskScoreColor
                        td.dataset.scoreId = riskScore.id
                        td.dataset.likelihoodId = riskScore.likelihood_id
                        td.dataset.impactId = riskScore.impact_id
                        td.dataset.score = riskScore.score

                        let tdContent = document.createTextNode(riskScore.score)
                        td.appendChild(tdContent)

                       


                        /* Adding event listener*/
                        td.addEventListener("click", function () {
                            let riskScorePickerToggler = riskScorePickerEl.querySelector('.risk-score-picker-toggler');
                            let scoreColor = this.dataset.color

                            /* Updating the input value*/
                            riskScorePickerInput.value = this.dataset.scoreId


                            riskScorePickerToggler.style.backgroundColor = scoreColor
                            riskScorePickerToggler.innerHTML = this.dataset.score
                        })

                        tr.appendChild(td)
                    }
                }


                /* Appending tr to table*/
                tableEl.insertBefore(tr, tableEl.childNodes[0])
           }
       }



       return tableEl
    }

    this.getRiskMaxScore = function() {
        const riskScores = [].concat.apply([], this.data.riskScores)

        let maxScore = Math.max.apply(Math, riskScores.map(function(o) { 
            return o.score; 
        }))

        return maxScore;
    }

    this.getRiskScoreById = function (id) {
        const riskScores = [].concat.apply([], this.data.riskScores)

        const riskScore = riskScores.find(riskScore => riskScore.id == id)

        return riskScore ? riskScore.score : 0
    }

    this.getRiskScoreLevelByScore = function (cellScore) {
        const riskScoreLevels = this.data.riskScoreLevels
        const riskScoreLevelsLastIndex = riskScoreLevels.length-1

        

        for (const key in riskScoreLevels) {
            if (Object.hasOwnProperty.call(riskScoreLevels, key)) {
                const index = parseInt(key)
                const riskScoreLevel = riskScoreLevels[index];
                const startScore = (index == 0) ? 1 : riskScoreLevels[index-1].max_score+1
                const endScore = (index == riskScoreLevelsLastIndex) ? this.riskMaxScore : riskScoreLevel.max_score
                
                if(cellScore >= startScore && cellScore <= endScore){
                    return riskScoreLevel
                }
            }
        }
    }

    this.init = function () {
        /* Setting the risk max score */
        this.riskMaxScore = this.getRiskMaxScore();

        this.renderRiskScorePicker()

    }

    this.renderRiskScorePicker = function() {
        let riskScorePickers = document.querySelectorAll('.risk-score-picker')

        for (const key in riskScorePickers) {
            if (Object.hasOwnProperty.call(riskScorePickers, key)) {
                const riskScorePickerEl = riskScorePickers[key];
                
                /* creating risk score picker trigger button */
                let scorePickerTriggerBtn = document.createElement('button')
                scorePickerTriggerBtn.className = 'risk-score-picker-toggler'
                scorePickerTriggerBtn.setAttribute("type","button");

                /*  Assigning toggler button score*/
                let riskScorePickerInput = riskScorePickerEl.querySelector('.risk-score-picker-input')

                const riskScoreId = riskScorePickerInput.value;

                /* finding score */
                let riskScore = this.getRiskScoreById(riskScoreId)

                var score = document.createTextNode(riskScore);

                scorePickerTriggerBtn.appendChild(score)

                riskScorePickerEl.appendChild(scorePickerTriggerBtn)

 
                /* Creating score picker dropdown */
                let scorePickerDropdown = document.createElement('div')

                scorePickerDropdown.className = 'risk-score-picker-dropdown'

                riskScorePickerEl.appendChild(scorePickerDropdown)


                /* Adding score matrix */
                let scoreMatrix = this.createRiskScoreMatrix(riskScorePickerEl)


                scorePickerDropdown.appendChild(scoreMatrix)


                scorePickerTriggerBtn.addEventListener("click", function () {
                    scorePickerDropdown.classList.toggle("show")
                })
            }
        }
    }
}