<div class="tab-pane active">
    <!-- top head -->
    @foreach($risks as $index => $risk)
    <!-- main content box -->
    <div class="content__box d-flex">
        <div class="icon-box">
            <a data-toggle="collapse" class="collapse-el" href="#risk_item_{{ $index }}" aria-expanded="false" aria-controls="toggle-div">
                <i class="icon fas fa-chevron-down expand-icon-w"></i>
                <h5 class="risk__name-text ms-2">{{ $risk->name }}</h5>
            </a>
        </div>
        <div class="checkbox checkbox-success descrip__checkbox">
            <input id="risk_item_checkbox_{{ $risk->id  }}" name="risk-item" type="checkbox" value="{{ $risk->id }}" {{ in_array( $risk->id, $selectedRiskIds) ? 'checked' : ''}} >
            <label for="risk_item_checkbox_{{ $risk->id  }}"></label>
        </div>
    </div>


    <!-- main content box ends -->

    <!--main content decription box -->
    <div class="description__box collapse show" id="risk_item_{{ $index }}">
        <div class="text__2">
            <p class="descrip m-0 p-2">
                {{ $risk->risk_description }}
            </p>
        </div>
    </div>
    <!-- decription box ends -->
    @endforeach
        <!-- pagination -->
        <div class="risks-pagination-wp pagination-rounded mt-4">
            {{ $risks->links() }}
        </div>
</div>

<div class="import-sec-btn mt-3">
    @if($risks->count() > 0)
        @if($isConfirmTab)
        <!-- Confirm tab -->

            <form action="{{ route('risks.wizard.yourself-risks-setup') }}" method="post" name="confirm-risk-setup-form">
                @csrf
                    <input type="hidden" name="is_map" id="is_map">
                    <button type="submit" class="btn btn-primary btn-generate-risk float-end risklist__confirm-btn">Confirm</button>
            </form>

                <!-- choose control mapping project -->
            @if($projects->count() > 0)
                <div id="control-mapping-project-modal" class="modal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title">
                                <p style="text-align:center;">Choose control mapping</p>
                                 <br>
                                Use this feature if you want to automatically map the risks to existing compliance controls. This is an optional feature.

                                </h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <select class="form-control select2-inputs" name="control_mapping_project">
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" style="margin-right: 213px;" id="proceed-setup-without-mapping" class="btn btn-primary">Proceed without mapping</button>
                                <button type="button" id="proceed-setup-map" class="btn btn-primary">Map</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

    @else
        @if($isConfirmTab)
            <div class="empty-data-section">No risks selected</div>
        @endif
    @endif

    <div class='d-flex'>
        <!-- back and next button -->
        @if($currentTabIndex != 1)
            <button class="btn btn-danger btn-back risk-category-back-btn" data-prev-stage="{{ $currentTabIndex-1 }}">Back</button>
        @endif

        @if(!$isConfirmTab)
        <!-- next btn -->
        <button class="btn btn-primary btn-generate-risk risk-category-next-btn ms-auto" data-next-stage="{{ $currentTabIndex+1 }}">Next</button>
        @endif

    </div>

</div>
<!--/.import-sec-btn-->

