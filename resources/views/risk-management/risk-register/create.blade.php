@extends('layouts.layout')

@php $pageTitle = $risk->id ? "Edit Risk" : "Add Risk"; @endphp

@section('title', $pageTitle)

@section('custom_css')
<link href="{{asset('assets/css/modules/risk-management.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .top__head-text {
            border-bottom: 2px solid var(--secondary-color);
            margin-bottom: 20px;
        }

        /* .likelihood__range, .impact__range {
            margin-top: -17px;
        } */
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
            font-size: 10px;
        }
        .risk-store-form .dropdown .inner{
            overflow-y: unset!important;
        }
        .category-overflow__xaxis .dropdown-menu.show {
            overflow:auto;
        }
        button.dropdown-toggle{
            background-color: #fff!important;
            border-color: #ced4da!important;
        }
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
    </style>
@endsection

@section('plugins_css')
    <link href="{{asset('assets/libs/multiselect/multi-select.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/libs/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- rangeSlider css -->
    <link href="{{asset('assets/libs/ion-rangeslider/ion.rangeSlider.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    <!-- breadcrumbs -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">Risk Management</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('risks.register.index') }}">Risk Register</a></li>
                        <li class="breadcrumb-item"><a href="#">{{ $risk->id ? 'Edit' : 'Add' }} Risk</a></li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $pageTitle }}</h4>
            </div>
        </div>
    </div>
    <!-- end of breadcrumbs -->

    @include('includes.flash-messages')

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body project-box">
                    <div class="top__head-text d-flex justify-content-between pb-2">
                        <h4>{{ $risk->id ? 'Edit' : 'Add' }} Risk</h4>
                        @if(!$risk->id)
                        <a href="{{ route('risks.manual.setup') }}" class="btn btn-primary float-end">Manual Import</a>
                        @endif
                    </div>
                    <!-- form starts here -->
                    <form action="{{ $risk->id ? route('risks.register.risks-update', $risk->id) : route('risks.register.risks-store') }}" class="risk-store-form" method="post">
                        @csrf
                        <div class="row mb-3">
                            <label for="riskname" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Risk Name<span class="required text-danger ms-1">*</span></label>
                            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                <input type="text" name="risk_name" value="{{ old('risk_name') ? old('risk_name') : decodeHTMLEntity($risk->name) }}"  class="form-control" id="riskname" placeholder="Enter risk name here">
                                <div class="invalid-feedback d-block">
                                    @if ($errors->has('risk_name'))
                                        {{ $errors->first('risk_name') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="description" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Description<span class="required text-danger ms-1">*</span></label>
                            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                <textarea class="form-control" name="risk_description" id="description" rows="5" placeholder="Enter description here">{{old('risk_description') ? old('risk_description') : decodeHTMLEntity($risk->risk_description)}}</textarea>
                                <div class="invalid-feedback d-block">
                                    @if ($errors->has('risk_description'))
                                        {{ $errors->first('risk_description') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="treatment" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Treatment<span class="required text-danger ms-1">*</span></label>
                            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                <textarea class="form-control" name="treatment" id="treatment" rows="5" placeholder="Enter treatment here">{{old('treatment') ? old('treatment') : decodeHTMLEntity($risk->treatment)}}</textarea>
                                <div class="invalid-feedback d-block">
                                    @if ($errors->has('treatment'))
                                        {{ $errors->first('treatment') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3 edit-risk__category category-overflow__xaxis">
                            <label for="category" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Category<span class="required text-danger ms-1">*</span></label>
                            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                <select class="form-control selectpicker" id="category" name="category">
                                    @foreach($riskCategories as $riskCategory)
                                    <option value="{{ $riskCategory->id }}" {{ old('category', $risk->category_id) == $riskCategory->id ? 'selected' : '' }}> {{ decodeHTMLEntity($riskCategory->name) }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback d-block">
                                    @if ($errors->has('category'))
                                        {{ $errors->first('category') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="edit-risk__affected-properties row mb-3">
                            <label for="aff-property" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Affected property(ies)<span class="required text-danger ms-1">*</span></label>
                            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                @php
                                    $affectedProps = explode(',', $risk->affected_properties);
                                @endphp
                                <select class="form-control selectpicker"  multiple="" id="aff-property" name="affected_properties[]">

                                    <optgroup label="Common Attributes">
                                    @foreach($risksAffectedProperties['common'] as $risksAffectedProperty)
                                        <option value="{{ $risksAffectedProperty }}" {{ in_array($risksAffectedProperty, $affectedProps) ? 'selected' : '' }}>{{ $risksAffectedProperty }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Other Attributes" class="">
                                @foreach($risksAffectedProperties['other'] as $risksAffectedProperty)
                                        <option value="{{ $risksAffectedProperty }}" {{ in_array($risksAffectedProperty, $affectedProps) ? 'selected' : '' }}>{{ $risksAffectedProperty }}</option>
                                    @endforeach
                                </optgroup>
                                </select>
                                <div class="invalid-feedback d-block">
                                    @if ($errors->has('affected_properties'))
                                        {{ $errors->first('affected_properties') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="edit-risk__risk-treatment row mb-3">
                            <label for="risk-treatment" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Risk Treatment<span class="required text-danger ms-1">*</span></label>
                            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                <select name="treatment_options" class="selectpicker form-control" data-style="btn-light">
                                    <option value="Mitigate" {{ $risk->treatment_options == 'Mitigate' ? 'selected' : '' }}>Mitigate</option>
                                    <option value="Accept" {{ $risk->treatment_options == 'Accept' ? 'selected' : '' }}>Accept</option>

                                </select>
                                <div class="invalid-feedback d-block">
                                    @if ($errors->has('treatment_options'))
                                        {{ $errors->first('treatment_options') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="affected-function-asset" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 form-label col-form-label">Affected function/asset: </label>
                            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                <input type="text" class="form-control" value="{{ old('affected_functions_or_assets', $risk->affected_functions_or_assets) }}" name="affected_functions_or_assets" id="affected-function-asset" placeholder="Enter affected function / asset">
                                <div class="invalid-feedback d-block">
                                    @if ($errors->has('affected_functions_or_assets'))
                                        {{ $errors->first('affected_functions_or_assets') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                    <div class="row mb-3">
                        <label for="likelihood" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-form-label">Likelihood</label>
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                            <div class="likelihood__range">
                                <input type="text"  class="js-range-slider likelihood" data-risk-id="{{ $risk->id?:1 }}"  id="likelihood-slider-el-{{ $risk->id?:1 }}" data-from="{{ $risk->id ? $risk->likelihood -1  : 0 }}"/>
                                <input type="hidden" name="likelihood" id="likelihood-input-el-{{ $risk->id?:1 }}" value="{{ $risk->id ? $risk->likelihood -1  : 0 }}"/>
                                <div class="invalid-feedback d-block">
                                    @if ($errors->has('likelihood'))
                                        {{ $errors->first('likelihood') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="impact" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-form-label">Impact</label>
                            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                <div class="impact__range">
                                    <input type="text"  class="js-range-slider impact" data-risk-id="{{ $risk->id?:1 }}" id="impact-slider-el-{{ $risk->id?:1 }}" data-from="{{  $risk->id ? $risk->impact - 1  : 0 }}"/>
                                    <input type="hidden" name="impact" id="impact-input-el-{{ $risk->id?:1 }}" value="{{ $risk->id ? $risk->impact -1  : 0 }}"/>
                                    <div class="invalid-feedback d-block">
                                        @if ($errors->has('impact'))
                                            {{ $errors->first('impact') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="risk__score" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-form-label">Inherent Risk Score</label>
                            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                <h4 class="pt-1">
                                    <span class="inherent-risk-score-wp" id="risk_inherent_score_{{$risk->id?:1}}">{{ $risk->id ? $risk->inherent_score : 1 }}</span>
                                    <span style="color: {{@$risk->inherentRiskScoreLevel->color}}" id="risk_inherent_level_{{$risk->id?:1}}" class=" font-xs ms-2 inherent-risk-level-wp risk-score-tag" >{{ $risk->id ? Str::ucfirst(@$risk->inherentRiskScoreLevel->name) : 'Low' }}</span>
                                </h4>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="risk__score" class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-form-label">Residual Risk Score</label>
                            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                <h4 class="pt-1">
                                    <span class="residual-risk-score-wp" id="risk_residual_score_{{$risk->id?:1}}">{{ $risk->id ? $risk->residual_score : 1 }}</span>
                                    <span id="risk_residual_level_{{$risk->id?:1}}" class=" font-xs ms-2 residual-risk-level-wp risk-score-tag" style="color: {{@$risk->residualRiskScoreLevel->color}}">
                                    {{ $risk->id ? Str::ucfirst(@$risk->residualRiskScoreLevel->name) : 'Low' }}
                                    </span>
                                </h4>
                            </div>
                        </div>

                        <div class="save-button d-flex justify-content-end">
                        @if(isset($risk->id))
                            <a class="btn btn-danger back-btn width-xl" style="margin-right:5px;"  href="{{url('risks/risks-register/'.$risk->id.'/show')}}">Back</a>
                    @endif
                            <input type="submit" class="btn btn-primary width-xl secondary-bg-color" value="Save">
                        </div>
                    </form>
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
    <script src="{{ asset('assets/libs/underscore-js/underscore-umd-min.js') }}"></script>
    <!-- custome-libs-js -->
    <script src="{{ asset('assets/custom-libs/risk-likelihood-impact-slider/risk-likelihood-impact-slider.js') }}"></script>
@endsection

@section('custom_js')
    <script nonce="{{ csp_nonce() }}">
        $(document).ready(function () {
            /* Risk likelihood and impact slider */
            const riskLikelihoodAndImpactSlider = new RiskLikelihoodAndImpactSlider({
                likelihoods: @json($riskMatrixLikelihoods),
                impacts: @json($riskMatrixImpacts),
                scores: @json($riskMatrixScores),
                levels: @json($riskScoreActiveLevelType),
            })

            riskLikelihoodAndImpactSlider.init()
        })
    </script>
@endsection
