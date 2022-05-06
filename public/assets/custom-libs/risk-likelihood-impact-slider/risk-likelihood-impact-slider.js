const RiskLikelihoodAndImpactSlider = function(config) {

    this.data = {
        likelihoods: config.likelihoods,
        impacts: config.impacts,
        scores: config.scores,
        scoreLevels: config.levels,
        sliderImpacts: [],
        sliderLikelihoods: []
    }

    this.init = function () {
        const data = this.data
        const likelihoods = data.likelihoods
        const impacts = data.impacts

        /* Creating likelihoods slider array*/
        for (const key in likelihoods) {
            if (Object.hasOwnProperty.call(likelihoods, key)) {
                const likelihood = likelihoods[key];

                data.sliderLikelihoods.push(likelihood.name);
            }
        }

        /* Creating impact slider array*/
        for (const key in impacts) {
            if (Object.hasOwnProperty.call(impacts, key)) {
                const impact = impacts[key];
                data.sliderImpacts.push(impact.name)
            }
        }

        this.renderLikelihoods()
        this.renderImpacts()
    }

    this.renderImpacts = function() {
        const mainObject = this
        const data = this.data

        $(".impact").each(function () {
            let impactId = this.id
            let riskId = this.getAttribute('data-risk-id')

            $(`#${impactId}`).ionRangeSlider({
                    grid: true,
                    skin: "round",
                    values: data.sliderImpacts,
                    onChange: function (data) {
                        let likelihoodIndex = $(`#likelihood-slider-el-${riskId}`).data().from
                        let impactIndex = data.from;

                        /* updating impact input val*/
                         $(`#impact-input-el-${riskId}`).val(impactIndex)

                        mainObject.updateRiskScoreAndLevel(riskId, likelihoodIndex, impactIndex)
                    }
            });
        })
    }

    this.renderLikelihoods = function () {
        const mainObject = this
        const data = this.data

       
        // range slider
        $(".likelihood").each(function () {
            let likelihoodId = this.id
            let riskId = this.getAttribute('data-risk-id')

            $likelihoodSliderEl = $(`#${likelihoodId}`)

            $likelihoodSliderEl.ionRangeSlider({
                grid: true,
                skin: "round",
                values: data.sliderLikelihoods,
                onChange: function (data) {
                    let likelihoodIndex = data.from
                    let impactIndex = $(`#impact-slider-el-${riskId}`).data().from;

                    /* updating likelihood input val*/
                    $(`#likelihood-input-el-${riskId}`).val(likelihoodIndex)

                    mainObject.updateRiskScoreAndLevel(riskId, likelihoodIndex, impactIndex)
                }
            });
        })
    }

    this.updateRiskScoreAndLevel = function (riskId, likelihoodIndex, impactIndex) {
        const data = this.data
        const likelihoodId = data.likelihoods[likelihoodIndex]['index']
        const impactId = data.impacts[impactIndex]['index']
        const riskScores = [].concat.apply([], data.scores)
        const maxScore = Math.max.apply(Math, riskScores.map(function(o) { 
            return o.score; 
        }))
        const scoreLevels = data.scoreLevels.levels

        /* Finding the risk score matching the likelihood and impact */
        let targetScore = riskScores.find(score => {
            return (score.likelihood_index == likelihoodId && score.impact_index == impactId)
        })

        const riskScore = targetScore.score

    
        /* finding the color for the score*/
        let scoreLevel = scoreLevels.find((level, key )=> {
            let index = parseInt(key)
            let lastIndex = scoreLevels.length-1
            let startScore = (index == 0) ? 1 : (scoreLevels[index-1]['max_score']+1)
            let endScore = (index == lastIndex) ? maxScore : level['max_score']

            /* Giving matrix cell color if it falls within the range */
            return riskScore >= startScore && riskScore <= endScore
        })

        $(`#risk_inherent_score_${riskId}`).html(riskScore)
        $(`#risk_inherent_level_${riskId}`).css("color", scoreLevel.color)
        $(`#risk_inherent_level_${riskId}`).html(scoreLevel.name)

        $(`#risk_residual_score_${riskId}`).html(riskScore)
        $(`#risk_residual_level_${riskId}`).css("color", scoreLevel.color)
        $(`#risk_residual_level_${riskId}`).html(scoreLevel.name)
    }


    this.refresh = function name(params) {
        let data = this.data

        /* Resetting the slider data*/
        data.sliderLikelihoods = []
        data.sliderImpacts = []
       
        /* Initializing*/
        this.init()
    }
}