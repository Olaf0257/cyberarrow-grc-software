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
            <h3 style="color: #000000 !important; font-size: 16px;font-weight: inherit !important;">{{ decodeHTMLEntity($data['title'])}}</h3>
            <br/>
            <p style="color: #000000 !important;margin-bottom:20px;">
                <b style="color: #000000 !important;">Project Name: </b>{{$data['project']->name}}
                <br>
                <b style="color: #000000 !important;">Standard: </b>{{ $data['project']->standard}}
            </p>
            @foreach($data['projectControls'] as $key=>  $projectControl)
                <b style="color: #000000;">Control Name: </b> {{decodeHTMLEntity($projectControl->name)}}
                <br/>
                <b style="color: #000000;">Control ID: </b> {{$projectControl->controlId}}
                <br>
                <hr>
            @endforeach
        </td>
    </tr>
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block"
            style=" color:#000000; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0; padding: 0 0 20px;"
            valign="top">
            {{ $data['information'] }}
        </td>
    </tr>

@endcomponent

