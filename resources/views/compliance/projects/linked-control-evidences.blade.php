@extends('layouts.layout')
@section('plugins_css')
    @include('includes.assets-libs.datatable-css-libs')
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
					<li class="breadcrumb-item"><a href="{{ route('compliance-project-show', [$project->id]) }}">Controls</a></li>
					<li class="breadcrumb-item active"><a href="{{ route('compliance-project-control-show', [$project->id, $linkedToControlId,'tasks']) }}" id="conrtol_detail_link">Details</a></li>
                    <li class="breadcrumb-item active"><a href="#">Evidences</a></li>
				</ol>
			</div>
			<h4 class="page-title">Linked evidences</h4>
		</div>
	</div>
</div>

<!-- end of breadcrumbs -->

@include('includes.flash-messages')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-4">Evidences</h4>

                <table class="table table-centered display table-hover w-100"  id="linked-control-evidences-datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Task Deadline</th>
                            <th>Created On</th>
                            <th>Actions</th>
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
@include('compliance.projects.components.display-text-evidence')

@endsection

@section('plugins_js')
    @include('includes.assets-libs.datatable-js-libs')
@endsection
@section('custom_js')
<script nonce="{{ csp_nonce() }}">
    $( document ).ready(function() {
        $('#back_btn').on('click',function(){
            var url = new URL(window.location.href);
            var refer=url.searchParams.get('refer');
            if(refer)
                window.location.href=url.origin + refer;
            else
                window.location=$('#conrtol_detail_link').attr('href');
        });
        const evidences = @json($evidences);
        $("#linked-control-evidences-datatable").DataTable({
            serverSide: true,
            "processing": true,
            searching: false,
            ordering: false,
            responsive: true,
            stateSave: true,
            ajax: {
                "url": "{{ route('project-control-linked-controls-evidences', [$project->id, $projectControlId, $linkedToControlId]) }}",
                "type": "GET",
            },
             "columnDefs": [
                {
                    "render": function ( data, type, row ) {
                        return $.fn.dataTable.render.text().display(data, type, row);
                    },
                    "targets": [0]
                }
            ],
        });

        $(document).on('click', '.open-evidence-text-modal', function () {
            const selectedEvidenceId = $(this).data('evidenceId');
            const evidence = evidences.find(evidence => evidence.id === selectedEvidenceId)
            $('#text-evidence-modal .modal-title').html(evidence.name);
            $('#text-evidence-modal .evidence-text').html(evidence.text_evidence);
            $('#text-evidence-modal').modal();
        })
    });
</script>
@endsection
