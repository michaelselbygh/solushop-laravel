

@extends('app.layouts.home')
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
<section class="slider-area pt-30 white-bg pb-10">
        <div class="container">
            <div class="row">
                @include('app.main.general.success-and-error.message') 

                <!--Categories Menu Start-->
                <div class="col-md-3 col-sm-3">
                    <div class="side-menu">
                        <div class="category-heading">
                            <h2><i class="ion-android-menu"></i><span>Categories</span></h2>
                        </div>
                        <div id="cate-toggle" class="category-menu-list">
                            <ul>
                                @for ($i = 0; $i < sizeof($side_bar_pc_options); $i++)

                                    <li class="right-menu">
                                        <a href="{{url('shop/category/'.$side_bar_pc_options[$i]['pc_slug'])}}">{{ $side_bar_pc_options[$i]['pc_description'] }}</a>
                                        <ul class="cat-mega-menu">
                                            <li>
                                                <ul>
                                                    @for ($j = 0; $j < sizeof($side_bar_pc_options[$i]['pc_sub_category']); $j++)
                                                        <li>
                                                            <a href="{{url('shop/category/'.$side_bar_pc_options[$i]['pc_sub_category'][$j]['pc_slug'])}}">{{ $side_bar_pc_options[$i]['pc_sub_category'][$j]['pc_description'] }}</a>
                                                        </li>
                                                    @endfor
                                                </ul>
                                            </li>
                                        </ul>		
                                    </li>
                                @endfor														    
                            </ul>
                        </div>
                    </div>
                </div>
                <!--Categories Menu End-->

                <!--Slider Start-->
                <div class="col-md-6 col-sm-6" style="padding-left: 0px; padding-right:0px; ">
                    <div class="slider-wrapper theme-default">
                        <div id="slider" class="nivoSlider" style="border-radius: 5px;">
                            <img src="{{ url('app/assets/img/slider/2.jpg') }}"  alt="" title="#htmlcaption" />
                            <img src="{{ url('app/assets/img/slider/3.jpg') }}"  alt="" title="#htmlcaption2" />  
                            <img src="{{ url('app/assets/img/slider/4.jpg') }}"  alt="" title="#htmlcaption3" /> 
                            <img src="{{ url('app/assets/img/slider/5.jpg') }}"  alt="" title="#htmlcaption4" />  
                            <img src="{{ url('app/assets/img/slider/6.jpg') }}"  alt="" title="#htmlcaption5" />
                        </div>

                        <div id="htmlcaption" class="nivo-html-caption">
                            <div class="slider-caption">
                                <div class="slider-text">
                                    <br>
                                    <div class="slider-button">
                                        <a href="shop.php" class="wow button animated fadeInLeft" data-text="Shop now" data-wow-duration="2.5s" data-wow-delay="0.5s">Shop Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="htmlcaption2" class="nivo-html-caption">
                            <div class="slider-caption">
                                <div class="slider-text">
                                    <br>
                                    <div class="slider-button">
                                        <a href="shop.php?CC=1" class="wow button animated fadeInLeft" data-text="Shop now" data-wow-duration="2.5s" data-wow-delay="0.5s">Shop Now</a>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div id="htmlcaption3" class="nivo-html-caption">
                            <div class="slider-caption">
                                <div class="slider-text">
                                    <br>
                                    <div class="slider-button">
                                        <a href="shop.php?CC=10" class="wow button animated fadeInLeft" data-text="Shop now" data-wow-duration="2.5s" data-wow-delay="0.5s">Shop Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="htmlcaption4" class="nivo-html-caption">
                            <div class="slider-caption">
                                <div class="slider-text">
                                    <br>
                                    <div class="slider-button">
                                        <a href="shop.php?CC=8" class="wow button animated fadeInLeft" data-text="Shop now" data-wow-duration="2.5s" data-wow-delay="0.5s">Shop Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="htmlcaption5" class="nivo-html-caption">
                            <div class="slider-caption">
                                <div class="slider-text">
                                    <br>
                                    <div class="slider-button">
                                        <a href="shop.php" class="wow button animated fadeInLeft" data-text="Shop now" data-wow-duration="2.5s" data-wow-delay="0.5s">Shop Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Slider End-->
                <!--Right Side Product Start-->
                <div class="col-md-3 col-sm-3">
                    <!--New arrivals Product Start-->
                    <div class="new-arrivals-product">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="new-arrivals-product-title">
                                    <!--Section Title2 Start-->
                                    <div class="category-heading">
                                        <h2 style="background: #001337">Featured Products</h2>
                                    </div>
                                    <!--Section Title2 End-->
                                    <!--Hot Deal Single Product Start-->
                                    <div class="hot-del-single-product">
                                        <div class="row">
                                            <div class="slide-active2">
                                                <!--Single Product Start-->
                                                @for($i=0; $i < sizeof($featured_products); $i++) 
                                                    <div class="col-md-12">
                                                        <div class="single-product style-2 list">
                                                            <div class="col-4">
                                                                <div class="product-img">
                                                                    <a href="{{ url('shop/'.$featured_products[$i]['vendor']['username'].'/'.$featured_products[$i]['product_slug']) }}">
                                                                        <img class="first-img" src="{{ url('app/assets/img/products/thumbnails/'.$featured_products[$i]['images'][0]['pi_path'].'.jpg') }}" style='border-radius:10px;' alt="">
                                                                        @if (sizeof($featured_products[$i]['images']) > 1) 
                                                                            <img class="hover-img" src="{{ url('app/assets/img/products/thumbnails/'.$featured_products[$i]['images'][1]['pi_path'].'.jpg') }}" style='border-radius:10px;' alt="">
                                                                        @endif
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="product-content">
                                                                    <a href="{{ url('shop/'.$featured_products[$i]['vendor']['username'].'/'.$featured_products[$i]['product_slug']) }}">
                                                                        {{ ucwords($featured_products[$i]['product_name']) }}
                                                                    </a>
                                                                    <div class="product-price">
                                                                        <span class="new-price">GH¢ {{ $featured_products[$i]['product_selling_price'] - $featured_products[$i]['product_discount'] }}</span>
                                                                        @if($featured_products[$i]['product_discount'] > 0)
                                                                            <span class="old-price">GH¢ {{ $featured_products[$i]['product_selling_price'] }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endfor
                                                <!--Single Product End-->
                                            </div>
                                        </div>
                                    </div>
                                    <!--Hot Deal Single Product Start-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--New arrivals Product End--> 
                </div>
                            <!--Right Side Product End-->
            </div>
        </div>
    </section>

    <!--Hot Sections Start-->
    <section class="All Product Area pt-10">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    @for ($i = 0; $i < sizeof($sections['id']); $i++)
                        <div class="desktop-television-product">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="section-title1-border">
                                        <div class="section-title1">
                                            <h3>{{ $sections['description'][$i] }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            
                            <div class="tab-content">
                                <div id="tab1" class="tab-pane fade in active">
                                    <div class="row">
                                        <div class="all-product mb-85  owl-carousel" style="margin-bottom:0px;">
                                            @for ($j = 0; $j < sizeof($sections['products'][$i]); $j++)
                                                <div class="col-md-12 item-col">
                                                    <div class="single-product" style=' text-align:center;'>
                                                        <div class="product-img">
                                                            <a href="{{ url('shop/'.$sections['products'][$i][$j]['vendor']['username'].'/'.$sections['products'][$i][$j]['product_slug'])}}">
                                                                <img class="first-img" src="{{ url('app/assets/img/products/thumbnails/'.$sections['products'][$i][$j]['images'][0]['pi_path'].'.jpg') }}" style='border-radius:10px;' alt="">

                                                                @if (sizeof($sections['products'][$i][$j]['images']) > 1) 
                                                                    <img class="hover-img" src="{{ url('app/assets/img/products/thumbnails/'.$sections['products'][$i][$j]['images'][1]['pi_path'].'.jpg') }}" style='border-radius:10px;' alt="">
                                                                @endif

                                                                @if($sections['products'][$i][$j]['product_discount'] > 0)
                                                                    <span class="sicker">- {{ ceil($sections['products'][$i][$j]['product_discount']/$sections['products'][$i][$j]['product_selling_price'] * 100) }} %</span>
                                                                @endif
                                                        
                                                            </a>
                                                        </div>
                                                        <div class="product-content">
                                                            <a href="{{ url('shop/'.$sections['products'][$i][$j]['vendor']['username'].'/'.$sections['products'][$i][$j]['product_slug'])}}">{{ ucwords($sections['products'][$i][$j]['product_name']) }}</a>
                                                            <div class="product-price">
                                                                <span class="new-price">GH¢ {{ $sections['products'][$i][$j]['product_selling_price'] - $sections['products'][$i][$j]['product_discount'] }}</span>
                                                                @if($sections['products'][$i][$j]['product_discount'] > 0)
                                                                    <span class="old-price">GH¢ {{ $sections['products'][$i][$j]['product_selling_price'] }}</span>
                                                                @endif
                                                            </div>

                                                             
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    @endfor

                </div>
            </div>
        </div>
    </section>
    <!--Hot Sections End-->

    
@endsection