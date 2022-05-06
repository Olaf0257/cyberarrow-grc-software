@extends('layouts.layout')

@php $pageTitle = "View Projects"; @endphp

@section('title', $pageTitle)

@section('content')

<style nonce="{{ csp_nonce() }}">
 .project-div {
    height: 265px;
}
.project-div h4{
    word-break: break-all;
}


</style>

<!-- breadcrumbs -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('compliance-dashboard') }}">Compliance</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Projects</a></li>
                </ol>
            </div>
            <h4 class="page-title">{{ $pageTitle }}</h4>
        </div>
    </div>
</div>
<!-- end of breadcrumbs -->

<!-- shows flash messages here -->
@include('includes.flash-messages')

<div class="row">
    <div class="col">
        <div class="float-end">
            <div class="ms-3 mb-3">
                Search:&nbsp;&nbsp;<input type="text" name="project_name" class="form-control form-control-sm">
            </div>
        </div>
    </div>
</div>


<div class="row" id="projects-wp">
    <!-- project list -->

</div>
@endsection

@section('custom_js')
<script nonce="{{ csp_nonce() }}">
$(document).ready(function () {

    // Render project to view
    function loadProjects(){
        var projectListWpEl = $("#projects-wp")

        projectListWpEl.append(`
            <div id="content-loading" class="p-2 d-flex align-items-center">
                <div class="spinner"></div>
                <p class="text-center m-0 px-2">Loading...</p>
            </div>
        `)

        $.get("{{ route('compliance.projects.list') }}", {
            project_name: function() {
                return $("input[name=project_name]").val()
            }
        })
        .done(function(res) {

            if (res.success) {
                projectListWpEl.html(res.data)
            } else {
                projectListWpEl.html("")
            }
        })
    }

    loadProjects()

    // filtering projects
    $(document).on('keyup', 'input[name=project_name]', function() {
        loadProjects()
    })

    // DELETE PROJECT

    $(document).on("click", ".project-delete-btn", function(event) {
        event.preventDefault()

        swal({
                title: "Are you sure?",
                text: "You will not be able to recover this project!",
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


}) // END OF DOCUMENT READY
</script>
@endsection
