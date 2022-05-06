@extends('layouts.layout')

@php
    $pageTitle = "Upload Controls for ". $standard->name;
@endphp

@section('title', $pageTitle)

@section('content')

@include('includes.breadcrumbs')

<!-- upload cv section -->
<section id="upld-cv">
    <!-- top row -->
        <div class="row">
            <div class="col-xl-12">
                <div class="top">
                    <!-- left box -->
                    <div class="top-left">
                        <h3>Upload Control CSV</h3>
                    </div>

                    <!-- right box -->
                    <div class="top-right">
                        <ul class="d-none d-md-block">
                            <li><a href="#" class="text-dark">My Dashboard</a><span class="text-muted m-1"> > </span></li>
                            <li><a href="#" class="text-dark">Controls Library</a><span class="text-muted m-1"> > </span></li>
                            <li><a href="#" class="text-dark">Upload Control CSV</a></li>
                        </ul>
                    </div>


                </div>
            </div>
        </div>

    <!-- bottom row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="bottom-info">
                <h4>Upload a CSV File to Create New Controls</h4>
                <a href="#" class="btn back-btn">Back</a>
            </div>
        </div>
    </div>

    <!-- last row -->
    <div class="last bg-white py-3">
        <div class="row">
            <div class="col-xl-7">
                <div class="upload">
                    <h4>CSV File to Upload</h4>
                    <span ><button class="me-2">Browse..</button>No file selected</span>
                </div>
                <a href="#" class="btn btn-upload text-white mt-2">Upload Controls</a>

            </div>


            <div class="col-xl-5">
                <div class="cv-info m-2">
                    <h5 class="text-uppercase text-white">the csv file should have the following header line:</h5>
                    <p>name, description</p>
                    <p>All fields are mandatory. Separator should be a comma <span>,</span> and the file should be a valid CSV. You can validate your file at <a href="#" class="text-white">http://csvlint.io</a></p>
                    <p>Field size limits for the CSV are: </p>
                    <ul>
                        <li>name: 255 character limit</li>
                        <li>description: 65,536 character limit</li>
                    </ul>
                    <p>CSV imports are temporarily limited to 300 rows of data</p>
                </div>
            </div>

        </div>
    </div>
</section>



@endsection

<!-- creating a new contact section ends here -->
@section('custom_js')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script nonce="{{ csp_nonce() }}">
   $("#validate-form").validate({
     errorClass: 'invalid-feedback',
     rules: {
         name: {
             required: true,
             maxlength: 190
         },
         description: {
             required: true,
         }
     },
     messages: {
         name: {
             required: 'The name field is required.',
             maxlength: 'The name may not be greater than 190 characters.'
         },
         description: {
             required: 'The description field is required.',
         }
     },
     submitHandler: function(form) {
         form.submit();
     }
    });
    </script>
@endsection
