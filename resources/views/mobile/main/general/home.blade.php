@extends('mobile.layouts.home')
@section('page-title')
    Ghana's Most Trusted Online Store.
@endsection
@section('page-image')
    {{ url('app/assets/img/Solushop.jpg') }}
@endsection
@section('page-description')
    Solushop is Ghana&#039;s most trusted Online Shopping Mall ➜Shop electronics, accessories, books, fashion &amp; more online ✔ Great customer care ✔ Top quality products ✓ super fast shipping ✓ Order now and enjoy a revolutionary shopping experience!
@endsection
@section('page-content')
    <div class="page page-home">
        <div class="preloader-wrap">
            <div class="percentage" id="precent"></div>
        </div>
        @include('mobile.main.general.includes.toolbar')
        <div class="wrap">
            @if(!Auth::check())
				<div class="bts-popup" role="alert">
					<div class="bts-popup-container">
					<img src="{{ url('app/assets/img/WelcomeToTheFamily.png') }}" alt="Welcome to the Solushop Family" style="width:70%; display: inline-block" />
						<p>Yep! It's for real! Sign up today and get <b>¢ 5.00</b> instantly for shopping on our platform! <br><br>Already a member? Login below.</p>
							<a href="{{ route('login') }}" class="external">
								<div class="bts-popup-button">
								Login / Register
								</div>
							</a>
						<a href="#0" class="bts-popup-close img-replace">Close</a>
					</div>
				</div>
			@endif
            <div class="tabs page-content">
                <div id="tab-1" class="tab tab-active">
                    <!-- home -->

                    @include('mobile.main.general.includes.navbar')

                    @include('mobile.main.general.includes.sidebar')

                    <!-- slider -->
                    <div class="slider">
                        <div class="container">
                            <div data-pagination='{"el": ".swiper-pagination"}' data-space-between="10" class="swiper-container swiper-init swiper-container-horizontal">
                                <div class="swiper-pagination"></div>
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <div class="content">
                                            <img src="{{ url('app/assets/img/slider/2.jpg') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="content">
                                            <img src="{{ url('app/assets/img/slider/3.jpg') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="content">
                                            <img src="{{ url('app/assets/img/slider/4.jpg') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="content">
                                            <img src="{{ url('app/assets/img/slider/5.jpg') }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end slider -->

                    <!-- categories home -->
                    <div class="categories-home segments">
                        <div class="container">
                            <div class="section-title">
                                <h3>Hot Categories
                                </h3>
                            </div>
                            <div class="row">
                                <div class="col-25">
                                    <div class="content">
                                        <a href=" {{ url('shop/category/books') }} " class="external">
                                            <i class="ti-book"></i>
                                            <span>Books</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-25">
                                    <div class="content">
                                        <a href=" {{ url('shop/category/womens-fashion') }} " class="external">
                                            <i class="ti-heart"></i>
                                            <span>Women</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-25">
                                    <div class="content">
                                        <a href=" {{ url('shop/category/kitchenware') }} " class="external">
                                            <i class="ti-plug"></i>
                                            <span>Kitchenware</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-25">
                                    <div class="content">
                                        <a href=" {{ url('shop/category/mens-fashion') }} " class="external">
                                            <i class="ti-user"></i>
                                            <span>Men</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-25">
                                    <div class="content">
                                        <a href=" {{ url('shop/category/phones') }} " class="external">
                                            <i class="ti-mobile"></i>
                                            <span>Phones</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-25">
                                    <div class="content">
                                        <a href=" {{ url('shop/category/tablets') }} " class="external">
                                            <i class="ti-tablet"></i>
                                            <span>Tablets</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-25">
                                    <div class="content">
                                        <a href=" {{ url('shop/category/home-and-living') }} " class="external">
                                            <i class="ti-home"></i>
                                            <span>Home</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-25">
                                    <div class="content">
                                        <a href=" {{ url('shop/category/grocery') }} " class="external">
                                            <i class="ti-gift"></i>
                                            <span>Grocery</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end categories home -->

                    <!-- divider -->
                    <div class="divider"></div>
                    <!-- end divider -->

                    @for ($i = 0; $i < sizeof($sections['id']); $i++)
                        <div class="promotion segments">
                            <div data-space-between="10" data-slides-per-view="auto" class="swiper-container swiper-init demo-swiper-auto">
                                <div class="section-title">
                                    <h5>{{ $sections['description'][$i] }}</h5>
                                </div>
                                <div class="swiper-wrapper">
                                    @for ($j = 0; $j < sizeof($sections['products'][$i]); $j++)
                                        <div class="swiper-slide">
                                            <a href="{{ url('shop/'.$sections['products'][$i][$j]['vendor']['username'].'/'.$sections['products'][$i][$j]['product_slug'])}}" class="external">
                                                <div class="content">
                                                    <img src="{{ url('app/assets/img/products/thumbnails/'.$sections['products'][$i][$j]['images'][0]['pi_path'].'.jpg') }}" alt="{{ ucwords($sections['products'][$i][$j]['product_name']) }}">
                                                    <div class="text">
                                                        <a href="{{ url('shop/'.$sections['products'][$i][$j]['vendor']['username'].'/'.$sections['products'][$i][$j]['product_slug'])}}" class="external">
                                                            <p>{{ ucwords($sections['products'][$i][$j]['product_name']) }}</p>
                                                        </a>
                                                        <span class="price">
                                                            GH¢ {{ $sections['products'][$i][$j]['product_selling_price'] - $sections['products'][$i][$j]['product_discount'] }} 
                                                            @if($sections['products'][$i][$j]['product_discount'] > 0)
                                                                <span style="color: #a4a4a4; text-decoration: line-through; margin-right: 5px; font-size: 10px;">
                                                                    GH¢ {{ $sections['products'][$i][$j]['product_selling_price'] }}
                                                                </span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endfor

                    <!-- end home -->

                </div>
            </div>
        </div>
    </div>
@endsection