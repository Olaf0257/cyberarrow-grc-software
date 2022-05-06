@extends('layouts.layout')

@php $pageTitle = "Risk Setup"; @endphp

@section('title', $pageTitle)

@section('content')

@section('custom_css')
<link href="{{asset('assets/css/modules/risk-management.css')}}" rel="stylesheet" type="text/css" />
<style>
    a.risk-btn {
        min-width: 100px;
    }

    /***************
        Risk 
        Setup 
        Box 
    ***************/
    @media (min-width: 773px) and (max-width: 824px) {
        .wizard-content-box p.wizard-subtext {
            padding-bottom: 21px;
        }
    }

    @media (min-width: 1085px) and (max-width: 1121px) {
        .wizard-content-box p.wizard-subtext {
            padding-bottom: 21px;
        }
    }

    @media (min-width: 1203px) and (max-width: 1243px) {
        .wizard-content-box p.wizard-subtext {
            padding-bottom: 21px;
        }
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
                    <li class="breadcrumb-item"><a href="#">Risk Setup</a></li>
                </ol>
            </div>
            <h4 class="page-title">{{ $pageTitle }}</h4>
        </div>
    </div>
</div>
<!-- end of breadcrumbs -->


<div class="row">
    <div class="col-xl-6 col-lg-6 col-md-6">
        <div class="card">
            <div class="card-body project-box">
                <div class="head-text text-center manual-content-box">
                    <h4>Manual Import</h4>
                    <p class="my-3 manual-subtext">Manual import allows you to manually upload  a large number of risks using a CSV template. This  is great for organizations who want to bulk upload risks. </p>
                    <a href="{{ route('risks.manual.setup') }}" class="btn btn-primary risk-btn">Go</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6">
        <div class="card">
            <div class="card-body project-box">
                <div class="head-text text-center wizard-content-box">
                    <h4>Wizard</h4>
                    <p class="my-3 wizard-subtext">The wizard allows you to automatically generate  risks based on compliance projects and to choose risks based on international standards. </p>
                    <a href="{{ route('risks.wizard.setup') }}" class="btn btn-primary risk-btn">Go</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



