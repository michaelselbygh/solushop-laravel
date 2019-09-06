<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title>{{ config('app.name') }} - @yield('page-title')</title>
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
				z-index : 2; 
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
		<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<script>
		  (adsbygoogle = window.adsbygoogle || []).push({
			google_ad_client: "ca-pub-3159494382407876",
			enable_page_level_ads: true
		  });
		</script>
	</head>
	<body>
		<div class="preloader-wrap">
			<div class="percentage" id="precent"></div>
		</div>
	
		<div class="wrap">
			@if(!Auth::check())
				<div class="bts-popup" role="alert">
					<div class="bts-popup-container">
					<img src="{{ url('app/assets/img/WelcomeToTheFamily.png') }}" alt="Welcome to the Solushop Family" width="80%" />
						<p>Yep! It's for real! Sign up today and get <b>¢ 5.00</b> instantly for shopping on our platform! <br><br>Already a member? Login below.</p>
							<a href="{{ route('login') }}">
								<div class="bts-popup-button">
								Login / Register
								</div>
							</a>
						<a href="#0" class="bts-popup-close img-replace">Close</a>
					</div>
				</div>
			@endif

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