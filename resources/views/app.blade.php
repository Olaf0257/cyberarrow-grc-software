<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title', decodeHTMLEntity($globalSetting->display_name))</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="api-base-url" content="{{ url('/') }}" />
    <meta name="top-level-data-scope" content='@json($topLevelDataScope)' />
    <meta name="page-title" content="{{ @$pageTitle }}" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset($globalSetting->favicon) }}">

    <!-- css root variables -->
    @include('layouts.partials.root-css-vars')

    <!-- Plugins css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/custom.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background: var(--secondary-color) !important;
        }
        </style>


        @routes(null, csp_nonce())

</head>

    <body class="center-menu @yield('body-class')">
        @inertia
    <!-- React App js-->
    <!-- <link href="{{ mix('/css/app.css') }}" rel="stylesheet" /> -->
    <script src="{{ asset('/js/app.js') }}?v={{substr(microtime(),-10)}}" defer></script>
</body>

</html>
