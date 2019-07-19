<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta property="og:image" content="{{ url('app/assets/img/Solushop.jpg') }}">
		<title>{{ config('app.name') }} - @yield('page-title')</title>
		<meta name="description" content="Solushop is Ghana&#039;s most trusted Online Shopping Mall ➜Shop electronics, accessories, books, fashion &amp; more online ✔ Great customer care ✔ Top quality products ✓ super fast shipping ✓ Order now and enjoy a revolutionary shopping experience!">
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
			#note {
				position: absolute;
				z-index: 101;
				top: 0;
				left: 0;
				right: 0;
				font-size: 16px;
				color: white;
				background: green;
				text-align: center;
				line-height: 2.8;
				overflow: hidden; 
				-webkit-box-shadow: 0 0 5px black;
				-moz-box-shadow:    0 0 5px black;
				box-shadow:         0 0 5px black;
			}
			@-webkit-keyframes slideDown {
				0%, 100% { -webkit-transform: translateY(-50px); }
				10%, 90% { -webkit-transform: translateY(0px); }
			}
			@-moz-keyframes slideDown {
				0%, 100% { -moz-transform: translateY(-50px); }
				10%, 90% { -moz-transform: translateY(0px); }
			}
			.cssanimations.csstransforms #note {
				-webkit-transform: translateY(-50px);
				-webkit-animation: slideDown 5s 1.0s 1 ease forwards;
				-moz-transform:    translateY(-50px);
				-moz-animation:    slideDown 5s 1.0s 1 ease forwards;
			}
			.cssanimations.csstransforms #close {
				display: none;
			}

			#register { display:none; }
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
			$('#toggleLogin').click(function() {
				var ix = $(this).index();
				$('#login').toggle( ix === 1 );
				$('#register').toggle( ix === 0 )
			});
			$('#toggleRegister').click(function() {
				var ix = $(this).index();
				$('#login').toggle( ix === 1 );
				$('#register').toggle( ix === 0 );
			});
		</script>
	</body>
</html>