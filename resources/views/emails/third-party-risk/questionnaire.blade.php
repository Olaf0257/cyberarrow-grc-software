@component('mail::layout')
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block"
            style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0;"
        >
            <p style="margin-bottom: 20px;">Hi {{decodeHTMLEntity($vendor->contact_name)}},</p>
            <p>You have been enrolled in the <strong>{{ decodeHTMLEntity($project->name)}}</strong> third party risk
                project. Please visit the link below and complete
                the questionnaire:</p>
        </td>
    </tr>
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block"
            style="text-align: center;font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0;"
        >
            <a class="btn btn-primary"
               style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; text-transform: capitalize; background-color: #6658dd; margin: 0; border-color: #6658dd; border-style: solid; border-width: 8px 16px;margin-bottom: 16px;"
               href="{{ route('third-party-risk.take-questionnaire', $vendor_token->token) }}">
                Questionnaire
            </a>
        </td>
    </tr>
    <br/>
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block" itemprop="handler" itemscope
            itemtype=""
            style="font-weight: 600;color: #3d4852;font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0;"
        >
            You must complete the questionnaire by {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $project->due_date, 'UTC')->setTimezone($project->timezone)->format('d-m-Y h:i A') }}
        </td>
    </tr>
@endcomponent
