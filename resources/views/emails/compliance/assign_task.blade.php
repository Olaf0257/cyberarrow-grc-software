@component('mail::layout')
    {{-- Body --}}
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block"
            style=" color:#000000; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0; padding: 0 0 20px;"
            valign="top">
            {{ $data['greeting'] }},
        </td>
    </tr>
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block"
            style=" color: #000000; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0; padding: 0 0 20px;"
            valign="top">
            <h3 style="color: #000000 !important; font-size: 16px;font-weight: inherit !important;">{{ $data['title'] }}</h3>
            <p style="color: #000000 !important;margin-bottom:20px;">
                <b style="color: #000000 !important;">Project Name: </b> {{ decodeHTMLEntity($data['project']->name) }}
                <br>
                <b style="color: #000000 !important;">Standard: </b>{{decodeHTMLEntity($data['project']->standard)}}
            </p>
            @foreach($data['projectControls'] as $key=>  $projectControl)
                <b style="color: #000000;">Control Name: </b> {{decodeHTMLEntity($projectControl->name)}}
                <br/>
                <b style="color: #000000;">Control ID: </b> {{ $projectControl->controlId }}
                <br>
                <b style="color: #000000;">Deadline: </b> {{ date('j M Y', strtotime($projectControl->deadline)) }}
                <br>
                <hr>
            @endforeach
        </td>
    </tr>
    @if(  array_key_exists("action", $data) )
        @if($data['action']['action_title'])
            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
                <td class="content-block"
                    style="color:#000000; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0; padding: 0 0 20px;"
                    valign="top">
                    {{ $data['action']['action_title'] }}
                </td>
            </tr>
        @endif
        @if($data['action']['action_button_text'])
            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
                <td class="content-block" itemprop="handler" itemscope
                    itemtype=""
                    style="text-align: center;font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0; padding: 0 0 20px;"
                    valign="top">
                    <a href="{{ $data['action']['action_url'] }}" class="btn-primary" itemprop="url"
                    style="margin-bottom: 16px;font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; background-color: #6658dd; margin: 0; border-color: #6658dd; border-style: solid; border-width: 8px 16px;margin-bottom: 16px;">
                    {{ $data['action']['action_button_text'] }}
                    </a>
                </td>
            </tr>
        @endif
    @endif
    @if($data['information'])
             <td class="content-block"
            style="color:#000000;font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0; padding: 0 0 20px;"
            valign="top">
            {{decodeHTMLEntity($data['information'])}}
        </td>
    @endif
@endcomponent


