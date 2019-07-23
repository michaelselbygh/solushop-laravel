@extends('mobile.layouts.general')
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
        <div class="toolbar tabbar tabbar-labels toolbar-bottom">
            <div class="toolbar-inner">
                <a href="/" class="tab-link tab-link-active external">
                    <i class="ti-home"></i>
                    <span class="tabbar-label">Home</span>
                </a>
                <a href="/shop" class="tab-link external">
                    <i class="ti-gift"></i>
                    <span class="tabbar-label">Shop</span>
                </a>
                <a href="/cart" class="tab-link external">
                    <i class="ti-shopping-cart"></i>
                    <span class="tabbar-label">Cart</span>
                </a>
                <a href="/wishlist" class="tab-link external">
                    <i class="ti-heart "></i>
                    <span class="tabbar-label">Wishlist</span>
                </a>
                <a href="/my-account/" class="tab-link external">
                    <i class="ti-user"></i>
                    <span class="tabbar-label">Account</span>
                </a>
            </div>
        </div>
        <div class="tabs page-content">
            <div id="tab-1" class="tab tab-active">
                <!-- home -->

                <!-- navbar home -->
                <div class="navbar navbar-home">
                    <div class="navbar-inner sliding">
                        <div class="left">
                            <a href="#" class="panel-open" data-panel="left"><i class="ti-align-left"></i></a>
                        </div>
                        <div class="title">
                            <form class="searchbar">
                                <div class="searchbar-input-wrap">
                                    <input type="search" placeholder="Search">
                                    <i class="searchbar-icon"></i>
                                    <span class="input-clear-button"></span>
                                </div>
                                <span class="searchbar-disable-button">Cancel</span>
                            </form>
                        </div>
                        <div class="right">
                            <a href="/notification/"><i class="ti-bell"></i></a>
                            <span></span>
                        </div>
                    </div>
                </div>
                <!-- end navbar home -->

                <!-- sidebar -->
                <div class="panel panel-left panel-cover">
                    <div class="list links-list">
                        <ul>
                            <li>
                                <a href="/product/" class="panel-close">
                                    <div class="item-media">
                                        <i class="ti-shopping-cart"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Product
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="/categories/" class="panel-close">
                                    <div class="item-media">
                                        <i class="ti-layers-alt"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Categories
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="/blog/" class="panel-close">
                                    <div class="item-media">
                                        <i class="ti-rss"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Blog
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="/order-history/" class="panel-close">
                                    <div class="item-media">
                                        <i class="ti-time"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Order History
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="/tracking-order/" class="panel-close">
                                    <div class="item-media">
                                        <i class="ti-truck"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Tracking Order
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="/settings/" class="panel-close">
                                    <div class="item-media">
                                        <i class="ti-settings"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Settings
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="/sign-in/" class="panel-close">
                                    <div class="item-media">
                                        <i class="ti-shift-right"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Sign In
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="/sign-up/" class="panel-close">
                                    <div class="item-media">
                                        <i class="ti-plus"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Sign Up
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="/index/" class="panel-close">
                                    <div class="item-media">
                                        <i class="ti-power-off"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Logout
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- end sidebar -->

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
                                    <a href="/categories-details/">
                                        <i class="ti-crown"></i>
                                        <span>Fashion</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-25">
                                <div class="content">
                                    <a href="/categories-details/">
                                        <i class="ti-slice"></i>
                                        <span>Food</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-25">
                                <div class="content">
                                    <a href="/categories-details/">
                                        <i class="ti-car"></i>
                                        <span>Automotive</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-25">
                                <div class="content">
                                    <a href="/categories-details/">
                                        <i class="ti-camera"></i>
                                        <span>Camera</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-25">
                                <div class="content">
                                    <a href="/categories-details/">
                                        <i class="ti-plug"></i>
                                        <span>Tech</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-25">
                                <div class="content">
                                    <a href="/categories-details/">
                                        <i class="ti-basketball"></i>
                                        <span>Sport</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-25">
                                <div class="content">
                                    <a href="/categories-details/">
                                        <i class="ti-desktop"></i>
                                        <span>Electronic</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-25">
                                <div class="content">
                                    <a href="/categories-details/">
                                        <i class="ti-plus"></i>
                                        <span>Health</span>
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
                                        <a href="{{ url('shop/'.$sections['products'][$i][$j]['vendor_slug'].'/'.$sections['products'][$i][$j]['product_slug'])}}" class="external">
                                            <div class="content">
                                                <img src="{{ url('app/assets/img/products/thumbnails/'.$sections['products'][$i][$j]['images'][0]['pi_path'].'.jpg') }}" alt="{{ ucwords($sections['products'][$i][$j]['product_name']) }}">
                                                <div class="text">
                                                    <a href="{{ url('shop/'.$sections['products'][$i][$j]['vendor_slug'].'/'.$sections['products'][$i][$j]['product_slug'])}}" class="external">
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
@endsection