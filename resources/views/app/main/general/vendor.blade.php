@extends('app.layouts.general')
@section('page-title'){{ $vendor['name'] }} @endsection
@section('page-image'){{ url('app/assets/img/vendor-banner/'.$vendor['id'].'.jpg') }}@endsection
@section('page-description')Visit {{ $vendor['name'] }} on Solushop today! @endsection
@section('page-content')
    <!--Heading Banner Area Start-->
    <section class="heading-banner-area pt-10">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading-banner">
                        <div class="breadcrumbs">
                            <ul>
                                <li><a href="{{ route('home') }}">Home</a><span class="breadcome-separator">></span></li>
                                <li><a href="{{ route('show.shop') }}">Shop</a><span class="breadcome-separator">></span></li>
                                <li>{{ $vendor['name'] }}</li>
                            </ul>
                        </div><br>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Heading Banner Area End-->

    <div class="product-list-grid-view-area">
        <div class="container">
            <div class="row">
                <!--Shop Product Area Start-->
                <div class="col-lg-9 col-md-9">
                    <div class="shop-desc-container">
                        <div class="row">
                            <!--Shop Product Image Start-->
                            <div class="col-md-12">
                                
                                <div class="shop-product-img mb-10 img-full">
                                    <img style="border-radius:20px;" src="{{ url('app/assets/img/vendor-banner/'.$vendor['id'].'.jpg') }}"  alt="">
                                </div>
                            </div>
                            <!--Shop Product Image Start-->
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3" style='text-align: center;'>
                    <div class='single-product-quantity' style=' padding: 10px; border-radius: 10px; min-height: 240px; line-height: 16px; background: #f7f7f7 !important; margin-bottom:20px;'>
                        <h4 style='text-align: center; font-size: 14px;'>
                            <img src='{{ url('app/assets/img/icon/vendor.png') }}' style='width:60px; height:auto'/>
                            <br>
                            <a style='color:#f68b1e' href="{{ url('shop/'.$vendor['username']) }}"><span style='font-weight: 400; font-size:13px;'>{{ $vendor['name'] }}</span></a>
                            <br>
                            <span style='font-size: 11px;'>Joined <span style='font-weight: 450'>{{ $vendor['vendor_date_joined'] }}</span></span>
                        </h4>
                        <br>

                        @if($vendor['vendor_purchases'] > 0 OR isset($vendor['vendor_rating_count'])) 
                            <h4 style='text-align: center; font-size: 13px;'>
                                {{ $vendor['vendor_sales_and_rating_header'] }}
                            </h4>
                            <h5 style="text-align: center; font-weight: 300">
                                <span style='font-size: 11px;'>
                                    @if($vendor['vendor_purchases'] > 0)
                                        <span style='font-weight: 450'>{{ $vendor['vendor_purchases'] }}</span> successful 
                                        @if($vendor['vendor_purchases'] > 1)
                                            {{ str_plural('purchase') }}
                                        @else
                                            purchase
                                        @endif
                                    @endif

                                    @if(isset($vendor['vendor_rating_count']))
                                        <br>
                                        <span style='padding:2px; border-radius: 5px; color:white; background-color:#f68b1e;'><span style='font-weight: 450'>{{ $vendor['vendor_rating'] }} % </span></span>&nbsp; 
                                        ( {{ $vendor['vendor_rating_count'] }}  
                                            @if($vendor['vendor_rating_count'] > 1)
                                                {{ str_plural('review') }}
                                            @else
                                                review
                                            @endif
                                        )
                                    @endif
                                </span>
                            </h5>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <!--Shop Product Area Start-->
                <div class="col-lg-12 col-md-12" >
                    @include('app.main.general.success-and-error.message') 
                    <!--Shop Tab Menu Start-->
                    <div class="shop-tab-menu">
                        <div class="row">
                            <!--List & Grid View Menu Start-->
                            <div class="col-md-5 col-sm-5 col-lg-6 col-xs-12">
                                <div class="shop-tab">
                                    <ul>
                                    </ul>
                                </div>
                            </div>
                            <!--List & Grid View Menu End-->
                            <!-- View Mode Start-->
                            <div class="col-md-7 col-sm-7 col-lg-6 hidden-xs text-right">
                                <div class="show-result">
                                    <p>
                                            Showing 
                                            <span style='font-weight: 450'>
                                                {{($vendor['products']->currentPage()-1)* $vendor['products']->perPage() + 1}}
                                            </span>
                                            to 
                                            <span style='font-weight: 450'>
                                                {{ ($vendor['products']->currentPage()-1)* $vendor['products']->perPage() + $vendor['products']->perPage() }} 
                                            </span>
                                            of   
                                            <span style='font-weight: 450'>
                                                {{ $vendor['products']->total() }} 
                                            </span>
                                            records.
                                    </p>
                                </div>
                            </div>
                            <!-- View Mode End-->
                        </div>
                    </div>
                    <!--Shop Tab Menu End-->
                    <!--Shop Product Area Start-->
                    <div class="shop-product-area">
                        <div class="tab-content">
                            <!--Grid View Start-->
                            <div id="grid-view" class="tab-pane fade in active">
                                <div class="row">
                                    <div class="product-container">

                                        <!--Single Product Start-->
                                            @for ($i=0; $i < sizeof($vendor['products']); $i++) 
                                                <div class="col-md-2 col-sm-2 item-col-2">
                                                    <div class="single-product" style='height:290px; text-align:center;'>
                                                        <div class="product-img">
                                                            <a href="{{ url('shop/'.$vendor['products'][$i]['vendor']['username'].'/'.$vendor['products'][$i]['product_slug']) }}">
                                                                <img class="first-img" src="{{ url('app/assets/img/products/thumbnails/'.$vendor['products'][$i]['images'][0]['pi_path'].'.jpg') }}" style='border-radius:10px;' alt="">
                                                                @if (sizeof($vendor['products'][$i]['images']) > 1) 
                                                                    <img class="hover-img" src="{{ url('app/assets/img/products/thumbnails/'.$vendor['products'][$i]['images'][1]['pi_path'].'.jpg') }}" style='border-radius:10px;' alt="">
                                                                @endif

                                                                @if($vendor['products'][$i]['product_discount'] > 0)
                                                                    <span class="sicker">- {{ ceil($vendor['products'][$i]['product_discount']/$vendor['products'][$i]['product_selling_price'] * 100) }} %</span>
                                                                @endif
                                                            </a>
                                                        </div>
                                                        <div class="product-content">
                                                            <a href="{{ url('shop/'.$vendor['products'][$i]['vendor']['username'].'/'.$vendor['products'][$i]['product_slug'])}}">{{ ucwords($vendor['products'][$i]['product_name']) }}</a>
                                                            <div class="product-price">
                                                                <span class="new-price">GH¢ {{ $vendor['products'][$i]['product_selling_price'] - $vendor['products'][$i]['product_discount'] }}</span>
                                                                @if($vendor['products'][$i]['product_discount'] > 0)
                                                                    <span class="old-price">GH¢ {{ $vendor['products'][$i]['product_selling_price'] }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        <!--Single Product End-->
                                    </div>
                                </div>
                            </div>
                            <!--Grid View End-->
                        </div>
                    </div>
                    <!--Shop Product Area End-->

                    <!--Pagination Start-->
                    <div style="width: 100%; text-align: center;">
                        {!! $vendor['products']->render() !!}
                    </div>
                    <!--Pagination End--> 

                </div>
                <!--Shop Product Area End-->
            </div>
        </div>
    </div>
@endsection