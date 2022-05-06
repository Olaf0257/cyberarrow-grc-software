<!-- control mapping -->
<div id="linking-existing-controls-modal" class="modal fade" role="dialog" aria-hidden="true" aria-labelledby="control-mappingLabel" style="display: none;">
    <div class="modal-dialog modal-full modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="full-width-modalLabel">Control Mapping</h4>
                <button type="button" class="btn-close mx-1" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- table -->
            <div class="card">
                <div class="card-body">
                    <div class="row linking-existing-controls-modal__filters d-flex justify-content-center justify-content-md-end">
                        <div class="w-25 mx-1 mb-1">
                            <select name="standard_filter" class="form-control select2-picker select-standard">
                                <option value>Select Standard</option>
                                @foreach($allStandards as $standard)
                                    <option value="{{ $standard->id }}"> {{ $standard->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-25 mx-1 mb-1">
                            <select name="project_filter" disabled class="form-control select2-picker select-project">
                                <option value="">Select Project</option>
                                <option value=""></option>
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="me-2 ms-1 mb-1">
                            <button name="search" class="btn btn-primary">Search</button>
                        </div>
                    </div>

                    <table id="linking-existing-controls-datatable" class="table table-borderless table-hover nowrap" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Project</th>
                                <th>Standard</th>
                                <th>Control ID</th>
                                <th>Control Name</th>
                                <th>Control Description</th>
                                <th>Frequency</th>
                                <th>Select</th>
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
