@extends('layouts.layout')

@php $pageTitle = decodeHTMLEntity($standard->name)." associated controls"; @endphp

@section('title', $pageTitle)

@section('plugins_css')
    @include('includes.assets-libs.datatable-css-libs')
@endsection

@section('custom_css')
<style>
    .card-body{
        display:flex;
        justify-content: space-between;
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
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Administration</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('compliance-template-view') }}">Compliance Template</a></li>
                    <li class="breadcrumb-item active"><a href="javascript: void(0);">Controls</a></li>
                </ol>
            </div>
            <h4 class="page-title">{{  $pageTitle }}</h4>
        </div>
    </div>
</div>
<!-- end of breadcrumbs -->

@include('includes.flash-messages')


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="text">
                    <h4 class="d-none d-sm-block">Manage Controls</h4>
                </div>
                <div class="card-box-menu">
                    <a href="{{ route('compliance-template-create-controls', [$standard->id]) }}" type="button" class="btn btn-sm btn-primary waves-effect waves-light my-2">
                        <i class="mdi mdi-plus-circle" title="Add New Control"></i> Add New Control
                    </a>
                </div>
            </div>
        </div>

    <!-- table starts here -->
        <table class="table table-hover m-0 table-centered dt-responsive w-100 bg-white" id="template-controls-datatable">
            <thead>
                <tr>
                    <th width='10%'>
                        ID
                    </th>
                    <th width='20%'>Name</th>
                    <th width='50%'>Description</th>
                    @if(!$standard->is_default)
                    <th class="hidden-sm" width='20%' >Action</th>
                    @endif
                </tr>
            </thead>

            <tbody>
            </tbody>
        </table>
    </div><!--/.col-12-->
</div>
@endsection
@section('plugins_js')
    @include('includes.assets-libs.datatable-js-libs')
@endsection

@section('custom_js')
<script nonce="{{ csp_nonce() }}">
    $(document).ready(function () {
        $("#template-controls-datatable").DataTable({
            stateSave: true,
            serverSide: true,
            ordering: false,
            ajax: {
                "url": "{{ route('compliance-template-controls-get-json-data', [$standard->id]) }}",
                "type": "GET",
            },
            "columnDefs": [
                {
                    "render": function ( data, type, row ) {
                        return $.fn.dataTable.render.text().display(data, type, row);
                    },
                    "targets": 2
                },
                {
                    "render": function ( data, type, row ) {
                        return $.fn.dataTable.render.text().display(data, type, row);
                    },
                    "targets": 0
                },
                {
                    "render": function ( data, type, row ) {
                        return $.fn.dataTable.render.text().display(data, type, row);
                    },
                    "targets": 1
                }
            ]
        })
    });
</script>
@endsection
