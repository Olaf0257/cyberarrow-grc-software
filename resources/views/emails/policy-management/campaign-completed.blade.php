@component('mail::layout')

    {{-- Body --}}
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block"
            style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0;"
            valign="top">
            <p style="margin-bottom: 20px;">Hi {{ decodeHTMLEntity($campaign->owner->full_name) }},</p>
            <p><strong>{{ decodeHTMLEntity($campaign->name) }}</strong> policy management campaign created by you on {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $campaign->created_at)->format('d-m-Y h:i A') }} having <strong> below </strong> policy(ies) has been completed.</p>
        </td>
    </tr>
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block"
            style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0;"
            valign="top">
            <ul>
            @foreach($campaign->policies as $policy)
                <li>
                    {{decodeHTMLEntity($policy->display_name)}}
                </li>
            @endforeach
            </ul>
        </td>
    </tr>
@endcomponent
