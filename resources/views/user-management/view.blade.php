@extends('layouts.layout')

@section('body-class', 'user-disabling-page')

@php $pageTitle = "Users View"; @endphp

@section('title', decodeHTMLEntity($pageTitle))

@section('plugins_css')
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet" type="text/css" />
@include('includes.assets-libs.datatable-css-libs')
<link href="{{ asset('assets/css/custom.css' )}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

@section('custom_css')
<style nonce="{{ csp_nonce() }}">
    table {
        padding-bottom: 120px !important;
    }

    div.dataTables_scrollBody table {
        margin-bottom: 60px !important;
    }

    table.dataTable.dtr-inline.collapsed>tbody>tr[role=row]>td:first-child:before {
        top: 50%;
        margin-top: -9px;
    }

    .user-disabling-page .select2-container {
    box-sizing: border-box;
    display: inline-block;
    margin: 0;
    position: relative;
    vertical-align: middle;
    z-index: 9999;
}

#custom-datatable td .dropdown-menu .dropdown-item{
    display: flex;
    justify-content: center;
    align-items: center;
}

</style>
@endsection

<!-- breadcrumb -->
<div class="row">
    <div class="col-xl-12">
        <!-- top info -->
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">User management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin-user-management-view') }}">Users</a></li>
                    <li class="breadcrumb-item active">List</li>
                </ol>
            </div>
            <h4 class="page-title">{{ $pageTitle }}</h4>
        </div>
    </div>
</div>
<!-- breadcrumb -->

@include('includes.flash-messages')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('admin-user-management-create') }}" type="button" class="btn btn-sm btn-primary waves-effect waves-light float-end">
                    <i class="mdi mdi-plus-circle"></i> Add User
                </a>
                <h4 class="header-title mb-4">Manage Users</h4>

                <table class="table table-centered display table-hover w-100"  id="custom-datatable">
                    <thead>
                        <tr>
                            <th>
                                ID
                            </th>
                            <th>Auth Method</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Phone</th>
                            <th>Roles</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Last Login</th>
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
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>
@include('includes.assets-libs.datatable-js-libs')
@endsection

@section('custom_js')
<script nonce="{{ csp_nonce() }}">
    $(document).ready(function () {
        var usersListDatatable = $("#custom-datatable").DataTable({
            serverSide: true,
            // scrollX: true,
            "processing": true,
            ordering: false,
            responsive: true,
            stateSave: true,
            columns: [
                { responsivePriority: 0, targets:0 },
                { responsivePriority: 1, targets:1 },
                { responsivePriority: 2,targets:2 },
                { responsivePriority: 3, targets:3 },
                { responsivePriority: 4, targets:4 },
                { responsivePriority: 5, targets:5 },
                { responsivePriority: 6, targets:6 },
                { responsivePriority: 7, targets:7 },
                { responsivePriority: 9, targets:8 },
                { responsivePriority: 12, targets:9 },
                { responsivePriority: 11, targets:10 },
                { responsivePriority: 10, targets:11 },
                { responsivePriority: 8, targets:12 }
            ],
            "columnDefs": [
                {
                    "render": function ( data, type, row ) {
                        return $.fn.dataTable.render.text().display(data, type, row);
                    },
                    "targets": [0,1,2,3,4,5,6,9,10,11]
                }
            ],
            ajax: {
                "url": "{{ route('admin-user-management-get-json-data') }}",
                "type": "GET",
            }
        })

        $(document).find(".swal2-select").select2();


        @include('user-management.disable-user-script')


        // Reactivating user
        $(document).on('click', '.activate-user', function(event){
            event.preventDefault()

            const userReactivatingUrl = this.href

            $.get( userReactivatingUrl )
                .done(function( data, statusText, xhr) {
                    if(xhr.status == 200){
                        usersListDatatable.ajax.reload(null, false);

                        Swal.fire({
                            text: data.message,
                            confirmButtonColor: '#b2dd4c',
                            imageUrl: '{{ asset('assets/images/success.png') }}',
                            imageWidth: 120
                        })
                    }
                })

        })

        // Deleting Users Permanently
        $(document).on('click', '.delete-user', function (event) {
            event.preventDefault()

            const userDestroyUrl = this.href

            // Are you sure
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to reactivate this user!",
                    showCancelButton: true,
                    confirmButtonColor: '#ff0000',
                    confirmButtonText: 'Yes, delete it!',
                    closeOnConfirm: false,
                    imageUrl: '{{asset('assets/images/warning.png')}}',
                    imageWidth: 120

                    })
                    .then(confirmed => {
                        if(confirmed.value && confirmed.value == true)
                        {
                            $.get( userDestroyUrl )
                                .done(function( data, statusText, xhr) {
                                    if(xhr.status == 200){
                                        usersListDatatable.ajax.reload(null, false);

                                        Swal.fire({
                                            text: data.message,
                                            confirmButtonColor: '#b2dd4c',
                                            imageUrl: '{{ asset('assets/images/success.png') }}',
                                            imageWidth: 120
                                        })
                                    }
                                })
                        }
                    });


        });

    });
</script>



@endsection
