<!-- only showing campaign add button when active campaigns are filtered -->
@if($campaignStatus == 'active')
<div class="col-lg-4 col-sm-6">
    <a href="" data-toggle="modal" data-target="#add-campaign-modal">
        <div class="card">
            <div class="card-body project-box project-div d-flex justify-content-center align-items-center" style="min-height: 15.5rem; font-size: 4rem; color: #323b43;">
                <i class="mdi mdi-plus"></i>
            </div> 
        </div>
    </a>
</div>
@endif

@forelse($campaigns as $campaign)
    @php
        $completedAcknowledgmentPercentage = 0;
        $acknowledgments = $campaign->acknowledgements;

        $totalAcknowledgment = $acknowledgments->count();
        $completedAcknowledgment = $acknowledgments->where('status', 'completed')->count();

        if($totalAcknowledgment && $completedAcknowledgment){
            $completedAcknowledgmentPercentage = ($completedAcknowledgment/$totalAcknowledgment) * 100;
        }
    @endphp
    <div class="col-lg-4 col-sm-6">
        <div class="card">
            <div class="card-body project-box project-div">
                <div class="dropdown float-end">
                    <a href="#" class="dropdown-toggle card-drop arrow-none" data-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-dots-horizontal m-0 text-muted h3"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item campaign-duplicate-btn" href="{{ route('policy-management.campaigns.get-campaign-data', $campaign->id) }}">Duplicate</a>
                        <a class="dropdown-item campaign-delete-btn" href="{{ route('policy-management.campaigns.delete', $campaign->id) }}">Delete</a>
                    </div>
                </div> <!-- end dropdown -->

                <a href="{{ route('policy-management.campaigns.show', $campaign->id) }}" class="text-dark">
                    <!-- Title-->
                    <h4 class="mt-0">{{ decodeHTMLEntity($campaign->name) }}</h4>
                    <p class="mt-3 clearfix">
                        <b>
                            # policies: {{ $campaign->policies->count() }}
                        </b>

                        @php

                            $nowDateTime = new \DateTime("now", new \DateTimeZone($campaign->timezone) );
                            $campaignLaunchDate = new \DateTime($campaign->launch_date, new \DateTimeZone($campaign->timezone) );


                            if($campaignLaunchDate > $nowDateTime){
                                $campaignStatusBadge = 'bg-danger';
                                $campaignStatusBadgeText = 'Not Started';
                            } else {

                                if($campaign->status == 'active'){
                                    $campaignStatusBadge = 'bg-info';
                                    $campaignStatusBadgeText = 'In progress';
                                } else {
                                    $campaignStatusBadge = 'bg-success';
                                    $campaignStatusBadgeText = 'Completed';
                                }
                            }

                        @endphp

                        <span class="badge {{ $campaignStatusBadge }} float-end">{{ $campaignStatusBadgeText }}</span>
                    </p>
                    @php

                    @endphp
                    <!-- Task info-->
                    <p class="mb-1 row campaign-card-date">
                        <span class="col-12 mb-2 text-nowrap d-inline-block ">
                            <b>Start Date: &nbsp;</b> <span class="text-muted" >{{ date('Y-m-d h:i A', strtotime($campaign->launch_date)) }}</span>
                        </span>

                        <span class="col-12 mb-2 text-nowrap d-inline-block">
                            <b>Due Date: &nbsp;</b> <span class="text-muted">{{ date('Y-m-d h:i A', strtotime($campaign->due_date)) }} </span>
                        </span>
                    </p>
                    <!-- Progress-->
                    <p class="mb-2 fw-bold">Acknowledgement <span class="float-end"></span></p>
                    <div class="progress mb-1" style="height: 7px;">
                        <div class="progress-bar"
                            role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100"
                            style="width: {{$completedAcknowledgmentPercentage}}%;">
                        </div><!-- /.progress-bar .progress-bar-danger -->
                    </div><!-- /.progress .no-rounded -->
                </a>
            </div>
        </div>
    </div>
@empty
@endforelse
