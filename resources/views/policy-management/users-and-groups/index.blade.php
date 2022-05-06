    @extends('layouts.layout')

@php $pageTitle = "Users & Groups - Policy Management"; @endphp

@section('title', $pageTitle)

@section('plugins_css')

@include('includes.assets-libs.datatable-css-libs')
@endsection

@section('custom_css')
<style  nonce="{{ csp_nonce() }}">
.style-container__user .invalid-feedback {
    bottom: 30px !important;
}

@media (max-width: 991px) {
    .style-container__user {
        flex-direction: column;
    }
    .style-container__user .invalid-feedback {
    bottom: -25px !important;
}
.style-container__user {
    position: relative;
}
}
.absolute-error-form label.invalid-feedback {
    bottom: -6px !important;
}
.styleAegis {
    text-align: center;
}
.style-container__user {
    display: flex;
    justify-content: space-evenly;
}

#add-existing-users-modal-datatable tbody>tr.selected,#add-existing-users-modal-datatable tbody>tr:hover{
    background-color: #f5f6f8;
}
#add-existing-users-modal-datatable tbody>tr.selected td{
    border-color: #dee2e6;
    color: #6c7592;
}
#add-existing-users-modal-datatable tbody>tr td{
    cursor: pointer;
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
                    <li class="breadcrumb-item"><a href="{{ route('policy-management.campaigns') }}">Policy Management</a></li>
                    <li class="breadcrumb-item"><a href="#">Users & Groups</a></li>
                </ol>
            </div>
            <h4 class="page-title">{{ $pageTitle }}</h4>
        </div>
    </div>
</div>
<!-- end of breadcrumbs -->
@include('includes.flash-messages')

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <button type="button" id="add-new-group-btn" class="btn btn-sm btn-primary waves-effect waves-light float-end">
                            <i class="mdi mdi-plus-circle"></i> New Group
                        </button>
                    </div>
                    <div class="col-12" id="users-and-groups-tabs-section">
                        <ul class="nav nav-tabs nav-bordered">
                            <li class="nav-item">
                                <a href="#groups-tabs" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                    Groups
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#users-tab" data-toggle="tab" aria-expanded="true" class="nav-link">
                                    Users
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <!-- groups tabs -->
                            <div class="tab-pane active table-container" id="groups-tabs">
                                <table id="groups-datatable" class="table dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>No. of Members</th>
                                            <th>Date Created</th>
                                            <th>Last Updated</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <!--end of tab -->

                            <!-- users tabs -->
                            <div class="tab-pane" id="users-tab">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body table-container">
                                                <table id="users-datatable" class="table dt-responsive nowrap">
                                                    <thead>
                                                        <tr>
                                                        <th>  </th>
                                                            <th>First Name</th>
                                                            <th>Last Name</th>
                                                            <th>Email</th>
                                                            <th>Groups</th>
                                                            <th>Status</th>
                                                            <th>Date Created</th>
                                                            <th>Last Updated</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>


                                                    </tbody>
                                                </table>
                                            </div> <!-- end card body-->
                                        </div> <!-- end card -->
                                    </div><!-- end col-->
                                </div>
                                <!-- end row-->
                            </div>
                            <!--end of tab -->
                        </div>
                    </div> <!-- end col -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALS SECTIONS -->

<!-- Add and update group modal -->
<div id="add-group-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-4">
                <form id="add-group-form" class="absolute-error-form" action="" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="group-name" class="form-label">Name  <span class="required text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="group-name" placeholder="Group name">
                            </div>
                        </div>
                    </div>

                </form>
                <!-- bulk import user section -->
                <div class="row mb-3 mt-1">
                    <div class="col-12 styleAegis">
                        <input type="file" name="users-bulk-import" class="d-none" id="users-bulk-import">
                        <button type="button" id="users-bulk-import-btn" class="btn btn-danger width-xl waves-effect waves-light">Bulk Import Users</button>
                        <a href="{{ route('policy-management.users-and-groups.users.download-csv-template') }}">
                            <button type="button" class="btn btn-outline-secondary width-xl waves-effect waves-light">Download CSV Template</button>
                        </a>
                        <button type="button" id="add-existing-users-to-group-btn" class="btn btn-outline-secondary width-xl waves-effect waves-light">Add Existing Users</button>
                        <!-- LDAP User import button -->
                        <!-- <button type="button" id="import-ldap-users-to-group-btn" class="btn btn-outline-secondary width-xl waves-effect waves-light">Import LDAP Users</button> -->
                    </div>
                </div>
                <!-- End of bulk import section -->
                <form id="add-user-form" class="absolute-error-form" action="{{ route('policy-management.users-and-groups.users.store') }}">
                    @csrf
                    <div class="container style-container__user">
                        <div class="">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="first_name" placeholder="First Name">
                            </div>
                        </div>
                        <div class="">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="last_name" placeholder="Last Name">
                            </div>
                        </div>
                        <div class="">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="email" placeholder="Email">
                            </div>
                        </div>
                        <div class="">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-danger waves-effect waves-light">Add</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!--/.row -->
            </div>

            <!-- user list -->
            <div class=" table-container">
                <table id="users-to-be-added-in-group-modal-datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" id="submit-group-btn">Save Changes</button>
            </div>
        </div>
    </div>
</div><!-- /.modal -->

<!-- Add existing user modal -->
<div id="add-existing-user-to-group-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Existing User To Group</h4>
                <button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body table-container">
                <table id="add-existing-users-modal-datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th class='d-none'>  </th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                <button type="button" id="add-existing-to-group-btn" class="btn btn-primary waves-effect waves-light">Add To Group</button>
            </div>
        </div>
    </div>
</div><!-- /.modal -->

<!-- Import ldap user modal -->
<div id="import-ldap-users-to-group-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add LDAP Users To Group</h4>
                <button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body table-container">
                <table id="ldap-users-modal-datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                <button type="button" id="add-to-group-btn" class="btn btn-primary waves-effect waves-light">Add to Group</button>
            </div>
        </div>
    </div>
</div><!-- /.modal -->


<!-- EDIT USER MODAL -->
<div id="edit-users-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="update-users-form" action="" method="post">
                    @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Edit Users</h4>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body p-4">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first-name" class="form-label">First Name</label>
                                <input type="text" class="form-control" name="first_name" id="first-name" placeholder="First name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last-name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" id="last-name" placeholder="Last name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" name="email" id="email" placeholder="Email">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.modal -->



@endsection

@section('plugins_js')
@include('includes.assets-libs.datatable-js-libs')

<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
@endsection

@section('custom_js')
<script nonce="{{ csp_nonce() }}">
    $(document).ready(function() {
        // FOR multiple modal open scrollable issue
        $('.modal').on("hidden.bs.modal", function(e) { //fire on closing modal box
            if ($('.modal:visible').length) { // check whether parent modal is opend after child modal close
                $('body').addClass('modal-open'); // if open mean length is 1 then add a bootstrap css class to body of the page
            }
        });

        /***
        * EDIT USER
        */
        // User and group delete
    $(document).on('click', '.delete', function(event){
        event.preventDefault()

        let deleteUserAndGroupURL = this.href

        // Are you sure
        swal({
                title: "Are you sure?",
                text: "You will not be able to retrive group!",
                showCancelButton: true,
                confirmButtonColor: '#ff0000',
                confirmButtonText: 'Yes, delete it!',
                closeOnConfirm: false,
                imageUrl: '{{asset('assets/images/warning.png')}}',
                imageWidth: 120
        })
        .then(confirmed => {
            if(confirmed.value && confirmed.value == true){
                window.location.href = deleteUserAndGroupURL;
            }
        });
    })

        /**
        * FORM VALIDATE
        */
        var updateUsersFormValidator = $("#update-users-form").validate({
            errorClass: 'invalid-feedback',
            rules: {
                first_name: {
                    required: true,
                    maxlength: 190,
                },
                last_name: {
                    required: true,
                    maxlength: 190,
                },
                email: {
                    required: true,
                    maxlength: 190
                }
            },
            messages: {
                first_name: {
                    required: 'The First Name field is required',
                    maxlength: 'The Name may not be greater than 190 characters',
                    remote: 'The Group Name already taken'
                },
                last_name: {
                    required: 'The Last Name field is required',
                    maxlength: 'The Name may not be greater than 190 characters',
                    remote: 'The Group Name already taken'
                },
                email: {
                    required: 'The Email field is required',
                    maxlength: 'The Name may not be greater than 190 characters',
                    remote: 'The Group Name already taken'
                },
            },
            submitHandler: function(form, event) {
                form.submit()
            }
        })


        $(document).on('click', '.edit-user-action', function(event){
            event.preventDefault()

            let userInfoURL = this.getAttribute('data-user-edit-url')
            let userUpdateURL = this.href
            let updateUsersForm = $("#update-users-form")

            updateUsersForm.attr('action', userUpdateURL)

            $.get(userInfoURL).done(function(res){
                if (res.success) {
                    let data = res.data

                    updateUsersForm.find('input[name="first_name"]').val(data.first_name)
                    updateUsersForm.find('input[name="last_name"]').val(data.last_name)
                    updateUsersForm.find('input[name="email"]').val(data.email)

                }
            })
            updateUsersFormValidator.resetForm()
            $("#edit-users-modal").modal({})


        })




        // CONSTANT DEFINATIONS
        var usersToBeAddedInGroup = []

        // resetting
        function resetUsersToBeAddedInGroup(){
            usersToBeAddedInGroup = []
        }

        /***
        *
        * Users and groups tabs
        */
        const usersDatatable = $("#users-datatable").DataTable({
            serverSide: true,
            ajax: {
                "url": "{{ route('policy-management.users-and-groups.users.get-data') }}",
                "type": "GET",
            },
            columns: [
                {
                    targets: 0, //first name
                    responsivePriority: 0
                },
                {
                    targets: 1, // last name
                    responsivePriority: 1
                },
                {
                    targets: 2, // email
                    responsivePriority: 3
                },
                {
                    targets: 3, // group
                    responsivePriority: 5
                },
                {
                    targets: 4, //status
                    responsivePriority: 6
                },
                {
                    targets: 5, // date created
                    responsivePriority: 8
                },
                {
                    targets: 6, // date updated
                    responsivePriority: 7
                },
                {
                    targets: 7,
                    responsivePriority: 2
                },
                {
                    targets: 8,
                    responsivePriority: 4
                },
            ],
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                },
                { "orderable": false, "targets": [-1, 3] },
                {
                    "render": function ( data, type, row ) {
                        return $.fn.dataTable.render.text().display(data, type, row);
                    },
                    "targets": [1,2,3,4,6,7]
                }
            ]
        })

        $(document).on( 'shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            if ($(e.target).attr('href') == '#users-tab') {
                usersDatatable.ajax.reload()
            }
        })


        // resetting usersToBeAddedInGroup
        $(document).on( 'hidden.bs.modal', '#add-group-modal', function (e) {
            resetUsersToBeAddedInGroup()
        })


        // Trigger bulk import modal

        $(document).on('click', '#users-bulk-import-btn', function(){
            document.getElementById('users-bulk-import').click()
        })
        // USERS TO BE ADDED IN GROUPS SCRIPT
        $("#users-bulk-import").on('change', function() {
            let csvFile = this.files[0];

            let csvData = csvJSON(csvFile)
        })


        // USER TO BE ADDED IN GROUP MODAL DATATABLE
        const usersModalDatatable = $("#users-to-be-added-in-group-modal-datatable").DataTable({
            "data": usersToBeAddedInGroup,
            "columns": [{
                    "data": "first_name"
                },
                {
                    "data": "last_name"
                },
                {
                    "data": "email"
                },
                {
                    "data": "actions"
                }
            ],
            "columnDefs": [
                {
                    "render": function ( data, type, row ) {
                        return $.fn.dataTable.render.text().display(data, type, row);
                    },
                    "targets": [0,1,2]
                }
            ]
        })


        const groupsDatatable = $("#groups-datatable").DataTable({
            serverSide: true,
            ajax: {
                "url": "{{ route('policy-management.users-and-groups.groups.get-json-data') }}",
                "type": "GET",
            },
            columns: [{
                    targets: 0,
                    responsivePriority: 0
                },
                {
                    targets: 1,
                    responsivePriority: 5
                },
                {
                    targets: 2,
                    responsivePriority: 1
                },
                {
                    targets: 3,
                    responsivePriority: 2
                },
                {
                    targets: 4,
                    responsivePriority: 3
                },
                {
                    targets: 5,
                    responsivePriority: 4
                }
            ],
            "columnDefs": [
                {
                    "render": function ( data, type, row ) {
                        return $.fn.dataTable.render.text().display(data, type, row);
                    },
                    "targets": [0,2,3,4]
                },
                { "orderable": false, "targets": [-1] }
            ]
        })

        function reloadUsersModalDatatable(usersToBeAddedInGroup) {
            usersModalDatatable
                .clear()
                .draw();

            usersModalDatatable.rows.add(uniqueArrayOfObject(usersToBeAddedInGroup, 'email'))
                .draw();
        }

        function uniqueArrayOfObject(array, keyToBeUnique) {
            return Object.values(array.reduce((tmp, x) => {
                // You already get a value
                if (tmp[x[keyToBeUnique]]) return tmp;

                // You never envcountered this key
                tmp[x[keyToBeUnique]] = x;

                return tmp;
            }, {}));
        }

        //var csv is the CSV file with headers
        function csvJSON(csvFile) {
            var ext = csvFile.name.split(".").pop().toLowerCase();

            if ($.inArray(ext, ["csv"]) == -1) {
                alert('upload csv file')
                return false;
            }

            if (csvFile != undefined) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    csvResult = e.target.result.split(/\r|\n|\r\n/);

                    $.each(csvResult, function(index, value) {
                        if (value && (index != 0)) {
                            let csvRaw = value.split(',');

                            if (csvRaw[0] && csvRaw[1] && (csvRaw[2] && /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(csvRaw[2]))) {
                                usersToBeAddedInGroup.push({
                                    first_name: csvRaw[0],
                                    last_name: csvRaw[1],
                                    email: csvRaw[2],
                                    actions: `<span style="cursor:pointer;" data-delete-group-user="${csvRaw[2]}"><i class="fe-trash-2" style="font-size:12px;"></i></span>`
                                })
                            }
                        }
                    });

                    // reloading datable
                    reloadUsersModalDatatable(usersToBeAddedInGroup)
                }
                reader.readAsText(csvFile);
            }
        }

        // EDIT GROUP SCRIPT START
        $(document).on('click', '.edit-group-action', function(event) {
            event.preventDefault()

            let groupEditDataURL = this.href
            let formActionURL = this.dataset.formActionUrl
            let groupId = this.dataset.groupId

            $('#add-group-form input[name="name"]').rules('add', {
                remote: {
                    url: `{{ route('policy-management.users-and-groups.groups.check-name-taken') }}/${groupId}`,
                    type: "get"
                }
            });


            $.get(groupEditDataURL).then(function(res) {
                if (res.success) {
                    let editData = res.data
                    let groupEditData = editData.group
                    let groupUsers = editData.users
                    let addGroupForm = $("#add-group-form");

                    addGroupForm.attr("action", formActionURL);
                    addGroupForm.find("input[name='name']").val(groupEditData.name)

                    usersToBeAddedInGroup = [];
                    groupUsers.forEach(function(item, index) {

                        usersToBeAddedInGroup.push({
                            id: item.id,
                            first_name: item.first_name,
                            last_name: item.last_name,
                            email: item.email,
                            actions: `<span style="cursor:pointer;" data-delete-group-user="${item.email}"><i class="fe-trash-2" style="font-size:20px;"></i></span>`
                        })
                    })
                    // adding users
                    reloadUsersModalDatatable(usersToBeAddedInGroup)
                }
            })

            $("#add-group-modal").find('.modal-title').text('Update Group')

            $("#add-group-modal").modal()
        });

        // remove added users from group
        $(document).on('click', '[data-delete-group-user]', function() {
            let userToRemove = this.dataset.deleteGroupUser;

            usersToBeAddedInGroup = usersToBeAddedInGroup.filter(function(item) {
                return item.email != userToRemove
            })

            // RELOADING DATATABLE
            reloadUsersModalDatatable(usersToBeAddedInGroup)
        })
        // EDIT GROUP SCRIPT ENDS


        // ADD GROUP SRCIPTS STARTS
        $(document).on("click", "button#add-new-group-btn", function() {

            let formActionURL = "{{ route('policy-management.users-and-groups.groups.store') }}"

            $('#add-group-form input[name="name"]').rules('add', {
                remote: {
                    url: `{{ route('policy-management.users-and-groups.groups.check-name-taken') }}`,
                    type: "get"
                }
            });


            $("#add-group-form").attr("action", formActionURL);

            $("#add-group-modal").find('.modal-title').text('Add New Group')

            $("#add-group-modal").modal()
        });

        //

        // add user validation
        $.validator.addMethod("validate_email", function(value, element) {
            if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)) {
                return true;
            } else {
                return false;
            }
        }, "Please enter a valid email address.");

        // Add user to group
        const addUserInGroupFormValidator = $("#add-user-form").validate({
            errorClass: 'invalid-feedback',
            rules: {
                first_name: {
                    required: true,
                    maxlength: 190
                },
                last_name: {
                    required: true,
                    maxlength: 190
                },
                email: {
                    required: true,
                    validate_email: true
                }
            },
            messages: {
                first_name: {
                    required: 'The First Name field is required',
                    maxlength: 'The First Name may not be greater than 190 characters'
                },
                last_name: {
                    required: 'The Last Name field is required',
                    maxlength: 'The Last Name may not be greater than 190 characters'
                },
                email: {
                    required: 'The Email field is required',
                    validate_email: 'Please enter a valid email address',
                    remote: 'The email address already taken'
                }
            },
            submitHandler: function(form, event) {
                event.preventDefault();

                const formAction = form.action

                let firstName = $(form).find('input[name="first_name"]')
                let lastName = $(form).find('input[name="last_name"]')
                let email = $(form).find('input[name="email"]')

                // adding new user to group
                usersToBeAddedInGroup.push({
                    first_name: firstName.val(),
                    last_name: lastName.val(),
                    email: email.val(),
                    actions: `<span style="cursor:pointer;" data-delete-group-user="${email.val()}"><i class="fe-trash-2" style="font-size:20px;"></i></span>`
                })

                // resetting form
                $(form).trigger("reset")

                // RELOADING DATATABLE
                reloadUsersModalDatatable(usersToBeAddedInGroup)

                return false;
            }
        });



        var addGroupForm = $("#add-group-form")
        addGroupForm.on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
        // Add group submit
        $(document).on("click", "#submit-group-btn", function() {
            let validAddGroupForm = addGroupForm.valid()

            if (addGroupForm.valid()) {
                // submitting form when validation is ok
                let requests = usersToBeAddedInGroup.map((item) => {
                    return new Promise((resolve) => {
                        addGroupForm.append(`<input type="hidden" name="user_first_name[]" value="${item.first_name}">`)
                        addGroupForm.append(`<input type="hidden" name="user_last_name[]" value="${item.last_name}">`)
                        addGroupForm.append(`<input type="hidden" name="user_email[]" value="${item.email}">`)
                    });
                })

                addGroupForm.submit()
            }
        })


        // Add group validation
        const addGroupFormValidator = $("#add-group-form").validate({
            errorClass: 'invalid-feedback',
            rules: {
                name: {
                    required: true,
                    maxlength: 190,
                },

            },
            messages: {
                name: {
                    required: 'The Name field is required',
                    maxlength: 'The Name may not be greater than 190 characters',
                    remote: 'The Group Name already taken'
                },
                description: {
                    required: 'The Description field is required',
                    maxlength: 'The Last Name may not be greater than 190 characters'
                }
            }
        })

        $('#add-group-modal').on('hide.bs.modal', function() {
            // Resetting the modal data
            let addGroupForm = $("form#add-group-form")
            addGroupForm.trigger('reset')
            // RELOADING DATATABLE
            reloadUsersModalDatatable([])

            addGroupFormValidator.resetForm();
            addUserInGroupFormValidator.resetForm()
        })

        // ADD EXISTING USER TO GROUP
        const existingUsersTable = $("#add-existing-users-modal-datatable")

        $(document).on('click', '#add-existing-users-to-group-btn', function() {
            $("#add-existing-user-to-group-modal").modal()

            // Reloading datatable

            existingUsersTable.DataTable().destroy()

            var existingUsersDatatable = existingUsersTable.DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ route('policy-management.users-and-groups.users.get-data') }}",
                    "type": "GET",
                },
                rowCallback: function(row, data) {
                    // hiding the first td
                    $(row).children().first().addClass('d-none')
                     // checking if the user is already selected
                    let isSelected = usersToBeAddedInGroup.find((o) => o.id === data[0]);

                    if (isSelected) {
                        $(row).addClass('selected')
                    }
                },
                "columnDefs": [
                    {
                        "render": function ( data, type, row ) {
                            return $.fn.dataTable.render.text().display(data, type, row);
                        },
                        "targets": [0,1,2]
                    }
                ]
            })

        })

        $(document).on('click', '#add-existing-to-group-btn', function() {
            // reload users to be added in group modal
            reloadUsersModalDatatable(usersToBeAddedInGroup)

            $("#add-existing-user-to-group-modal").modal('hide')
        });

        // handeling users to be added in group select event
        $(document).on('click', '#add-existing-users-modal-datatable tbody tr', function() {
            var selectRowData = existingUsersTable.DataTable().row(this).data();
            let isAlreadySelected = usersToBeAddedInGroup.find((o) => o.id === selectRowData[0]);

            if (isAlreadySelected) {
                //filtering out users
                usersToBeAddedInGroup = usersToBeAddedInGroup.filter((o) => o.id != selectRowData[0])

            } else {
                let Id = selectRowData[0]
                let firstName = selectRowData[1]
                let lastName = selectRowData[2]
                let email = selectRowData[3]

                if (firstName && lastName && email) {
                    usersToBeAddedInGroup.push({
                        id: Id,
                        first_name: firstName,
                        last_name: lastName,
                        email: email,
                        actions: `<span style="cursor:pointer;" data-delete-group-user="${email}"><i class="fe-trash-2" style="font-size:20px;"></i></span>`
                    })
                }
            }

            $(this).toggleClass('selected');
        });

        const ldapUsersModaltable = $("#ldap-users-modal-datatable")

        $(document).on('click', '#import-ldap-users-to-group-btn', function() {
            return false;

            $('#import-ldap-users-to-group-modal').modal()

            ldapUsersModaltable.DataTable().destroy()

            ldapUsersModaltable.DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ route('policy-management.users-and-groups.get-ldap-users') }}",
                    "type": "GET",
                }
            })
        })
    })
</script>
@endsection
