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
            @media screen and (max-width: 750px) {
                .owl-carousel .owl-item img {
                width:80%;
                margin:auto;
                }
                .product-img a img.hover-img {
                display: none;
                }
            }

            .img-replace {
                /* replace text with an image */
                display: inline-block;
                overflow: hidden;
                text-indent: 100%; 
                color: transparent;
                white-space: nowrap;
            }
            .bts-popup {
                z-index: 999999999;
                position: fixed;
                left: 0;
                top: 0;
                height: 100%;
                width: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                opacity: 0;
                visibility: hidden;
                -webkit-transition: opacity 0.3s 0s, visibility 0s 0.3s;
                -moz-transition: opacity 0.3s 0s, visibility 0s 0.3s;
                transition: opacity 0.3s 0s, visibility 0s 0.3s;
            }
            .bts-popup.is-visible {
                opacity: 1;
                visibility: visible;
                -webkit-transition: opacity 0.3s 0s, visibility 0s 0s;
                -moz-transition: opacity 0.3s 0s, visibility 0s 0s;
                transition: opacity 0.3s 0s, visibility 0s 0s;
            }

            .bts-popup-container {
                border-radius: 10px;
                position: relative;
                width: 90%;
                max-width: 400px;
                margin: 4em auto;
                background: #f68b1e;
                border-radius: none; 
                text-align: center;
                box-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
                -webkit-transform: translateY(-40px);
                -moz-transform: translateY(-40px);
                -ms-transform: translateY(-40px);
                -o-transform: translateY(-40px);
                transform: translateY(-40px);
                /* Force Hardware Acceleration in WebKit */
                -webkit-backface-visibility: hidden;
                -webkit-transition-property: -webkit-transform;
                -moz-transition-property: -moz-transform;
                transition-property: transform;
                -webkit-transition-duration: 0.3s;
                -moz-transition-duration: 0.3s;
                transition-duration: 0.3s;
            }
            .bts-popup-container img {
                padding: 20px 0 0 0;
            }
            .bts-popup-container p {
                color: white;
                padding: 10px 40px;
            }
            .bts-popup-container .bts-popup-button {
                padding: 5px 25px;
                border-radius: 20px;
                border: 2px solid white;
                    display: inline-block;
                margin-bottom: 10px;
            }

            .bts-popup-container a {
                color: white;
                text-decoration: none;
                text-transform: uppercase;
            }

            .bts-popup-container .bts-popup-close {
                position: absolute;
                top: 8px;
                right: 8px;
                width: 30px;
                height: 30px;
            }
            .bts-popup-container .bts-popup-close::before, .bts-popup-container .bts-popup-close::after {
                content: '';
                position: absolute;
                top: 12px;
                width: 16px;
                height: 3px;
                background-color: white;
            }
            .bts-popup-container .bts-popup-close::before {
                -webkit-transform: rotate(45deg);
                -moz-transform: rotate(45deg);
                -ms-transform: rotate(45deg);
                -o-transform: rotate(45deg);
                transform: rotate(45deg);
                left: 8px;
            }
            .bts-popup-container .bts-popup-close::after {
                -webkit-transform: rotate(-45deg);
                -moz-transform: rotate(-45deg);
                -ms-transform: rotate(-45deg);
                -o-transform: rotate(-45deg);
                transform: rotate(-45deg);
                right: 6px;
                top: 13px;
            }
            .is-visible .bts-popup-container {
                -webkit-transform: translateY(0);
                -moz-transform: translateY(0);
                -ms-transform: translateY(0);
                -o-transform: translateY(0);
                transform: translateY(0);
            }

            

            @media only screen and (min-width: 1170px) {
                .bts-popup-container {
                    margin: 8em auto;
                }
            }

            @media only screen and (max-width: 600px) {
                .single-product{
                    height: 230px;
                }
            }

            .preloader-wrap {
                width: 100%;
                height: 100%;
                position: fixed;
                top: 0; 
                bottom: 0;
                background: white;
                z-index : 1000; 
            }

            .percentage {
                z-index: 100;
                text-align:center;
                color: #001337;
                line-height: 30px;
                font-size : 20px;
                position: absolute;
                margin: auto;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                width: 100px;
                height: 100px;
            }

            .loader:after,
            .percentage:after {
                content: "";
                display: block;
                width: 100%;
                height: 100%;
                position: absolute;
                top: 0;
                left: 0;
            }

            .trackbar {
                width: 100%;
                height: 100%;
                border-radius: 20px;
                color: #001337;
                text-align: center;
                line-height: 30px;
                overflow: hidden;
                position: relative;
                opacity: 0.99;
            }

            .loadbar {
                width: 0%;
                height: 100%;
                background: #f68b1e; /* Stripes Background Gradient */
                box-shadow: 0px 0px 14px 1px #f68b1e; 
                position: absolute;
                top: 0;
                left: 0;
                animation: flicker 5s infinite;
                overflow: hidden;
            }

            .glow {
                width: 0%;
                height: 0%;
                border-radius: 20px;
                box-shadow: 0px 0px 60px 10px #f68b1e;
                position: absolute;
                bottom: -5px;
                animation: animation 5s infinite;
            }

            @keyframes animation {
                10% {
                    opacity: 0.9;
                }
                30% {
                    opacity: 0.86;
                }
                60% {
                    opacity: 0.8;
                }
                80% {
                    opacity: 0.75;
                }
            }

            .wrap { 
                -webkit-background-size: cover; 
                -moz-background-size: cover; 
                -o-background-size: cover; 
                background-size: cover; 
                width: 100%; 
                height: 100%; 
                position: relative;  
                z-index : 1; 
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
        <script src="{{ url('app/assets/js/vendor/jquery-1.12.4.min.js') }}"></script>
        <script>
            var width = 100,
            perfData = window.performance.timing, // The PerformanceTiming interface represents timing-related performance information for the given page.
            EstimatedTime = -(perfData.loadEventEnd - perfData.navigationStart),
            time = parseInt((EstimatedTime/1000)%60)*100;

            // Loadbar Animation
            $(".loadbar").animate({
            width: width + "%"
            }, time);

            // Loadbar Glow Animation
            $(".glow").animate({
            width: width + "%"
            }, time);

            // Percentage Increment Animation
            var PercentageID = $("#precent"),
                    start = 0,
                    end = 100,
                    durataion = time;
                    animateValue(PercentageID, start, end, durataion);
                    
            function animateValue(id, start, end, duration) {
            
                var range = end - start,
                current = start,
                increment = end > start? 1 : -1,
                stepTime = Math.abs(Math.floor(duration / range)),
                obj = $(id);
                
                var timer = setInterval(function() {
                    current += increment;
                    $(obj).text(current + "%");
                //obj.innerHTML = current;
                    if (current == end) {
                        clearInterval(timer);
                    }
                }, stepTime);
            }

            // Fading Out Loadbar on Finised
            setTimeout(function(){
            $('.preloader-wrap').fadeOut(300);
            }, time);


            jQuery(document).ready(function($){
                // setTimeout(() => {
                // 	alert("Test");
                // }, 5000);
                window.onload = function (){
                    setTimeout(() => {
                        
                            $(".bts-popup").addClass('is-visible');
                        
                    }, 10000);
                }
                
                //open popup
                $('.bts-popup-trigger').on('click', function(event){
                    event.preventDefault();
                    $('.bts-popup').addClass('is-visible');
                });
                
                //close popup
                $('.bts-popup').on('click', function(event){
                    if( $(event.target).is('.bts-popup-close') || $(event.target).is('.bts-popup') ) {
                        event.preventDefault();
                        $(this).removeClass('is-visible');
                    }
                });
                //close popup when clicking the esc keyboard button
                $(document).keyup(function(event){
                    if(event.which=='27'){
                        $('.bts-popup').removeClass('is-visible');
                    }
                });
            });
        </script>
    </body>
</html>