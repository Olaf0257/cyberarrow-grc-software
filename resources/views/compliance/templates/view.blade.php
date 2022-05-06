@extends('layouts.layout')

@php $pageTitle = "View Standards"; @endphp

@section('title', $pageTitle)

@section('plugins_css')
    @include('includes.assets-libs.datatable-css-libs')
@endsection

@section('content')

<!-- breadcrumbs -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Administration</a></li>
                    <li class="breadcrumb-item active"><a href="javascript: void(0);">Compliance Template</a></li>
                    <li class="breadcrumb-item active"><a href="javascript: void(0);">View</a></li>
                </ol>
            </div>
            <h4 class="page-title">{{ $pageTitle }}</h4>
        </div>
    </div>
</div>
<!-- end of breadcrumbs -->


@include('includes.flash-messages')

@section('custom_css')
<style>
    /* table.dataTable {
        border-collapse: separate !important;
    } */

</style>
@endsection
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('compliance-template-create') }}" type="button" class="btn btn-sm btn-primary waves-effect waves-light float-end">
                    <i class="mdi mdi-plus-circle" title="Add New Standard"></i> Add New Standard
                </a>
                <h4 class="header-title mb-4">Manage Standards</h4>

                <table class="table table-hover m-0 table-centered  nowrap" width="100%" id="custom-datatable">
                    <thead>
                        <tr>
                            <th>
                                ID
                            </th>
                            <th>Name</th>
                            <th>Version</th>
                            <th>Controls</th>
                            <th>Created On</th>
                            <th class="hidden-sm">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- end col -->
</div>
<!-- end row -->
@endsection
@section('plugins_js')
    @include('includes.assets-libs.datatable-js-libs')
@endsection

@section('custom_js')
<script nonce="{{ csp_nonce() }}">
    $(document).ready(function () {

    $("#custom-datatable").DataTable({

            serverSide: true,
            scrollX: true,
            ordering: false,
            stateSave: true, 
            ajax: {
                "url": "{{ route('compliance-template-get-json-data') }}",
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

        $(document).on("click", ".delete-standard-btn", function(event) {
        event.preventDefault()

        swal({
                title: "Are you sure?",
                text: "You will not be able to recover this template!",
                showCancelButton: true,
                confirmButtonColor: '#ff0000',
                confirmButtonText: 'Yes, delete it!',
                imageUrl: '{{ asset('assets/images/warning.png') }}',
                imageWidth: 120
            }).then(confirmed => {
                if(confirmed.value && confirmed.value == true){
                    window.location.href = this.href
                }
            });
        });
    });
</script>
@endsection
