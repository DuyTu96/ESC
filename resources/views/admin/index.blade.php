<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="{{ asset('dist/img/favicon_admin.ico') }}" />
        <title>{{ config('app.appname_admin') }}</title>
        @routes
        <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
        <link rel="stylesheet" href="{{ mix('dist/css/app.css') }}">
    </head>
    <body class="bg-lightBlue">
        <div id="admin"></div>
        <script src="{{ mix('dist/js/manifest.js') }}"></script>
        <script src="{{ mix('dist/js/vendor.js') }}"></script>
        <script src="{{ mix('dist/js/admin.js') }}"></script>
        <script src="{{ mix('dist/js/main.js') }}"></script>
    </body>
</html>
