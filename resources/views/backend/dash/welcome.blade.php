<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ThunderCodes</title>\
        <link href="https://fonts.googleapis.com/css?family=Oswald:700" rel="stylesheet">
        <link href="{{ asset('assets/css/welcome.min.css') }}" rel="stylesheet">
    </head>
    <body>
        <h1 data-heading="</"><span>Thun &nbsp; er</span><span>Codes</span></h1>
        <script>setTimeout(function(){window.location.href="{{ route('login') }}";},2000);</script>
    </body>
</html>
