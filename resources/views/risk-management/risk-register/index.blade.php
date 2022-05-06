@extends('layouts.layout')

@php $pageTitle = "Risk Register"; @endphp

@section('title', $pageTitle)

@section('plugins_css')
    <!-- rangeSlider css -->
    <link href="{{ asset('assets/libs/ion-rangeslider/ion.rangeSlider.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/libs/multiselect/multi-select.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/libs/ladda/ladda-themeless.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/libs/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('custom_css')
    <link href="{{asset('assets/css/modules/risk-management.css')}}" rel="stylesheet" type="text/css"/>
    <style nonce="{{ csp_nonce() }}">
        .risk-score h4 {
            line-height: 1.5;
        }

        .risk-score .riskscore-value {
            margin-left: 25px;
        }

        .risk-score-container {
            display: flex;
        }

        .risk-score-container .risk-score {
            margin: auto;
        }

        .top__search {
            margin-left: -10px;
        }

        .middle__box {
            background: #f5f6f8;
        }

        .riskbox {
            padding: 5px 15px;
            background: #f5f6f8;
            border-radius: 5px;
            border-left: 2px solid var(--secondary-color);
            margin-bottom: 3px;
        }

        .risk__title {
            display: flex;
            align-items: center;
        }


        .risk__one-descrip {
            border-radius: 5px;
        }

        .top__search input[type="text"]::placeholder {
            text-align: center;
        }

        /*** search */

        .searchbox {
            position: relative;
            display: inline-block;
            margin-left: -4px;
        }

        .searchbox input:focus {
            outline: none;
        }


        .fa-search {
            position: absolute;
            top: 35%;
            left: 5%;
            color: #8e99a4;
        }

        .search {
            color: #8e99a4;
            font-weight: 400;
            padding-left: 50px;
            font-size: 18px;
            z-index: 3;

        }

        /** search ends */

        .checkbox-sec {
            display: inline-block;
            position: relative;
            top: -16px;
        }

        .checkbox-success input[type=checkbox]:checked + label::before {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
        }

        .mid__checkbox {
            position: relative;
            top: -4px;
            left: 7px;
        }

        .middle-box {
            margin-bottom: 10px;
            background: #f5f6f8;
            padding: 10px 8px;
        }

        .br-dark {
            border: 5px solid #f5f6f8;
        }


        .alert-pill {
            min-width: 20px;
            position: relative;
            top: -5px;
        }

        /*** range slider custom css */
        .irs--round .irs-line {
            margin-top: -15px;
        }

        span.irs-single {
            display: none;
        }

        .irs-grid-pol.small {
            display: none;
        }

        .irs-min, .irs-max {
            visibility: hidden !important;
        }

        .irs--round .irs-grid-text {
            color: gray;
        }

        .irs--round .irs-grid-text {
            color: gray;
            font-size: 10px;
        }

        /** color code for risks */
        .font-xs {
            font-size: smaller;
        }

        .low {
            color: #92D050; /**green */
        }

        .high {
            color: #FFC000; /** orange */
        }

        .extreme {
            color: #FF0000; /**red */
        }

        .top__box-btn {
            display: flex;
            justify-content: flex-end;
        }

        .risk-register-title {
            display: inline-block;
        }

        table.risk-register-table {
            margin-bottom: 0;
        }

        .searchbox input[type="text"]::placeholder {
            position: relative;
            left: -25px;
        }

        /*******************
            RESPONSIVE
        ********************/
        @media screen and (max-width: 460px) {
            .irs--round .irs-grid-text {
                font-size: 7px;
                white-space: break-spaces;
                width: 40px;
            }

            .risk-update-form .slider-div {
                overflow: auto;
            }
        }

        /******* 320px to 425px */

        @media (min-width: 320px) and (max-width: 425px) {
            .top__box-btn {
                flex-direction: column;
                display: flex;
                justify-content: center;
            }

            .export__risk-btn {
                margin-bottom: 5px;
            }

            .export__risk-btn, .add__risk-btn {
                min-width: 150px;
            }

            .searchbox input {
                max-width: 100%;
            }

            .animated input {
                max-width: 222px;
            }

            .fa-search {
                position: absolute;
                top: 35%;
                left: 7%;
            }

            .middle-box {
                flex-direction: column;
            }

            .text__box h5 {
                font-weight: normal;
            }

            .mid__checkbox {
                position: relative;
                top: -18px;
                left: 7px;
            }

            .risk__one-descrip .top__text h5 {
                display: none;
            }

            .risk__id-width {
                width: 81px;
            }

            .hide-on-xs {
                display: none;
            }

            .items__num {
                display: none;
            }

            .riskbox h5.risk-register-title {
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
                width: 167px;
            }

            .riskbox .icon-box .icon {
                position: relative;
                top: 50%;
            }

        }


        /******* 426px to 575px */
        @media (min-width: 426px) and (max-width: 575px) {
            .top__box-btn {
                justify-content: center;
                display: flex;
            }

            .export__risk-btn {
                margin-right: 5px;
            }

            .middle-box {
                flex-direction: column;
            }


            .risk__id-width {
                width: 81px;
            }

            .hide-on-xs {
                display: none;
            }

            .risk-register-title {
                max-width: 243px;
            }


        }

        @media (min-width: 500px) and (max-width: 543px) {

            .middle-box .searchbox input {
                width: 235px;
                margin-left: 85px;
            }

            .top__text .searchbox input {
                width: 210px;
            }

            .search-icon {
                position: absolute;
                top: 35%;
                left: 25%;
            }

            .text__box h5 {
                padding-left: 20px;
            }

            .top__text h5 {
                white-space: nowrap;
            }

            .mid__checkbox {
                position: relative;
                top: -11px;
                left: 10px;
            }


        }


        /******* 576px to 767px */
        @media (min-width: 576px) and (max-width: 767px) {
            .export__risk-btn {
                margin-right: 5px;
            }

            .fa-search {
                position: absolute;
                top: 20%;
                left: 5%;
            }

            .searchbox input {
                max-width: 255px;
                margin-right: 5px;
            }

            .mid__checkbox {
                position: relative;
                top: -15px;
                left: 7px;
            }

            .hide-on-sm {
                display: none !important;
            }

        }
        div.affected_function_select .inner{
            overflow-y: auto !important;
 
        }

        div.affected_function_select .inner::-webkit-scrollbar {
                display: none;
        }
        /****This is for Risk Register Table */
        /******* 769px to 961px */
        @media (min-width: 768px) and (max-width: 992px) {


            .risk__id-width {
                width: 90px;
            }
        }

        @media (max-width: 768px) {
            .risk-score-container .risk-score {
                margin-bottom: 15px;
                margin-left: 0;
            }

        }

        .display-info .display-info__allign {
            margin-top: 7px;
        }
    </style>
@endsection
@section('content')
    <!-- breadcrumbs -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('risks.dashboard.index') }}">Risk Management</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Risk Register</a></li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $pageTitle }}</h4>
            </div>
        </div>
    </div>
    <!-- end of breadcrumbs -->

    @include('includes.flash-messages')
    <div class="saved-risk"></div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body project-box">
                    <!-- top div -->
                    <div class="top__box">
                        <!-- <h4 class="text-center border-bottom mb-2 pb-2">Risks By Category</h4> -->
                        <div class="top__box-btn mb-2 pb-2">
                            <a href="{{ route('risks.register.risks-export') }}" class="btn btn-primary export__risk-btn">Export
                                Risks</a>
                            <a href="{{ route('risks.register.risks-create') }}"
                            class="btn btn-primary add__risk-btn mx-md-2">Add New Risks</a>
                        </div>
                    </div>
                    <!-- top div ends -->

                    <!-- middle box -->
                    <div class="middle-box pb-2 d-flex justify-content-between">
                        <!-- first search -->
                        <div class="searchbox top__search">
                            <form method="get">
                                <input type="text" placeholder="Search by Risk Name" name="search_by_risk_name"
                                    class="search"><i class="fas fa-search search-icon"></i>
                            </form>
                        </div>

                        <div class="text__box d-flex display-info">
                            <h5 class="pt-md-1 display-info__allign">Display only risks with incomplete information</h5>
                            <div class="checkbox checkbox-success mid__checkbox">
                                <input id="updated-risks-filter" type="checkbox">
                                <label for="updated-risks-filter"></label>
                            </div>
                        </div>
                    </div>
                    <!-- middle box ends -->
                    <div id="risk-by-category-section">
                        <!-- risk by category section -->
                    @include('risk-management.risk-register.partials.risks-by-category-section')
                    <!-- end of risk by category section -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('plugins_js')
    <script src="{{ asset('assets/libs/multiselect/jquery.multi-select.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>


    <!-- Ion Range Slider-->
    <script src="{{asset('assets/libs/ion-rangeslider/ion.rangeSlider.min.js')}}"></script>
    <script src="{{ asset('assets/libs/ladda/spin.js') }}"></script>
    <script src="{{ asset('assets/libs/ladda/ladda.js') }}"></script>
    <script src="{{ asset('assets/libs/toastr/toastr.min.js') }}"></script>

    <!-- custome-libs-js -->
    <script
        src="{{ asset('assets/custom-libs/risk-likelihood-impact-slider/risk-likelihood-impact-slider.js') }}"></script>
@endsection

@section('custom_js')
    <script nonce="{{ csp_nonce() }}">
        $(document).ready(function () {


            /* Risk likelihood and impact slider */
            const riskLikelihoodAndImpactSlider = new RiskLikelihoodAndImpactSlider({
                likelihoods: {!! json_encode($riskMatrixLikelihoods) !!},
                impacts: {!! json_encode($riskMatrixImpacts) !!},
                scores: {!! json_encode($riskMatrixScores) !!},
                levels: {!! json_encode($riskScoreActiveLevelType) !!},
            })

            riskLikelihoodAndImpactSlider.init()


            var openDropDownCategory = [];
            var openDropDownRisk = [];

            // changing expan icon
            $(document).on('click', '.expandable-icon-wp', function () {
                $(this).find('.fas').toggleClass('fa-chevron-right fa-chevron-down');
            });

            //Show previous open drop down category and risk item
            function showPreviousDropdown() {
                //show previous open drop down risk category
                if (openDropDownCategory) {
                    $.each(openDropDownCategory, function (index, value) {

                        $(".risk-category[data-id='" + value + "']").attr("aria-expanded", "true")
                        $(".risk-category[data-id='" + value + "']").find('.icon').removeClass('fa-chevron-right').addClass('fa-chevron-down')
                        $(".risk__one-descrip[data-id='" + value + "']").toggleClass('show')

                    });

                }

                //show previous open drop down risk item
                if (openDropDownRisk) {
                    $.each(openDropDownRisk, function (index, value) {

                        $(".risk-single-list[data-id='" + value + "']").attr("aria-expanded", "true")
                        $(".risk-single-list[data-id='" + value + "']").find('.icon').removeClass('fa-chevron-right').addClass('fa-chevron-down')
                        $(".risk-item-expand[data-id='" + value + "']").toggleClass('show')
                    });
                }
            }

            $(document).on('click', '.risk-category', function () {

                if ($(this).attr('aria-expanded') == 'true') {

                    openDropDownCategory.push($(this).attr('data-id'));
                } else {
                    openDropDownCategory.pop($(this).attr('data-id'));
                }
            })

            $(document).on('click', '.risk-single-list', function () {

                if ($(this).attr('aria-expanded') == 'true') {

                    openDropDownRisk.push($(this).attr('data-id'));
                } else {
                    openDropDownRisk.pop($(this).attr('data-id'));
                }
            })


            // Deleting Risk Permanently
            $(document).on('click', '.delete-risk', function (event) {

                event.preventDefault();

                const riskDestroyUrl = this.href

                // Are you sure
                swal({
                    title: "Are you sure that you want to delete the risk?",
                    text: "This action is irreversible and any mapped controls will be unmapped.",
                    showCancelButton: true,
                    confirmButtonColor: '#ff0000',
                    confirmButtonText: 'Yes, delete it!',
                    closeOnConfirm: false,
                    imageUrl: '{{asset('assets/images/warning.png')}}',
                    imageWidth: 120

                })
                    .then(confirmed => {
                        if (confirmed.value && confirmed.value == true) {
                            $.get(riskDestroyUrl)
                                .done(function (data, statusText, xhr) {
                                    if (xhr.status == 200) {
                                        Swal.fire({
                                            text: data.message,
                                            confirmButtonColor: '#b2dd4c',
                                            imageUrl: '{{ asset('assets/images/success.png') }}',
                                            imageWidth: 120,
                                        })

                                        //Reloading the risk category section
                                        reloadRiskCategorySection()
                                    }
                                })
                        }
                    });
            });


            // updated risk filter
            $(document).on('click', '#updated-risks-filter', function () {

                //Reloading the risk category section
                reloadRiskCategorySection()
            });

            //Disable form submit on enter for search
            $(document).on('keydown', 'input[name=search_by_risk_name]', function (e) {
                if(e.keyIdentifier=='U+000A'||e.keyIdentifier=='Enter'||e.keyCode==13) {
                    e.preventDefault();
                    return false;
                }
            });

            // Searching risk
            $(document).on('keyup', 'input[name=search_by_risk_name]', function (e) {
                //Reloading the risk category section
                reloadRiskCategorySection()
            })

            var counter = 0;

            //Reload risk category section
            function reloadRiskCategorySection() {
                // check if the key up request is active if so ignore all the request before current
                let ajaxRequestParams = {
                    updated_risks_filter: $("#updated-risks-filter").prop('checked'),
                    risk_name_search_query: $("input[name=search_by_risk_name]").val()
                }

                var seqNumber = ++counter;
                $.get("{{ route('risks.register.index') }}", ajaxRequestParams).done(function (res) {
                    if (res.success && (seqNumber === counter)) {
                        $("#risk-by-category-section").html(res.data)

                        //Show prevoius dropdown category and risk list
                        showPreviousDropdown()
                        // initialise js plugins
                        refreshPlugins()
                    }
                })
            }


            // within category risk search
            $(document).on('keyup', 'input[name=risk_name_search_within_category_query]', function () {
                let categoryId = $(this).data('category-id')

                let ajaxRequestParams = {
                    updated_risks_filter: $("#updated-risks-filter").prop('checked'),
                    risk_category: categoryId,
                    risk_name_search_query: $("input[name=search_by_risk_name]").val(),
                    risk_name_search_within_category_query: $(this).val()
                }

                $.get('{{ route('risks.register.index') }}', ajaxRequestParams).done(function (res) {
                    if (res.success) {
                        $(`#risk-items-wp-${categoryId}`).html(res.data)

                        //Show prevoius dropdown category and risk list
                        showPreviousDropdown()

                        // initialise js plugins
                        refreshPlugins()
                    }
                })
            })

            // handling pagination
            $(document).on('click', '.risks-pagination-wp a.page-link', function (e) {
                e.preventDefault()

                let categoryId = $(this).parent().parent().parent().parent().data('category-id');
                let URL = this.href

                let ajaxRequestParams = {
                    updated_risks_filter: $("#updated-risks-filter").prop('checked'),
                    risk_category: categoryId,
                    risk_name_search_query: $("input[name=search_by_risk_name]").val(),
                    risk_name_search_within_category_query: $('input[name=risk_name_search_within_category_query]').val()
                }

                $.get(URL, ajaxRequestParams).done(function (res) {
                    if (res.success == true) {
                        $(`#risk-items-wp-${categoryId}`).html(res.data)

                        // initialise js plugins
                        refreshPlugins()
                    }
                });
            })

            // RISK ITEMS UPDATE
            $(document).on('submit', '.risk-update-form', async function (e) {
                e.preventDefault()

                let loadingBtn = Ladda.create(e.target.querySelector('button[type=submit]'))

                loadingBtn.start()
                let updatedForm = $(this)
                let URL = this.action
                let riskCategoryId = $(this).data('category-id')

                try {
                    let res = await $.post(URL, $(this).serialize())

                    let resData = res.data

                    //Updating UI data
                    if (resData) {
                        let riskData = resData.risk

                        let riskCategoryWp = $(`#risk-category-wp_${riskCategoryId}`)
                        let riskItemsWp = riskCategoryWp.find(`#risk-items-wp-${riskCategoryId}`)
                        let riskItemTrHeader = riskItemsWp.find(`#risk-item-tr-header_${riskData.id}`)
                        let riskResidualScoreTd = riskItemTrHeader.find(`.residual-score-td`).first()
                        let riskInherentScoreTd = riskItemTrHeader.find(`.inherent-score-td`).first()
                        let riskLikelihoodTd = riskItemTrHeader.find(`.inherent-likelihood-td`).first()
                        let riskImpactTd = riskItemTrHeader.find(`.inherent-impact-td`).first()

                        riskResidualScoreTd.html(riskData.residualRiskScore)
                        riskInherentScoreTd.html(riskData.inherentRiskScore)
                        riskLikelihoodTd.html(riskData.inherent_likelihood)
                        riskImpactTd.html(riskData.inherent_impact)
                        updatedForm.find('#risk_status').text('Open').addClass('extreme');

                        if (riskData.status == 'Close') {
                            updatedForm.find('#risk_status').text('Close').removeClass("extreme").addClass('low');
                        }
                        showPreviousDropdown()
                    }

                    if (res.success) {
                        // let targetRiskCountEl = `#un-updated-risks-${upUpdatedRiskCountEL}`

                        //Show prevoius dropdown category and risk list
                        showPreviousDropdown()
                        reloadRiskCategorySection();
                        loadingBtn.stop()
                        updatedRisk();
                    }
                } catch (error) {
                    console.log(error);
                    loadingBtn.stop()
                }
            })

            function updatedRisk() {
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-bottom-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                toastr.success('Risk updated successfully.');
            }

            function refreshPlugins() {
                /* Refreshing range slider */
                riskLikelihoodAndImpactSlider.refresh()
                $('.selectpicker').selectpicker()
            }

        }) /* END OF DOCUMENT READY*/
    </script>
@endsection
