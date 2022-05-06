@if($loggedInUser->hasAnyRole(['Global Admin', 'Compliance Administrator']))
    <div class="col-lg-4 col-sm-6">
        <a href="{{ route('compliance-projects-create') }}">
            <div class="card">
                <div class="card-body project-box project-div d-flex justify-content-center align-items-center" style="min-height: 15.5rem; font-size: 4rem; color: #323b43;">
                    <i class="mdi mdi-plus"></i>
                </div>
            </div>
        </a>
    </div>
@endif
@forelse($projects as $project)
    <div class="col-lg-4 col-sm-6">
        <div class="card">
            <div class="card-body project-box project-div">
                @if($loggedInUser->hasAnyRole(['Global Admin', 'Compliance Administrator']))
                <div class="dropdown float-end">
                    <a href="#" class="dropdown-toggle card-drop arrow-none" data-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-dots-horizontal m-0 text-muted h3"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('compliance-projects-edit', $project->id) }}">Edit</a>
                        <a class="dropdown-item project-delete-btn" href="{{ route('compliance-projects-delete', $project->id) }}">Delete</a>
                    </div>
                </div> <!-- end dropdown -->
                @endif

                <a href="{{ route('compliance-project-show', [$project->id]) }}" class="text-dark">
                    <!-- Title-->
                    <h4 class="mt-0">{{decodeHTMLEntity($project->name)}}</h4>
                    <!-- <p class="text-muted text-uppercase"><i class="mdi mdi-account-circle"></i> <small>Orange Limited</small></p> -->
                    <p></p>
                    <p>
                        <b>
                            Standard: {{ $project->standard }}
                        </b>
                    </p>
                    <p class="text-muted font-13 mb-3 sp-line-2">
                        {{ decodeHTMLEntity($project->description)}}
                    </p>
                    @php
                    $percentage = 0;
                    $total = count($project->controls()->where('applicable', 1)->get());
                    $implemented = count($project->controls()->where('applicable', 1)->where('status', 'Implemented')->get());
                    $notImplemented = $total - $implemented;

                    if($implemented > 0 && $total > 0)
                    {
                        $percentage = ($implemented/$total) * 100;
                    }

                    @endphp
                    <!-- Task info-->
                    <p class="mb-1">
                        <span class="pe-2 text-nowrap mb-2 d-inline-block">
                            <!-- <i class="mdi mdi-format-list-bulleted-type text-muted"></i> -->
                            <b>{{ count($project->controls) }}</b> Controls
                        </span>
                    </p>
                    <!-- Progress-->
                    <p class="mb-2 fw-bold">Controls Implemented <span class="float-end">{{$implemented}}/{{$total}}</span></p>
                    <div class="progress mb-1" style="height: 7px;">
                        <div class="progress-bar"
                            role="progressbar" aria-valuenow="{{$implemented}}" aria-valuemin="0" aria-valuemax="100"
                            style="width: {{$percentage}}%;">
                        </div><!-- /.progress-bar .progress-bar-danger -->
                    </div><!-- /.progress .no-rounded -->
                </a>
            </div>
        </div>
    </div>
@empty
<!-- <h2>No Projects available</h2> -->
@endforelse
