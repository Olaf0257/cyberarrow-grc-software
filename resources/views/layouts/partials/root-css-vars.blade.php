<style nonce="{{ csp_nonce() }}">
    :root {
        --primary-color : {{ $globalSetting->primary_color }};
        --secondary-color: {{ $globalSetting->secondary_color }};
        --default-text-color: {{ $globalSetting->default_text_color }};
    }


    .primary-bg-color {
        background: var(--primary-color) !important;
    }

    .secondary-bg-color {
        background: var(--secondary-color) !important;
    }

    .default-text-color {
        color: var(--default-text-color) !important;
    }

    .secondary-text-color {
        color : var(--secondary-color) !important;
    }

</style>

<script nonce="{{ csp_nonce() }}" type="text/javascript">

// hover color button
function ColorLuminance(hex, lum) {

    // validate hex string
    hex = String(hex).replace(/[^0-9a-f]/gi, '');
    if (hex.length < 6) {
        hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
    }
    lum = lum || 0;

    // convert to decimal and change luminosity
    var rgb = "#", c, i;
    for (i = 0; i < 3; i++) {
        c = parseInt(hex.substr(i*2,2), 16);
        c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
        rgb += ("00"+c).substr(c.length);
    }

    return rgb;
    }

    var primary_bg_color_hover = ColorLuminance("{{ $globalSetting->primary_color }}", -0.1)
    var secondary_bg_color_hover = ColorLuminance("{{ $globalSetting->secondary_color }}", -0.1)
    var secondary_color_darker = ColorLuminance("{{ $globalSetting->secondary_color }}", -0.2)

    document.documentElement.style.setProperty('--primary-color-hover', primary_bg_color_hover);
    document.documentElement.style.setProperty('--secondary-color-hover', secondary_bg_color_hover);
    document.documentElement.style.setProperty('--secondary-color-darker', secondary_color_darker);


</script>
