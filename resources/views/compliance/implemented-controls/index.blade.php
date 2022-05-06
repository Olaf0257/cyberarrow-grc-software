@extends('layouts.layout')

@section('plugins_css')
    @include('includes.assets-libs.datatable-css-libs')
<link href="{{ asset('assets/libs/multiselect/multi-select.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('custom_css')
    <style>

        hr {
            height: 2px;
        }
        td .statuss {
            border: 1px solid #ddd;
            padding: 8px 6px;
            background: #fff;
            text-align:center;
        }

        .fa option {
            font-weight: 900;
        }

        table tbody tr td button.non-clickable{
            cursor: default !important;
        }

        #tasks-datatable .select2-container .select2-selection--single .select2-selection__arrow {
            right: -4px;
        }
        .select2-dropdown {
           min-width: 136px!important;
        }

        .filter-row .ms-parent button:after {
            content: "\F140";
            display: inline-block;
            font: normal normal normal 24px/1 "Material Design Icons";
            font-size: inherit;
            text-rendering: auto;
            line-height: inherit;
            -webkit-font-smoothing: antialiased;
            position: absolute;
            right: 3px;
            top: 50%;
            transform: translateY(-50%);
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
        }

        .filter-row{
            background-color: #f4f5f7;
        }

        @media screen and (min-width: 560px){
            .filter-row .all-controlName input, .filter-row .all-standards input{
                width: 120px;
                }

            .all-standards .filter-input,.filter-row #search{
                width: 180px!important;
            }
        }
        @media screen and (max-width: 560px){
            .filter-row__wrap > div,.all-standards .filter-input{
                width: 100%!important;
            }
            .filter-row #search{
                width: 100%;
            }
        }

    </style>
@endsection
@section('content')
<!-- page title here -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="javascript: void(0);">
                            Controls
                        </a>
                    </li>
                </ol>
            </div>
            <h4 class="page-title">Controls</h4>
        </div>
    </div>
</div>
<!-- page title ends here -->


<div class="row">
    <div class="card-box w-100">
        <div class="col-12">
            <div class="filter-row d-flex flex-column flex-sm-row justify-content-between my-2 p-2 rounded">
                <div class="filter-row__wrap d-flex flex-wrap">
                    <div class="all-standards m-1">
                        <select class="form-control select2-field filter-input" name="standard_id">
                            <option value="">Select Standard</option>
                                @foreach($allStandards as $standard)
                            <option value="{{ $standard->id }}">{{ decodeHTMLEntity($standard->name)}}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="all-standards m-1">
                        <select class="form-control select2-field filter-input" disabled name="project_id" id="project_id">
                            <option value="">Select Project</option>
                        </select>
                    </div>
                    <div class="m-1 all-controlID">
                        <input class="form-control filter-input" name="controlID" type="text" placeholder="Control ID">
                    </div>
                    <div class="m-1 all-controlName">
                        <input class="form-control filter-input" name="control_name" type="text" placeholder="Control Name">
                    </div>
                    <div class="all-users m-1">
                        @php
                           $responsibleUserFilterAllowed = $loggedInUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Auditor'])
                        @endphp
                            <select class="form-control select2-field filter-input" name="responsible_user" {{ !$responsibleUserFilterAllowed ? 'disabled' : '' }}>
                                <option value="">All Users</option>
                                @foreach($taskContributors as $key => $value)
                                    <option value="{{ $value }}" {{ !$responsibleUserFilterAllowed ? ($loggedInUser->id ==  $value ? 'selected' : '') : '' }}> {{ decodeHTMLEntity($key)}}</option>
                                @endforeach
                             </select>
                    </div>
                </div>
                <div class="m-1 w-5 text-center text-sm-auto">
                    <button class="btn btn-primary" type="button" id="search"> Search </button>
                </div>
            </div>
        <div>


<div class="row">
    <div class="col-12">
         <!-- table -->
            <table id="implemented-controls-datatable" class="display table table-bordered border-light w-100" >
                <thead class="table-light">
                    <tr>
                        <th>Standard</th>
                        <th>Project</th>
                        <th>Control ID</th>
                        <th>Control Name</th>
                        <th>Control Description</th>
                        <th>Last Uploaded</th>
                        <th>Responsible</th>
                        <th>Action</th>
                    </tr>
                 </thead>
                <tbody class="tbody-light">

                </tbody>
            </table>
  </div>

@endsection

@section('plugins_js')
@include('includes.assets-libs.datatable-js-libs')
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>

<script nonce="{{ csp_nonce() }}">
$(document).ready(function() {
    const implementedControlsDatatable = $("#implemented-controls-datatable").DataTable({
            serverSide: true,
            processing: true,
            searching: false,
            stateSave: true,
            lengthChange: true,
            pagingType: "simple_numbers",
            // scrollX: true,
            ordering: false,
            responsive: true,
            "autoWidth": false,
            columns: [
                { responsivePriority: 0, targets:0 },
                { responsivePriority: 3, targets:1 },
                { responsivePriority: 2, targets:2 },
                { responsivePriority: 6, targets:3 },
                { responsivePriority: 5, targets:4 },
                { responsivePriority: 4, targets:5 },
                { responsivePriority: 1, targets:6 },
                { responsivePriority: 7, targets:7 },
            ],
            "columnDefs": [
                {
                    "render": function ( data, type, row ) {
                        return $.fn.dataTable.render.text().display(data, type, row);
                    },
                    "targets": [0,1,4,6]
                }
            ],
            ajax: {
                url: "{{ route( 'compliance.implemented-controls.data' ) }}",
                type: "GET",
                data: {
                    standard_id: function() {
                        return $(document).find('select[name="standard_id"]').val()
                    },
                    project_id: function() {
                        return $(document).find('select[name="project_id"]').val()
                    },
                    controlID: function() {
                        return $(document).find('input[name="controlID"]').val()
                    },
                    control_name: function() {
                        return $(document).find('input[name="control_name"]').val()
                    },
                    responsible_user: function () {
                        return $(document).find('select[name="responsible_user"]').val()
                    }
                }
            },
    })

    implementedControlsDatatable.on('responsive-resize', function ( e, datatable, columns ) {
        console.log(columns)
        for (i=0; i< columns.length; i++){
            let requiredIndex = i + 1
            if (columns[i]){
                $("#implemented-controls-datatable thead tr:first-child > th:nth-child(" + requiredIndex + ")").show()
            }
            else {
                $("#implemented-controls-datatable thead tr:first-child > th:nth-child(" + requiredIndex + ")").hide()
            }
        }
    });

    // initializing select2
    $(".select2-field").select2()

    //handling filter
    // datatable reload
    $(document).on('click', '#search', function(){
        implementedControlsDatatable.ajax.reload()
    })

    /* handling link evidences open in new tab */
    $(document).on('click', '.link-evidences-action', function(event){
        event.preventDefault()

        let urls = $(this).data('urls')
        console.log('link evidences, open', urls);

        urls.forEach(function (url, index) {
            window.open(url+"?refer=/compliance/implemented-controls");
        })


    })

    /* Populating project_filter  */
    $(document).on('change', 'select[name="standard_id"]', function () {
        if($(this).val() != "")
        {
            $('#project_id').removeAttr('disabled',false)
        }
        else
        {
            $('#project_id').attr('disabled', 'disabled');
        }

        let selectedOption = this.options[this.selectedIndex];

        $.ajax({
            url: "{{ route('compliance.tasks.get-projects-by-standards') }}",
            method: 'GET',
            data: { standardId: selectedOption.value }
        }).done(function(res) {

            // resetting select  box options for projects select box
            let targetSelectBox = $('select[name="project_id"]');


            targetSelectBox.empty();
            targetSelectBox.append(`<option value="">Select Project</option>`);

            if(res.length > 0){
                res.forEach(element => {
                    targetSelectBox.append(`<option value="${element.id}">${element.name}</option>`);
                });
            }
        });
    });
}) // END OF DOCUMENT READY
</script>
@endsection
