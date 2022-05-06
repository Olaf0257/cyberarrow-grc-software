@if($category->registerRisks->count() > 0)
    @php
        $risks = $category->registerRisks()->paginate(5);
        $i = ($risks->currentPage() - 1) * $risks->perPage();

    @endphp

    @foreach($risks as $risk)
        <!-- first risk item -->
        <tr id="risk-item-tr-header_{{ $risk->id }}" class="risk-table">
            <td style="width: 10%;">
                <span class="icon-sec me-2 expandable-icon-wp">
                    <a data-toggle="collapse" href="#expanded__box_{{ $risk->id }}" class ="risk-single-list" data-id ="{{ $risk->id }}" aria-expanded="false" aria-controls="collapseExample">
                        <i class="icon fas fa-chevron-right me-2 expand-icon-w"></i>
                        {{ ++$i }}
                    </a>
                </span>
            </td>
            <td style="width: 46%;">
                <a href="{{ route('risks.register.risks-show', $risk->id) }}"> {{decodeHTMLEntity($risk->name)}}</a>
            </td>
            <td style="width: 5%;" class="hide-on-xs hide-on-sm">
                @if($risk->mappedComplianceControl)
                <a href="{{ route('compliance-project-control-show', [$risk->mappedComplianceControl->project_id, $risk->mappedComplianceControl->id]) }}">
                    {{ $risk->mappedComplianceControl->controlId }}
                </a>
                @else
                    None
                @endif
            </td>
            <td style="width: 10%;" class="hide-on-xs inherent-likelihood-td">{{ $risk->likelihood }} </td>
            <td style="width: 5%;" class="hide-on-xs hide-on-sm inherent-impact-td">{{ $risk->impact }} </td>
            <td style="width: 12%;" class="hide-on-xs hide-on-sm inherent-score-td">{{ $risk->inherent_score }}</td>
            <td style="width: 12%;" class="hide-on-xs hide-on-sm residual-score-td">{{ $risk->residual_score }}</td>
            <td>
                <div class='btn-group dropdown dropstart'>
                    <a href='javascript: void(0);' class='table-action-btn dropdown-toggle arrow-none btn btn-light btn-sm' data-toggle='dropdown'
                    aria-expanded='false'>
                        <i class='mdi mdi-dots-horizontal'></i>
                    </a>
                    <div class='dropdown-menu'>
                        <a class='dropdown-item delete-risk' data-id="{{$risk->id}}" href="{{ route('risks.register.risks-delete', $risk->id) }}">
                        <i class='mdi mdi-delete-forever me-2 text-muted font-18 vertical-middle'></i>Delete</a>
                    </div>
                </div>
            </td>
        </tr>

        <!-- risk details  -->
        <tr>
            <td colspan="7" width="100%">
                <div class="border collapse risk-item-expand p-2" id="expanded__box_{{ $risk->id }}" data-id="{{ $risk->id }}">
                    <form action="{{ route('risks.register.risks-update', $risk->id) }}"
                          method="post"
                          class="risk-update-form"
                          data-category-id="{{ $category->id }}"
                    >
                        @csrf
                        <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-11 col-10">
                            <!-- description box -->
                            <div class="expanded__box-description">
                                <h4 class="">Description:</h4>
                                <p class="m-0 p-0">
                                    {{decodeHTMLEntity($risk->risk_description)}}
                                </p>
                            </div>
                        </div>
                        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-6 col-12">
                            <div class="slider-div py-3">
                                <div class="slider__1">
                                    <span><h5 class="m-0 p-0">Likelihood:</h5></span>
                                    <input type="text" class="js-range-slider likelihood" data-risk-id="{{ $risk->id }}" id="likelihood-slider-el-{{ $risk->id }}" data-from="{{ $risk->likelihood - 1  }}" value="" />
                                    <input type="hidden" name="likelihood" id="likelihood-input-el-{{$risk->id}}" value="{{ $risk->likelihood - 1 }}"/>
                                </div>
                                <div class="slider__2 pt-2">
                                    <h5 class="m-0">Impact:</h5>
                                    <input type="text"  class="js-range-slider impact" data-risk-id="{{ $risk->id }}" id="impact-slider-el-{{ $risk->id }}" data-from="{{$risk->impact - 1}}" value="" />
                                    <input type="hidden" name="impact" id="impact-input-el-{{$risk->id}}" value="{{ $risk->impact - 1 }}"/>
                                </div>
                            </div>

                            <div class="affected_function_select mb-3">
                                <label for="affected-props" class="form-label text-dark">Affected property(ies):</label>
                                @php
                                     $affectedProps = explode(',', $risk->affected_properties);
                                 @endphp 
                                <select class="form-control selectpicker"  multiple="" name="affected_properties[]">

                                    <optgroup label="Common Attributes">
                                        @foreach($risksAffectedProperties['common'] as $risksAffectedProperty)
                                            <option value="{{ $risksAffectedProperty }}" {{ in_array($risksAffectedProperty, $affectedProps) ? 'selected' : '' }}>{{ $risksAffectedProperty }}</option>
                                        @endforeach
                                    </optgroup>
                                     <optgroup label="Other Attributes" class="">
                                        @foreach($risksAffectedProperties['other'] as $risksAffectedProperty)
                                            <option value="{{ $risksAffectedProperty }}" {{ in_array($risksAffectedProperty, $affectedProps) ? 'selected' : '' }}>{{ $risksAffectedProperty }}</option>
                                        @endforeach
                                    </optgroup>
                                 </select>
                            </div>

                            <div class="mb-3">
                                <label for="risk-treatment" class="form-label text-dark">Risk Treatment:</label>
                                <select name="treatment_options" class="selectpicker form-control" data-style="btn-light">
                                    <option value="Mitigate" {{ $risk->treatment_options == 'Mitigate' ? 'selected' : '' }}>Mitigate</option>
                                    <option value="Accept" {{ $risk->treatment_options == 'Accept' ? 'selected' : '' }}>Accept</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="risk-treatment" class="form-label text-dark">Affected function/asset:</label>
                                <input type="text" class="form-control" name="affected_functions_or_assets" value="{{ $risk->affected_functions_or_assets }}">
                            </div>
                        </div>


                        <div class="risk-score-container col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
                        <!-- risk score section -->
                            <div class="risk-score">
                                <div class="riskscore mt-2">
                                    <h4>
                                        Inherent Risk Score:
                                        <br>
                                        <div class="riskscore-value">
                                            <span id="risk_inherent_score_{{ $risk->id }}">{{ $risk->inherent_score }}</span>
                                            <span class="risk-score-tag ms-2 font-xs" id="risk_inherent_level_{{ $risk->id }}" style="color:{{@$risk->InherentRiskScoreLevel->color}}">
                                            {{ @$risk->InherentRiskScoreLevel->name }}
                                            </span>
                                        </div>
                                    </h4>
                                </div>

                                <div class="res-riskscore mt-3">
                                    <h4>
                                        Residual Risk Score:
                                        <br>
                                        <div class="riskscore-value">
                                            <span id="risk_residual_score_{{ $risk->id }}">{{ $risk->residual_score }}</span>
                                            <span id="risk_residual_level_{{ $risk->id }}" class="risk-score-tag ms-2 font-xs" style="color:{{$risk->residualRiskScoreLevel->color}}">
                                                {{ @$risk->residualRiskScoreLevel->name }}
                                            </span>
                                        </div>
                                    </h4>
                                </div>
                                <!-- risk status -->
                                @php

                                    $status = '<span class="risk-score-tag extreme ms-2 font-xs" id="risk_status">Open</span>';
                                    if($risk->status == 'Close')
                                    {
                                        $status = '<span class="risk-score-tag low ms-2 font-xs" id="risk_status">Closed</span>';
                                    }
                                @endphp
                                <div class="mt-3">
                                    <h4>Status: {!! $status !!}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </div>
                    </form>
                </div>
            </td>
        </tr>
        <!-- risk details ends -->

    @endforeach
    <!-- pagination -->
    @if($risks->hasPages())
    <tr  class="risks-pagination-wp" >
        <td colspan="7" data-category-id="{{ $category->id }}">
            {{ $risks->links() }}
        </td>
    </tr>
    @endif
@else
    <tr>
        <td colspan="7">
            <p class="text-center">No records found</p>
        </td>
    </tr>
@endif

