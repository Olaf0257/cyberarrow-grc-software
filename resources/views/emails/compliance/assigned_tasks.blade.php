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
            {!! $data['content1']!!}
            <br/>
            <br/>
            {!! $data['content2'] !!}
            <br/>
            {!! $data['content3'] !!}
            <br/>
            {!! $data['content4'] !!}
            <br/>
            @if($data['content5'])
            {!! $data['content5'] !!}
            @endif
            <br/>
            @if($data['content6'])
           {!! $data['content6']!!}
            @endif
            <br/>
            @if(isset($data['content8']))
                {!! $data['content8']!!}
            @endif
            <br/>
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
    @if($data['content7'])
             <td class="content-block"
            style="color:#000000;font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0; padding: 0 0 20px;"
            valign="top">
            {!! $data['content7'] !!}
        </td>
    @endif
@endcomponent
