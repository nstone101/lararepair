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
        var public_directory = "{{  \Request::path() }}";
        </script>
        <link href="{{asset('pos/css/pos.css')}}" rel="stylesheet">
        <link href="{{asset('pos/css/custom.css')}}" rel="stylesheet">
    </head>
    <body>
        <div id="app">
            <pos-app></pos-app>
        </div>

        <script src="{{ asset('js/lang') }}"></script>
        
        <script src="{{ asset('pos/js/custom.js') }}"></script>
        <script src="{{ asset('pos/js/manifest.js') }}"></script>
        <script src="{{ asset('pos/js/vendor.js') }}"></script>
        <script src="{{ asset('pos/js/app.js') }}"></script>

        <script type="text/javascript">
             function widthFunctions(e) {
                var wh = $(window).height(),
                    leftContainer = $('.left-container').height(),
                    lth = $('#pricing').height();
                    lbh = 370;

                // $('#item-list').css("height", wh - 140);
                // $('#item-list').css("min-height", 515);
                $('#pricing').css("height", (wh - lbh));
                // $('#pricing').css("min-height", 270);
                // $('#product-list').css("height", wh - lth - lbh - 107);
                // $('#product-list').css("min-height", 278);
            }
             $(window).bind("resize", widthFunctions);
            $(document).ready(function () {
                widthFunctions();
            });
        </script>
    </body>
</html>
