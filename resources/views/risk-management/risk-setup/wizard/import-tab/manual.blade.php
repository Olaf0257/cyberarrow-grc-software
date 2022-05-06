<!-- nested tab here -->
<div class="nested__tabs">
    <ul class="nav  circular">
        <div class="liner"></div>
        @foreach($riskCategories as $index => $category)
        <li class="nav-item">
            <a href="{{ route('risks.wizard.get-risk-import-risks-list-section') }}" data-category-id="{{ $category->id }}" data-current-category-tab="{{ $index+1 }}" class="nav-link risk-category-tab-nav {{ $loop->first ?  'current-tab active' : 'disabled' }}" title="{{ $category->name }}">
                <span class="round-tabs">
                    {{ $index+1 }}
                    <i data-feather="check"></i>
                </span>
            </a>
            <p> {{ $category->name }}</p>
        </li>
        @endforeach
        <li class="nav-item">
            <a href="{{ route('risks.wizard.get-risk-import-risks-list-section') }}" data-current-category-tab="{{ $riskCategories->count()+1 }}" class="nav-link risk-category-tab-nav disabled" title="Comfirm">
                <span class="round-tabs">
                    {{ $riskCategories->count() +1 }}
                    <i data-feather="check"></i>
                </span>
            </a>
            <p> Confirm</p>
        </li>
    </ul>
</div>

<!-- content for individual tabs -->
<div class="tab-content" >
    <div class="top__head">
        <div class="row ms-4">
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                <div class="top__one"><h5 id="risk-category"> </h5></div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-6">
                <div class="top__two justify-content-lg-center d-flex">
                    <div class="searchbox animated zoomIn">
                        <input  type="text" placeholder="Search by Risk Name" id="search" name="risk_name_search_query" class="search">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-6">
                <div class="top__three d-flex me-3">
                    <h5 class="ms-auto select-all-text">Select All</h5>
                    <div class="checkbox checkbox-success checkbox4 select_all_checkbox">
                        <input id="select_all_risk_items_checkbox" type="checkbox">
                        <label for="select_all_risk_items_checkbox"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="risk-list-wp">

    </div>
</div>

<!-- nested tab ends here -->
