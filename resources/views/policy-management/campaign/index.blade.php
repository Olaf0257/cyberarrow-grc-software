@extends('layouts.layout')

@php $pageTitle = "Campaigns - Policy Management"; @endphp


@section('plugins_css')
<link rel="stylesheet" href="{{ asset('assets/css/icons.css') }}">
<link href="{{asset('assets/libs/multiselect/multi-select.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/ladda/ladda-themeless.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/libs/bootstrap4-date-time-picker/css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content')

<style nonce="{{ csp_nonce() }}">
.project-div {
    height: 265px;
}

.project-div h4{
    word-break: break-all;
}


.bootstrap-datetimepicker-widget .fe-chevron-up,
.bootstrap-datetimepicker-widget .fe-chevron-down
{
    background: #fff;
    color: #6c757d;
    font-size: 18px;
    border: 1px solid var(--secondary-color);
    font-weight: bolder;
    transition: 0.3s ease;

}


.bootstrap-datetimepicker-widget .fe-chevron-up,
.bootstrap-datetimepicker-widget .fe-chevron-down

{
    background: #fff !important;
    color: #6c757d;
    font-size: 18px;
    font-weight: bolder;
    transition: 0.3s ease;
}

.bootstrap-datetimepicker-widget .fe-clock,
.bootstrap-datetimepicker-widget .fe-calendar{
    font-size: 18px;
}


.absolute-error-form label.invalid-feedback {
    bottom: -11px;
}
#content-loading {
    position:absolute;
    left: 50%;
}

</style>

<!-- breadcrumbs -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('policy-management.campaigns') }}">Policy Management</a></li>
                    <li class="breadcrumb-item"><a href="#">Campaigns</a></li>
                </ol>
            </div>
            <h4 class="page-title">{{ $pageTitle }}</h4>
        </div>
    </div>
</div>
<!-- end of breadcrumbs -->

<!-- shows flash messages here -->
@include('includes.flash-messages')
<div class="flash"></div>

<div class="row  mb-3">
    <div class="col-8">
        <button type="button" class="btn btn-primary campaign-status-btn active" data-status="active">Active Campaigns</button>
        <button type="button" class="btn btn-primary campaign-status-btn" data-status="archived" >Archived Campaigns</button>
        <input type="hidden" name="campaign_status_filter" value="active">
    </div>

    <div class="col-4 clearfix">
        <div class="">
            <div class="mb-3">
                Search:&nbsp;&nbsp;<input type="text" name="campaign_name" class="form-control form-control-sm">
            </div>
        </div>
    </div>
</div>

<!-- campaign list -->
<div class="row" id="campaigns-wp">

</div>

<!-- create campaign modal -->
<div id="add-campaign-modal" class="modal fade absolute-error-form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="add-campaign-form" action="{{ route('policy-management.campaigns.store') }}"  method="POST">
                @csrf

                    <div class="modal-header">
                        <h4 class="modal-title">New Campaign</h4>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="required text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" id="name" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="policies" class="form-label">Policy(ies) <span class="required text-danger">*</span></label>
                                    <select name="policies[]" class="form-control select-picker" multiple="multiple">
                                        @foreach($policies as $policy)
                                        <option value="{{ $policy->id }}">{{ decodeHTMLEntity($policy->display_name." - ".$policy->version) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="launch-date_add-form" class="form-label">Launch Date <span class="required text-danger">*</span></label>
                                    <input type="text" class="form-control date-time-picker new_launch-date_add-form" name="launch_date" id="launch-date_add-form" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="due-date_add-form" class="form-label">Due Date <span class="required text-danger">*</span></label>
                                    <input type="text" class="form-control date-time-picker" name="due_date" id="due-date_add-form" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="timezone-add-form" class="form-label">Time Zone <span class="required text-danger">*</span></label>
                                    <select name="timezone" class="form-control" id="timezone-add-form">
                                        @foreach($timezones as $index => $timezone)
                                        <option value="{{ $index }}" {{ $index == 'Asia/Dubai' ? 'selected' : ''}}>{{ $timezone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="group" class="form-label">Groups <span class="required text-danger">*</span></label>
                                    <select class="form-control select-picker" name="groups[]" multiple="multiple">
                                        @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="group" class="form-label">Auto-enroll future group users <span class="required text-danger">*</span></label>
                                    <select class="form-control text-center" name="auto_enroll_users">
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.modal-body-->

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light ladda-button">Launch Campaign</button>
                    </div>
                </form>
            </div>
        </div>
</div>
<!-- /.modal -->

<!-- campaign duplicate modal -->
<div id="duplicate-campaign-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="duplicate-campaign-form" class="absolute-error-form" action="{{ route('policy-management.campaigns.store') }}"  method="POST">
            @csrf
                <input type="hidden" name="duplicate_campaign_form" value="">
                <div class="modal-header">
                    <h4 class="modal-title">Duplicate Campaign</h4>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="required text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="policies" class="form-label">Policy(ies) <span class="required text-danger">*</span></label>
                                <select name="policies[]" class="form-control select-picker" multiple="multiple">
                                    @foreach($policies as $policy)
                                    <option value="{{ $policy->id }}">{{ decodeHTMLEntity($policy->display_name." - ".$policy->version) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="launch-date_add-form" class="form-label">Launch Date <span class="required text-danger">*</span></label>
                                <input type="text" class="form-control date-time-picker dublicate-launch-date_add-form" name="launch_date" id="duplicate-launch-date_add-form" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="due-date_add-form" class="form-label">Due Date <span class="required text-danger">*</span></label>
                                <input type="text" class="form-control date-time-picker" name="due_date" id="duplicate-due-date_add-form" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="timezone-add-form" class="form-label">Time Zone <span class="required text-danger">*</span></label>
                                <select name="timezone" class="form-control" id="timezone-add-form">
                                    @foreach($timezones as $index => $timezone)
                                    <option value="{{ $index }}" {{ $index == 'Asia/Dubai' ? 'selected' : ''}}>{{ $timezone }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="group" class="form-label">Groups <span class="required text-danger">*</span></label>
                                <select class="form-control select-picker" name="groups[]" multiple="multiple">
                                    @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="group" class="form-label">Auto-enroll future group users <span class="required text-danger">*</span></label>
                                <select class="form-control text-center" name="auto_enroll_users">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.modal-body-->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light ladda-button">Duplicate Campaign</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /.modal -->
@if (count($errors) > 0)
    <script type="text/javascript">
        $( document ).ready(function() {
             $('#add-campaign-modal').modal('show');
        });
    </script>
  @endif
@endsection

@section('plugins_js')
<script src="{{ asset('assets/libs/multiselect/jquery.multi-select.js') }}"></script>
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('assets/libs/moment/moment.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/libs/bootstrap4-date-time-picker/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('assets/libs/ladda/spin.js') }}"></script>
<script src="{{ asset('assets/libs/ladda/ladda.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
@endsection

@section('custom_js')
<script nonce="{{ csp_nonce() }}">

$(document).ready(function () {

    /*Handling campaign status btn click*/
    $(document).on('click', '.campaign-status-btn', function(event){
        event.preventDefault()

        $(".campaign-status-btn").removeClass('active')
        $(this).addClass('active')

        let status = $(this).data('status')


        $("input[name=campaign_status_filter]").val(status)

        /* Rendering campaigns*/
        renderCampaigns()
    })

    // $('.select-picker').select2();


    function date()
    {
        //Set current date for add new campaign launch date after reset
        const date = new Date();

        let year = date.getFullYear();

        let day = date.getDate();
        day = day < 10 ? '0'+ day : day

        let month = date.getMonth() + 1;
        month = month < 10 ? '0'+ month : month

        let hour = date.getHours();
        var ampm = hour >= 12 ? 'PM' : 'AM';
        hour = hour % 12;
        hour = hour ? hour : 12;

        let min = date.getMinutes();
        min =  min < 10 ? '0'+min : min;

        let today = `${month}/${day}/${year} ${hour}:${min} ${ampm}`;


        $('.new_launch-date_add-form').val(today);
        $('.dublicate-launch-date_add-form').val(today);
    }


    //delete tag after press backspace
    $.fn.select2.amd.require(['select2/selection/search'], function (Search) {
        var oldRemoveChoice = Search.prototype.searchRemoveChoice;

        Search.prototype.searchRemoveChoice = function () {
            oldRemoveChoice.apply(this, arguments);
            this.$search.val('');
        };

        $('.select-picker').select2({
            width:'300px'
        });
    });
    const addCampaignForm = $("#add-campaign-form")
    const duplicateCampaignForm = $("#duplicate-campaign-form")

    //Set current date for add new campaign launch date
    $('#launch-date_add-form').datetimepicker({
        minDate : moment(),
        defaultDate:new Date()
    });

    //Set current date for add dublicate campaign launch date
    $('.duplicate-launch-date_add-form').datetimepicker({
        minDate : moment(),
        defaultDate:new Date()
    });

    // Listen to change event and set min date accordingly

    $('#launch-date_add-form').on('dp.change',function (e){
        updateMinDate(e.date,'#due-date_add-form');
    });
    $('#duplicate-launch-date_add-form').on('dp.change',function (e){
        updateMinDate(e.date,'#duplicate-due-date_add-form');
    });

    //function to update due date's minDate value according to launchDate
    function updateMinDate(launchDate,elId)
    {
        $(elId).data("DateTimePicker").minDate(launchDate);
        $(elId).data('DateTimePicker').date(launchDate);
    }

    // Date time picker initialize
    $('.date-time-picker').each(function(elem) {
        $(this).datetimepicker({
            minDate : moment(),
            icons: {
                time: "fe-clock",
                date: "fe-calendar",
                up: "fe-chevron-up",
                down: "fe-chevron-down"
            }
        });
    });

    // validate add campaign form
    var campaignAddFormValidation = addCampaignForm.validate({
        errorClass: 'invalid-feedback',
        rules: {
            name: {
                required: true
            },
            'policies[]': {
                required: true
            },
            launch_date: {
                required: true
            },
            due_date: {
                required: true
            },
            timmezone: {
                required: true
            },
            'groups[]': {
                required: true
            },
            'auto_enroll_users': {
                required: true
            }
        },
        messages: {
            name: {
                required: 'The Name field is required'
            },
            'policies[]': {
                required: 'The Policies field is required'
            },
            launch_date: {
                required: 'The Launch Date field is required'
            },
            due_date: {
                required: 'The Due Date field is required'
            },
            timmezone: {
                required: 'The Timezone field is required'
            },
            'groups[]': {
                required: 'The Groups field is required'
            },
            'auto_enroll_users': {
                required: 'The Auto-enroll future group users field is required'
            }
        },
          //shown validate error from controller for add campaign
        submitHandler: function (form ,e) {
            e.preventDefault();

            let loadingBtnCampaign = Ladda.create(document.querySelector('#add-campaign-form button[type=submit]'))

            $(form).find('button[type=submit]').prop('disabled', true)

            loadingBtnCampaign.start()

            let form_data = $(form).serialize()

            $(document).find("span.text-danger").remove();

            $('.invalid-feedback').fadeOut(500);

            $.ajax({
                url : $(form).attr('action'),
                type : "POST",
                data : form_data,
                success : function(response){
                    if (response.success) {
                        $('.alert-success').fadeOut(300);
                        $('.flash').append('<div class="alert alert-success alert-block">'+
                        '<button type="button" class="btn-close" data-dismiss="alert">×</button>'+
                        '<strong>'+response.message+'</strong>'+
                        '</div>')
                        $("#add-campaign-form").trigger('reset');

                        Ladda.stopAll()

                         // shows pop up botton for add campaign button
                        const showUrl = response.url;
                        // Are you sure
                        swal({
                                title: "Campaign Scheduled!",
                                text: "This campaign has been scheduled for launch!",
                                showCancelButton: true,
                                confirmButtonColor: '#ff0000',
                                confirmButtonText: 'OK',
                                closeOnConfirm: false,
                                imageUrl: '{{asset('assets/images/success.png')}}',
                                imageWidth: 120
                        })
                        .then(confirmed => {
                            if(confirmed.value && confirmed.value == true){
                                window.location.href = showUrl;
                            }
                            else
                            {
                                 renderCampaigns()
                            }
                        });

                        $('#add-campaign-modal').modal('toggle');

                         //Set current date for add new campaign launch date after reset
                            date();

                        $('.select-picker').select2();

                    }
                },
                error:function (response){
                    $(form).find('button[type=submit]').prop('disabled', false)
                    Ladda.stopAll()
                    $.each(response.responseJSON,function(field_name,error){
                        $(document).find('[name='+field_name+']').after('<label class="invalid-feedback d-block">' +error+ '</label>')
                    })
                }
            })
        }

    });

    // Campaign searching
    $(document).on('keyup', 'input[name=campaign_name]', function() {
        renderCampaigns()
    })

    renderCampaigns()

    // Render campaign
    function renderCampaigns() {
        var campaingsWpEl = $("#campaigns-wp")

        campaingsWpEl.append(`
            <div id="content-loading" class="p-2 d-flex align-items-center">
                <div class="spinner"></div>
                <p class="text-center m-0 px-2">Loading...</p>
            </div>
        `)

        $.get( "{{ route('policy-management.campaigns.render') }}", {
            campaign_name: function() {
                return $("input[name=campaign_name]").val()
            },
            campaign_status: function() {
                return  $("input[name=campaign_status_filter]").val()
            },
        })
            .done(function(response) {

                if (response.success) {
                    campaingsWpEl.html(response.data)
                } else {
                    campaingsWpEl.html("")
                }
            })
            .fail(function() {
                alert( "error" );
            })

    }


    // CAMPAIGN DUPLICATE
        // VALIDATE CAMPAIGN DUPLICATE FORM
    // validate add campaign form
    var duplicateCampaignFormValidation = duplicateCampaignForm.validate({
        errorClass: 'invalid-feedback',
        rules: {
            name: {
                required: true
            },
            'policies[]': {
                required: true
            },
            launch_date: {
                required: true
            },
            due_date: {
                required: true
            },
            timmezone: {
                required: true
            },
            'groups[]': {
                required: true
            },
            'auto_enroll_users': {
                required: true
            }
        },
        messages: {
            name: {
                required: 'The Name field is required'
            },
            'policies[]': {
                required: 'The Policies field is required'
            },
            launch_date: {
                required: 'The Launch Date field is required'
            },
            due_date: {
                required: 'The Due Date field is required'
            },
            timmezone: {
                required: 'The Timezone field is required'
            },
            'groups[]': {
                required: 'The Groups field is required'
            },
            'auto_enroll_users': {
                required: 'The Auto-enroll future group users field is required'
            }
        },
         //shown validate error from controller for dublicate campaign
        submitHandler: function (form ,e) {
            e.preventDefault();
            let loadingBtnDublicate = Ladda.create(document.querySelector('#duplicate-campaign-form button[type=submit]'))

            $(form).find('button[type=submit]').prop('disabled', true)

            loadingBtnDublicate.start()

            let form_data = $(form).serialize()

            $(document).find("span.text-danger").remove();

            $('.invalid-feedback').fadeOut(500);

            $.ajax({
                url : $(form).attr('action'),
                type : "POST",
                data : form_data,
                success : function(response){

                    let alertClass = response.success ? 'alert-success' : 'alert-danger'

                    $(`.${alertClass}`).fadeOut(300);

                    $('.flash').append(`<div class="alert ${alertClass} alert-block">
                    <button type="button" class="btn-close" data-dismiss="alert">×</button>
                     <strong>${response.message}</strong>
                     </div>`)

                     $('.select-picker').select2();
                    $("#duplicate-campaign-form").trigger('reset');
                     //Set current date for add new campaign launch date after reset
                     date();

                Ladda.stopAll()

                $('#duplicate-campaign-modal').modal('toggle');

                    // shows pop up botton for dublicate campaign button
                    const showUrl = response.url;
                    // Are you sure
                    swal({
                        title: "Campaign Scheduled!",
                                text: "This campaign has been scheduled for launch",
                                showCancelButton: true,
                                confirmButtonColor: '#ff0000',
                                confirmButtonText: 'OK',
                                closeOnConfirm: false,
                                imageUrl: '{{asset('assets/images/success.png')}}',
                                imageWidth: 120
                    })
                    .then(confirmed => {
                        if(confirmed.value && confirmed.value == true){
                            window.location.href = showUrl;
                        }
                        else
                        {
                                renderCampaigns()
                        }
                    });
                },
                error:function (response){
                    $(form).find('button[type=submit]').prop('disabled', false)
                    Ladda.stopAll()
                    $.each(response.responseJSON,function(field_name,error){
                        $(document).find('[name='+field_name+']').after('<label class="invalid-feedback d-block">' +error+ '</label>')
                    })
                }

            })
        }
    });

    $(document).on('click', '.campaign-duplicate-btn',function(event){
        event.preventDefault()

        /** RESETTING THE FORM VALIDATION **/
        duplicateCampaignFormValidation.resetForm()

        let duplicateCampaignForm = $("#duplicate-campaign-form");
        let campaignDataURL = this.href


        $.get(campaignDataURL).then(function(res) {

            if (res.success) {
                let data = res.data

                let policies = data.policies.map(item => { return item.policy_id });
                duplicateCampaignForm.find("input[name=name]").val(`Copy of - ${data.name}`)
                duplicateCampaignForm.find("select[name='policies[]']").val([...policies]).trigger('change')
                duplicateCampaignForm.find(`select[name='auto_enroll_users']`).val(`${data.auto_enroll_users}`).trigger('change')


                $("#duplicate-campaign-modal").modal()
            }
        })
    })

    $('.select-picker').on('select2:select select2:unselect', function (e) {
        $(this).valid();
    });

    /* Resetting form on modal close */
    $('#duplicate-campaign-modal' ).on('hidden.bs.modal', function (e) {
        duplicateCampaignForm.find("select[name='policies[]']").val([]).trigger('change')
        duplicateCampaignForm.find("select[name='groups[]']").val([]).trigger('change')
        duplicateCampaignFormValidation.resetForm();
        duplicateCampaignForm.trigger('reset')


        //Set current date for add dublicate campaign launch date on reset
        date();

    })


    // Campaing delete
    $(document).on('click', '.campaign-delete-btn', function(event){
        event.preventDefault()

        let deleteCampaignURL = this.href

        // Are you sure
        swal({
                title: "Are you sure?",
                text: "You will not be able to reactivate this campaign!",
                showCancelButton: true,
                confirmButtonColor: '#ff0000',
                confirmButtonText: 'Yes, delete it!',
                closeOnConfirm: false,
                imageUrl: '{{asset('assets/images/warning.png')}}',
                imageWidth: 120
        })
        .then(confirmed => {
            if(confirmed.value && confirmed.value == true){
                window.location.href = deleteCampaignURL;
            }
        });
    })

    /** Campaign add modal **/
    $('#add-campaign-modal').on('hidden.bs.modal', function () {
        campaignAddFormValidation.resetForm();

        addCampaignForm.trigger('reset');

        $('.select-picker').select2();

         //Set current date for add new campaign launch date after reset
         date();

        renderCampaigns()
    })

})
</script>
@endsection
