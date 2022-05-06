@extends('layouts.layout')

@php $pageTitle = "Manual Import"; @endphp

@section('title', $pageTitle)

@section('content')

@section('plugins_css')
    <link href="{{ asset('assets/libs/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('custom_css')
    <link href="{{asset('assets/css/modules/risk-management.css')}}" rel="stylesheet" type="text/css"/>
    <style>
        .csv__contents-box {
            background: #38414A;
            color: #f7f7f7;
            padding: 10px 15px;
        }
    </style>
@endsection
<!-- breadcrumbs -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="#">Risk Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('risks.setup') }}">Risk Setup</a></li>
                    <li class="breadcrumb-item"><a href="#">Manual Import</a></li>
                </ol>
            </div>
            <h4 class="page-title">{{  $pageTitle }}</h4>
        </div>
    </div>
</div>
<!-- end of breadcrumbs -->

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body project-box">
                <div class="manual-import">
                    <h4 class="title pb-2">Upload a CSV file to create new risk</h4>
                    <div class="box">
                        <div class="row">
                            <!-- csv box -->
                            <div class="col-xl-6">
                                @if ($message = Session::get('csv_upload_error'))
                                    @if($message == 'All rows successfully inserted')
                                        <div class="alert alert-success alert-block">
                                            @else
                                                <div class="alert alert-danger alert-block">
                                                    @endif
                                                    <button type="button" class="btn-close" data-dismiss="alert">×</button>
                                                    <strong>{{ $message }}</strong>
                                                </div>
                                            @endif
                                            <form action="{{ route('risks.manual.risks-import') }}" method="post"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="csv__box mb-2">
                                                    <input type="file" class="btn btn-csv-upload my-2 dropify"
                                                        name="csv_upload" data-height="207">
                                                    <div class="invalid-feedback d-block">
                                                        @if ($errors->has('csv_upload'))
                                                            {{ $errors->first('csv_upload') }}
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="csv-buttons py-2">
                                                    <button class="upload__btn btn btn-primary">Upload Risks</button>
                                                    <a href="{{ route('risks.manual.download-sample') }}"
                                                    class="sample__btn btn btn-primary">Download Sample</a>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- csv contents box -->
                                        <div class="col-xl-6">
                                            <div class="csv__contents-box">
                                                <h5 class="text-uppercase text-white">the csv file should have the
                                                    following:</h5>
                                                <ul>
                                                    <li>name (required): 191 character limit</li>
                                                    <li>risk_description (required)</li>
                                                    <li>affected_properties (required) :
                                                        @foreach($risksAffectedProperties as $key => $riskAP)
                                                            <br>
                                                            <b> {{$key}} </b>
                                                            (@foreach($riskAP as $rap){{$rap}}{{!$loop->last ? ',' : ''}}@endforeach)
                                                        @endforeach
                                                    </li>
                                                    <li>affected_functions_or_assets (required) : 255 character limit</li>
                                                    <li>treatment (required)</li>
                                                    <li>category (required) :
                                                        @foreach($riskCategories as $riskCategory)
                                                            {{ $riskCategory->name }}
                                                            => {{ $riskCategory->id }}{{ !$loop->last ? ', ' : '' }}
                                                        @endforeach
                                                    </li>
                                                    <li>treatment_options: Mitigate, Accept</li>
                                                    <li>
                                                        likelihood (optional) :
                                                        @foreach($riskLikelihoods as $riskLikelihood)
                                                            {{ $riskLikelihood->name }}
                                                            => {{ $riskLikelihood->index+1 }}{{ !$loop->last ? ', ' : '' }}
                                                        @endforeach
                                                    </li>
                                                    <li>
                                                        impact (optional) :
                                                        @foreach($riskImpacts as $riskImpact)
                                                            {{ $riskImpact->name }}
                                                            => {{ $riskImpact->index+1 }}{{ !$loop->last ? ', ' : '' }}
                                                        @endforeach
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @section('plugins_js')
        <script src="{{ asset('assets/libs/dropify/dropify.min.js') }}"></script>
        <script src="{{ asset('assets/js/pages/form-fileuploads.init.js') }}"></script>
        <script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
@endsection
