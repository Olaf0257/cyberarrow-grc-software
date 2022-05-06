@extends('layouts.layout')

<!-- plugin css should be placed here -->
@section('plugins_css')
<link href="{{asset('assets/libs/switchery/switchery.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{asset('assets/libs/colorpicker/css/asColorPicker.min.css')}}">
<link href="{{asset('assets/libs/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/nestable2/jquery.nestable.min.css')}}" rel="stylesheet" />
<link href="{{asset('assets/libs/custombox/custombox.min.css')}}" rel="stylesheet" />
<link href="{{ asset('assets/libs/ladda/ladda-themeless.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/libs/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
<!-- custom plugins js -->
<link href="{{ asset('assets/custom-libs/risk-matrix/risk-matrix.css') }}" rel="stylesheet" type="text/css">
<!-- custom-css -->
<link href="{{ asset('assets/css/modules/global-settings/index.css') }}" rel="stylesheet" type="text/css">
@endsection

<!-- custom css should be placed here -->
@section('custom_css')
<style>
    .timezone-select + .select2-container{
        z-index: 1;
    }
    .asColorPicker-wrap{
        display: unset;
    }
    .asColorPicker-clear ,  .asColorPicker-alpha{
        display: none;
    }
    .colorpicker-element{
        color:black
    }
    .asColorPicker-trigger {
        border: none;
    }
    .asColorPicker-dropdown{
        z-index: 1;
    }


</style>

@endsection

@section('content')
<!-- top row -->
<div class="row">
    <div class="col-xl-12">
        <!-- top info -->

        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Administration</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Global Settings</a></li>
                    <!-- <li class="breadcrumb-item active">Tabs & Accordions</li> -->
                </ol>
            </div>
            <h4 class="page-title">View  Account Details</h4>
        </div>
    </div>
</div>
<!-- ends top row -->
@include('includes.flash-messages')

@php
    $activeTab = '';

    if( Request::old('global_settings') || Session::get('activeTab') == 'global_settings' || is_null(Session::get('activeTab')) ){
        $activeTab = 'global_settings';
    } elseif ( Request::old('mail_settings') || Session::get('activeTab') == 'mail_settings' ) {
        $activeTab = 'mail_settings';
    } elseif (Request::old('ldap_settings') || Session::get('activeTab') == 'ldap_settings'){
        $activeTab = 'ldap_settings';
    } elseif (Request::old('saml_settings') || Session::get('activeTab') == 'saml_settings'){
        $activeTab = 'saml_settings';
    } elseif (Request::old('organization_settings') || Session::get('activeTab') == 'organization_settings'){
        $activeTab = 'organization_settings';
    } elseif (Request::old('risk_settings_tab') || Session::get('activeTab') == 'risk_settings_tab'){
        $activeTab = 'risk_settings_tab';
    }
@endphp
<!-- second row -->
<div class="row bg-white">
    <div class="col-xl-12">
        <div class="tabs-menu-down">
                <ul class="nav nav-tabs nav-bordered" id="myTab" role="tablist"> <!-- ul nav nav-tabs begins -->
                    <!-- <li class="nav-item">
                        <a class="nav-link {{ ($activeTab == '') ? 'active' : ''}}" id="account-tab" data-toggle="tab" href="#account-overview" role="tab" aria-controls="home" aria-selected="true">Account Overview</a>
                    </li> -->

                    <li class="nav-item">
                        <a class="nav-link {{ ($activeTab == 'global_settings') ? 'active' : ''}}" id="profile-tab" data-toggle="tab" href="#global-setting" role="tab" aria-controls="profile" aria-selected="false">Global Settings</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ ($activeTab == 'mail_settings') ? 'active' : ''}}" id="mail-setting-tab" data-toggle="tab" href="#mail-setting" role="tab" aria-controls="profile" aria-selected="false" style="display:none">SMTP Settings</a>
                    </li>

                    <!-- <li class="nav-item">
                        <a class="nav-link ldap-link-tab {{ ($activeTab == 'ldap_settings') ? 'active' : ''}}" id="ldap-setting-tab"  data-toggle="tab" href="#ldap-settings" role="tab" aria-controls="profile" aria-selected="false">LDAP Settings</a>
                    </li> -->

                    <li class="nav-item">
                        <a class="nav-link saml-link-tab {{ ($activeTab == 'saml_settings') ? 'active' : ''}}" id="saml-setting-tab"  data-toggle="tab" href="#saml-settings" role="tab" aria-controls="profile" aria-selected="false">SAML</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link saml-link-tab {{ ($activeTab == 'organization_settings') ? 'active' : ''}}" id="organization-settings-tab"  data-toggle="tab" href="#organization-tab" role="tab" aria-controls="organization" aria-selected="false">Organizations</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link saml-link-tab {{ ($activeTab == 'risk_settings_tab') ? 'active' : ''}}" id="risk-score-settings-tab-nav"  data-toggle="tab" href="#risk-score-settings-tab" role="tab" aria-controls="" aria-selected="false">
                         Risk Settings
                        </a>
                    </li>
                </ul> <!-- ul nav nav-tabs ends -->

                <!-- tab-content begins -->
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade  {{ ($activeTab == '') ? 'show active' : ''}}" id="account-overview" role="tabpanel" aria-labelldedby="home-tab">
                        <!-- table begins -->
                        <!-- This is for Account Overview -->
                        <table class="table table-hover mb-0">
                                
                        
                                <tbody>
                                    <tr>
                                        <td>Name</td>
                                        <td>{{$licensedTo}}</td>
                                    </tr>

                                    <tr>
                                        <td>License Expiration</td>
                                        <td class="pb-3">{{$licenseExpiryDate}}</td>    
                                    </tr>

                                    <tr>
                                        <td>Version</td>
                                        <td class="pb-3">{{$licenseCurrentVersion}}</td>  
                                    </tr>
                                    <tr id="check-for-update-tr">
                                        <td>
                                       
                                        </td>
                                        <td>
                                            <button class="btn btn-primary checkUpdate" id="checkUpdateBtn"  data-style="expand-right">
                                                <span class="ladda-label">
                                                    Check for updates
                                                </span>
                                                <span class="ladda-spinner">
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="d-none" id="download-btn-tr">
                                        <td>
                                          
                                        </td>
                                        <td>
                                            <button class="btn btn-primary downloadUpdate" id="downloadBtn" data-update-id=""  data-style="expand-right">
                                                <span class="ladda-label">
                                                  Download and install 
                                                </span>
                                               
                                                <span class="ladda-spinner">
                                            </button>
                                        </td>
                                    </tr>
                                    
                                     <tr  class="d-none" id="updateMessageTr">
                                        <td>
                                           
                                        </td>
                                        <td id="updateMessage">
                                            
                                            
                                        </td>
                                    </tr>
                                </tbody>

                        </table> <!-- ends table here -->
                    </div> <!-- tab-pane ends -->

                    <div class="modal fade bs-example-modal-center" id="messageLoader" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateInstallModal">
                                        <span class="">Downloading and installing new updates</span></h5>
                                    <div id="progresDiv"></div>    
                                </div>
                                <div class="modal-body">
                                    <div id="progressMessage">
                                        
                                    </div>

                                    <div id="downloadSuccesBtn" class="text-center mt-4" style="display: none;">
                                        <button class="btn btn-primary downloadSuccess" id="downloadCompleteBtn" data-update-id=""  data-style="expand-right">
                                            <span class="ladda-label">
                                           
                                            </span>
                                            <span class="ladda-spinner">
                                        </button>
                                    </div>

                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <!-- Account settings -->
                    @include('administration.global-settings.app-settings.index')

                    <!-- Account settings ends-->

                    <!-- mail setting -->

                    @include('administration.global-settings.mail-settings')

                    <!-- tab-pane for mail settings ends -->

                    <!-- ldap settings -->

                    @include('administration.global-settings.ldap-settings')

                    <!-- ldap settings end -->

                    <!-- saml settings -->
                    @include('administration.global-settings.saml-settings')
                    <!-- saml settings end -->

                    <!-- departments settings -->
                    @include('administration.global-settings.organization-settings.index')

                    <!-- risk score settings -->
                    @include('administration.global-settings.risk-score-settings.index')
                </div><!-- tab-content ends -->

        </div> <!-- ends tabs-menu-down -->
    </div> <!-- ends col -->
</div> <!-- second row ends -->
<br><br><br>
@endsection

<!-- all plugins js file should be here -->
@section('before-bootstrap-bundle')
<script src="{{ asset('assets/libs/jquery-ui/jquery-ui.min.js') }}"></script>
@endsection

@section('plugins_js')
<script src="{{ asset('assets/libs/colorpicker/js/jquery-asColor.js') }}"></script>
<script src="{{ asset('assets/libs/colorpicker/js/jquery-asColorPicker.min.js') }}"></script>
<script src="{{ asset('assets/libs/custombox/custombox.min.js') }}"></script>
<script src="{{ asset('assets/libs/dropify/dropify.min.js') }}"></script>
<script src="{{ asset('assets/libs/switchery/switchery.min.js') }}"></script>
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-fileuploads.init.js') }}"></script>
<script src="{{ asset('assets/libs/ladda/spin.js') }}"></script>
<script src="{{ asset('assets/libs/ladda/ladda.js') }}"></script>

<!-- Plugins js-->
<script src="{{ asset('assets/libs/nestable2/jquery.nestable.min.js') }}"></script>
<script src="{{ asset('assets/libs/underscore-js/underscore-umd-min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>

<!-- custom plugins js -->
<script src="{{ asset('assets/custom-libs/risk-matrix/risk-matrix.js') }}"></script>
@endsection

<!-- custon scripts are here -->
@section('custom_js')
<script nonce="{{ csp_nonce() }}">
$(document).ready(function(){


    /* Risk settings js */

    /* Risk matrix */

    const DynamicRiskScoreMatrix = new RiskMatrix({
        matixConfig: {
            containerEl: document.getElementById("risk-matrix-container"),
            el: document.getElementById('risk-matrix'),
            data: {
                likelihoods: @json($riskMatrixLikelihoods),
                impacts: @json($riskMatrixImpacts),
                riskScores: @json($riskMatrixScores),
                riskPickerScores: @json($riskPickerScores)
            }
        },
        levelSliderConfig: {
            containerEl: document.getElementById('risk-level-slider-container'),
            el: document.getElementById('risk-matrix-levels'),
            levelsSwitcherEl: document.getElementById('risk-levels-switcher'),
            data: {
                levelTypes: @json($riskScoreLevelTypes),
            }
        },
        acceptableRiskScorePickerEl: document.getElementById('acceptable-risk-score-picker')
    })


    /* initializing risk matrix*/
    DynamicRiskScoreMatrix.init();

    /* validation rule for update-risk-matrix-form form*/
    $("#update-risk-matrix-form").validate({
        errorClass: 'invalid-feedback',
        rules: {
            risk_acceptable_score: {
                required: true,
            }
        },
        messages: {
            risk_acceptable_score: {
                required: 'The risk acceptable score field is required',
            },
        }
    });
    /* validation rule for add department */
    let addDeparmentFormValidator = $('#add-department-form').validate({
        errorClass: 'invalid-feedback'
    });

    $(document).on('click', '#save-updated-risk-matrix-btn', function(event) {
        event.preventDefault();
        let isValidForm = $("#update-risk-matrix-form").valid()

        /* Return when invalid*/
        if(!isValidForm){

            $("#acceptable-risk-score-picker")[0].scrollIntoView({
                behavior: "smooth", // or "auto" or "instant"
                block: "end" // or "end"
            });

            return

        }

        $("#confirmUpdateRiskMatrixModal").modal('toggle')

    })


   
    function updateProgress() {

        $("#progresDiv").html(
        `<div class="progress mb-2 progress-md">
            <div class="progress-bar bg-success" role="progressbar" width="50%" id="prog" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>`);
    }
    
    /* Handling risk matrix save event*/
    $(document).on("click", "#save-updated-risk-matrix-confirm-btn", function(event) {
        event.preventDefault();
        const updateRiskMatrixForm = $("#update-risk-matrix-form")
        const riskMatrixData = JSON.stringify(DynamicRiskScoreMatrix.getRiskMatrixData())
        updateRiskMatrixForm.find(`textarea[name="risk_matrix_data"]`).val(_.unescape(riskMatrixData));
        updateRiskMatrixForm.submit();
    })

    /* handing update risk marixk */

    /* Handling risk matrix reset to default action */
    $(document).on("click", "#reset-risk-matrix-to-default", function(event) {
        event.preventDefault();
        const resetRiskMatrixToDefaultForm = $("#reset-risk-matrix-to-default-form")

        resetRiskMatrixToDefaultForm.submit()
    });

      
    $(document).on("click", ".downloadUpdate", function(event) {

       event.preventDefault();

       let downloadBtn = Ladda.create( document.querySelector( '#downloadBtn' ) );
       document.body.style.pointerEvents = 'none';
       downloadBtn.start();
        $(".ladda-label").html('The file is being downloaded.Do not refresh this page!');

       var updateId = $(this).data('update-id');
    //    alert(id);
        

        // Previously Used Ajax Call
        // $("#prog").removeClass('d-none');
       
        // $.ajax(
        //     {
        //         "url": "{{route('license.download.update')}}",
        //         "type": "POST",
        //         "data":

        //         {
        //             "_token": "{{ csrf_token() }}",
        //             "id" : updateId,
        //         },
        //         success:function(response)
        //         {
        //             updateProgress();
        //             downloadBtn.stop();
                
                    
        //             Swal.fire({
        //                 title: "Update Successfull",
        //                 imageUrl: '{{ asset('assets/images/success.png') }}',
        //                 imageWidth: 120,
        //                 confirmButtonColor: '#b2dd4c'
        //             }).then(()=> {
        //             $("#updateMessageTr").append(response);
        //             $("#check-for-update-tr").show();
        //             $("#download-btn-tr").hide();
                
        //             $(".ladda-label").html('Check For Updates');
        //             console.log(response);
        //                 // location.reload();
        //             })

        //         }
                    
        //     }
        // )

    var xhr = new XMLHttpRequest();     
   
     
 xhr.addEventListener("load",() => {
       
        document.body.style.pointerEvents = 'auto';
                    //  var msg = xhr.responseText.split('\n');
                    // $("#progressMessage").hide();
                    $("#updateMessageTr").hide();
                    $("#check-for-update-tr").hide();
                    $("#download-btn-tr").hide();
                    $("#downloadSuccesBtn").show();        
                    $(".ladda-label").html('Finish');
                    $("#downloadSuccesBtn").show();            
                    $("#downloadCompleteBtn").click(() => {
                        window.location.href = "{{ route('compliance-dashboard') }}";
                    })
                        

      });
      
    xhr.addEventListener("progress", function(evt) {
        
        $("#messageLoader").modal({backdrop: 'static', keyboard: false})  
        $("#messageLoader").modal('show');
        
        var lines = evt.currentTarget.response.split("\n");
        
        if(lines.length)    
        {
           var progress = lines[lines.length-1];
        //    console.log(progress);
            var progressPer = 0;   
        }else{
            var progress = 0;
        }
       
       $("#updateMessage").hide();

      updateProgress();
    
         $("#progressMessage").html(progress);
       
        // $("#prog").innerHtml = lines;\

        
    }, false);
    xhr.open('POST', "{{route('license.download.update')}}", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(`_token={{csrf_token()
    }}&id=${updateId}`);        
    });
    
    $(document).on("click", ".checkUpdate", function(event) {
       
       event.preventDefault();

       let checkForUpdateBtn = Ladda.create( document.querySelector('#checkUpdateBtn') );

       checkForUpdateBtn.start();

        $.get(
            {
                "url": "{{route('license.check.update')}}",
                "type": "GET",
                "data":
                {
                    "_token": "{{ csrf_token() }}",
                }
        }).done(function(response) {
                
                checkForUpdateBtn.stop();
                
                $("#updateMessageTr").removeClass('d-none');

                if(response.status)
                {
                    $("#check-for-update-tr").hide(); 
                    $("#download-btn-tr").removeClass('d-none');
                    $("#download-btn-tr").show();
                    $("#downloadBtn").attr("data-update-id", response.update_id);
                  
                    $("#updateMessage").html(
                       `<div>
                            <p>${response.message}</p>
                            <p class="font-bold">Version: ${response.version}</p>
                            <p class="font-bold">Released Date: ${response.release_date}</p>
                            <p>${response.changelog}</p>
                            ${response.summary ?? '' } 
                        </div>`
                    );
                         
                }else{
                $("#check-for-update-tr").show();
                $("#updateMessage").html(
                    `${response.message}`                   
                );
                }
                
               
             
        })

    });


    


/** END OF RISK SETTING SCRIPT **/

    $(document).find(".swal2-select").select2();

    @include('administration.global-settings.organization-settings.delete-department-script')
    //initialize timezone selecct2
    $('.timezone-select').select2();

    //plugins initialize js
    let secondaryColor = '{{ $globalSetting->secondary_color }}'
    var first_time = true;

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

    if (first_time){

        var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));


        elems.forEach(function(html) {
            var switchery = new Switchery(html, {color: secondaryColor });
        });

        $(".colorpicker-element").asColorPicker({
            mode: 'complex',
            color: {
                format: 'HEX',
            },
            onApply: function(val){
                var hex_input_value=this.$dropdown.find('.asColorPicker-hex').val();
                if(hex_input_value!==val.toHEX()){
                    if(is_hex_color(hex_input_value)){
                        this._set(hex_input_value);
                    }
                    else{
                        this._set(val.toHEX());
                    }
                }
                else{
                    this._set(val.toHEX());
                }
            }
        });
        $('.asColorPicker-clear').remove();

    }

    first_time = false
  })
  //** check if valid hex color */
  function is_hex_color(clr){
    var RegExp = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i;
    return RegExp.test(clr);
  }

  $('a[data-toggle="tab"]').trigger('shown.bs.tab');//plugins initialize js

  $("#allow_document_upload").on('change', function(el){
    let targetInput = $("input[name=allow_document_upload]");

    if(this.checked){
        targetInput.val(1)
    } else {
        targetInput.val(0)
    }
  })

  $("#allow_document_link").on('change', function(el){
    let targetInput = $("input[name=allow_document_link]");

    if(this.checked){
        targetInput.val(1)
    } else {
        targetInput.val(0)
    }
  })

  $("#enable_support").on('change', function(el){
    let targetInput = $("input[name=enable_support]");

    if(this.checked){
        targetInput.val(1)
    } else {
        targetInput.val(0)
    }
  })


  // SAML SETTING JS
    $(document).on('click', '#upload-saml-metadata-btn', function(){
        $('#saml-provider-metadata').trigger('click');
    });

    $(document).on('change', '#saml-provider-metadata', function() {
        if (this.files.length > 0) {
            $("form[name=saml-provider-metadata-upload-form]").submit();
        }
    })


    $(document).on('mouseover', '.copy-input-option', function(){
        /* Update tooltip text */
        $(this).tooltip('hide')
          .attr('data-original-title', 'Copy to clipboard')
            .tooltip('show');
    });

    $(document).on('click', '.copy-input-option', function(){
       /* Get the text field */
        var inputToCopy = this.previousSibling.previousSibling;

        /* Select the text field */
        inputToCopy.select();
        inputToCopy.setSelectionRange(0, 99999); /*For mobile devices*/

        /* Copy the text inside the text field */
        document.execCommand("copy");

        /* Update tooltip text */
        $(this).tooltip('hide')
          .attr('data-original-title', 'Copied')
            .tooltip('show');
    })


    /* ORGANIZATION SETTINGS JS*/

    function refreshOrganizationTab() {
        /* Refresh Organization tab*/
        $("#organization-tab").load(document.URL +  ' #organization-tab > *', function () {
            initDepartmentNestableList()
        });
    }

    function initDepartmentNestableList() {
        $('.dd').nestable('destroy');
        $('.dd').nestable({maxDepth: 50}).nestable('expandAll');

    }

    $(document).on('change', '.dd', function(e) {
        /* on change event */
        let data =  $(this).nestable('serialize');
        let organizationId = $(this).data('organization-id')

        let output = $(document).find(`#nestable-output_${organizationId}`)

        if (window.JSON) {
            output.val(window.JSON.stringify(data));//, null, 2));
        } else {
            output.val('JSON browser support required for this demo.');
        }
    });

    /* Department nestable list initialization*/
    initDepartmentNestableList()

    function getDepartments(URL) {
        return $.get(URL)
    }

    function setDepartmentSelectInputOptions(departments, selectedDepartment = null) {
        /*Removing select options to avoid dubplicate*/
        $("#add-department-form").find('option:not(:first)').remove();

        for (const key in departments) {
            if (Object.hasOwnProperty.call(departments, key)) {
                const department = departments[key];

                let option;

                if (selectedDepartment && selectedDepartment == department.id ) {
                    option = `<option selected value="${department.id}"> ${department.name} </option>`
                } else {
                    option =   `<option value="${department.id}"> ${department.name} </option>`
                }

                $("#add-department-form select").append(option);
            }
        }
    }

    /* Add organiztion*/

    $("#add-organization-modal form").validate({
        errorClass: 'invalid-feedback',
        rules: {
            name:{
                required :true,
                maxlength: 190,
                // Using the normalizer to trim the value of the element
                // before validating it.
                normalizer: function(value) {
                    return $.trim(value);
                },
            }
        },
        messages: {
            name: {
                required: 'The Name field is required',
                maxlength: 'The Name may not be greater than 190 characters'
            },
        },
        submitHandler: function(form) {
            // do other things for a valid form
            form.submit();
        }
    });

    /* Edit organiztion*/
    $(document).on('click', '.edit-organizations-action', async function (event) {
        event.preventDefault();
        const formAction = this.href
        const organizationName = $(this).data('organization-name')
        const editOrganizationModel = $("#edit-organization-modal")
        const editOrganizationForm = editOrganizationModel.find('form')[0]
        const organizationEditInput = $(editOrganizationForm).find("input[name='name']")

        /* Setting form action*/
        $(editOrganizationForm).attr('action', formAction)

        /* Giving input val */
        organizationEditInput.val(_.unescape(organizationName));

        editOrganizationModel.modal('toggle');
    });

    $("#edit-organization-modal form").validate({
        errorClass: 'invalid-feedback',
        rules: {
            name:{
                required :true,
                maxlength: 190,
                // Using the normalizer to trim the value of the element
                // before validating it.
                normalizer: function(value) {
                    return $.trim(value);
                },
            }
        },
        messages: {
            name: {
                required: 'The Name field is required',
                maxlength: 'The Name may not be greater than 190 characters'
            },
        },
        submitHandler: function(form) {
            // do other things for a valid form
            form.submit();
        }
    });

    $(document).on('click', '.add-department-link', async function (event) {
        event.preventDefault();

        let formAction = this.href
        let getDepartmentsLink = $(this).data('get-departments-link')
        let departmentId = $(this).data('department-id')


        //remove disable property for parent department
        $('.parent-department').prop('disabled', false);

        // Adding action attribute for create department form
        $("#add-department-form").attr('action', formAction);


        // Getting departments of a organization
        try {
            let res = await getDepartments(getDepartmentsLink)

            if (res.success) {
                let resData = res.data


                setDepartmentSelectInputOptions(resData, departmentId)

                // Department add modal hide/show
                $('#add-department-modal').modal('toggle')


                $('#add-department-form select').select2()
            }

        } catch (error) {

        }
    });


    /* EDIT DEPARTMENT*/
    $(document).on('click', '.edit-department-action', async function (event) {
        event.preventDefault();

        let formAction = this.href
        let editURL = $(this).data('edit-url')
        let getDepartmentsLink = $(this).data('get-departments-link')
        let parentId = $(this).data('parent-id')

        //add disable property for parent department on edit
        $('.parent-department').prop('disabled', true);

        // Adding action attribute for create department form
        $("#add-department-form").attr('action', formAction);

        /* setting edit department info*/
        $.get(editURL).done(function(res) {
            if (res.success) {
                let data = res.data

                $("#add-department-form input[name=name]").val(_.unescape(data.name));

            }
        })

        try {

            let res = await getDepartments(getDepartmentsLink)

            if (res.success) {
                let resData = res.data


                setDepartmentSelectInputOptions(resData, parentId)

                 // Department add modal hide/show
                $('#add-department-modal').modal('toggle')


                $('#add-department-form select').select2()
            }

        } catch (error) {

        }

    })

    // resetting add-department-form form select box
    $(document).on('hidden.bs.modal', '#add-department-modal',function (e) {
        $(".parent-department").val(null).trigger('change');
        $("#add-department-form").find('input').val('');
    })


    // Department create form submit handle
    $("#add-department-form").on('submit',async function (event) {
        if(!event.target.checkValidity()){
            return false;
        }
        event.preventDefault();

        let departmentAddFormSubmitBtn = Ladda.create( document.querySelector( '#add-department-form button[type=submit]' ) );
		departmentAddFormSubmitBtn.start();

        let formAction = this.action

        try {

           let res = await  $.post( formAction,{
                "_token": "{{ csrf_token() }}",
                "name": $(this).find("input[name=name]").val(),
                "parent_id": $(this).find("select[name=parent_id]").val(),
            });



            /* stops button loader */
            departmentAddFormSubmitBtn.stop()

            if (!res.success) {
                addDeparmentFormValidator.showErrors({'name':res.error.name[0]});
            }else {
                /* In case of success */
                Swal.fire({
                    title: res.message,
                    imageUrl: '{{ asset('assets/images/success.png') }}',
                    imageWidth: 120,
                    confirmButtonColor: '#b2dd4c'
                }).then(()=> {
                    $('#add-department-modal').modal('hide')

                    /* Refresh Organization tab*/
                    refreshOrganizationTab()
                })
            }


        } catch (error) {
            /* stops button loader */
            departmentAddFormSubmitBtn.stop()


            Swal.fire({
                type: 'error',
                text: 'Oops something went wrong!'
            }).then(()=> {
                $('#add-department-modal').modal('hide')

                /* Refresh Organization tab*/
                refreshOrganizationTab()
            })
        }

    })
}) // END OF DOCUMENT READY
</script>

@endsection
