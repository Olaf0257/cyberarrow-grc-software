<div class="tab-pane mb-5 {{ ($activeTab == 'risk_settings_tab') ? 'show active' : ''}}" id="risk-score-settings-tab">
    <form method="POST" id="reset-risk-matrix-to-default-form" action="{{ route('global-settings.risk-matrix.restore-to-default') }}">
        @csrf
        <input type="hidden" name="risk_settings_tab" value="1">
    </form>
    <form method="POST" action="{{ route('global-settings.risk-matrix.update') }}" id="update-risk-matrix-form">
        @csrf
        <input type="hidden" name="risk_settings_tab" value="1">
    <div class="row">
        <div class="col-12 clearfix mb-5 px-3">
            <textarea name="risk_matrix_data" class="d-none"></textarea>
            <button type="button" id="save-updated-risk-matrix-btn" class="btn btn-primary float-end">
                Save matrix
            </button>

            <button type="button" id="reset-risk-matrix-to-default" class="btn btn-secondary float-end mx-2">
                Restore to default
            </button>
        </div>
        <!--/. col -->
        <div class="col-12">
            <!-- risk matrix section -->
            <div class="row">
                <div class="col">

                    <div id="risk-matrix-container">
                       <!-- matrix actions bttons -->
                        <button type="button" class="btn bg-light risk-matrix-popover risk-matrix-actions column-width" id="add-matrix-row">
                            <i class="mdi mdi-plus-circle-outline text-success"></i>
                        </button>

                        <button class="btn bg-light risk-matrix-actions column-width" id="remove-matrix-column" type="button" >
                            <i class="mdi mdi-minus-circle-outline text-danger"></i>
                        </button>

                        <button class="btn bg-light risk-matrix-actions column-height" id="remove-matrix-row" type="button">
                            <i class="mdi mdi-minus-circle-outline text-danger"></i>
                        </button>

                        <button type="button" class="btn bg-light risk-matrix-popover risk-matrix-actions column-height" id="add-matrix-column">
                            <i class="mdi mdi-plus-circle-outline text-success"></i>
                        </button>

                        <div class="d-flex align-items-center position:relative;">
                            <div class="table-probability"><h4 style="transform: rotate(-90deg);">Probability</h4></div>
                            <div class="table-scroll-wrapper">
                                <table class="table table-bordered mb-0" id="risk-matrix">
                                    <tbody>
                                    </tbody>
                                    </table>
                                </div>
                            <div class="risk-matrix-caption"><h4>Impact</h4></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- confirm save risk matrix Modal Starts -->
            <div id="confirmUpdateRiskMatrixModal" class="modal fade" tabindex="-1">
                <div class="modal-dialog modal-confirm modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title w-100">Save changes</h3>
                            <button type="button" class="btn-close align-self-center" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body text-center py-4">
                            <img src="{{asset('assets/images/info1.png')}}" height="100" width="100" alt="warning">
                            <h4 class="py-3">
                                This will only affect the newly created risks; existing risks will keep their old scoring and will have to be updated manually.
                            </h4>
                            <h4 class="pb-3"><b>Make sure to revisit the existing risks.</b></h4></h4>
                            <h4><b>Do you wish to continue?</b></h4></h4>
                        </div>
                        <div class="modal-footer justify-content-end py-2">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" id="save-updated-risk-matrix-confirm-btn" data-dismiss="modal" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- confirm save risk matrix -->
            <!-- risk levels slider -->
            <section class="risk-levels-slider pt-2 pb-5 my-5">
                <div class="row mt-3 mb-4">
                    <div class="col-md-10 mb-4">
                        <h4>Risk Level Ranges</h4>
                        <p>Define the risk score ranges for each risk level</p>
                    </div>
                    <div class="risk-levels col-md-2 mb-3">
                        <h5>Risk levels</h5>
                        <select id="risk-levels-switcher" class="" data-style="btn-secondary">
                            @foreach($riskScoreLevelTypes as $riskScoreLevelType)
                                <option value="{{ $riskScoreLevelType->level }}" data-icon="mdi mdi-camera-iris me-1" {{ $riskScoreLevelType->is_active == true ? 'selected' : '' }}>{{ $riskScoreLevelType->level }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 my-4">
                        <div id="risk-level-slider-container">
                            <!--risk levels slider element -->
                            <div id="risk-matrix-levels">
                            </div>
                            <p class="clearfix">
                                <span class="float-end max-value-el">
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Acceptable risk score picker UI -->
                <div class="row">
                    <div class="acceptable-risk-scores col-md-2 offset-md-10">
                        <h5>Acceptable risk score</h5>
                        <select id="acceptable-risk-score-picker" data-acceptable-score="{{$riskMatrixAcceptableScore->score}}" data-size="auto" data-live-search="true" name="risk_acceptable_score" class="bootstrap-selectpicker" title="select one" data-style="btn-secondary">
                        </select>
                        <div class="invalid-feedback d-block">
                            @if ($errors->has('acceptable-risk-score'))
                            {{ $errors->first('acceptable-risk-score') }}
                            @endif
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.row -->
        </div>
    </div>


    </form>
</div>
<!-- END OF TAB PAGE -->
