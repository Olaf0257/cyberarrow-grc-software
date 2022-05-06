@extends('layouts.layout')

@php $pageTitle = "Project Details"; @endphp

@section('title', $pageTitle)


@section('plugins_css')
    @include('includes.assets-libs.datatable-css-libs')
<link href="{{asset('assets/libs/multiselect/multi-select.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/ladda/ladda-themeless.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/c3/c3.min.css') }}" rel="stylesheet" type="text/css" />
@endsection


@section('custom_css')

	<style nonce="{{ csp_nonce() }}">
		.basic-datepicker{
			max-width: 95px;
		}

		.input-group {
			display: inline-flex;
		}

		table.dataTable {
			border-collapse: separate!important;
		}

		table thead tr .id,
		table tbody td:nth-child(2)  {
			white-space: nowrap;
		}



		table tbody td,  table tbody td.child span.dtr-data {
			position: relative;
		}

		.row-input-error {
			width: max-content;
			color: #fff;
			font-size: 10px;
			padding: 4px 10px;
			border-radius: 4px;
			text-align: center;
			position: absolute;
			top: -18px;
			left: 13px;
			z-index: 1000;
		}

		.row-input-error:after {
			content: '';
			width: 0;
			height: 0;
			position: absolute;
			z-index: 100;
			border: 8px solid transparent;
			border-top: 8px solid #F1556C;
			bottom: -16px;
			left: 20px;
			transform: translate(-50%);
		}

		 /* Below css is for error message  */


		.form-control {
			padding: 5px;
		}

		.select2-container .select2-selection--single .select2-selection__rendered {
			padding-left: 5px;
		}
		#basic-datatable .select2-container--default{
			width: 188px!important;
		}

		table tbody tr td:nth-last-child(3) {
			/* padding: 0px; */
			min-width: 140px !important;
		}

		table tbody tr td  .dtr-data {
			white-space: normal !important;
		}

		/* Media query for flatpickr on following screen */

		@media (min-width: 768px) and (max-width: 1440px) {
			/* .input-group-text  {
				padding: 6px;
			} */

			table tbody tr td:nth-last-child(2) {
			min-width: 100px !important;
		}

		table tbody tr td:nth-last-child(3) {
			/* padding: 0px; */
			min-width: 140px !important;
		}




			 .form-control {
				width: 100px;
			}

			span .input-group-text {
				padding: 7px;
			}
		}


		input[type="text"]:disabled,
		td .input-group input[type="text"]:disabled > div, input:disabled + div > span.bg-none{
			background: #eee !important;
		}

		.fix-width{
			max-width: 150px;
		}

		@media (max-width: 1440px) and (min-width: 768px){
			.form-control{
				width: 100%;
			}
		}
		.select2-container .select2-selection--single .select2-selection__arrow {
    height: 34px;
    width: 20px;
    right: 3px;
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
					<li class="breadcrumb-item"><a href="{{ route('compliance-dashboard') }}">Compliance</a></li>
					<li class="breadcrumb-item"><a href="{{ route('compliance-projects-view') }}">Projects</a></li>
					<li class="breadcrumb-item active"><a href="javascript: void(0);">Details</a></li>
				</ol>
			</div>
			<h4 class="page-title">{{ $pageTitle }}</h4>
		</div>
	</div>
</div>
<!-- end of breadcrumbs -->



<div class="alert alert-success alert-block" id="alert-success" style="display: none;">
	<button type="button" class="btn-close" data-dismiss="alert">×</button>
		<strong> Control Detail is successfully updated.</strong>
</div>
<div class="row">
	<div class="col-xl-12">
		<div class="card">
			<div class="card-body pt-0">
				<ul class="nav nav-tabs nav-bordered">
					<li class="nav-item">
						<a href="#details" data-toggle="tab" aria-expanded="false" class="nav-link active">
							Details
						</a>
					</li>
					<li class="nav-item">
						<a href="#controls" data-toggle="tab" aria-expanded="true" class="nav-link">
							Controls
						</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="details">
						<h5 class="mt-0">
							{{decodeHTMLEntity($project->name)}}
							( Standard: {{$project->standard->name}} )
						</h5>
						<p>{!! $project->description !!}</p>
					</div>

					<div class="tab-pane show" id="controls">
						<form action="{{route('compliance-project-all-controls-details-update', [$project->id])}}" method="POST" id="update-control-assignment-form">
							@csrf

						@if(Auth::guard('admin')->user()->hasAnyRole(['Global Admin', 'Compliance Administrator']))
							<div class="save-button d-flex justify-content-end">
								<button class="btn btn-primary" data-style="expand-right" id="update-control-assignment-btn">
									<span class="ladda-label">
										Save
									</span>
									<span class="ladda-spinner"></span>
								</button>
							</div>
						@endif

						<table id="basic-datatable" class="display table table-bordered border-light low-padding responsive table__hide-ellipsis" style="width:100%">
							<thead class="table-light">
								<tr>
									<th>Applicable</th>
									<th class="id">Control ID</th>
									<th>
										<div class="fix-width">
											Name
										</div>
									</th>
									<th>
										<div class="fix-width">
											Control Description
										</div>
									</th>
									<th>Status</th>
									<th>Responsible</th>
									<th>Approver</th>
									<th>Deadline</th>
									<th>Frequency</th>
									<th></th>
								</tr>
							</thead>
							<tbody class="tbody-light">
							</tbody>
						</table>
						</form>
					</div>
				</div>
			</div> <!-- end card -->
		</div>
	</div> <!-- end col -->
</div>
<!-- end row -->
@php
	$total = $project->controls()->count();
	$notApplicable = $project->controls()->where('applicable', 0)->count();
	$implemented = $project->controls()->where('applicable', 1)->where('status', 'Implemented')->count();
	$notImplemented = $project->controls()->where('applicable', 1)->where('status', 'Not Implemented')->orWhere('status', 'Rejected')->count();
	$underReview = $project->controls()->where('applicable', 1)->where('status', 'Under Review')->count();
	$perImplemented = ($total > 0) ?($implemented/$total) * 100 :  0;
	$perUnderReview = ($total > 0) ? ($underReview/$total) * 100 : 0;
	$perNotImplemented = ($total > 0) ? ($notImplemented/$total) * 100 : 0;
@endphp
<div class="row">
	<div class="col-lg-12">
		<h4 class="page-title mb-3">Overview</h4>
	</div>
	<div class="col-lg-6">
		<div class="card h-100">
			<div class="card-body">
				<h4 class="header-title">Control Status</h4>
				<hr>
				<table id="control-status-table" class="table no-bordered" style="width:100%;">
					<tbody>
						<tr>
							<td>Total Controls:</td>
							<td><strong>{{$total}}</strong></td>
						</tr>
						<tr>
							<td>Not Applicable:</td>
							<td><strong>{{$notApplicable}}</strong></td>
						</tr>
						<tr>
							<td>Implemented Controls:</td>
							<td><strong>{{$implemented}}</strong></td>
						</tr>
						<tr>
							<td>Under Review:</td>
							<td><strong>{{$underReview}}</strong></td>
						</tr>
						<tr>
							<td>Not Implemented Controls:</td>
							<td><strong>{{$notImplemented}}</strong></td>
						</tr>
					</tbody>
				</table>

			</div> <!-- end card-body-->
		</div> <!-- end card-->
	</div> <!-- end col -->
	<div class="col-lg-6">
		<div class="card h-100">
			<div class="card-body">
				<h4 class="header-title">Implementation Progress</h4>
				<hr>
				<div id="pie-chart" style="height: 300px;" dir="ltr"></div>
			</div> <!-- end card-body-->
		</div> <!-- end card-->
	</div> <!-- end col -->
</div>

<!-- end row -->

@endsection

@section('plugins_js')
    @include('includes.assets-libs.datatable-js-libs')
<script src="{{ asset('assets/libs/multiselect/jquery.multi-select.js') }}"></script>
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('assets/libs/ladda/spin.js') }}"></script>
<script src="{{ asset('assets/libs/ladda/ladda.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<!--C3 Chart-->
<script src="{{ asset('assets/libs/d3/d3.min.js') }}"></script>
<script src="{{ asset('assets/libs/c3/c3.min.js') }}"></script>

@endsection

@section('custom_js')
<script nonce="{{ csp_nonce() }}">
	!function(t){"use strict";var e=function(){};e.prototype.init=function(){
	c3.generate({
		bindto:"#pie-chart",
		data:{
			columns:[
				["Implemented", {{$perImplemented}}],
				["Under Review", {{$perUnderReview}}],
				["Not Implemented", {{$perNotImplemented}}]
			],
			type:"pie"
		},
		color:{pattern:["#359f1d","#5bc0de","#cf1110"]},
		pie:{label:{show:!1}}
	})
},t.ChartC3=new e,t.ChartC3.Constructor=e}(window.jQuery),function(t){"use strict";window.jQuery.ChartC3.init()}();



$(document).ready(function () {

	$('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {
		$.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
	} );

	var basicDataTable = $("#basic-datatable").DataTable({
		serverSide: true,
		lengthChange: false,
		searching: false,
		processing: true,
		ordering: false,
		stateSave: true,
		responsive: {
			details: {
				type: 'column',
				target: '.responsive-collapse-btn'
			},

    	},
		autoWidth: false,
		// scrollX: true,
		columnDefs: [
			{ "width": "100px", "className": "text-center", "targets": 0 },
			{ "width": "400px", "targets": 2 },
			{ "width": "400px", "targets": 3 },
			{ "className": "", "targets": 5 },
		],

		columns: [
			{ responsivePriority: 0, targets:0 },
			{ responsivePriority: 6, targets:1 },
			{ responsivePriority: 8, targets:2 },
			{ responsivePriority: 9, targets:3 },
			{ responsivePriority: 7, targets:4 },
			{ responsivePriority: 5, targets:5 },
			{ responsivePriority: 2, targets:6 },
			{ responsivePriority: 3, targets:7 },
			{ responsivePriority: 4, targets:8 },
			{ responsivePriority: 1, targets:9 }
		],

		language: {
			paginate: {
				previous: "<i class='mdi mdi-chevron-left'>",
				next: "<i class='mdi mdi-chevron-right'>"
			}
		},
		ajax: {
			"url": "{{ route('compliance-project-detail-get-json-data', [$project->id]) }}",
			"type": "GET",
		},
		drawCallback: function () {
			$(".dataTables_paginate > .pagination").addClass("pagination-rounded");
			$(".basic-datepicker").flatpickr({
				dateFormat:"Y-m-d",
				minDate: "today",
				disableMobile : true,
			});

			initSelect2Picker()

		}
	})

	function initSelect2Picker() {
		// Initializing select2
		$(".responsible-select2-picker").select2({
			allowClear: true,
			placeholder: "Search Responsible...",
		});

		$(".approver-select2-picker").select2({
			allowClear: true,
			placeholder: "Search Approver...",
		});
	}
	//toggle plus minus icons for datatable responsive target collapse icon
	$(document).on("click", ".row-toggle", function(){
		if ($(this).hasClass("fa-plus-circle")){
			$(this).removeClass("fa-plus-circle").addClass("fa-minus-circle")
        }
        else{
			$(this).removeClass("fa-minus-circle").addClass("fa-plus-circle")
		}
	})


	// updating applicable hidden input
	$(document).on('click', ".applicableCheckbox", function (){
	   let nextEl = $(this).parent().find('input[name^="applicable"]')

	   if($(this).prop('checked'))
	   {
			$(nextEl).val(1)
	   } else {
			$(nextEl).val(0)
	   }
	});

	/**
	 * Resolving collapsable responsive datatable code starts here
	 */

	// Setting value on actual element on change event triggered on plugin for Select2
	$(document).on("select2:select", "select", function (e) {

        var data = e.params.data;

        $(this).find('option').removeAttr("selected")

        tablevalidatehandle();

		if(data.id){
            $(this).find(`option[value='${data.id}']`).attr("selected", "selected")
		}
    });

	//Setting value on actual element on change event triggered on plugin for FlatPickr
	$(document).on("change", ".flatpickr-input", function (e) {
		$(this).attr("value", $(this).val())
	})

	// Collapse the children on responsive resize
	basicDataTable.on( 'responsive-resize', function ( e, datatable, columns ) {
		datatable.rows().every(function(){
			this.child.hide()
			$(this.nodes()).removeClass("parent")

			// making minus button to plus button
			$(this.nodes()).find(".responsive-collapse-btn").removeClass('collapsed')
		})

		// toggleRowCollapseButton
		toggleRowCollapseButton()
	});

	basicDataTable.on( 'responsive-display', function ( e, datatable, row, showHide, update ) {
		if (showHide){
			child = row.child()
			parent = row.selector.rows

			//syncing parent to child
			parentInputs = $(parent).find("select, input.basic-datepicker")

			$(parentInputs).each(function(index){
				let currentInput = this
				if (this.tagName=="SELECT"){
					let valueOfSelect = $(currentInput).find(":selected").attr("value")
					if($(currentInput).parent().css("display")=="none"){
						$(child).find("select:eq(" + index + ")").children(`option[value='${valueOfSelect}']`).attr("selected", "selected")
					}
				}
				else if (this.tagName=="INPUT"){
					let valueOfInput = $(currentInput).attr("value")
					$(child).find("input.basic-datepicker").attr("value", valueOfInput).flatpickr({
						dateFormat:"Y-m-d",
						minDate: "today",
						disableMobile : true
					});
				}
			})

			//syncing child to parent
			childSelectInputs = $(child).find("select, input.basic-datepicker")

			$(childSelectInputs).each(function(index){
				let indexOfChildSelect = index

				if (this.tagName=="SELECT"){
					$(this).on("change", function(){
						let optionValue = $(this).val()
						$(parent).find("select:eq(" + indexOfChildSelect + ")").find('option').removeAttr("selected")
						$(parent).find("select:eq(" + indexOfChildSelect + ")").find(`option[value='${optionValue}']`).attr("selected", "selected").trigger('change')
					})
				}
				else if (this.tagName=="INPUT"){
					$(this).on("change", function(){
						let optionValue = $(this).val()
						$(parent).find("input.basic-datepicker").attr("value", optionValue).flatpickr({
							dateFormat:"Y-m-d",
							minDate: "today",
							disableMobile : true,
							defaultDate: optionValue
						})
					})
				}
			})

			// Initializing select2
			initSelect2Picker()
		}
	} );

	/**
	 * Resolving collapsable responsive datatable code Ends here
	 */

	// Responsive datatable toggle plus plus icons
	 $(document).on('click', '.responsive-collapse-btn', function(){
		$(this).toggleClass('collapsed')
	 })

	$(document).on('submit','#update-control-assignment-form',function(e) {

        e.preventDefault();

        var is_validate =  tablevalidatehandle();

		 if(is_validate){

			//Collapse child rows on submit
			basicDataTable.rows().every(function(){
				this.child.hide()
				$(this.nodes()).removeClass("parent")
			})

			$('.responsive-collapse-btn').removeClass('collapsed')

			var myform = $('#update-control-assignment-form');
			// Find disabled inputs, and remove the "disabled" attribute
			var disabled = myform.find(':input:disabled').removeAttr('disabled');
			var serialize = myform.serialize();

			// re-disabled the set of inputs that you previously enabled
			disabled.attr('disabled','disabled');
			var updateControlAssignmentBtn = Ladda.create( document.querySelector( '#update-control-assignment-btn' ) );
			updateControlAssignmentBtn.start();

			$.ajax({
				url: $("#update-control-assignment-form").attr('action'),
				method: 'POST',
				data: serialize,
				success: function(response) {
					updateControlAssignmentBtn.stop()

					if(response.exception){
						Swal.fire({
							type: 'error',
							text: response.exception
						})
					} else {

						let pageInfo = $('#basic-datatable').DataTable().page.info()
						let lastPage = pageInfo.pages
						let currentPage = pageInfo.page+1

						if(response.trim() == "success") {

							if(lastPage != currentPage){
								Swal.fire({
									title: "Control assignment updated successfully!",
									text: "Do you want to continue to the next page?",
									imageUrl: '{{ asset('assets/images/success.png') }}',
									imageWidth: 120,
									showCancelButton: true,
									confirmButtonColor: '#b2dd4c',
									cancelButtonColor: '#d33',
									confirmButtonText: 'Yes',
									cancelButtonText: 'No'
								}).then((result) => {
									if (result.value) {
										$('#basic-datatable').DataTable().page( 'next' ).draw( 'page' );
									} else {
										$('#basic-datatable').DataTable().ajax.reload(function(){
											// toggleRowCollapseButton
											toggleRowCollapseButton()
										}, false);
									}
								})
							} else {
								Swal.fire({
									title: "Control assignment updated successfully!",
									imageUrl: '{{ asset('assets/images/success.png') }}',
									imageWidth: 120,
									confirmButtonColor: '#b2dd4c'
								}).then(()=> {
									$('#basic-datatable').DataTable().ajax.reload(function(){
										// toggleRowCollapseButton
										toggleRowCollapseButton()
									}, false);
								})
							}

							$('#control-status-table').load(document.URL +  ' #control-status-table > *');
						}
					}
				},
				error: function(error) {
					console.log(error);
				}
			});
		}
    })

//Function for valdation of responsible user and approval user
  function tablevalidatehandle()
    {
        var tableRows = $(this).find("table tr");
		var rowsToValidate = 0;
		var validRows = 0

		basicDataTable.rows().every(function(){

			let tr = this.node()
			let hasChild = this.child()

			// Removing previous error logs
			$(tr).find(".row-input-error").remove()


			let responsible = $(tr).find("select[name='responsible[]']")
			let approver = $(tr).find("select[name='approver[]']")

			if( responsible.val() || approver.val() ) {
				rowsToValidate += 1

				// calculating valid rows
				if( (responsible.val() && approver.val() ) && responsible.val() != approver.val() ){
					validRows += 1
				} else {

					if(!responsible.val() || !approver.val() || (responsible.val() == approver.val()) ){
						// Showing child row on validation error
						if(!hasChild){
							$(tr).find("td:first-child").trigger('click')
						} else {
							// removeing previous error message
							$(this.child()).find(".row-input-error").remove()
						}

						// this should be after click triggered
						let child = this.child()

						if( !responsible.val() ){
							let errorMessage = `<p class="tootip bg-danger row-input-error">You must select any one responsible !</p>`
							$(responsible).after(errorMessage)
							$(child).find("select[name='responsible[]']").after(errorMessage)
						}

						if( !approver.val() ){
							let errorMessage = `<p class="tootip bg-danger row-input-error">You must select any one approver !</p>`

							$(approver).after(errorMessage)
							$(child).find("select[name='approver[]']").after(errorMessage)
						}

						if(responsible.val() == approver.val()){
							let errorMessage = `<p class="tootip bg-danger row-input-error">Responsible & Approver can't be same</p> `

							$(responsible).after( errorMessage )

							errorMessage = `<p class="tootip left-35 bg-danger row-input-error">Responsible & Approver can't be same</p> `

							$(approver).after( errorMessage )

							$(child).find("select[name='responsible[]']").after(errorMessage)
							$(child).find("select[name='approver[]']").after(errorMessage)
						}
					}
				}
			}
        })

        return (rowsToValidate == validRows ? true : false);
    }


	function toggleRowCollapseButton(){
		// Togglling custom responsive table collapsable button
		let hiddenTdsCount = $(basicDataTable.row().node()).find("td:hidden").length

		if(hiddenTdsCount > 0){
			$(".responsive-collapse-btn").removeClass('d-none')
		} else {
			$(".responsive-collapse-btn").addClass('d-none')
		}
	}
}); // end of document ready
</script>
@endsection
