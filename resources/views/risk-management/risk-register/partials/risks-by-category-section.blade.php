@if($riskCategories->count() > 0)
@foreach($riskCategories as $category)
    <!--bottom box -->
    <div class="risks__box" id="risk-category-wp_{{ $category->id }}">
        <div class="risk__one riskbox d-flex align-items-center">
            <div class="icon-box d-flex align-items-center">
                <a data-toggle="collapse" href="#risk-{{ $category->id }}" data-id ="{{ $category->id }}" aria-expanded="false" aria-controls="collapseExample" class="expandable-icon-wp risk-category">
                    <i class="icon fas fa-chevron-right expand-icon-w"></i>
                        <h5 class="ms-2 risk-register-title">{{ decodeHTMLEntity($category->name) }}</h5>
                </a>
            </div>

            <div class="items__num ms-auto pt-3">
                <p>{{ $category->registerRisks->count() }} item(s)
                    <sup id="un-updated-risks-{{ $category->id }}">
                        <span class="alert-pill badge bg-danger rounded-pill">{{ $category->registerRisks->where('is_complete', 0)->count() }}</span>
                    </sup>
                </p>
            </div>
        </div>


        <!-- display on toggle -->
        <div class="risk__one-descrip collapse" id="risk-{{ $category->id }}" data-id="{{ $category->id }}">
            <div class="top__text d-flex p-2">
                <h5 class="">Search Risk Items</h5>
                <div class="searchbox animated zoomIn ms-auto">
                    <form method="get">
                        <input  type="text" placeholder="Search by Risk Name" name="risk_name_search_within_category_query"
                                class="search" data-category-id="{{ $category->id }}"><i class="fas fa-search"></i>
                    </form>
                </div>
            </div>

            <!-- risk table -->
            <div class="risk__table border mb-1">
                <table class="table risk-register-table dt-responsive">
                    <thead class="table-light">
                    <tr>
                        <th class="risk__id-width"> Risk ID </th>
                        <th> Risk Name </th>
                        <th class="hide-on-sm hide-on-xs"> Control </th>
                        <th class="hide-on-xs">Likelihood</th>
                        <th class="hide-on-xs hide-on-sm"> Impact</th>
                        <th class="hide-on-xs hide-on-sm"> Inherent Score </th>
                        <th class="hide-on-sm hide-on-xs"> Residual Score </th>
                        <th class="hide-on-sm hide-on-xs"> Action </th>
                    </tr>
                    </thead>

                    <tbody id="risk-items-wp-{{ $category->id }}">
                        @include('risk-management.risk-register.partials.risk-items-section')
                    </tbody>
                </table>
            </div>
            <!-- risk table ends -->
        </div>
        <!-- display on toggle ends -->
    </div>
    <!--bottom box -->
@endforeach
@else
<p class="empty-data-section"> No records found</p>
@endif


