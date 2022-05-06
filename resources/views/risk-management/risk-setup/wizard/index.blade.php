@extends('layouts.layout')

@php $pageTitle = "Risk Wizard"; @endphp

@section('title', $pageTitle)

@section('custom_css')
<link href="{{asset('assets/css/modules/risk-management.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .navtab-bg li>a {
            margin: 0;
        }

        .nav-pills .nav-link {
            border-radius: 0;
        }

        .br-dark {
            border: 5px solid #f5f6f8;
        }

        /***********************
            circular tabs
        ***********************/
        .nested__tabs {
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);

        }

        ul.circular {
            list-style: none;
            display: flex;
            justify-content: space-around;
            padding-top: 10px;
            padding-bottom: 10px;

        }

        .circular li a, .icon-box a {
            color: #000;
        }

        .liner{
            height: 2px;
            background: #ddd;
            position: relative;
            width: 100%;
            margin: 0 auto;
            left: 0;
            right: 0;
            top: 30px;
        }

        span.round-tabs {
            height: 40px;
            width: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 5px;
            background: #fff;
            position: relative;
        }

        .top__head {
            background: #f5f6f8;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            padding: 10px 0;

        }

        /*** search */

        .searchbox{
            position: relative;
            display: inline-block;
            margin-left: 1.5rem;
        }

        .searchbox input:focus {
            outline: none; /*by default,input shows an outline on focus */
        }

        .searchbox input{
            z-index: 0;
            height: 40px;
            background:#fff;
            border-radius: 25px;
            border: 2px solid #f1f3f4;
            margin-top: 0;
            font-size: .9375rem;
            font-weight: 600;
            transition: all .3s linear;
        }

        input:focus {
            border: 2px solid var(--secondary-color);
            background: #fff;
        }

        form{
            display:inline-block;
        }


        .fa-search{
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

        .content__box {
            border-bottom: 2px solid rgba(0,0,0,0.12);
            margin-top: 2px;
            padding: 10px 15px;
        }

        .icon-box {
            align-items: center;
            justify-content: center;
            display: flex;

        }


        .description__box {
            box-shadow: 0 1px 2px rgba(0,0,0,0.12), 0 1px 3px rgba(0,0,0,0.24);

        }

        .checkbox-success input[type=checkbox]:checked+label::before {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
        }

        .top__one h5 {
            margin-left: -30px;
            padding-top: 2px;

        }

        .top__three h5 {
            margin-left: 45px;
            padding-top: 2px;

        }

        a.risk-category-tab-nav.completed span, a.risk-category-tab-nav.current-stage span{
            background: var(--secondary-color);
            color: #fff;
        }

        a.risk-category-tab-nav.active span {
            background: var(--secondary-color-darker);
            color: #fff;
        }

        a.risk-category-tab-nav.disabled span {
            background: var(--light-gray);
            border: var(--light-gray);
            color: #000;
        }

        .top__three .checkbox4 {
            right: -15px;
            position: relative;
            top: -5px;

        }


        .descrip__checkbox {
            position: relative;
            top: -11px;
            display: flex;
            align-items: center;
            margin-left: auto;
            left: 7px;
        }

        .project-select-option h4 {
            margin-right: 30px;
        }

        .select2-container .select2-selection--single {
            min-width: 170px;
        }

        .nested__tabs ul li p {
            display: none;
        }

        .standard {
            white-space: nowrap;
        }

        .risklist__confirm-btn {
            position: absolute;
            right: 33px;
        }

        .risk__name-text {
            display: inline-block;
        }

        /******* checkbox ********/
       .checkbox-btn label {
            display: inline-block;
            background-color: rgba(255, 255, 255, .9);
            /* border: 2px solid rgba(139, 139, 139, .3); */
            border: 2px solid var(--secondary-color);
            color: #adadad;
            border-radius: 25px;
            white-space: nowrap;
            margin: 3px 0px;
            padding: 6px 12px;
            cursor: pointer;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
            transition: all .2s;
        }

        .checkbox-btn label::before {
            display: inline-block;
            font-style: normal;
            font-variant: normal;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            font-size: 12px;
            padding: 2px 6px 2px 2px;
            content: "\f067";
            transition: transform .3s ease-in-out;
        }

        .checkbox-btn input[type="checkbox"]:checked + label::before {
            content: "\f00c";
            transform: rotate(-360deg);
            transition: transform .3s ease-in-out;
        }

        .checkbox-btn input[type="checkbox"]:checked + label {
            border: 2px solid var(--secondary-color);
            background-color: var(--secondary-color);
            color: #fff;
            transition: all .2s;
        }

        .checkbox-btn input[type="checkbox"][disabled] + label {
            background-color: rgba(255, 255, 255, .9);
            border: 2px solid rgba(139, 139, 139, .3);
            color: #adadad;
            cursor: no-drop;
        }

        .checkbox-btn input[type="checkbox"] {
            position: absolute;
            opacity: 0;
        }

        #import-tab .nav-link.disabled{
            background:none !important
        }

        @media (min-width: 400px) {
            .wizard-content-box p {
                padding-bottom: 10px !important;
                color: red !important;
            }
        }



        /**********************************
                RESPONSIVE HERE
         *********************************/

         @media (min-width: 500px) and (max-width: 713px) {
            .searchbox input {
                position: relative;
                left: -60px;
                top: 3px;
            }

            .search-icon {
                z-index: 1;
                position: absolute;
                top: 40% !important;
			    left: -44px !important;
            }

         }

         @media (min-width: 714px) and (max-width: 767px) {
            .searchbox input {
                position: relative;
                left: -60px;
                top: -3px;
                z-index: 1;
            }

            .search-icon {
                position: absolute;
                top: 12px;
                left: -44px;
            }

         }

        /**************************
          Remove effect of br on
        ***************************/
        /* @media (max-width: 1239px) {
            .standard-box br,
            .approach-box br {
                display: none;
            }
        } */

        /**************************
         risk name width reduced
        ***************************/
        @media (min-width: 320px) and (max-width: 415px) {

            .icon-box .collapse-el .icon {
                display: none;
            }

            .icon-box h5.risk__name-text {
               position: relative;
               left: -20px;
            }

            .top__two .searchbox {
                position: relative;
                display: inline-block;
                margin-left: -45px;
            }

            .top__two .searchbox input {
                width: 220px
            }

        }

        @media (min-width: 416px) and (max-width: 500px) {
            .icon-box h5.risk__name-text{
                max-width: 200px;
            }
        }

        /**********
        middle part
        **********/

        @media (min-width: 320px) and (max-width: 398px) {
            .top__three {
                position: relative;
                left: -123px;
                top: 35px;
                padding-bottom: 30px;
            }

            .top__three .select_all_checkbox {
                position: relative;
                top: -23px;
                left: 104px;
            }

            .top__three h5.select-all-text {
                white-space: nowrap;
                word-spacing: 10px;
            }
        }

        @media (min-width: 399px) and (max-width: 415px) {
            .top__three h5.select-all-text {
                width: 45px;
                word-break: break-all;
            }

            .top__two .searchbox input {
                width: 202px;
            }
        }

        /***********************
        *
        * Circular tabs here
        *
        ***********************/
        @media(min-width: 320px) and (max-width: 335px) {
            span.round-tabs {
                height: 20px;
                width: 20px;
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 5px;
                background: #fff;
                position: relative;
            }

            .nav-link {
                padding: 0;
            }

            .liner {
                display: none;
            }
        }

        @media (min-width: 336px) and (max-width: 575px) {
            span.round-tabs {
                height: 20px;
                width: 20px;
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 5px;
                background: #fff;
                position: relative;
            }

            .nested__tabs ul li a.nav-link {
                padding: 0;
            }

            .liner{
                height: 2px;
                background: #ddd;
                position: relative;
                width: 100%;
                margin: 0 auto;
                left: 0;
                right: 0;
                top: 11px;
            }

            ul.circular {
                list-style: none;
                display: flex;
                justify-content: space-around;
                padding-top: 15px;
                padding-bottom: 15px;
            }
        }

        @media (min-width: 576px) and (max-width: 768px) {
            span.round-tabs {
                height: 30px;
                width: 30px;
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 5px;
                background: #fff;
                position: relative;
            }

            .nested__tabs ul li a.nav-link {
                padding: 0;
            }

            .liner{
                height: 2px;
                background: #ddd;
                position: relative;
                width: 100%;
                margin: 0 auto;
                left: 0;
                right: 0;
                top: 16px;
            }
        }

        @media (min-width: 769px) and (max-width: 959px) {
            span.round-tabs {
                height: 35px;
                width: 35px;
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 5px;
                background: #fff;
                position: relative;
            }

            .nested__tabs ul li a.nav-link {
                padding: 0;
            }

            .liner{
                height: 2px;
                background: #ddd;
                position: relative;
                width: 100%;
                margin: 0 auto;
                left: 0;
                right: 0;
                top: 19px;
            }
        }


        /*******************
        Risk Wizard Box >> standard
        ***************/
        @media (min-width: 810px) and (max-width: 841px) {
            .tab-content #standard-tab p.isr-subtext {
                padding-bottom: 20px;
            }
        }

        @media (min-width: 946px) and (max-width: 979px) {
            .tab-content #standard-tab p.isr-subtext {
                padding-bottom: 20px;
            }
        }

        @media (min-width: 1071px) and (max-width: 1123px) {
            .tab-content #standard-tab p.isr-subtext {
                padding-bottom: 20px;
            }
        }

        @media (min-width: 1200px) and (max-width: 1245px) {
            .tab-content #standard-tab p.isr-subtext {
                padding-bottom: 20px;
            }
        }

        @media (min-width: 1448px) and (max-width: 1578px) {
            .tab-content #standard-tab p.isr-subtext {
                padding-bottom: 20px;
            }
        }

        /*******************
        Risk Wizard Box >> Approach
        ***************/

        @media (min-width: 768px) and (max-width: 812px) {
            .tab-content #approach-tab p.yourself-subtext {
                padding-bottom: 20px;
            }
        }

        @media (min-width: 813px) and (max-width: 855px) {
            .tab-content #approach-tab p.yourself-subtext {
                padding-bottom: 40px;
            }
        }

        @media (min-width: 856px) and (max-width: 1135px) {
            .tab-content #approach-tab p.yourself-subtext {
                padding-bottom: 20px;
            }
        }

        @media (min-width: 1200px) and (max-width: 1259px) {
            .tab-content #approach-tab p.yourself-subtext {
                padding-bottom: 20px;
            }
        }

        @media (min-width: 1538px)  {
            .tab-content #approach-tab p.yourself-subtext {
                padding-bottom: 20px;
            }
        }

    </style>
@endsection

@section('plugins_css')
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<!-- breadcrumbs -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('risks.setup') }}">Risk Setup</a>
                    </li>
                    <li class="breadcrumb-item active"><a href="#">Risk Wizard</a></li>
                </ol>
            </div>
            <h4 class="page-title">{{ $pageTitle }}</h4>
        </div>
    </div>
</div>
<!-- end of breadcrumbs -->

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body project-box">
                <!-- tab -->
                @if ($message = Session::get('risk_setup_errors'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="btn-close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif

                <ul class="nav nav-pills navtab-bg nav-justified risk-setup-nav-wp">
                    <li class="nav-item">
                        <a href="#standard-tab" data-toggle="tab" aria-expanded="false" class="nav-link active standard">
                            Choose Standard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#approach-tab" data-toggle="tab" aria-expanded="true" class="nav-link  approach">
                            Approach
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#import-tab" data-toggle="tab" aria-expanded="false" class="nav-link import">
                            Import
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="bar" class="progress mb-2" style="height: 7px;">
                        <div id="risk-setup-progress-bar" class="bar progress-bar progress-bar-striped progress-bar-animated secondary-bg-color risk-progress-bar"  role="progressbar" style="width: 33.33%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                <div class="tab-pane active" id="standard-tab">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 standard-box">
                            <div class="card">
                                <div class="card-body project-box br-dark">
                                    <div class="head-text text-center">
                                        <h4>ISO/IEC 27002:2013</h4>
                                        <p class="my-3 iso-subtext">ISO/IEC 27002:2013 is an international information security  standard which includes internationally accepted security controls. This standard is applicable to organizations of any type and size.  The  risk assessment will be based on the controls stated in ISO/IEC 27002:2013.
                                        </p>
                                        <div class="checkbox-btn">
                                            <input type="checkbox" name="risk-setup-standard" value="ISO/IEC 27002:2013" aria-label="ISO 27002" id="iso-27002">
                                            <label for="iso-27002">Choose</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6 standard-box">
                            <div class="card">
                                <div class="card-body project-box br-dark">
                                    <div class="head-text text-center">
                                        <h4>ISR V2</h4>
                                        <p class="my-3 isr-subtext">ISR V2 is a local information security standard developed  specifically for government entities in the emirate of Dubai in the  UAE. The standard is also applicable to any type  of organization. The risk assessment will be based on the controls stated in ISR V2. </p>
                                        <div class="checkbox-btn">
                                            <input type="checkbox" name="risk-setup-standard" value="ISR V2" aria-label="ISR V2" id="isr-standard">
                                            <label for="isr-standard">Choose</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6 standard-box">
                            <div class="card">
                                <div class="card-body project-box br-dark">
                                    <div class="head-text text-center">
                                        <h4>SAMA Cyber Security Framework</h4>
                                        <p class="my-3 isr-subtext">
                                            SAMA Cybersecurity Framework is a local information security standard developed specifically for
                                            financial institutions regulated by SAMA in the Kingdom of Saudi Arabia. The standard is also
                                            applicable to any type of financial institution. The risk assessment will be based on the controls
                                            stated in SAMA Cybersecurity Framework. </p>
                                        <div class="checkbox-btn">
                                            <input type="checkbox" name="risk-setup-standard" value="SAMA Cyber Security Framework" aria-label="SAMA Cyber Security Framework" id="scf-standard">
                                            <label for="scf-standard">Choose</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6 standard-box">
                            <div class="card">
                                <div class="card-body project-box br-dark">
                                    <div class="head-text text-center">
                                        <h4>NCA ECC-1:2018</h4>
                                        <p class="my-3 isr-subtext">
                                            NCA Essential Cybersecurity Controls is a local information security standard developed specifically
                                            for government entities in the Kingdom of Saudi Arabia. The standard is also applicable to any type
                                            of organization. The risk assessment will be based on the controls stated in NCA ECC–1:2018.</p>
                                        <div class="checkbox-btn">
                                            <input type="checkbox" name="risk-setup-standard" value="NCA ECC-1:2018" aria-label="NCA ECC-1:2018" id="nca_ecc-standard">
                                            <label for="nca_ecc-standard">Choose</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6 standard-box">
                            <div class="card">
                                <div class="card-body project-box br-dark">
                                    <div class="head-text text-center">
                                        <h4>NCA CSCC-1:2019</h4>
                                        <p class="my-3 isr-subtext">NCA Critical Systems Cybersecurity Controls is a local information security standard developed
                                        specifically for critical systems within national organizations in the Kingdom of Saudi Arabia. The
                                        standard is also applicable to any type of organization with critical systems. The risk assessment will
                                        be based on the controls stated in NCA CSCC–1:2019. </p>
                                        <div class="checkbox-btn">
                                            <input type="checkbox" name="risk-setup-standard" value="NCA CSCC-1:2019" aria-label="NCA CSCC-1:2019" id="nca_cscc-standard">
                                            <label for="nca_cscc-standard">Choose</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-xl-6 col-lg-6 col-md-6 standard-box">
                            <div class="card">
                                <div class="card-body project-box br-dark">
                                    <div class="head-text text-center">
                                        <h4>UAE IA</h4>
                                        <p class="my-3 isr-subtext">UAE IA is an information security standard developed specifically for government entities in the UAE. The standard is also applicable to any type of organization. The risk assessment will be based on the controls stated in UAE IA.</p>
                                        <div class="checkbox-btn">
                                            <input type="checkbox" name="risk-setup-standard" value="UAE IA" aria-label="UAE IA" id="uae-ia-standard">
                                            <label for="uae-ia-standard">Choose</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6 standard-box"></div>

                        <button class="btn btn-primary go-to-next-step-btn clearfix mt-2 float-end d-flex ms-auto" data-current-tab="1">Next</button>
                    </div>
                </div>
                <!-- Approach tab -->
                <div class="tab-pane" id="approach-tab">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="card">
                                <div class="card-body project-box approach-box br-dark">
                                    <div class="head-text text-center">
                                        <h4>Automated</h4>
                                        <p class="my-3 automated-subtext">The automated approach will automatically import and  map risks and  controls against chosen standard based on  an active  compliance project. </p>
                                        <div class="checkbox-btn choose-risk-setup-approach">
                                            <input type="checkbox" name="risk-setup-approach" id="automated-setup-approach" value="Automated" aria-label="Automated">
                                            <label for="automated-setup-approach">Choose</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="card">
                                <div class="card-body project-box approach-box br-dark">
                                    <div class="head-text text-center">
                                        <h4>Yourself</h4>
                                        <p class="my-3 yourself-subtext">Choose this approach if you want to select the risks manually based on the chosen standard. </p>
                                        <div class="checkbox-btn choose-risk-setup-approach">
                                            <input type="checkbox" name="risk-setup-approach" value="Yourself" aria-label="Yourself" id="yourself-setup-approach">
                                            <label for="yourself-setup-approach">Choose</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary go-to-next-step-btn clearfix mt-2 float-end d-flex ms-auto" data-current-tab="2">Next</button>
                    </div>
                </div>

                <div class="tab-pane" id="import-tab">

                </div>
            </div>
        </div>
     <!-- end card -->
    </div>
</div>
@endsection
@section('plugins_js')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
@endsection
@section('custom_js')
<script nonce="{{ csp_nonce() }}">
    $( document ).ready(function() {
        const wizardRiskSetup = {
            currentStage : 1,
            riskStandard: null,
            riskSetupApproach: null,
            updateSetupWizard: function(el) {
                let progressBar = $('#risk-setup-progress-bar')

                switch(this.currentStage) {
                    case 1:
                        progressBar.css('width', '33.33%');
                        // code block
                        break;
                    case 2:
                        progressBar.css('width', '66.66%')

                        // resetting input values
                        $('.choose-risk-setup-approach').find('input').prop('checked', false)

                        $("a[href='#approach-tab']").trigger('click')

                        this.handleRiskSetupApproachTabLoad()
                        // code block
                        break;
                    case 3:
                        progressBar.css('width', '100%')

                        $.get( "{{ route('risks.wizard.get-risk-import-setup-page') }}", {
                            setupApproach: this.riskSetupApproach,
                            standard: this.riskStandard
                        }).done(function (res) {
                            // rendering import tab
                            if (res.success){
                                $( "#import-tab" ).html( res.data.page );

                                if(res.type == 'automated'){
                                    if(wizardRiskSetup.riskSetupApproach == 'Automated'){
                                        wizardRiskSetup.automatedRiskImport.init()
                                    }
                                } else {
                                    // resetting yourself approach import tab states
                                    wizardRiskSetup.yourselfRiskImport.init()

                                    $(".risk-category-tab-nav").first().trigger('click')
                                }

                                // goto import tab
                                $("a[href='#import-tab']").trigger('click')


                            }
                        });
                        // code block
                        break;
                    default:
                    // code block
                }
            },
            goToNextStep: function (el) {
                // setting up the next stage
                let currentTab = $(el).data('current-tab')



                // process control validation
                if(currentTab == 1) {
                    let riskSetupStandard = $('input[name=risk-setup-standard]:checked').val()

                    if (riskSetupStandard) {
                        this.setRiskStandard(riskSetupStandard)
                    } else {
                        return
                    }
                }

                if (currentTab == 2){
                        let riskSetupApproach = $('input[name=risk-setup-approach]:checked').val()

                        if (riskSetupApproach){
                            this.setRiskSetupApproach(riskSetupApproach)
                        } else {
                            return
                        }
                }

                this.setNextStage(currentTab)

                this.updateSetupWizard(el)
            },
            setNextStage: function(currentStage){
              this.currentStage =   currentStage+1
            },
            goToPrevStep: function () {
            },
            setRiskStandard(standard){
                this.riskStandard = standard
            },
            handleRiskSetupApproachTabLoad: function(){
                $.get( "{{ route('risks.wizard.check-compliance-projects-exists') }}", { standard: this.riskStandard } ).done(function (res) {
                    if (res.success == true){
                        if (!res.exists){
                            $("#automated-setup-approach").prop( "disabled", true )
                        } else {
                            $("#automated-setup-approach").prop( "disabled", false )
                        }
                    }
                });
            },
            setRiskSetupApproach(approach){
                this.riskSetupApproach = approach
            },
            handleEvents: {
                chooseSetupApproach: function () {
                    $(document).on('click', '.choose-risk-setup-approach', function () {
                        $('.choose-risk-setup-approach').not(this).find('input').prop('checked', false)
                    })
                },
                switchTabs: function () {
                    // Not allowing to go to next step unless current setup step is complete

                    $(".risk-setup-nav-wp a[data-toggle=tab]").on("click", function(e) {
                        let elHrefAttr = $(this).attr('href')

                        // NOT ALLOWING TO GO TO APRROACH AND IMPORT SETUP TAB UNLESS CHOOSE STANDARD STEP IS COMPLETE
                        if (wizardRiskSetup.currentStage == 1) {
                            if (elHrefAttr == '#approach-tab' || elHrefAttr == '#import-tab' ){
                                e.preventDefault();
                                return false;
                            }
                        }

                        // Not allowing to goto import risk tab unless setup approach step is complete
                        if (wizardRiskSetup.currentStage == 2 ) {
                            if (elHrefAttr == '#import-tab'){
                                e.preventDefault();
                                return false;
                            }
                        }
                    });

                }
            },
            yourselfRiskImport: {
                selectedRisks: [],
                currentTabURL: null,
                isConfirmTab: false,
                currentTabCategoryId: null,
                currentTabIndex: 1,
                currentStage: null,
                setRisksToImport: function () {

                },
                loadRisksListSection: function(){
                    // initializing loader
                    wizardRiskSetup.loaderInit()

                    let dataOptions = {
                        standard: wizardRiskSetup.riskStandard,
                        is_confirm_tab: this.isConfirmTab,
                        selected_risk_ids:  this.selectedRisks,
                        current_tab_index: this.currentTabIndex,
                        risk_name_search_query: $('input[name=risk_name_search_query]').val()
                    }

                    if (!this.isConfirmTab){
                        dataOptions['category_id'] =  this.currentTabCategoryId
                    }

                    $.ajax({
                        method: "GET",
                        url: wizardRiskSetup.yourselfRiskImport.currentTabURL,
                        data: dataOptions
                    })
                        .done(function( res ) {
                            if (res.success){
                                // destroying loader
                                wizardRiskSetup.loaderDestroy()

                                $("#risk-list-wp").html(res.page)

                                $("#risk-category").html(res.category)

                                wizardRiskSetup.selectAllHandle()
                            }
                        });
                },
                processControlValidate: function(currentTab){
                    if(currentTab > wizardRiskSetup.yourselfRiskImport.currentStage) {
                        return false;
                    } else {
                        return true;
                    }
                },
                handleEvents: {
                    renderRisksListSection: function(){
                        $(document).on('click', '.risk-category-tab-nav', function (e) {
                            e.preventDefault()

                            let url = this.href
                            let currentCategoryTab = $(this).data('current-category-tab')

                            // process control validation
                            if(!wizardRiskSetup.yourselfRiskImport.processControlValidate(currentCategoryTab)){
                                return
                            }

                            // updating request url
                            wizardRiskSetup.yourselfRiskImport.currentTabURL = url


                            if(  this.hasAttribute('data-category-id') ){
                                wizardRiskSetup.yourselfRiskImport.currentTabCategoryId = $(this).data('category-id')
                                wizardRiskSetup.yourselfRiskImport.isConfirmTab = false
                            } else {
                                // comfirm tab
                                wizardRiskSetup.yourselfRiskImport.currentTabCategoryId = null
                                wizardRiskSetup.yourselfRiskImport.isConfirmTab = true
                            }

                            // removing active class form prev and adding active to current
                            $(".risk-category-tab-nav").removeClass("active");

                            $(this).addClass("active")

                            wizardRiskSetup.yourselfRiskImport.currentTabIndex = currentCategoryTab
                            // loading risk list tab data
                            wizardRiskSetup.yourselfRiskImport.loadRisksListSection()
                        })
                    },
                    handlePagination: function(){
                        $(document).on('click', '.risks-pagination-wp a.page-link', function (e) {
                            e.preventDefault()

                            let url = this.href

                            // updating request url
                            wizardRiskSetup.yourselfRiskImport.currentTabURL = url

                            // loading risk list tab data
                            wizardRiskSetup.yourselfRiskImport.loadRisksListSection()

                        })
                    },
                    riskNameSearch: function(){
                        $(document).on('keyup', 'input[name=risk_name_search_query]', function () {
                            wizardRiskSetup.yourselfRiskImport.loadRisksListSection()
                        })
                    },
                    handleRiskSelect: function () {
                        $(document).on('change', "#import-tab input[name=risk-item]", function () {
                            let targetCheckbox = $(this)
                            let riskId = targetCheckbox.val()

                            if(targetCheckbox.prop('checked')){
                                if(!wizardRiskSetup.yourselfRiskImport.selectedRisks.includes(riskId)){
                                    wizardRiskSetup.yourselfRiskImport.selectedRisks.push(riskId)
                                }
                            } else {
                                if(wizardRiskSetup.yourselfRiskImport.selectedRisks.includes(riskId)){
                                    wizardRiskSetup.yourselfRiskImport.selectedRisks = wizardRiskSetup.yourselfRiskImport.selectedRisks.filter(function(item) {
                                        return item !== riskId
                                    })
                                }
                            }
                        })
                    },
                    handleRiskSelectAll: function () {
                        $(document).on('click', "#select_all_risk_items_checkbox", function () {
                            if($(this).prop('checked')){
                                $("#import-tab input[name=risk-item]").prop('checked', true).trigger('change')
                            } else {
                                $("#import-tab input[name=risk-item]").prop('checked', false).trigger('change')
                            }
                        })
                    },
                    goToNextStage: function () {
                        $(document).on('click', '.risk-category-next-btn', function () {
                            let nextStage = $(this).data('next-stage')

                            wizardRiskSetup.yourselfRiskImport.currentStage = nextStage

                            // updating current nav
                            $(`a[data-current-category-tab=${nextStage-1}]`)
                                .removeClass('disabled')
                                .addClass('completed').find('span').html(`<svg xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>`)

                            // resetting search inputs values
                            $("#search").val("");
                            $("#select_all_risk_items_checkbox").prop('checked', false)

                            // removing current tab class form prev
                            $(`a[data-current-category-tab=${nextStage-1}]`)
                                .removeClass('current-stage')
                            // switching to next tab
                            $(`a[data-current-category-tab=${nextStage}]`)
                                    .removeClass('disabled')
                                            .addClass('current-stage')
                                                    .trigger('click')
                        });
                    },
                    goToPrevStage: function () {
                        $(document).on('click', '#risk-list-wp .risk-category-back-btn', function () {
                            let prevStep  = $(this).data('prev-stage')

                            // switching to next tab
                            $(`a[data-current-category-tab=${prevStep}]`)
                                    .trigger('click')

                        })
                    },
                    confirmSetup: function () {
                        $(document).on('click', 'form[name=confirm-risk-setup-form]', function (e) {
                            e.preventDefault()

                            if(wizardRiskSetup.yourselfRiskImport.selectedRisks.length > 0 )
                            {

                                var input = $("<input>")
                                    .attr("type", "hidden")
                                    .attr("name", "selected_risk_ids").val(wizardRiskSetup.yourselfRiskImport.selectedRisks);

                                $('form[name=confirm-risk-setup-form]').append(input);


                                if($('select[name=control_mapping_project]').length > 0)
                                {
                                    $('#control-mapping-project-modal').modal('show');

                                    $(document).on('change', 'select[name=control_mapping_project]', function () {

                                        $(document).find("input[name=control_mapping_project]").remove()

                                        if($(this).val())
                                        {
                                            var controlMappingProjectInput = $("<input>")
                                                .attr("type", "hidden")
                                                .attr("name", "control_mapping_project")
                                                .val($(this).val());
                                            $('form[name=confirm-risk-setup-form]').append(controlMappingProjectInput);
                                        }
                                    })

                                    $(document).on('click', '#proceed-setup-without-mapping', function () {
                                        swal({
                                            title: "Are you sure?",
                                            text: "All old risks will be deleted",
                                            showCancelButton: true,
                                            confirmButtonColor: '#ff0000',
                                            confirmButtonText: 'Yes',
                                            closeOnConfirm: false,
                                            imageUrl: '{{ asset('assets/images/warning.png') }}',
                                            imageWidth: 120
                                        }).then((result) => {
                                            if (result.value) {
                                                // SUBMITTING THE FORM
                                                $('#is_map').val('0');
                                                $('form[name=confirm-risk-setup-form]').submit()
                                            }
                                        })
                                    })
                                    $(document).on('click', '#proceed-setup-map', function () {
                                        swal({
                                            title: "Are you sure?",
                                            text: "All old risks will be deleted",
                                            showCancelButton: true,
                                            confirmButtonColor: '#ff0000',
                                            confirmButtonText: 'Yes',
                                            closeOnConfirm: false,
                                            imageUrl: '{{ asset('assets/images/warning.png') }}',
                                            imageWidth: 120
                                        }).then((result) => {
                                            if (result.value) {
                                                // SUBMITTING THE FORM
                                                $('#is_map').val('1');
                                                $('form[name=confirm-risk-setup-form]').submit()
                                            }
                                        })
                                    })
                                } else {
                                    swal({
                                        title: "Are you sure?",
                                        text: "All old risks will be deleted",
                                        showCancelButton: true,
                                        confirmButtonColor: '#ff0000',
                                        confirmButtonText: 'Yes',
                                        closeOnConfirm: false,
                                        imageUrl: '{{ asset('assets/images/warning.png') }}',
                                        imageWidth: 120
                                    }).then((result) => {
                                        if (result.value) {
                                            // SUBMITTING THE FORM
                                            $('form[name=confirm-risk-setup-form]').submit()
                                        }
                                    })
                                }
                            } else {
                                console.log('not submitted')
                            }

                        })
                    }
                },
                init: function () {
                    this.selectedRisks = []
                    this.currentTabURL = null
                    this.currentStage = 1

                    // binding events
                    this.handleEvents['handleRiskSelect']()
                    this.handleEvents['handleRiskSelectAll']()
                    this.handleEvents['renderRisksListSection']()
                    this.handleEvents['riskNameSearch']()
                    this.handleEvents['goToNextStage']()
                    this.handleEvents['goToPrevStage']()
                    this.handleEvents['handlePagination']()
                    this.handleEvents['confirmSetup']()
                }
            },
            automatedRiskImport: {
                handleEvents: {
                    confirmSetup: function () {
                        validateAutomatedRiskImportForm()
                    }
                },
                init: function () {
                    // binding events
                    this.handleEvents['confirmSetup']()
                }
            },

            /**
             * Make sure select all checkbox is checked only if all checkboxes are selected on the page.
             */
            selectAllHandle: function(){
                const allAreChecked = $("#import-tab input[name=risk-item]:checked").length === $("#import-tab input[name=risk-item]").length;
                $('#select_all_risk_items_checkbox').prop('checked', allAreChecked);
            },

            loaderInit: function(){
                // $('#risk-list-wp').prepend(`<div class="spinner-border avatar-lg text-primary m-2" role="status"></div>`)
            },
            loaderDestroy: function(){
                // $('#risk-list-wp').remove(`.spinner-border`)
            },
            init: function () {
                this.updateSetupWizard()

                // binding events
                this.handleEvents['chooseSetupApproach']()
                this.handleEvents['switchTabs']()
            }
        }

        // initializing risk setup wizard
        wizardRiskSetup.init()

        $(document).on('click','.go-to-next-step-btn', function () {
            wizardRiskSetup.goToNextStep(this)
        });

        // handling collale icon change
        $(document).on('click', '.collapse-el', function () {
            let targetEl = $(this).find('i')

            if (targetEl.hasClass( "fa-chevron-right" )){
                targetEl.removeClass('fa-chevron-right').addClass("fa-chevron-down")
            } else {
                targetEl.removeClass('fa-chevron-down').addClass("fa-chevron-right")
            }
        });

        // handling risk standard select
        $(document).on('click','input[name=risk-setup-standard]', function () {
            $('input[name=risk-setup-standard]').not(this).prop('checked', false)
        });

        // Front end validation for automated risk import page
        function validateAutomatedRiskImportForm(){
            $("#risk-wizard-automated-import-form").validate({
                errorClass: 'invalid-feedback',
                errorPlacement: function(error, element) {
                    error.appendTo('#automated-setup-error');
                },
                rules: {
                    project: {
                        required: true
                    }
                },
                messages: {
                    project: {
                        required: 'The project field is required.'
                    }
                },
                submitHandler: function(form, event) {

                    swal({
                        title: "Are you sure?",
                        text: "All old risks will be deleted",
                        showCancelButton: true,
                        confirmButtonColor: '#ff0000',
                        confirmButtonText: 'Yes',
                        closeOnConfirm: false,
                        imageUrl: '{{ asset('assets/images/warning.png') }}',
                        imageWidth: 120
                    }).then((result) => {
                        if (result.value) {
                            // SUBMITTING THE FORM
                            form.submit()
                        }
                    })
                }
            });
            $('.select2-inputs').select2()
        }
    });
</script>
@endsection
