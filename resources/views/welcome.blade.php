<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{config('config.company_name')}}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="base-url" content="{{ url('/') }}" />
        <script>
        var base_url = "{{ url('/') }}";
        var public_directory = "{{ $_SERVER['REQUEST_URI'] }}";
        </script>
        <link href="{{asset('css/app.css')}}" rel="stylesheet">
        <link href="{{asset('css/custom.css')}}" rel="stylesheet">
        <style type="text/css">
            *:not(i) {
                font-size: {{config('config.page_font_size')}} !important;
            }
        </style>
    </head>
    <body>
        <div id="app">
            <main-app></main-app>
        </div>

        <script src="{{ asset('js/lang') }}"></script>
        <script src="{{ asset('js/custom.js') }}"></script>
        <script src="{{ asset('js/manifest.js') }}"></script>
        <script src="{{ asset('js/vendor.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>
        
        <script src="https://maps.googleapis.com/maps/api/js?key={{config('config.google_api_key')}}&libraries=places"></script> 
        
    </body>
</html>
