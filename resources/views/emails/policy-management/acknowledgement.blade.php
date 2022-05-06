@component('mail::layout')
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block"
            style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0;"
            valign="top">
            <p style="margin-bottom: 20px;">Hi {{decodeHTMLEntity($user->first_name)}} {{ decodeHTMLEntity($user->last_name)}},</p>
            <p>You have been enrolled in the <strong>{{ decodeHTMLEntity($campaign->name)}}</strong> policy management campaign. Please visit the link below to read and acknowledge the following policies:</p>
        </td>
    </tr>
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block"
            style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0;"
            valign="top">
            <ol style="line-height: 1.7;">
            @foreach($acknowledgementGroup as $acknowledgement)
                <li>
                    {{ decodeHTMLEntity($acknowledgement->policy->display_name) }}
                </li>
            @endforeach
            </ol>
        </td>
    </tr>
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block"
            style="text-align: center;font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0;"
            valign="top">
            <a class="btn btn-primary" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; text-transform: capitalize; background-color: #6658dd; margin: 0; border-color: #6658dd; border-style: solid; border-width: 8px 16px;margin-bottom: 16px;" href="{{ route('policy-management.campaigns.acknowledgement.show', $acknowledgmentUserToken->token) }}">View policies</a>
        </td>
    </tr>
    <br/>
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block" itemprop="handler" itemscope
            itemtype=""
            style="font-weight: 600;color: #3d4852;font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0; padding:"
            valign="top">
            You must read and acknowledge the policy(ies) by {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $campaign->due_date, 'UTC')->setTimezone($campaign->timezone)->format('d-m-Y h:i A') }}
        </td>
    </tr>
@endcomponent
