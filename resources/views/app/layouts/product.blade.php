<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title>Buy @yield('page-title') on Solushop Ghana</title>
		<meta name="description" content="@yield('page-description')">
		<meta property="og:image" content="@yield('page-image')">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- Place favicon.ico in the root directory -->
		<link rel="shortcut icon" type="image/x-icon" href="{{ url('app/assets/img/favicon.ico') }}">
		<!-- Ionicons Font CSS-->
		<link rel="stylesheet" href="{{ url('app/assets/css/ionicons.min.css') }}">
		<!-- font awesome CSS-->
		<link rel="stylesheet" href="{{ url('app/assets/css/font-awesome.min.css') }}">
		<!-- Animate CSS-->
		<link rel="stylesheet" href="{{ url('app/assets/css/animate.css') }}">
		<!-- UI CSS-->
		<link rel="stylesheet" href="{{ url('app/assets/css/jquery-ui.min.css') }}">
		<!-- Chosen CSS-->
		<link rel="stylesheet" href="{{ url('app/assets/css/chosen.css') }}">
		<!-- Meanmenu CSS-->
		<link rel="stylesheet" href="{{ url('app/assets/css/meanmenu.min.css') }}">
		<!-- Fancybox CSS-->
		<link rel="stylesheet" href="{{ url('app/assets/css/jquery.fancybox.css') }}">
		<!-- Normalize CSS-->
		<link rel="stylesheet" href="{{ url('app/assets/css/normalize.css') }}">
		<!-- Nivo Slider CSS-->
		<link rel="stylesheet" href="{{ url('app/assets/css/nivo-slider.css') }}">
		<!-- Owl Carousel CSS-->
		<link rel="stylesheet" href="{{ url('app/assets/css/owl.carousel.min.css') }}">
		<!-- EasyZoom CSS-->
		<link rel="stylesheet" href="{{ url('app/assets/css/easyzoom.css') }}">
		<!-- Slick CSS-->
		<link rel="stylesheet" href="{{ url('app/assets/css/slick.css') }}">
		<!-- Bootstrap CSS-->
		<link rel="stylesheet" href="{{ url('app/assets/css/bootstrap.min.css') }}">
		<!-- Default CSS -->
		<link rel="stylesheet" href="{{ url('app/assets/css/default.css') }}">
		<!-- Style CSS -->
		<link rel="stylesheet" href="{{ url('app/assets/css/style.css') }}">
		<!-- Responsive CSS -->
		<link rel="stylesheet" href="{{ url('app/assets/css/responsive.css') }}">
		<style>
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
			}

			#radioBtn .notActive{
				color: #f68b1e;
				background-color: #fff;
				border: 1px #f68b1e solid;
			}

			#radioBtn .active{
				color: #fff;
				background-color: #f68b1e;
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
		<!-- Modernizr Js -->
		<script src="{{ url('app/assets/js/vendor/modernizr-2.8.3.min.js') }}"></script>
		<script async src="{{ url('https://www.googletagmanager.com/gtag/js?id=UA-71743571-3') }}"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());
			
			gtag('config', 'UA-71743571-3');

			function gtag_report_conversion(url) {
			var callback = function () {
				if (typeof(url) != 'undefined') {
				window.location = url;
				}
			};
			gtag('event', 'conversion', {
				'send_to': 'AW-745721431/qr-NCKr6u50BENecy-MC',
				'event_callback': callback
			});
			return false;
			}

		</script>
	</head>
	<body>
		<!--[if lt IE 8]>
		<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
		<![endif]-->
		<div class="wrapper">
			<!--Header Area Start-->
			<header>
				<div class="header-container">
					@include("app.main.general.includes.header-top-area")
					@include("app.main.general.includes.header-middle-area")
					@include("app.main.general.includes.header-bottom-area")
					@include("app.main.general.includes.mobile-menu-area")
				</div>
			</header>
			<!--Header Area End-->
			@yield('page-content')
			
			@include("app.main.general.includes.footer")
		</div>
		<!--All Js Here-->
		<!--Jquery 1.12.4-->
		<script src="{{ url('app/assets/js/vendor/jquery-1.12.4.min.js') }}"></script>
		<!--Imagesloaded-->
		<script src="{{ url('app/assets/js/imagesloaded.pkgd.min.js') }}"></script> 
		<!--Isotope-->
		<script src="{{ url('app/assets/js/isotope.pkgd.min.js') }}"></script>       
		<!--Ui js-->
		<script src="{{ url('app/assets/js/jquery-ui.min.js') }}"></script>       
		<!--Countdown-->
		<script src="{{ url('app/assets/js/jquery.countdown.min.js') }}"></script>        
		<!--Counterup-->
		<script src="{{ url('app/assets/js/jquery.counterup.min.js') }}"></script>       
		<!--ScrollUp-->
		<script src="{{ url('app/assets/js/jquery.scrollUp.min.js') }}"></script> 
		<!--Chosen js-->
		<script src="{{ url('app/assets/js/chosen.jquery.js') }}"></script>
		<!--Meanmenu js-->
		<script src="{{ url('app/assets/js/jquery.meanmenu.min.js') }}"></script>
		<!--Instafeed-->
		<script src="{{ url('app/assets/js/instafeed.min.js') }}"></script> 
		<!--EasyZoom-->
		<script src="{{ url('app/assets/js/easyzoom.min.js') }}"></script> 
		<!--Fancybox-->
		<script src="{{ url('app/assets/js/jquery.fancybox.pack.js') }}"></script>       
		<!--Nivo Slider-->
		<script src="{{ url('app/assets/js/jquery.nivo.slider.js') }}"></script>
		<!--Waypoints-->
		<script src="{{ url('app/assets/js/waypoints.min.js') }}"></script>
		<!--Carousel-->
		<script src="{{ url('app/assets/js/owl.carousel.min.js') }}"></script>
		<!--Slick-->
		<script src="{{ url('app/assets/js/slick.min.js') }}"></script>
		<!--Wow-->
		<script src="{{ url('app/assets/js/wow.min.js') }}"></script>
		<!--Bootstrap-->
		<script src="{{ url('app/assets/js/bootstrap.min.js') }}"></script>
		<!--Plugins-->
		<script src="{{ url('app/assets/js/plugins.js') }}"></script>
		<!--Main Js-->
		<script src="{{ url('app/assets/js/main.js') }}"></script>
		<script>
			//initial setup
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

			jQuery(document).ready(function($){
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