@extends('layouts.layout')

@php $pageTitle = "Policies - Policy Management"; @endphp

@section('title', $pageTitle)

<!-- plugin css should be placed here -->
@section('plugins_css')
@include('includes.assets-libs.datatable-css-libs')
<link href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('custom_css')
<style>
    #create-link-policies-form table tr td{
        position: relative;
        padding-left: 5px;
        padding-bottom: 34px;
        border: none;
        padding-right: 5px;
        padding-top: 19px;
    }

    #create-link-policies-modal .absolute-error-form label.invalid-feedback{
        bottom: -10px !important;
        font-size: 11px;
        width: 95%;
    }

    #create-link-policies-form table tr:first-child td{
        padding-top: 50px;
    }

    #create-link-policies-form .modal-body .table-responsive{
        overflow: auto;
    }
    #policy-upload-preview-container .card{
         background-color: #dfdfdf33;
    }
    @media screen and (max-width: 768px) {
        .absolute-error-form label.invalid-feedback{
            position: relative !important;
        }

        .absolute-error-form label.invalid-feedback{
            font-size: 10px;
        }

        td{
            vertical-align: top !important;
        }
    }

@media (min-width: 769px) and (max-width: 991.98px) {
    #create-link-policies-modal .absolute-error-form label.invalid-feedback {
    margin-bottom: -25px;
    line-height: 12px;
}
}

@media (max-width: 768px) {
    #create-link-policies-modal .absolute-error-form label.invalid-feedback {
    margin-bottom: -15px;
}
}


</style>
@endsection


@section('content')
<style>
</style>
<!-- breadcrumbs -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('policy-management.campaigns') }}">Policy Management</a></li>
                    <li class="breadcrumb-item"><a href="#">Policies</a></li>
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
            <div class="card-body">
                <!-- link policies -->

                <a href="#" type="button" data-toggle="modal" data-target="#create-link-policies-modal" class="btn btn-sm btn-primary waves-effect waves-light ms-sm-2 mb-2 mb-sm-0 float-sm-end d-block d-sm-inline-block">
                    <i class="mdi mdi-plus-circle" title="Link Policies"></i> Link Policies
                </a>
                <!-- uploads policies -->
                <a href="#" type="button" data-toggle="modal" data-target="#upload-policies-modal" class="btn btn-sm btn-primary waves-effect waves-light mb-2 mb-sm-0 float-sm-end d-block d-sm-inline-block">
                    <i class="mdi mdi-plus-circle" title="Upload Policies"></i> Upload Policies
                </a>
                <h4 class="header-title my-3 my-sm-0 mb-sm-4">Manage Policies</h4>
                <table class="table table-centered display table-hover w-100" id="policies-datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Version</th>
                            <th>Created At</th>
                            <th>Updated At</th>
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

<!-- create link policies modal -->
<div id="create-link-policies-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="create-link-policies-form" action="{{ route('policy-management.policies.store-link-policies') }}" class="absolute-error-form" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Link Policies</h4>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Display Name</th>
                                    <th>Link</th>
                                    <th>Version</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" name="display_name[]">
                                    </td>
                                    <td>
                                        <input type="url" class="form-control" name="link[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="version[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="description[]">
                                    </td>
                                </tr>

                                <tr>
                                    <td scope="row">
                                        <input type="text" class="form-control" name="display_name[]">
                                    </td>
                                    <td>
                                        <input type="url" class="form-control" name="link[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="version[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="description[]">
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input type="text" class="form-control" name="display_name[]">
                                    </td>
                                    <td>
                                        <input type="url" class="form-control" name="link[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="version[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="description[]">
                                    </td>
                                </tr>

                                <tr>
                                    <td scope="row">
                                        <input type="text" class="form-control" name="display_name[]">
                                        <div class="invalid-feedback d-block">
                                            @if ($errors->has('display_name'))
                                            {{ $errors->first('display_name') }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <input type="url" class="form-control" name="link[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="version[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="description[]">
                                    </td>
                                </tr>

                                <tr>
                                    <td scope="row">
                                        <input type="text" class="form-control" name="display_name[]">
                                    </td>
                                    <td>
                                        <input type="url" class="form-control" name="link[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="version[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="description[]">
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.modal -->

<!-- upload policies modal -->
<div id="upload-policies-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="upload-policies-form" class="absolute-error-form" action="{{ route('policy-management.policies.upload-policies') }}" method="POST" enctype=multipart/form-data>
            @csrf
                <div class="modal-header">
                        <h4 class="modal-title">New Policies</h4>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body p-4">
                    <div class="dropzone clsbox mb-3" id="policyUploadDropzone">
                        <div class="fallback">
                            <input name="file" type="file" multiple />
                        </div>

                        <div class="dz-message needsclick">
                            <i class="h1 text-muted dripicons-cloud-upload"></i>
                            <h3>Drag and drop files or click</h3>
                            <span class="text-muted font-13">(Selected files are <strong>shown</strong> below.)</span>
                        </div>
                    </div>

                    <!-- Dropzone file upload errror -->
                    <div class="dz-file-upload-error">
                    </div>

                    <!-- file validation types-->
                    <div class="file-validation-limit">
                        <div class="mb-3">
                            <p><span>Accepted File Types:</span></p>
                            <p>
                                .png, .jpg, .jpeg, .gif, .pdf
                            </p>
                        </div>

                        <p><span>Maximum File Size: </span>10MB</p>
                        <p><span>Maximum Character Length:</span> 250</p>
                    </div>
                    <!-- dropzone preview -->
                    <div class="table table-striped" class="files" id="policy-upload-preview-container">
                        <div id="policy-upload-preview-template" class="card border p-3">
                            <!-- This is used as the file preview template -->
                            <div class="row">
                                <div class="col-11">
                                    <p class="name mb-1" data-dz-name></p>
                                    <strong class="error text-danger"></strong>
                                </div>
                                <!--/.col-->
                                <div class="col-1">
                                    <button type="button" data-dz-remove class="btn-close">×</button>
                                </div>
                                <!--/.col -->
                            </div>
                            <!--/.row-->
                            <div>
                                <p class="size" data-dz-size></p>
                                    <!-- file update error and success message -->
                                    <div>
                                        <div class="dz-error-message">
                                        </div>
                                        <div class="dz-file-success-message">
                                        </div>
                                    </div>
                                <div class="progress progress-striped active my-2" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                    <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6 mb-3">
                                    <input type="text" name="display_name[]" placeholder="Name" value="" class="form-control">
                                </div>
                                <div class="col-6 mb-3">
                                    <input type="text" name="version[]" placeholder="Version" class="form-control">
                                </div>
                                <div class="col-12">
                                    <textarea name="description[]" class="form-control" placeholder="Description" cols="3" rows="2"></textarea>
                                </div>
                            </div>
                            <!--/.row-->
                        </div>
                    </div>
                </div>
                <!--modal body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" id="clear-completed-uploads">Clear</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div><!-- /.modal -->
<!-- upload  policies modal -->


<!-- update policies modal -->
<div id="update-policies-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="update-policies-form" method="POST" class="absolute-error-form" enctype=multipart/form-data> @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Edit Policies</h4>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body p-4">
                    <!-- document policy -->
                    <div id="document-policy-update-section">
                        <input type="file" name="policy_file" class="dropify" data-max-file-size="10M" data-allowed-file-extensions='["png","jpg", "jpeg","gif","pdf"]' data-default-file="" data-height="300" />
                    </div>
                    <!-- file validation types-->
                    <div class="file-validation-limit mt-3">
                        <div class="mb-3">
                            <p><span>Accepted File Types:</span></p>
                            <p>
                            .png, .jpg, .jpeg, .gif, .pdf
                            </p>
                        </div>
                        <p><span>Maximum File Size: </span>10MB</p>
                        <p><span>Maximum Character Length:</span> 250</p>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6 mb-3">
                            <label for="display-name" class="form-label">Display Name</label>
                            <input type="text" id="display-name" name="display_name" placeholder="Display Name" value="" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="version" class="form-label">Version</label>
                            <input type="text" name="version" placeholder="Version" class="form-control">
                        </div>
                        <div class="col-12 mb-3" id="link-input-section">
                            <label for="link" class="form-label">URL</label>
                            <input type="url" id="link" class="form-control" placeholder="URL" name="link">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="link" class=" form-label">Description</label>
                            <textarea name="description" class="form-control" placeholder="Description" cols="3" rows="2"></textarea>
                        </div>
                    </div>
                    <!--/.row-->
                </div>
                <!--modal body -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.modal -->
@endsection

<!-- all plugins js file should be here -->
@section('plugins_js')
@include('includes.assets-libs.datatable-js-libs')
<script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<script src="{{ asset('assets/libs/dropify/dropify.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-fileuploads.init.js') }}"></script>
<script src="{{ asset('assets/libs/underscore-js/underscore-umd-min.js') }}"></script>
@endsection

@section('custom_js')
<script nonce="{{ csp_nonce() }}">
    Dropzone.autoDiscover = false;

    $(document).ready(function() {

        $.validator.prototype.errorsFor = function(element) {
            var name = this.idOrName(element);
            var elementParent = element.parentElement;
            return this.errors().filter(function() {
                return $(this).attr('for') == name && $(this).parent().is(elementParent);
            });
        }

        // Dropzone policies upload
        var previewNode = document.querySelector("#policy-upload-preview-template");
        previewNode.id = "";
        var previewTemplate = previewNode.parentNode.innerHTML;
        previewNode.parentNode.removeChild(previewNode);

        var policyUploadDropzone = new Dropzone('div#policyUploadDropzone', {
            url: "{{ route('policy-management.policies.upload-policies') }}",
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            paramName: 'policy_files',
            autoProcessQueue: false,
            uploadMultiple: false,
            parallelUploads: 10,
            previewsContainer: "#policy-upload-preview-container",
            previewTemplate: previewTemplate,
            acceptedFiles: ' .png, .jpg, .jpeg, .gif, .pdf',
            init: function() {
                this.on("addedfile", function(file, xhr, formData) {

                    $("#upload-policies-form").find('.dz-file-upload-error').html(``);

                    let validFileNameLength = file.name.length >= 250 ? false : true
                    let validFileSize = file.size > 10*1024*1024 ? false : true


                    if(!validFileNameLength) {
                        $("#upload-policies-form").find('.dz-file-upload-error').html(`<strong class="error text-danger">File name exceeds maximum character length limit</strong>`);
                    }

                    // FILE SIZE VALIDATION ERROR MESSAGE
                    if (!validFileSize) {
                        $("#upload-policies-form").find('.dz-file-upload-error').html(`<strong class="error text-danger">File size exceeds maximum file size limit</strong>`);
                    }

                    // REMOVING THE FILE VALIDATION ERROR OCCURES
                    if (!validFileNameLength || !validFileSize) {
                        // Remove current file
                        this.removeFile(file);

                        return false;
                    }


                    if (validFileNameLength && validFileSize) {
                        // showing newly added file at the top of preview container
                        $('#policy-upload-preview-container').prepend($(file.previewElement));

                        $(file.previewElement).find("input[name='display_name[]']").val(file.name.split('.').slice(0, -1).join('.'))
                    }

                });

                this.on('error', function(file, response) {
                    if (typeof response === 'object') {
                        // Making inputs readonly
                        $(file.previewElement).find("input").prop("readonly", true)
                        $(file.previewElement).find("textarea").prop("readonly", true)

                        let message = response.message;
                        if(response.errors && Object.keys(response.errors).length > 0){
                            message = response.errors[Object.keys(response.errors)[0]][0];
                        }
                        $(file.previewElement).find('.dz-error-message').html(`<div class="alert alert-danger my-1 py-1 px-1" role="alert"><strong class="error text-danger"> ${message} </strong></div>`);

                        $(file.previewElement).find('.progress-bar-success').addClass('bg-danger')
                    } else {
                        $("#upload-policies-form").find('.dz-file-upload-error').html(`<strong class="error text-danger"> ${response} </strong>`);

                        // i remove current file
                        this.removeFile(file);
                    }
                });
            }
        });

        //
        policyUploadDropzone.on("sending", function(file, xhr, formData) {
            let displayName = $(file.previewElement).find("input[name='display_name[]']").val()
            let version = $(file.previewElement).find("input[name='version[]']").val()
            let description = $(file.previewElement).find("textarea[name='description[]']").val()
            formData.append('_token', "{{ csrf_token() }}")
            formData.append('display_name', displayName)
            formData.append('version', version)
            formData.append('description', description)
            formData.append('policy_file', file)
        });

        var uploadPoliciesFormSubmit = $("form#upload-policies-form button[type=submit]");

        uploadPoliciesFormSubmit.on("click", function(e) {
            e.preventDefault();

            if ($("#upload-policies-form").valid()) {
                policyUploadDropzone.processQueue();
            }
        });

        // backend response
        policyUploadDropzone.on("success", function(file, response) {

            // Making inputs readonly
            $(file.previewElement).find("input").prop("readonly", true)
            $(file.previewElement).find("textarea").prop("readonly", true)

            if (response.success) {
                 $(file.previewElement).find(".dz-file-success-message").html(`<div class="alert alert-success my-1 py-1 px-1" role="alert"><strong class="text-success">Policy uploaded successfully. </strong></div>`)
                // this.removeAllFiles();
                policyDatatable.ajax.reload();
            }

            // handling exception
            if(response.exception){
                $(file.previewElement).find('.dz-error-message').html(`<div class="alert alert-danger my-1 py-1 px-1" role="alert"><strong class="error text-danger"> ${response.exception} </strong></div>`);

                $(file.previewElement).find('.progress-bar-success').addClass('bg-danger')
            }
        });

        // Remove completed uploads
        $(document).on('click', '#clear-completed-uploads', function() {
            policyUploadDropzone.removeAllFiles(true);
        });

        $('#upload-policies-modal').on('hidden.bs.modal', function (){
            policyUploadDropzone.removeAllFiles();
        });

        // validate policies form
        $("#upload-policies-form").validate({
            errorClass: 'invalid-feedback',
            rules: {
                'display_name[]': {
                    required: true,
                },
                'version[]': {
                    required: true
                },
                'description[]': {
                    required: true
                }
            },
            messages: {
                'display_name[]': {
                    required: 'The Display Name field is required'
                },
                'version[]': {
                    required: 'The Version field is required'
                },
                'description[]': {
                    required: 'The Description field is required',
                }
            },
            ignore: []
        });

        function isValidHttpUrl(value) {
            let isURL = /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z0-9\u00a1-\uffff][a-z0-9\u00a1-\uffff_-]{0,62})?[a-z0-9\u00a1-\uffff]\.)+(?:[a-z\u00a1-\uffff]{2,}\.?))(?::\d{2,5})?(?:[/?#]\S*)?$/i.test( value );

            let isNetworkShareFolderLink = /^(\\)(\\[\w\.\s\-_]+){2,}(\\?)$/.test(value)

            return isURL || isNetworkShareFolderLink;
        }

        // create-link-policies-form validation
        $("#create-link-policies-form button[type=submit]").on('click', function(e) {
            e.preventDefault();
            var form = $("#create-link-policies-form");
            var rowsToValidate = 0;
            var validRows = 0


            var tableRows = form.find('table tbody tr')

            tableRows.each(function() {
                let tr = this

                // Removing previous error logs
                $(tr).find(".row-input-error").remove()


                let displayName = $(tr).find("input[name='display_name[]']")
                let link = $(tr).find("input[name='link[]']")
                let version = $(tr).find("input[name='version[]']")
                let description = $(tr).find("input[name='description[]']")

                if (displayName.val() || link.val() || version.val() || description.val()) {
                    rowsToValidate += 1

                    // calculating valid rows
                    if (!displayName.val() || !link.val() || !version.val() || !description.val()) {

                        // this should be after click triggered
                        if (!displayName.val()) {
                            let errorMessage = `<label class="row-input-error invalid-feedback">The Display Name field is required</label>`

                            $(displayName).after(errorMessage)
                        }

                        if (!link.val()) {
                            let errorMessage = `<label class="row-input-error invalid-feedback">The Link field is required</label>`

                            $(link).after(errorMessage)
                        }

                        // else {

                        //     if (!isValidHttpUrl( link.val() )) {
                        //         let errorMessage = `<label class="row-input-error invalid-feedback">Please enter a valid URL or a valid shared/network folder.</label>`

                        //         $(link).after(errorMessage)
                        //     }

                        // }

                        if (!version.val()) {
                            let errorMessage = `<label class="row-input-error invalid-feedback">The Version field is required</label>`

                            $(version).after(errorMessage)
                        }

                        if (!description.val()) {
                            let errorMessage = `<label class="row-input-error invalid-feedback">The Description field is required</label>`

                            $(description).after(errorMessage)
                        }

                    } else {
                        // if (!isValidHttpUrl( link.val() )) {
                        //         let errorMessage = `<label class="row-input-error invalid-feedback">Please enter a valid URL</label>`

                        //         $(link).after(errorMessage)
                        // } else {
                            validRows += 1
                        // }
                    }
                }
            })

            if (rowsToValidate == validRows) {

                // Submitting the form
                form.submit()

            }
        })

        // policies datatable
        const policyDatatable = $("#policies-datatable").DataTable({
            serverSide: true,
            "processing": true,
            ordering: false,
            responsive: true,
            stateSave: true,
            ajax: {
                "url": "{{ route('policy-management.policies.get-json-data') }}",
                "type": "GET",
            },
            "columnDefs": [
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
                },
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
                    "targets": 3
                },
                {
                    "render": function ( data, type, row ) {
                        return $.fn.dataTable.render.text().display(data, type, row);
                    },
                    "targets": 3
                }
            ]
        });

        // Policies edit
        $(document).on('click', '.edit-action', async function(event) {
            event.preventDefault()

            let actionURL = this.href
            let policyType = this.dataset.type


            let getPolicyDataRoute = this.dataset.getPolicyRoute

            let policyRes = await $.get(getPolicyDataRoute)


            if (!policyRes.success) {
                return false
            }

            let policy = policyRes.data
            let policyDisplayName = policy.display_name
            let policyVersion = policy.version
            let policyDescription = policy.description
            let policyPath = policy.path


            // Toggling file upload section
            if (policyType == 'doculink') {
                $("#document-policy-update-section").hide()
                $("#update-policies-form").find('.file-validation-limit').hide()

                $("#link-input-section").show()
                $("#link-input-section").find("input[name='link']").prop('disabled', false)


            } else {
                $("#document-policy-update-section").show()
                $("#update-policies-form").find('.file-validation-limit').show()

                $("#link-input-section").hide()
                $("#link-input-section").find("input[name='link']").prop('disabled', true)
            }

            let updatePolicyForm = $("#update-policies-modal form")

            updatePolicyForm.attr("action", actionURL);


            updatePolicyForm.find("input[name='display_name']").val(_.unescape(policyDisplayName));
            updatePolicyForm.find("input[name='version']").val(_.unescape(policyVersion));
            updatePolicyForm.find("textarea[name='description']").val(_.unescape(policyDescription));

            if (policyType == 'doculink') {
                updatePolicyForm.find("input[name='link']").val(policyPath);
            } else {
                updatePolicyForm.find("input[name='link']").val('');
            }

            $("#update-policies-modal").modal()

        })

        // validate policies update form
        var updatePoliciesFormValidator = $("#update-policies-form").validate({
            errorClass: 'invalid-feedback',
            rules: {
                'display_name': {
                    required: true,
                },
                'version': {
                    required: true
                },
                'description': {
                    required: true
                },
                'link': {
                    required: true
                }
            },
            messages: {
                'display_name': {
                    required: 'The Display Name field is required'
                },
                'version': {
                    required: 'The Version field is required'
                },
                'description': {
                    required: 'The Description field is required',
                },
                'link': {
                    required: 'The Link field is required',
                }
            },
            ignore: [],
            submitHandler: function(form) {
                form.submit();
                $(form).find('button[type=submit]').prop('disabled', true)
            }
        });

        // RESET FORM VALIDATOR ON MODAL CLOSE`
        $('#update-policies-modal').on('hidden.bs.modal', function (e) {
            updatePoliciesFormValidator.resetForm();
        })

        // Delete policy(ies)
        $(document).on('click', '.policy-delete-link', function(event) {
            event.preventDefault()

            let deleteCampaignURL = this.href

            // Are you sure
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover this policy!",
                    showCancelButton: true,
                    confirmButtonColor: '#ff0000',
                    confirmButtonText: 'Yes, delete it!',
                    closeOnConfirm: false,
                    imageUrl: '{{asset('assets/images/warning.png')}}',
                    imageWidth: 120
                })
                .then(confirmed => {
                    if (confirmed.value && confirmed.value == true) {
                        window.location.href = deleteCampaignURL;
                    }
                });
        })

    });
</script>
@endsection
