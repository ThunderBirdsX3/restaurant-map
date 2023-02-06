<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    @vite('resources/sass/app.scss')
    @vite('resources/js/app.js')
</head>
<body>
    <div id="app">
        <map-component search-api="{{ route('search') }}"></map-component>
    </div>
</body>
</html>
