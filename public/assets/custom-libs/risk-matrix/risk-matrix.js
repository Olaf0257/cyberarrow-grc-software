const RiskMatrix = function (configs) {

    this.riskMatrixConfig = configs.matixConfig

    this.riskLevelSliderConfig = configs.levelSliderConfig

    this.popoverTemplate = `
            <div class="popover popover-matrix" role="tooltip">
                <div class="arrow"></div>
                <h3 class="popover-header"></h3>
                <div class="popover-body"></div>
                <div class="popover-footer">
                    <button type="button" class="btn btn-secondary cancel">
                    <i class="fas fa-times-circle text-medium"></i>
                    </button>
                    <button type="button" class="btn btn-primary save">
                    <i class="fas fa-check-circle text-medium"></i>
                    </button>
                </div>
            </div>
        `;

    this.acceptableRiskScorePickerEl = configs.acceptableRiskScorePickerEl

    this.getRiskMatrixData = function () {

        return {
            matrix: this.riskMatrixConfig.data,
            levels: this.riskLevelSliderConfig.data.ranges
        }
    }

    this.riskMatrixCellAddEditOption = (td) => {
        const mainObject = this

        let editOption = document.createElement("a");
        editOption.className = 'edit-cell-options risk-matrix-popover'

        let newIconTag = document.createElement("i");

        newIconTag.className = 'icon-note'

        editOption.appendChild(newIconTag)

        td.className = "clearfix";

        /* Based on risk matrix cell type creating edit input*/
        let editInput = ''

        if (td.dataset.score) {
            editInput = `<input type="text"  data-popover-type="edit-matrix-cell" />`
        } else {
            editInput = `<input type="text"  data-popover-type="edit-matrix-cell" />`
        }


        let popOverSettings = {
            placement: 'auto',
            container: td,
            html: true,
            sanitize: false,
            content: editInput,
            template: this.popoverTemplate
        }

        $(editOption).popover(popOverSettings)


        /* Handling popover save or cancel*/
        this.riskMatrixCellPopoverCallback(editOption)


        return td.appendChild(editOption)
    }

    this.riskMatrixCreateCell = (cellContent = null, likelihoodIndex = null, impactIndex = null) => {
        // create a new td element
        let newTd = document.createElement("td");

        /* Adding likelihoodIndex and impactIndex */
        newTd.dataset.likelihoodIndex = likelihoodIndex ? likelihoodIndex : ""


        newTd.dataset.impactIndex = impactIndex ? impactIndex : ""


        /* Setting score data arribut for score cell */
        if (likelihoodIndex && impactIndex) {
            newTd.dataset.score = cellContent
        }

        if (cellContent) {

            // create a span d element
            let newSpan = document.createElement("span");

            // and give it some content
            let spanContent = document.createTextNode(`${cellContent}`);
            //add the text node to the newly created span
            newSpan.appendChild(spanContent);


            /* giving class*/
            if (likelihoodIndex && impactIndex) {
                newSpan.className = "risk-score-value"
            }


            // add span
            newTd.appendChild(newSpan);
            newSpan.className = "truncate";


            /* Adding tooltips for likelihoods and impact span */
            if ((likelihoodIndex && !impactIndex) || (impactIndex && !likelihoodIndex)) {


                $(newSpan).tooltip({
                    title: _.unescape(cellContent),
                });
            }


            /* Adding edit cell option */
            this.riskMatrixCellAddEditOption(newTd)
        }

        return newTd
    }

    this.init = function () {
        /* Registration of events */
        this.registerRiskMatrixEvents()

        /*POC CODE*/
        this.riskMatrixDraw()

        this.initSliderHandlerSwitcher()

        this.renderAcceptableRiskScorePicker()
    };

    this.riskMatrixGetMaxScore = function () {
        const riskMatrixConfig = this.riskMatrixConfig
        const matrixData = riskMatrixConfig.data


        const riskNestedScores = matrixData.riskScores
        const riskScores = [];

        /* creating riskScores array*/
        riskNestedScores.map(function (scores) {
            scores.map(function (o) {
                riskScores.push(parseInt(o.score))
            })
        })

        /* Returning max score*/
        return Math.max.apply(Math, riskScores);
    }

    this.registerRiskMatrixEvents = function () {
        this.riskMarixHandleAddRowBtnClick('add-matrix-row')
        this.riskMarixHandleAddColumnBtnClick('add-matrix-column')
        document.getElementById('remove-matrix-row').addEventListener('click', this.riskMarixHandleRemoveRowBtnClick)
        document.getElementById('remove-matrix-column').addEventListener('click', this.riskMarixHandleRemoveColumnBtnClick)
    }

    this.riskMatrixDraw = () => {
        const riskMatrixConfig = this.riskMatrixConfig
        const matrixData = riskMatrixConfig.data
        const matrixEl = riskMatrixConfig.el
        let likelihoods = matrixData.likelihoods
        let impacts = matrixData.impacts
        let riskScores = matrixData.riskScores

        /* Rendering likelihoods and risk scores*/
        for (const likelihoodIndex in likelihoods) {

            if (Object.hasOwnProperty.call(likelihoods, likelihoodIndex)) {
                const likelihood = likelihoods[likelihoodIndex];

                // create a new tr element
                const newTr = document.createElement("tr");

                let newTd = this.riskMatrixCreateCell(_.unescape(likelihood.name), likelihoodIndex)

                newTr.appendChild(newTd)


                for (const impactIndex in impacts) {
                    if (Object.hasOwnProperty.call(impacts, impactIndex)) {
                        const impact = impacts[impactIndex];

                        /* Finding the risk score using likelihood and impact index */
                        let riskScore = riskScores[likelihoodIndex][impactIndex];

                        let riskScoreValue = riskScore ? riskScore.score : null


                        let newTd = this.riskMatrixCreateCell(riskScoreValue, likelihoodIndex, impactIndex)

                        newTr.appendChild(newTd)
                    }
                }

                matrixEl.insertBefore(newTr, matrixEl.childNodes[0])
            }
        }

        /* Rendering risk impacts */
        // create a new tr element
        const newTr = document.createElement("tr");
        // create a new td element
        let newTd = document.createElement("td");

        /* Appending td into tr*/
        newTr.appendChild(newTd)

        for (const key in impacts) {
            if (Object.hasOwnProperty.call(impacts, key)) {
                const impact = impacts[key];

                let newTd = this.riskMatrixCreateCell(_.unescape(impact.name), null, key)

                /* Appending td into tr*/
                newTr.appendChild(newTd)
            }
        }

        /* Appending new tr into table*/
        matrixEl.appendChild(newTr)

        /* prevents multiple risk matrix popover at a time  */
        $(document).on('click', '.risk-matrix-popover', function () {
            $('.risk-matrix-popover').not(this).popover('hide');
        });
    }

    this.rixMatrixReDraw = () => {
        const riskMatrixConfig = this.riskMatrixConfig
        const matrixEl = riskMatrixConfig.el

        matrixEl.innerHTML = '';
        this.riskMatrixDraw()

        /* update risk level slider */
        this.riskLevelSliderUpdateOnRiskMatrixUpdate()
    }

    /* Loops through trs and clone last td and append td*/
    this.riskMatrixAddColumn = (columnName) => {
        const riskMatrixConfig = this.riskMatrixConfig
        const matrixData = riskMatrixConfig.data
        let riskMatrixScores = matrixData.riskScores

        /*Adding new risk impact*/
        matrixData.impacts.push({id: null, name: columnName})


        /* Adding risk score */
        for (const key in riskMatrixScores) {
            if (Object.hasOwnProperty.call(riskMatrixScores, key)) {
                const riskScoreChunk = riskMatrixScores[key];

                let lastRiskScore = riskScoreChunk[riskScoreChunk.length - 1]

                matrixData.riskScores[key].push({...lastRiskScore, id: null, impact_index: null})
            }
        }


        /*Re rendering the matrix*/
        this.rixMatrixReDraw()
    }

    this.riskMatrixAddRow = (inputVal) => {
        const riskMatrixConfig = this.riskMatrixConfig
        const matrixData = riskMatrixConfig.data
        const matrixEl = riskMatrixConfig.el

        const newLikelihood = {id: null, name: inputVal}

        matrixData.likelihoods.push(newLikelihood)

        /* Getting the last item of risk scores*/
        let riskMatrixScores = matrixData.riskScores
        let lastRiskScoreChunk = riskMatrixScores[riskMatrixScores.length - 1]

        let newMatrixRaw = []

        /* Removing risk score ids*/
        lastRiskScoreChunk.map(riskScore => {
            newMatrixRaw.push({...riskScore, id: null, likelihood_index: null})
        })

        matrixData.riskScores.push(newMatrixRaw)

        /* Rerendering the matrix*/
        this.rixMatrixReDraw()
    }

    /* Handles the matrix cell POP OVER CALLBACK*/
    this.riskMatrixCellPopoverCallback = (el) => {
        let mainObject = this

        $(el).on('shown.bs.popover', function (eventShown) {
            var $popover = $('#' + $(eventShown.target).attr('aria-describedby'));
            let parentElement = this.parentElement
            let popoverInput = $popover.find('input')
            let popoverType = popoverInput.data('popoverType')
            let popoverInputValRules = ['required']


            /* matrix cell edit */
            if (popoverType == 'edit-matrix-cell') {
                /*populating input value*/

                popoverInput.val(_.unescape(parentElement.innerText))

                const {likelihoodIndex, impactIndex, score} = parentElement.dataset

                if (likelihoodIndex && impactIndex && score) {
                    /* for risk score cell */
                    /* Required to be number*/
                    popoverInputValRules.push('number');
                    popoverInputValRules.push('min:1');
                } else {
                    /* Required to be string*/
                    popoverInputValRules.push('string')
                }
            } else {
                /* Required to be string*/
                popoverInputValRules.push('string')
            }

            $popover.find('button.cancel').on('click', function (e) {
                $popover.find('input').popover('dispose')
                $popover.popover('hide');
            });


            $popover.find('button.save').off('click').on('click', function (e) {
                let inputEl = $popover.find('input')
                let inputVal = inputEl.val();

                /* Validation check*/
                let isValid = mainObject.handleMatrixInputValidation(inputEl[0], popoverInputValRules)

                /* Creating new row when valid*/
                if (isValid) {

                    switch (popoverType) {
                        case 'add-matrix-row':
                            mainObject.riskMatrixAddRow(inputVal)
                            break;
                        case 'add-matrix-column':
                            mainObject.riskMatrixAddColumn(inputVal)
                            break;
                        case 'edit-matrix-cell':
                            mainObject.riskMatrixUpdateCell(parentElement, inputVal)
                            break;
                        default:
                            break;
                    }


                    inputEl.popover('dispose')

                    $popover.popover('hide');
                }
            });
        })
    }

    this.riskMarixHandleAddColumnBtnClick = (elId) => {
        const mainObject = this
        const riskMatrixConfig = this.riskMatrixConfig
        const matrixData = riskMatrixConfig.data
        const {impacts} = matrixData


        document.getElementById(elId).addEventListener('click', function (event) {
            event.preventDefault();

            /* Not allowing to add rows more than 8*/
            if (impacts.length == 8) {
                return
            }

            $(this).popover('show')
        });

        let popOverSettings = {
            placement: 'auto',
            trigger: 'manual',
            html: true,
            sanitize: false,
            content: `
                    <input type="text" data-popover-type="add-matrix-column"/>
                `,
            template: this.popoverTemplate
        }

        $(`#${elId}`).popover(popOverSettings)

        /* Handling popover actions*/
        mainObject.riskMatrixCellPopoverCallback(document.querySelector(`#${elId}`))
    }

    this.destroyPopOverDialogbox = (el) => {
        $(el).popover('dispose')
    }

    this.renderValPopoverBox = (valEl, msg) => {
        $(valEl).popover('dispose').popover({
            placement: 'top',
            container: valEl.parentElement,
            html: true,
            content: msg,
            template: ' <div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>'
        }).popover('show')
    }

    this.riskMarixHandleAddRowBtnClick = (elId) => {
        const mainObject = this
        const riskMatrixConfig = this.riskMatrixConfig
        const matrixData = riskMatrixConfig.data
        const {likelihoods} = matrixData


        document.getElementById(elId).addEventListener('click', function (event) {
            event.preventDefault();

            /* Not allowing to add rows more than 8*/
            if (likelihoods.length == 8) {
                return
            }

            $(this).popover('show')
        });

        let popOverSettings = {
            placement: 'auto',
            // container: `#${elId}`,
            trigger: 'manual',
            html: true,
            sanitize: false,
            content: `
                    <input type="text" data-popover-type="add-matrix-row" />
                `,
            template: this.popoverTemplate
        }

        $(`#${elId}`).popover(popOverSettings)

        /* Handling popover actions*/
        mainObject.riskMatrixCellPopoverCallback(document.querySelector(`#${elId}`))
    }

    this.handleMatrixInputValidation = (valEl, rules = []) => {
        let isValid = false
        let mainObject = this
        let inputVal = valEl.value
        let valMsg = ''


        for (const key in rules) {
            if (Object.hasOwnProperty.call(rules, key)) {
                const rule = rules[key];

                if (rule == 'required') {
                    if (inputVal) {
                        isValid = true
                        continue;
                    }
                    /* Invalid case*/
                    isValid = false
                    valMsg = 'This field is required'
                    break;
                }

                /* String validation */
                if (rule == 'string') {
                    if (typeof inputVal == "string" && isNaN(inputVal)) {
                        isValid = true
                        continue;
                    }

                    /* Invalid case*/
                    isValid = false
                    valMsg = 'This field is must be a string'

                    break;
                }

                /* Number validation */
                if (rule == 'number') {
                    if (!isNaN(inputVal)) {
                        isValid = true
                        continue;
                    }

                    /* Invalid case*/
                    isValid = false
                    valMsg = 'This field is must be a number'

                    break;
                }

                /* Number min validation */
                if (rule.substr(0, 4) == 'min:') {
                    /* Valid case*/
                    if (parseFloat(inputVal) >= parseFloat(rule.substr(4))) {
                        isValid = true
                        continue;
                    }

                    /* Invalid case*/
                    isValid = false;
                    valMsg = 'This value must be equal or greater than ' + parseFloat(rule.substr(4));
                    break;
                }

            }
        }


        /* Showing val msg when isValid is false*/
        isValid ? '' : this.renderValPopoverBox(valEl, valMsg)

        return isValid
    }

    this.riskMarixHandleRemoveRowBtnClick = (event) => {
        const riskMatrixConfig = this.riskMatrixConfig
        const matrixData = riskMatrixConfig.data
        const {likelihoods} = matrixData


        /* validating to keep min 3 rows*/
        if (likelihoods.length == 3) {
            return
        }

        matrixData.likelihoods.pop()
        matrixData.riskScores.pop()

        /* Re drawing the matirx as matrix data is updated*/
        this.rixMatrixReDraw()
    }

    this.riskMarixHandleRemoveColumnBtnClick = () => {
        const riskMatrixConfig = this.riskMatrixConfig
        const matrixData = riskMatrixConfig.data
        const {impacts} = matrixData

        /* validating to allow min 3 column */
        if (impacts.length == 3) {
            return
        }

        matrixData.impacts.pop()

        matrixData.riskScores.map(score => {
            score.pop()
        })

        /* Re drawing the matirx as matrix data is updated*/
        this.rixMatrixReDraw()
    }

    this.riskMatrixUpdateCell = (cellEl, updatedContent) => {
        const riskMatrixConfig = this.riskMatrixConfig
        const matrixData = riskMatrixConfig.data

        let contentEl = cellEl.childNodes[0];

        let likelihoodIndex = cellEl.dataset.likelihoodIndex
        let impactIndex = cellEl.dataset.impactIndex

        /* Updating risk score */
        if (likelihoodIndex && impactIndex) {
            matrixData.riskScores[likelihoodIndex][impactIndex]['score'] = updatedContent

            this.renderAcceptableRiskScorePicker()
        }

        /* Updating risk likelihood */
        if (likelihoodIndex && !impactIndex) {
            matrixData.likelihoods[likelihoodIndex]['name'] = updatedContent
        }

        /* Updating impacts */
        if (impactIndex && !likelihoodIndex) {
            matrixData.impacts[impactIndex]['name'] = updatedContent
        }

        /*Re-rendering matrix after update*/
        this.rixMatrixReDraw()
    }

    this.riskLevelSliderCreateHandlerValue = function (handler, handlerArray) {
        const riskLevelSliderConfig = this.riskLevelSliderConfig
        const sliderMax = this.riskLevelSliderConfig.max

        if (handler >= sliderMax || handlerArray.includes(handler)) {
            return this.riskLevelSliderCreateHandlerValue(handler - 1, handlerArray)
        }

        return handler
    }

    /* Risk level Slider*/
    this.riskLevelSliderdraw = function () {
        const mainObject = this
        const riskLevelSliderConfig = mainObject.riskLevelSliderConfig
        const initialSliderHandlers = riskLevelSliderConfig.handlers.reverse()
        const sliderHandlers = []
        const riskLevelSliderEl = riskLevelSliderConfig.el

        /*Creating slider handler to avoid risk level more than risk max score nd next handler must be greater than prev */
        for (const key in initialSliderHandlers) {
            if (Object.hasOwnProperty.call(initialSliderHandlers, key)) {
                const initialSliderHandler = initialSliderHandlers[key];
                let updatedHandler = this.riskLevelSliderCreateHandlerValue(initialSliderHandler, sliderHandlers)

                sliderHandlers.push(updatedHandler)
            }
        }

        /* Updating slider handler in config*/
        riskLevelSliderConfig.handlers = sliderHandlers.reverse()

        riskLevelSliderEl.style.display = 'flex'
        /* Destroying the slider when already initalized */
        if ($(riskLevelSliderEl).hasClass('ui-slider')) {
            $(riskLevelSliderEl).slider('destroy');
        }

        const slider = $(riskLevelSliderEl).slider({
            min: 0,
            max: riskLevelSliderConfig.max,
            orientation: "horizontal",
            step: 1,
            values: sliderHandlers,
            create: function (event, ui) {
                let sliderUiHandles = event.target.querySelectorAll('.ui-slider-handle')

                for (const key in sliderUiHandles) {
                    if (Object.hasOwnProperty.call(sliderUiHandles, key)) {
                        const sliderUiHandle = sliderUiHandles[key];

                        mainObject.riskLevelSliderHandlersUpdateLables({
                            value: sliderHandlers[key],
                            handle: sliderUiHandle
                        })

                    }
                }

            },
            slide: function (event, ui) {
                let handleIndex = ui.handleIndex

                /* stoping handlers from overlapping while sliding from left to right */
                if ((ui.values[handleIndex]) >= ui.values[handleIndex + 1]) {
                    return false;
                }


                // stoping handlers from overlapping while sliding from right to left
                if ((ui.values[handleIndex]) <= ui.values[handleIndex - 1]) {
                    return false;
                }


                /* For first handler >> making the first level to have at least one risk score */
                if (handleIndex == 0 && ui.values[handleIndex] < 1) {
                    return false
                }

                /* For last index >> making the last level to have at least one risk score */
                if ((handleIndex == sliderHandlers.length - 1) && (ui.values[handleIndex] >= riskLevelSliderConfig.max)) {
                    return false
                }


                /* Updating slider handlers*/
                mainObject.riskLevelSliderConfig.handlers = ui.values

                /* Updating the slider ranges*/
                mainObject.riskLevelSliderUpdateRangeUiEl()


                /* Updating handlers labels*/
                mainObject.riskLevelSliderHandlersUpdateLables(ui)
            }
        })

        /* slider max value label */
        $(riskLevelSliderConfig.containerEl).find('.max-value-el').html(`Max value: ${riskLevelSliderConfig.max}`)


        /* Providing colors */
        this.riskLevelSliderRenderRanges()
    }

    this.riskLevelSliderGetActiveType = function (level) {
        const riskLevelSliderConfig = this.riskLevelSliderConfig
        const riskLevelSliderData = riskLevelSliderConfig.data
        const riskLevelSliderLevelTypes = riskLevelSliderData.levelTypes

        return riskLevelSliderLevelTypes.find(x => {
            return x.level == level
        })
    }
    this.riskLevelSliderCalRangeWidth = function (index, lastIndex) {
        const sliderConfig = this.riskLevelSliderConfig
        const sliderMax = sliderConfig.max
        const sliderHandlers = sliderConfig.handlers
        const currentHandler = sliderHandlers[index]
        const prevHandler = sliderHandlers[index - 1]

        let rangeWidthValue = 0

        if (index == 0) {
            rangeWidthValue = currentHandler
        } else {
            /* Returning when prev handler value is greater than slider max value*/
            if (prevHandler > sliderMax) return 0

            if (index == lastIndex) {
                rangeWidthValue = sliderMax - prevHandler
            } else {
                /* cal range width by max value when handler value is greater than max*/
                if (currentHandler > sliderMax) {
                    rangeWidthValue = Math.abs(sliderMax - prevHandler)
                } else {
                    rangeWidthValue = Math.abs(currentHandler - prevHandler)
                }
            }
        }

        const rangePercentage = (rangeWidthValue / sliderMax) * 100

        return rangePercentage.toFixed(1)
    }

    this.riskLevelSliderGetRangeData = function (levelId) {
        return this.riskLevelSliderConfig.data.ranges.find(range => {
            return range.id == levelId
        })
    }

    /* Render the ranges as html element for different levels*/
    this.riskLevelSliderRenderRanges = function () {
        const mainObject = this
        const sliderConfig = this.riskLevelSliderConfig
        const sliderEl = sliderConfig.el
        const sliderData = sliderConfig.data
        const sliderRanges = sliderData.ranges

        /* Removing the previous ranges*/
        sliderEl.querySelectorAll('.ui-slider-range').forEach(el => {
            /* removing the labels*/
            $(el).popover('dispose')
            el.remove()
        })

        for (const key in sliderRanges) {
            if (Object.hasOwnProperty.call(sliderRanges, key)) {
                const index = parseInt(key)
                const lastIndex = sliderRanges.length - 1
                const sliderRange = sliderRanges[index];

                /* Creating range ui el*/
                const rangeEl = document.createElement('span')
                rangeEl.className = 'range ui-slider-range'


                /* Determining range value to calculate range width*/
                const rangePercentage = this.riskLevelSliderCalRangeWidth(index, lastIndex)

                /* Assigning style*/
                rangeEl.style.backgroundColor = sliderRange.color
                rangeEl.style.width = `${rangePercentage}%`
                rangeEl.style.display = `inline-block`
                rangeEl.style.position = `initial`

                /* appending to slider */
                sliderEl.appendChild(rangeEl)

                /* Creating popover for range label*/
                let popOverSettings = {
                    placement: 'bottom',
                    html: true,
                    sanitize: false,
                    container: '#risk-level-slider-container',
                    content: `<span class="level-edit-span">${sliderRange.name}</span>
                            <input data-level-id="${sliderRange.id}" class="level-edit-input form-control form-control-sm d-none" type="text" value="">
                        `,
                    template: `
                        <div class="popover d-flex" role="tooltip">
                            <div class="popover-body">
                            </div>
                            <div class="popover-footer">
                                <button type="button" class="btn btn-secondary edit">
                                    <i class="icon-note"></i>
                                </button>
                                <button type="button" class="btn btn-secondary cancel d-none">
                                    <i class="fas fa-times-circle text-medium"></i>
                                </button>
                                <button type="button" class="btn btn-primary save d-none">
                                <i class="fas fa-check-circle text-medium"></i>
                                </button>
                            </div>
                        </div>
                        `
                }

                $(rangeEl).popover(popOverSettings).popover('show').off('click').on('shown.bs.popover', function (eventShown) {
                    var $popup = $('#' + $(eventShown.target).attr('aria-describedby'));

                    /* input  */
                    const levelInput = $popup.find('input.level-edit-input')
                    const levelId = levelInput.data('level-id')
                    const levelEditSpanEl = $popup.find('span.level-edit-span')
                    let editPopupToggleEls = 'button.edit, button.cancel, button.save, span.level-edit-span, input.level-edit-input'

                    $popup.find('button.cancel').off('click').on('click', function (e) {
                        /* disposing val popover*/
                        levelInput.popover('dispose')
                        $popup.find(editPopupToggleEls).toggleClass('d-none')
                    })

                    /* ON edit mode*/
                    $popup.find('button.edit').off('click').on('click', function (e) {
                        /* Finding the level input*/
                        let targetRange = mainObject.riskLevelSliderGetRangeData(levelId)

                        /* Assigning input value */
                        levelInput.val(_.unescape(targetRange.name));

                        $popup.find(editPopupToggleEls).toggleClass('d-none')
                    })

                    $popup.find('button.save').off('click').on('click', function (e) {
                        let updtedLevelValue = levelInput.val()

                        /* Checking validation*/
                        let isValid = mainObject.handleMatrixInputValidation(levelInput[0], ['required', 'string'])

                        /* When not valid stop further execution*/
                        if (isValid) {
                            /* Updating range data object*/
                            mainObject.riskLevelSliderUpdateRangeData(levelId, updtedLevelValue)

                            /* Updting the label*/
                            levelEditSpanEl.text(updtedLevelValue)

                            /* disposing val popover*/
                            levelInput.popover('dispose')

                            $popup.find(editPopupToggleEls).toggleClass('d-none')
                        }
                    })
                });
            }
        }


        /* rendering risk matrix cell colors*/
        this.provideRiskMatrixCellColor()
    }

    this.riskLevelSliderSetRanges = function (ranges) {
        this.riskLevelSliderConfig.data.ranges = ranges
    }

    this.initSliderHandlerSwitcher = function () {
        const mainObject = this
        const sliderConfig = this.riskLevelSliderConfig
        const riskLevelSliderSwitchEl = sliderConfig.levelsSwitcherEl

        $(riskLevelSliderSwitchEl).selectpicker();

        $(riskLevelSliderSwitchEl).on('change', function () {
            let selectedLevel = $(this).val()
            let selectedSliderType = mainObject.riskLevelSliderGetActiveType(selectedLevel)

            /* Updating the active slider type */
            mainObject.riskLevelSliderSetRanges(selectedSliderType.levels)

            mainObject.riskLevelSliderUpdateConfig()
        })

        $(riskLevelSliderSwitchEl).trigger('change')
    }

    this.riskLevelSliderUpdateConfig = function () {
        const riskLevelSliderConfig = this.riskLevelSliderConfig
        const riskLevelSliderData = riskLevelSliderConfig.data
        const riskLevelSliderRanges = riskLevelSliderData.ranges

        /* THIS WILL BE SET IN SLIDER DATA */
        const riskLevelSliderRangeColors = []
        const riskLevelSliderHandlers = []


        for (const riskLevelSliderRange of riskLevelSliderRanges) {
            const sliderRange = riskLevelSliderRange;

            /* creating the slider colors*/
            riskLevelSliderRangeColors.push(riskLevelSliderRange.color)


            /* Creating the slider handlers*/
            if (riskLevelSliderRange.max_score) {
                riskLevelSliderHandlers.push(riskLevelSliderRange.max_score)
            }

        }
        /* Setting the risk level slider max value*/
        this.riskLevelSliderUpdateMaxValue()

        /* Setting up the slider colors*/
        riskLevelSliderConfig.riskLevelSliderRangeColors = riskLevelSliderRangeColors


        /* Setting up the slider sliderHandlers*/
        riskLevelSliderConfig.handlers = riskLevelSliderHandlers

        /* Drawing slider */
        this.riskLevelSliderdraw()
    }

    this.riskLevelSliderHandlersUpdateLables = function (ui) {
        $(ui.handle).attr('data-value', ui.value);
    }

    this.riskLevelSliderUpdateMaxValue = function () {
        this.riskLevelSliderConfig.max = this.riskMatrixGetMaxScore()
    }

    this.riskLevelSliderUpdateRangeUiEl = function () {
        const sliderConfig = this.riskLevelSliderConfig
        const sliderEl = sliderConfig.el
        const sliderData = sliderConfig.data
        const sliderRanges = sliderData.ranges
        const sliderHandlers = sliderConfig.handlers

        for (const key in sliderRanges) {
            if (Object.hasOwnProperty.call(sliderRanges, key)) {
                const index = parseInt(key)
                const lastIndex = sliderRanges.length - 1
                const sliderRange = sliderRanges[index];

                /*Updating range datas*/
                sliderRange.max_score = sliderHandlers[index] ? sliderHandlers[index] : null

                const rangeEl = sliderEl.querySelectorAll('.ui-slider-range')[index]

                /* Determining range value to calculate range width*/
                const rangePercentage = this.riskLevelSliderCalRangeWidth(index, lastIndex)
                rangeEl.style.width = `${rangePercentage}%`

                /* Re-positioning range labels*/
                $(rangeEl).popover('update')
            }
        }


        // end with the last color to the right


        /* rendering risk matrix cell colors*/
        this.provideRiskMatrixCellColor()
    }

    this.riskLevelSliderUpdateRangeData = function (levelId, levelValue) {
        const sliderConfig = this.riskLevelSliderConfig
        const sliderData = sliderConfig.data

        let targetRange = this.riskLevelSliderGetRangeData(levelId)

        /* Updating range name */

        targetRange.name = levelValue
    }

    this.riskLevelSliderUpdateOnRiskMatrixUpdate = function () {
        /* Setting the risk level slider max value*/
        this.riskLevelSliderUpdateMaxValue()

        /* Drawing slider */
        this.riskLevelSliderdraw()
    }

    this.provideRiskMatrixCellColor = function () {
        const riskMatrixConfig = this.riskMatrixConfig
        const riskMatrixEl = riskMatrixConfig.el
        const sliderConfig = this.riskLevelSliderConfig
        const sliderRangeColors = sliderConfig.riskLevelSliderRangeColors
        const sliderHandlers = sliderConfig.handlers
        const sliderMax = sliderConfig.max

        let riskMatrixCells = $(riskMatrixEl).find('td[data-score]')


        for (const key in riskMatrixCells) {
            if (Object.hasOwnProperty.call(riskMatrixCells, key)) {
                const riskMatrixCell = riskMatrixCells[key];

                let score = $(riskMatrixCell).data('score')

                /* finding the color for the score*/
                for (const key in sliderRangeColors) {
                    if (Object.hasOwnProperty.call(sliderRangeColors, key)) {
                        let index = parseInt(key)
                        let lastIndex = sliderRangeColors.length - 1
                        const sliderRangeColor = sliderRangeColors[index];

                        let startScore = (index == 0) ? 1 : (sliderHandlers[index - 1] + 1)
                        let endScore = (index == lastIndex) ? sliderMax : sliderHandlers[index]


                        /* Giving matrix cell color if it falls within the range */
                        if (score >= startScore && score <= endScore) {
                            riskMatrixCell.style.backgroundColor = sliderRangeColor;
                        }

                    }
                }

            }
        }

    }

    this.renderAcceptableRiskScorePicker = function () {
        const acceptableRiskScorePicker = this.acceptableRiskScorePickerEl
        const riskMatrixConfig = this.riskMatrixConfig
        const matrixData = riskMatrixConfig.data
        const riskScores = [].concat.apply([], matrixData.riskPickerScores)
        const uniqueRiskScores = riskScores.filter((elem, index) => {
            return riskScores.findIndex(obj => obj.score == elem.score) == index
        })
        const {acceptableScore} = acceptableRiskScorePicker.dataset;
        /* Clearing the select options */
        acceptableRiskScorePicker.innerHTML = "";

        for (const key in uniqueRiskScores) {
            if (Object.hasOwnProperty.call(uniqueRiskScores, key)) {
                const uniqueRiskScore = uniqueRiskScores[key];
                let selectOptionEl = document.createElement("OPTION");

                selectOptionEl.setAttribute("value", uniqueRiskScore.score);

                var selectOptionText = document.createTextNode(uniqueRiskScore.score);

                if (acceptableScore) {
                    if (acceptableScore == uniqueRiskScore.score) {
                        selectOptionEl.setAttribute("selected", 'selected');
                    }
                }

                selectOptionEl.appendChild(selectOptionText);
                acceptableRiskScorePicker.appendChild(selectOptionEl);
            }
        }

        /* Initial bootstrap select picker*/
        $(acceptableRiskScorePicker).selectpicker('refresh');
    }
}

