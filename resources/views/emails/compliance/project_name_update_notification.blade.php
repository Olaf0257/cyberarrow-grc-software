@component('mail::layout')

    {{-- Body --}}
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block"
            style=" color:#000000; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0;"
            valign="top">
            {{ $data['greeting'] }},
            <br/>
            <br/>
        </td>
    </tr>
    <br/>
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block"
            style=" color: #000000; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0;"
            valign="top">
            <h3 style="color: #000000 !important; font-size: 16px;font-weight: inherit !important; margin-bottom: 0;">{!! $data['title'] !!}</h3>
            <br/>
            <p style="margin-bottom:20px; color: #000000;">
                <b style="color: #000000;">Project Name: </b>{{decodeHTMLEntity($data['projectName'])}}
                <br/>
                <b style="color: #000000;">Standard: </b>{{decodeHTMLEntity($data['standard'])}}
            </p>
            @foreach($data['projectControls'] as $key=>  $projectControl)
                <b style="color: #000000;">Control Name: </b> {{ decodeHTMLEntity($projectControl->name) }}
                <br/>
                <b style="color: #000000;">Control ID: </b> {{ $projectControl->controlId }}
                <br>
                <hr>
            @endforeach
        </td>
    </tr>

@endcomponent



