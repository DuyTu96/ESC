@php
    $config = [
        'appName' => config('app.name'),
        'locale' => $locale = app()->getLocale(),
        'locales' => config('app.locales'),
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{{ $config['appName'] }}</title>
        @routes
    </head>
    <body>
        <div id="portal"></div>
        <script>
            window.config = @json($config);
        </script>
        <script src="{{ mix('dist/js/manifest.js') }}"></script>
        <script src="{{ mix('dist/js/vendor.js') }}"></script>
        <script src="{{ mix('dist/js/main.js') }}"></script>
        <script src="{{ mix('dist/js/portal.js') }}"></script>
    </body>
</html>
