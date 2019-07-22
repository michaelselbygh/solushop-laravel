<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta http-equiv="Content-Security-Policy" content="default-src * 'self' 'unsafe-inline' 'unsafe-eval' data: gap:">
        <title>{{ config('app.name') }} - @yield('page-title')</title>
		<meta name="description" content="@yield('page-description')">
        <meta property="og:image" content="@yield('page-image')">
        <link rel="shortcut icon" type="image/x-icon" href="{{ url('app/assets/img/favicon.ico') }}">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,500i,700,900" rel="stylesheet">
        <link rel="stylesheet" href="{{ url('mobile/css/framework7.min.css') }}">
        <link rel="stylesheet" href="{{ url('mobile/css/framework7-icons.css') }}">
        <link rel="stylesheet" href="{{ url('mobile/css/themify-icons.css') }}">
        <link rel="stylesheet" href="{{ url('mobile/css/style.css') }}">
    </head>
    <body>
        <div id="app">
            <div class="view view-main view-init ios-edges" data-url="/">
                @yield('page-content')
            </div>
        </div>

        <script src="{{ url('mobile/js/framework7.js') }}"></script>
        <script src="{{ url('mobile/js/routes.js') }}"></script>
        <script src="{{ url('mobile/js/app.js') }}"></script>
    </body>
</html>