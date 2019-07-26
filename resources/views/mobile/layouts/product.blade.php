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

			.radio-button{
				width: auto;
				border: 1px solid #ebebeb;
				border-radius: 0px;
				height: 10px;
				font-size: 10px;
				line-height: 10px;
				color: #363f4d;
				padding: 0;
			}

			.sfl-button {
				background: #363f4d;
				box-shadow: none;
				border: 0;
				border-radius: 3px;
				color: #fff;
				display: block;
				font-size: 1em;
				font-weight: 300;
				height: 35px;
				line-height: 35px;
				overflow: hidden;
				padding: 0 10px;
				text-shadow: none;
				text-transform: capitalize;
				text-align: center;
				-webkit-transition: all .4s ease-out;
				-moz-transition: all .4s ease-out;
				-ms-transition: all .4s ease-out;
				-o-transition: all .4s ease-out;
				vertical-align: middle;
				white-space: nowrap;
				background: #f68b1e;
				margin: 0;
			}

			.product-comment .review-form-wrapper{
				margin-left: 25px;
			}

			@media only screen and (max-width: 600px) {
				.comment-img{
					display: none;
				}

				.review-comment > ul > li .product-comment .product-comment-content {
					margin-left: 0px;
					min-height: 65px;
				}

				.product-comment .review-form-wrapper{
					margin-left: 0px;
				}
			}

			.star{
				color: goldenrod;
				font-size: 3.0rem;
				padding: 0 2px; /* space out the stars */
			}
				.star::before{
				content: '\2606';    /* star outline */
				cursor: pointer;
			}
				.star.rated::before{
				/* the style for a selected star */
				content: '\2605';  /* filled star */
			}
				
			.stars{
				counter-reset: rateme 0;   
				font-size: 3.0rem;
				font-weight: 900;
			}
			.star.rated{
				counter-increment: rateme 1;
			}

			.rbtn {
				border-radius: 5px !important;
				margin-top: 2px;
				margin-right: 3px;
				transition: all 0.3s ease-out 0s;
			}

			#radioBtn .notActive{
				color: #f68b1e;
				background-color: #fff;
				border: 1px #f68b1e solid;
				padding: 10px;
			}

			#radioBtn .active{
				color: #fff;
				background-color: #f68b1e;
				border: 1px #f68b1e solid;
				padding: 10px;
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
				border-radius: 20px;
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
			}
			.bts-popup-container p {
				color: #363f4d;;
				padding: 10px 40px;
			}
			.bts-popup-container .bts-popup-button {
				padding: 5px 25px;
				border-radius: 20px;
				border: 2px solid #363f4d;;
					display: inline-block;
				margin-bottom: 10px;
			}

			.bts-popup-container a {
				color: #363f4d;;
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
              
            document.addEventListener('DOMContentLoaded', function(){
				let stars = document.querySelectorAll('.star');
				stars.forEach(function(star){
					star.addEventListener('click', setRating); 
				});
				
				let rating = parseInt(document.querySelector('.stars').getAttribute('data-rating'));
				let target = stars[rating - 1];
				target.dispatchEvent(new MouseEvent('click'));
			});

			$('#radioBtn a').on('click', function(){
				var sel = $(this).data('title');
				var tog = $(this).data('toggle');
				$('#'+tog).prop('value', sel);
				
				$('a[data-toggle="'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
				$('a[data-toggle="'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
			})

			function setRating(ev){
				let span = ev.currentTarget;
				let stars = document.querySelectorAll('.star');
				let match = false;
				let num = 0;
				stars.forEach(function(star, index){
					if(match){
						star.classList.remove('rated');
					}else{
						star.classList.add('rated');
					}
					//are we currently looking at the span that was clicked
					if(star === span){
						match = true;
						num = index + 1;
					}
				});
				document.querySelector('.stars').setAttribute('data-rating', num);
				document.getElementById("ratingValue").value = num;
			}
        </script>
    </body>
</html>