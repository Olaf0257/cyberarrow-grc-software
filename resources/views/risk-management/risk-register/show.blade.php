@extends('layouts.layout')

@php $pageTitle = "View Risk"; @endphp

@section('title', $pageTitle)

@section('plugins_css')
    @include('includes.assets-libs.datatable-css-libs')
    <link href="{{ asset('assets/libs/switchery/switchery.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/multiselect/multi-select.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/libs/ladda/ladda-themeless.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('custom_css')
    <link href="{{asset('assets/css/modules/risk-management.css')}}" rel="stylesheet" type="text/css"/>
    <style>

        .update__btn {
            margin-top: -10px;
        }

        .bg-gray {
            background: #f5f6f8;
            padding: 20px 10px;
            min-height: 200px;
        }

        .risk__detail h5.head__text {
            display: inline;
        }

        table tbody tr td .link {
            color: #b2dd4c;
        }

        .control__btn {
            margin-top: -38px;
        }

        table tbody .name__link, .map-icon-plus {
            color: #b2dd4c;
        }

        .map-icon-minus {
            color: red;

        }

        .overflow-right {
            overflow: auto;
        }

        .overflow-x-axis {
            overflow: auto;
            padding: 0px;
        }

        #mapping-controls-datatable td:first-child:before {
            top: 45%;
        }

        .disabled {
            border-color: #44444444 !important;
        }

        #manual-assign-form .invalid-feedback {
            position: absolute;
            bottom: -11px;
        }

        @media screen and (max-width: 990px) {
            #mapped-controls-datatable_wrapper div:nth-child(2) div {
                overflow: auto;
            }
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
                        <li class="breadcrumb-item"><a href="#">Risk Management</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('risks.register.index') }}">Risk Register</a></li>
                        <li class="breadcrumb-item"><a href="#">View Risk</a></li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $pageTitle }}</h4>
            </div>
        </div>
    </div>
    <!-- end of breadcrumbs -->
    @include('includes.flash-messages')
    <div class="flash"></div>

    <!-- View Risk section -->
    <div class="row" id="risk-overview-section">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body project-box">
                    <div class="risk__detail">
                        <div class="top__btn d-flex pb-1">
                            <div class="top__head-text"><h4>View Risk</h4></div>
                            <span class="update__btn ms-auto">
                                <a class="btn btn-danger back-btn width-lg" style="margin-right:5px;"
                                href="{{ url()->previous() }}">Back</a>
                                <a href="{{ route('risks.register.risks-edit', $risk->id) }}"
                                class="btn btn-primary width-lg">Edit</a>
                            </span>
                        </div>

                        <div class="row border py-3">
                            <div class="col-lg-8 col-md-12">
                                <div class="description">
                                    <div>
                                        <h5 class="head__text">Description:</h5>
                                        <span class="sub__text">
                                            {{ decodeHTMLEntity($risk->risk_description)}}
                                        </span>
                                    </div>

                                    <div class="my-2">
                                        <h5 class="head__text">Treatment:</h5>
                                        <span class="sub__text">{{ decodeHTMLEntity($risk->treatment)}}</span>
                                    </div>

                                    <div>
                                        <h5 class="head__text">Affected property(ies):</h5>
                                        <span>{{ $risk->affected_properties }}</span>
                                    </div>

                                    <div class="my-2">
                                        <h5 class="head__text">Affected function/asset:</h5>
                                        <span>{{ $risk->affected_functions_or_assets }}</span>
                                    </div>

                                    <div>
                                        <h5 class="head__text">Risk Treatment:</h5>
                                        <span>{{ $risk->treatment_options }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-12">
                                <div class="category">
                                    <div class="mb-2">
                                        <h5 class="head__text">Category:</h5>
                                        <span class="sub__text">{{ $risk->category->name }}</span>
                                    </div>

                                    <div>
                                        <h5 class="head__text">Likelihood:</h5>
                                        <span>{{ @$risk->riskMatrixLikelihood->name }}</span>
                                    </div>

                                    <div class="my-2">
                                        <h5 class="head__text">Impact:</h5>
                                        <span>{{ @$risk->riskMatrixImpact->name }}</span>
                                    </div>

                                    <div>
                                        <h5 class="head__text">Inherent Risk Score:</h5>
                                        <span>{{ $risk->inherent_score }}
                                            <span class="risk-score-tag  ms-2"
                                                style="color: {{ $risk->InherentRiskScoreLevel->color}};">
                                                {{ Str::ucfirst(@$risk->inherentRiskScoreLevel->name) }}
                                            </span>
                                        </span>
                                    </div>

                                    <div class="my-2">
                                        <h5 class="head__text">Residual Risk Score:</h5>
                                        <span>{{ $risk->residual_score }}
                                            <span class="risk-score-tag  ms-2"
                                                style="color: {{ $risk->residualRiskScoreLevel->color }}">
                                                {{ Str::ucfirst(@$risk->residualRiskScoreLevel->name) }}
                                            </span>
                                        </span>
                                    </div>

                                    @php

                                        $status = '<span class="risk-score-tag extreme">Open</span>';
                                        if($risk->status == 'Close')
                                        {
                                            $status = '<span class="risk-score-tag low ">Closed</span>';
                                        }


                                    @endphp
                                    <div>
                                        <h5 class="head__text">Status:</h5>
                                        {!! $status !!}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="controls__table">
                        <div class="top__head-text"><h4>Controls</h4></div>
                        <div class="control__btn mb-2 d-flex justify-content-end">
                            <!-- <a href="#" class="btn btn-primary width-lg create__cntrl-btn">Create Control</a> -->
                            <a href="#" class="btn btn-primary width-lg mapping-btn ms-2" data-toggle="modal"
                            data-target="#control-manual-assign-modal">Manual assignment</a>
                            @if($risk->treatment_options == 'Mitigate')
                                <a href="#" class="btn btn-primary width-lg mapping-btn ms-2" data-toggle="modal"
                                data-target="#control-mapping-modal">Edit Control Mapping(s)</a>
                            @else
                                <a class="btn btn-primary width-lg mapping-btn ms-2 disabled">Edit Control Mapping(s)</a>
                            @endif
                        </div>

                        <!-- manual assignment modal -->
                        <div id="control-manual-assign-modal" class="modal fade" role="dialog" aria-hidden="true"
                            aria-labelledby="control-manualAssignLabel" style="display: none;">
                            <div class="modal-dialog modal-full modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header align-items-center">
                                        <h4 class="modal-title" id="full-width-modalLabel">Manual assignment</h4>
                                        <button type="button" class="btn-close mx-1" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('risks.register.risks-manual-assign', $risk->id) }}"
                                        method="POST" id="manual-assign-form">
                                        @csrf
                                        <div class="modal-body p-4">
                                            <div class="row hop[a">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Risk owner<span
                                                                class="required text-danger">*</span></label>
                                                        <select name="owner" id="owner" class="form-control select-picker"
                                                                data-placeholder="Search owner...">
                                                            <option value="">Search owner...</option>
                                                            @foreach($contributors as $key => $value)
                                                                <option value="{{$value}}"
                                                                        @if($value == $risk->owner_id)
                                                                        selected
                                                                    @endif
                                                                    {{Auth::guard('admin')->user()->hasAnyRole([
                                                                            'Global Admin',
                                                                            'Compliance Administrator',
                                                                            'Risk Administrator',
                                                                            'Contributor',
                                                                            ]) ? "" : "disabled" }}>
                                                                    {{decodeHTMLEntity($key)}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="invalid-feedback d-block">
                                                            @if ($errors->has('custodian'))
                                                                {{ $errors->first('custodian') }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="policies" class="form-label">Risk custodian<span
                                                                class="required text-danger">*</span></label>
                                                        <select name="custodian" id="custodian"
                                                                class="form-control select-picker"
                                                                data-placeholder="Search custodian...">
                                                            <option value="">Search custodian...</option>
                                                            @foreach($contributors as $key => $value)
                                                                <option value="{{$value}}"
                                                                        @if($value == $risk->custodian_id)
                                                                        selected
                                                                    @endif
                                                                    {{Auth::guard('admin')->user()->hasAnyRole([
                                                                            'Global Admin',
                                                                            'Compliance Administrator',
                                                                            'Risk Administrator',
                                                                            'Contributor',
                                                                            ]) ? "" : "disabled" }}>
                                                                    {{decodeHTMLEntity($key)}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="invalid-feedback d-block">
                                                            @if ($errors->has('custodian'))
                                                                {{ $errors->first('custodian') }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary waves-effect"
                                                    data-dismiss="modal">Close
                                            </button>
                                            <button type="submit"
                                                    class="btn btn-primary waves-effect waves-light ladda-button">Save
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- control mapping -->
                        <div id="control-mapping-modal" class="modal fade" role="dialog" aria-hidden="true"
                            aria-labelledby="control-mappingLabel" style="display: none;">
                            <div class="modal-dialog modal-full modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header align-items-center">
                                        <h4 class="modal-title" id="full-width-modalLabel">Control Mapping</h4>
                                        <button type="button" class="btn-close mx-1" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <!-- filter section -->
                                    <div class="card">
                                        <div class="card-body">
                                            <div
                                                class="row linking-existing-controls-modal__filters d-flex justify-content-center justify-content-md-end">
                                                <div class="mx-1 mb-1">
                                                    <select name="standard_filter"
                                                            class="form-control select2-picker select-standard">
                                                        <option value>Select Standard</option>
                                                        @foreach($allComplianceStandards as $standard)
                                                            <option value="{{ $standard->id }}"> {{ $standard->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mx-1 mb-1">
                                                    <select name="project_filter" disabled
                                                            class="form-control select2-picker select-project">
                                                        <option value="">Select Project</option>
                                                    </select>
                                                </div>
                                                <div class="me-2 ms-1 mb-1">
                                                    <button name="search" class="btn btn-primary">Search</button>
                                                </div>
                                            </div>
                                            <!-- table -->
                                            <table id="mapping-controls-datatable"
                                                class="table table-borderless table-hover nowrap" style="width:100%">
                                                <thead class="table-light">
                                                <tr>
                                                    <th>Project</th>
                                                    <th>Standard</th>
                                                    <th>Control ID</th>
                                                    <th>Control Name</th>
                                                    <th>Control Description</th>
                                                    <th>Status</th>
                                                    <th>Is mapped</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- table -->
                        <table id="mapped-controls-datatable" class="table table-borderless table-hover nowrap w-100">
                            <thead class="table-light">
                            <tr>
                                <th>Control ID</th>
                                <th>Project</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Frequency</th>
                                <th>Responsible</th>
                                <th>Approver</th>
                            </tr>
                            </thead>

                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('plugins_js')
    <script src="{{ asset('assets/libs/switchery/switchery.min.js') }}"></script>
    <script src="{{ asset('assets/feather/js/feather.min.js') }}"></script>
    <script src="{{asset('assets/libs/multiselect/jquery.multi-select.js')}}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/ladda/spin.js') }}"></script>
    <script src="{{ asset('assets/libs/ladda/ladda.js') }}"></script>
    <script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
    @include('includes.assets-libs.datatable-js-libs')
@endsection


@section('custom_js')

    <script nonce="{{ csp_nonce() }}">
        $(document).ready(function () {
            $('.select2-picker').select2();
            const manualAssignSelector = $('.select-picker');
            manualAssignSelector.select2();

            $(document).on('change', '.select-standard', function () {
                if ($(this).val() != "") {
                    $('.select-project').removeAttr('disabled', false)
                } else {
                    $('.select-project').attr('disabled', 'disabled');
                }

            })

            const mappedRiskControlsDatatable = $('#mapped-controls-datatable').DataTable({
                serverSide: true,
                searching: false,
                ordering: false,
                processing: true,
                responsive: true,
                "destroy": true,
                columnDefs: [
                    {responsivePriority: 0, targets: 0},
                    {responsivePriority: 1, targets: 1},
                    {responsivePriority: 2, targets: 2},
                    {responsivePriority: 3, targets: 3},
                    {responsivePriority: 4, targets: 4},
                    {responsivePriority: 5, targets: 5},
                    {responsivePriority: 6, targets: -1}
                ],
                ajax: {
                    url: "{{ route('risks.register.get-risk-mapped-compliance-controls', $risk->id) }}",
                    method: "Get"
                }
            });


            /* MAPPING CONTROLS SCRIPT STARTS */
            $(document).on('shown.bs.modal', '#control-mapping-modal', function (event) {
                // on control mapping modal show data table initialized
                const mappingControlsDatatable = $('#mapping-controls-datatable').DataTable({
                    serverSide: true,
                    ordering: false,
                    searching: false,
                    autoWidth: false,
                    processing: true,
                    responsive: true,
                    stateSave: true,
                    columnDefs: [
                        {responsivePriority: 0, targets: 0},
                        {responsivePriority: 1, targets: 1},
                        {responsivePriority: 3, targets: 2},
                        {responsivePriority: 4, targets: 3},
                        {responsivePriority: 5, targets: 4},
                        {responsivePriority: 6, targets: 5},
                        {responsivePriority: 7, targets: 6},
                        {responsivePriority: 2, targets: -1}
                    ],
                    ajax: {
                        url: "{{ route('risks.register.get-risk-mapping-compliance-project-controls', $risk->id) }}",
                        method: 'GET',
                        "data": {
                            "standard_filter": function () {
                                return $('select[name="standard_filter"]').val()
                            },
                            "project_filter": function () {
                                return $('select[name="project_filter"]').val()
                            }
                        }
                    },
                    "drawCallback": function (settings) {
                        $(".dataTables_paginate > .pagination").addClass("pagination-rounded");

                        var elems = Array.prototype.slice.call(document.querySelectorAll('.map-risk-controls'));

                        elems.forEach(function (html) {
                            var switchery = new Switchery(html);
                        });


                        // Map and unmap risk control
                        $(elems).on('change', function () {

                            let controlId = $(this).data('control-id')

                            $.post({
                                url: "{{ route('risks.register.map-risk-controls') }}",
                                method: 'POST',
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    "risk_id": "{{ $risk->id }}",
                                    "control_id": controlId
                                }
                            }).done(function (res) {
                                if (res.success) {
                                    mappingControlsDatatable.ajax.reload(function () {
                                        mappedRiskControlsDatatable.ajax.reload()
                                        // Refreshing risk overview section
                                        $("#risk-overview-section").load(`${window.location.href} #risk-overview-section >*`);
                                    }, false)
                                }
                            })
                        });
                    }
                });

                // Callback for responsive Is mapped function
                mappingControlsDatatable.on('responsive-display',function (e,datatable,row,show,update){
                    var elems = Array.prototype.slice.call(document.querySelectorAll('.map-risk-controls'));

                    elems.forEach(function (html) {

                        if(html.closest('tr').classList.contains('child') && !html.nextSibling){
                            var switchery = new Switchery(html);
                        }
                    });

                    // Map and unmap risk control
                    $(elems).on('change', function () {

                        let controlId = $(this).data('control-id')

                        $.post({
                            url: "{{ route('risks.register.map-risk-controls') }}",
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "risk_id": "{{ $risk->id }}",
                                "control_id": controlId
                            }
                        }).done(function (res) {
                            if (res.success) {
                                mappingControlsDatatable.ajax.reload(function () {
                                    mappedRiskControlsDatatable.ajax.reload()
                                    // Refreshing risk overview section
                                    $("#risk-overview-section").load(`${window.location.href} #risk-overview-section >*`);
                                }, false)
                            }
                        })
                    });
                });

                 /* Populating project_filter  */
                $(document).on('change', 'select[name="standard_filter"]', function () {
                    let selectedOption = this.options[this.selectedIndex];

                    $.ajax({
                        url: "{{ route('compliance.tasks.get-projects-by-standards') }}",
                        method: 'GET',
                        data: {standardId: selectedOption.value}
                    }).done(function (res) {

                        // resetting select  box options for projects select box
                        let targetSelectBox = $('select[name="project_filter"]');


                        targetSelectBox.empty();
                        targetSelectBox.append(`<option value="">Select Project</option>`);

                        if (res.length > 0) {
                            res.forEach(element => {
                                targetSelectBox.append(`<option value="${element.id}">${element.name}</option>`);
                            });
                        }
                    });
                });

                $("#control-mapping-modal button[name='search']").on('click', function () {
                    mappingControlsDatatable.ajax.reload()
                })

            });


            /* MAPPING CONTROLS SCRIPT END */

            //mapping table

            //feather
            feather.replace()

            //handle manual assignment form
            $.validator.addMethod("differentUser", function (value, element) {
                const ownerVal = $('#owner').val();
                const custodianVal = $('#custodian').val();
                let isDifferent = true;
                if (ownerVal === custodianVal) {
                    isDifferent = false
                }
                return this.optional(element) || isDifferent;
            }, "Different user, please")

            const manualAssignForm = $("#manual-assign-form");
            const manualAssignFormValidation = manualAssignForm.validate({
                errorClass: 'invalid-feedback',
                rules: {
                    owner: {
                        required: true,
                        differentUser: true
                    },
                    custodian: {
                        required: true,
                        differentUser: true
                    }
                },
                messages: {
                    owner: {
                        required: "The owner field is required.",
                        differentUser: "Owner & custodian can't be same",
                    },
                    custodian: {
                        required: "The custodian field is required.",
                        differentUser: "Custodian & owner can't be same",
                    },
                },
                submitHandler: function (from, e) {
                    e.preventDefault();

                    const serialize = $("#manual-assign-form").serialize();
                    let loadingBtn = Ladda.create(document.querySelector('#manual-assign-form button[type=submit]'));

                    loadingBtn.start();

                    $.ajax({
                        url: $("#manual-assign-form").attr('action'),
                        method: 'POST',
                        data: serialize,
                        success: function (response) {
                            //Ladda.stopAll()
                            if (response.exception) {
                                Swal.fire({
                                    type: 'error',
                                    text: response.exception
                                })
                            }

                            if (response.success) {
                                let alertClass = response.success ? 'alert-success' : 'alert-danger'

                                $(`.${alertClass}`).fadeOut(300);

                                $('.flash').append(`
                                <div class="alert ${alertClass} alert-block">
                                    <button type="button" class="btn-close" data-dismiss="alert">×</button>
                                    <strong>Saved owner and custodian to risk</strong>
                                </div>`)

                                Ladda.stopAll();
                                $('#control-manual-assign-modal').modal('toggle');
                            }
                        },
                        error: function (response) {
                            $('#manual-assign-form').find('button[type=submit]').prop('disabled', false)
                            Ladda.stopAll();

                            if (response.responseJSON.errors) {
                                $.each(response.responseJSON.errors, function (field_name, error) {
                                    $(document).find('[name=' + field_name + ']').after('<label class="invalid-feedback d-block">' + error + '</label>')
                                })
                            }
                        }
                    })
                }
            })

            manualAssignSelector.on("change", function () {
                manualAssignForm.valid();
            })

            manualAssignSelector.on('select2:select select2:unselect', function (e) {
                $(this).valid();
            });

            $("#control-manual-assign-modal").on('hidden.bs.modal', function (e) {
                manualAssignSelector.select2("destroy").select2();
            })
        });
    </script>

@endsection
