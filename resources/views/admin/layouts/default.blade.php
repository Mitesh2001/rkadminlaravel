<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" {{ Metronic::printAttrs('html') }} {{ Metronic::printClasses('html') }}>
    <head>
        <meta charset="utf-8"/>

        {{-- Title Section --}}
        <title>{{ \Helper::getSetting('app_name') ?? "RKADMIN" }} | @yield('title', $page_title ?? '')</title>

        {{-- Meta Data --}}
        <meta name="description" content="@yield('page_description', $page_description ?? '')"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
		<meta name="csrf-token" content="{{ csrf_token() }}" />
        {{-- Favicon --}}
        <link rel="shortcut icon" href="{{ asset('media/logos/favicon.png') }}" />
		<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('media/logos/apple-touch-icon.png') }}">
		<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('media/logos/favicon-32x32.png') }}">
		<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('media/logos/favicon-16x16.png') }}">
		{{-- <link rel="manifest" href="{{ asset('media/logos/site.webmanifest') }}"> --}}

        {{-- Fonts --}}
        {{ Metronic::getGoogleFontsInclude() }}

        {{-- Global Theme Styles (used by all pages) --}}
        @foreach(config('layout.resources.css') as $style)
            <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($style)) : asset($style) }}?v=1.1.1" rel="stylesheet" type="text/css"/>
        @endforeach

        {{-- Layout Themes (used by all pages) --}}
        @foreach (Metronic::initThemes() as $theme)
            <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($theme)) : asset($theme) }}?v=1.1.1" rel="stylesheet" type="text/css"/>
        @endforeach


        <!-- Styles -->
        <link href="{{ asset('css/custom.css') }}?ver=0.0.3" rel="stylesheet">
        {{-- Includable CSS --}}
        @yield('styles')
    </head>

    <body {{ Metronic::printAttrs('body') }} {{ Metronic::printClasses('body') }}>

        @if (config('layouts.page-loader.type') != '')
            @include('admin.layouts.partials._page-loader')
        @endif

        @include('admin.layouts.base._layout')

        <script>var HOST_URL = "{{-- route('quick-search') --}}";</script>

        {{-- Global Config (global config for global JS scripts) --}}
        <script>
            var KTAppSettings = {!! json_encode(config('layout.js'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) !!};
        </script>

        {{-- Global Theme JS Bundle (used by all pages)  --}}
        @foreach(config('layout.resources.js') as $script)
            <script src="{{ asset($script) }}?v=1.1.7" type="text/javascript"></script>
        @endforeach
		{{-- Global JS Bundle (used by all pages)  --}}
        @foreach(config('layout.resources.globaljs') as $script)
            <script src="{{ ($script) }}?v=1.1.1" type="text/javascript"></script>
        @endforeach


        {{-- Includable JS --}}
        @yield('scripts')

    </body>
</html>

