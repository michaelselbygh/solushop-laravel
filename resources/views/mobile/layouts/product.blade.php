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
        <style>
            #snackbar {
                visibility: hidden; /* Hidden by default. Visible on click */
                min-width: 250px; /* Set a default minimum width */
                margin-left: -125px; /* Divide value of min-width by 2 */
                background-color: rgba(0, 19, 55, 0.8); /* Blue black background color */
                color: #fff; /* White text color */
                text-align: center; /* Centered text */
                border-radius: 10px; /* Rounded borders */
                padding: 10px; /* Padding */
                position: fixed; /* Sit on top of the screen */
                z-index: 1000; /* Add a z-index if needed */
                left: 50%; /* Center the snackbar */
                bottom: 70px; /* 30px from the bottom */
            }

            /* Show the snackbar when clicking on a button (class added with JavaScript) */
            #snackbar.show {
                visibility: visible; /* Show the snackbar */
                /* Add animation: Take 0.5 seconds to fade in and out the snackbar.
                However, delay the fade out process for 2.5 seconds */
                -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
                animation: fadein 0.5s, fadeout 0.5s 2.5s;
            }

                /* Animations to fade the snackbar in and out */
            @-webkit-keyframes fadein {
                from {bottom: 0; opacity: 0;}
                to {bottom: 70px; opacity: 1;}
            }

            @keyframes fadein {
                from {bottom: 0; opacity: 0;}
                to {bottom: 70px; opacity: 1;}
            }

            @-webkit-keyframes fadeout {
                from {bottom: 70px; opacity: 1;}
                to {bottom: 0; opacity: 0;}
            }

            @keyframes fadeout {
                from {bottom: 70px; opacity: 1;}
                to {bottom: 0; opacity: 0;}
            }
        </style>
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
        <script>
            function showToast() {
                var x = document.getElementById("snackbar");
                x.className = "show";
                setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
            }


            window.onload = function (){
                setTimeout(() => {
                    
                        showToast();
                    
                }, 1000);
            }
                
        </script>
    </body>
</html>