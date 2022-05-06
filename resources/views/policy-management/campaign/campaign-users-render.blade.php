@foreach($campaignUsers as $user)

<!-- first risk item -->
<tr>
    <td>

        <span class="icon-sec me-2 expandable-icon-wp">
            <a data-toggle="collapse" href="#expanded__box_{{ $user->id }}" aria-expanded="false" aria-controls="collapseExample">
                <i class="icon fas expand-icon-w fa-chevron-right me-2"></i>
            </a>
        </span>
    </td>
    <td>
        {{ $user->first_name }}
    </td>
    <td class="hide-on-xs hide-on-sm">
        {{ $user->last_name }}
    </td>
    <td class="hide-on-xs">
        {{ $user->email }}
    </td>
    <td class="hide-on-xs hide-on-sm">
            <span class="badge bg-soft-info text-info" style="background:{{$user->user_acknowledgement_status['color']}}"> {{$user->user_acknowledgement_status['status']}}</span>
    </td>
</tr>

<!-- User activities  -->
<tr class="user-activities-tr">
    <td class="user-activities" colspan="7">
        <div class="collapse px-2 pb-0" id="expanded__box_{{ $user->id }}">
            <h4 class="header-title mb-4">Timeline for  {{ $user->first_name }} {{ $user->last_name }}</h4>
            <ul class="list-group list-group-flush user-activity-lists">
                <li class="list-group-item user-activity-node d-flex align-items-center">
                    <div class="node-icon node-icon-green"><i class="dripicons-rocket"></i></div>
                    <span class="user-activity-node-title mx-2">Campaign created</span>
                    <span class="col-4 col-sm-4 col-md-3 col-lg-2">{{ $campaign->created_at}}</span>
                </li>

                @php
                    $userActivities = $user->activities()->where('campaign_id', $campaign->id)->get();

                @endphp

                @foreach($userActivities as $activity)
                <li class="list-group-item user-activity-node d-flex align-items-center">

                    @switch($activity->type)
                        @case('email-sent')
                        <div class="node-icon bg-success">
                            <i class="dripicons-mail"></i>
                        </div>
                            @break
                        @case('clicked-link')
                        <div class="node-icon bg-primary">
                            <i class="ti-hand-point-up"></i>
                        </div>
                            @break

                        @case('email-sent-error')
                        <div class="node-icon bg-danger">
                            <i class="dripicons-cross"></i>
                        </div>
                            @break
                        @case('policy-acknowledged')
                        <div class="node-icon node-icon-green bg-warning">
                            <i class="fas fa-check"></i>
                        </div>
                            @break
                        @default
                            ''
                    @endswitch


                    <span class="user-activity-node-title mx-2">{{ $activity->activity}}</span>
                    <span class="col-4 col-sm-4 col-md-3 col-xl-2">{{ $activity->created_at}}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </td>
</tr>
<!-- User activities ends -->

@endforeach
<!-- pagination -->
<tr>
    <td colspan="6">
        <div class="float-end campaign-users-pagination">
        {{ $campaignUsers->links() }}
        </div>
    </td>
</tr>
