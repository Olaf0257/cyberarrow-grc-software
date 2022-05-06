<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    @for($i = 2; $i <= count(Request::segments()); $i++)
                    @if(!is_numeric(Request::segment($i)))
                    <li class="breadcrumb-item">
                        <a href="javascript: void(0);">
                            {{ ucwords(str_replace("-", " ", Request::segment($i))) }}
                        </a></li>
                    @endif
                    @endfor
                </ol>
            </div>
            <h4 class="page-title">{{ $pageTitle ?? '' }}</h4>
        </div>
    </div>
</div>   
<!-- end page title -->