@extends('layouts.layout-react')

@php $pageTitle = "Risk Register"; @endphp

@section('title', $pageTitle)

@section('plugins_css')
@endsection

@section('custom_css')
<link href="{{asset('assets/css/modules/risk-management.css')}}" rel="stylesheet" type="text/css" />
<style nonce="{{ csp_nonce() }}">
        .risk-score h4{
            line-height: 1.5;
        }

        .risk-score .riskscore-value{
            margin-left: 25px;
        }

        .risk-score-container{
            display: flex;
        }

        .risk-score-container .risk-score{
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

        .searchbox{
            position: relative;
            display: inline-block;
            margin-left: -4px;
        }

        .searchbox input:focus {
            outline: none;
        }




        .fa-search {
            position: absolute;
            top:38%;
            left:5%;
            color:#8e99a4;
        }

        .search{
            color:#8e99a4;
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

        .checkbox-success input[type=checkbox]:checked+label::before {
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
        @media screen and (max-width: 460px){
            .irs--round .irs-grid-text {
                font-size: 7px;
                white-space: break-spaces;
                width: 40px;
            }
            .risk-update-form .slider-div{
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
            top:35%;
            left:25%;
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

    /****This is for Risk Register Table */
    /******* 769px to 961px */
     @media (min-width: 768px) and (max-width: 992px) {


        .risk__id-width {
            width: 90px;
        }
     }

    @media (max-width: 768px){
        .risk-score-container .risk-score{
            margin-bottom:15px;
            margin-left: 0;
        }

    }
    .display-info .display-info__allign {
        margin-top: 7px;
    }
</style>
@endsection
@section('content')
   <div id="risk-register-page"></div>
@endsection

@section('plugins_js')

@endsection

@section('custom_js')

@endsection
