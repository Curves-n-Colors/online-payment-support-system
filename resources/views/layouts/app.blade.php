<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ env('CMS_VERSION') }}</title>

    <link href="{{ asset('assets/img/favicon.ico') }}" rel="icon" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="{{ asset('assets/plugins/bootstrap/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

    @yield('page-specific-style')

    <link href="{{ asset('assets/css/thunder.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

</head>
<body class="fixed-header">
    
    <div class="page-container">

        @include('includes.header')

        <div class="page-content-wrapper">

            <div class="content">
                @yield('content')
            </div>
            
            @include('includes.footer')

        </div>

    </div>

    @include('includes.navigation')

    @yield('page-specific-modal')

    <script src="{{ asset('assets/plugins/pace/pace.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/jquery/jquery-3.2.1.min.js') }}" type="text/javascript"></script>
    <script>$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });</script>
    <script src="{{ asset('assets/plugins/modernizr.custom.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/popper/umd/popper.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/bootstrap/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/jquery/jquery-easy.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/jquery-actual/jquery.actual.min.js') }}"></script>
    <script src="{{ asset('assets/js/notify.min.js') }}"></script>
    
    <script type="text/javascript">
        // navbar menu collapse code when click anywhere in the document
        $(document).ready(function () {
            $(document).click(function (event) {
                var clickover = $(event.target);
                if (!clickover.hasClass("menu-active") && !clickover.hasClass("menu-open")) {
                    $(".fixed-header").removeClass('menu-active');
                    $("div.btn-menu").removeClass('menu-active');
                    $("div.menu-overlay").removeClass('menu-open');
                } 
            });
        });
    </script>
    @yield('page-specific-script')

    <script src="{{ asset('assets/js/thunder.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

    @include('includes.notify')
</body>
</html>