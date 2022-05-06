@component('mail::layout')

    {{-- Body --}}
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block"
            style="color:#000000; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0; padding: 0 0 20px;"
            valign="top">
            {{ $data['greeting'] }},
        </td>
    </tr>
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; margin: 0;">
        <td class="content-block"
            style="color:#000000; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0; padding: 0 0 20px;"
            valign="top">
            {!!$data['content1']!!}
            <br/>
            <br/>
            {!!$data['content2']!!}
            @if(isset($data['content3']))
            <br/>
           {!! $data['content3']!!}
            @endif
            <br/>
            {!! $data['content4']!!}
            <br/>
            {!! $data['content5']!!}
            <br/>
            <br/>
            {!! $data['content6']!!}
            </br>
        </td>
    </tr>
@endcomponent
