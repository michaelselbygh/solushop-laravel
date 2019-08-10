@extends('app.layouts.general')
@section('page-title')Shop @if(isset($category['pc_description'])) {{ $category['pc_description'] }} @endif @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Solushop is Ghana&#039;s most trusted Online Shopping Mall ➜Shop electronics, accessories, books, fashion &amp; more online ✔ Great customer care ✔ Top quality products ✓ super fast shipping ✓ Order now and enjoy a revolutionary shopping experience! @endsection
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
                                <li>Shop @if(isset($category['pc_description'])) {{ $category['pc_description'] }} @endif </li>
                            </ul>
                        </div><br>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Heading Banner Area End-->

    <div class="product-list-grid-view-area mt-20">
        <div class="container">
            <div class="row">
                <!--Shop Product Area Start-->
                <div class="col-lg-12 col-md-12" >
                    @if(isset($category['pc_description']))
                        <h3 style="text-align: center;"> {{ $category['pc_description'] }} </h3><br>
                    @endif
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
                                            {{($product->currentPage()-1)* $product->perPage() + 1}}
                                        </span>
                                        to 
                                        <span style='font-weight: 450'>
                                            {{ ($product->currentPage()-1)* $product->perPage() + $product->perPage() }} 
                                        </span>
                                        of   
                                        <span style='font-weight: 450'>
                                            {{ $product->total() }} 
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
                                            @for ($i=0; $i < sizeof($product); $i++) 
                                                <div class="col-md-2 col-sm-2 item-col-2">
                                                    <div class="single-product" style='height:290px; text-align:center;'>
                                                        <div class="product-img">
                                                            <a href="{{ url('shop/'.$product[$i]['vendor']['username'].'/'.$product[$i]['product_slug']) }}">
                                                                <img class="first-img" src="{{ url('app/assets/img/products/thumbnails/'.$product[$i]['images'][0]['pi_path'].'.jpg') }}" style='border-radius:10px;' alt="">
                                                                @if (sizeof($product[$i]['images']) > 1) 
                                                                    <img class="hover-img" src="{{ url('app/assets/img/products/thumbnails/'.$product[$i]['images'][1]['pi_path'].'.jpg') }}" style='border-radius:10px;' alt="">
                                                                @endif

                                                                @if($product[$i]['product_discount'] > 0)
                                                                    <span class="sicker">- {{ ceil($product[$i]['product_discount']/$product[$i]['product_selling_price'] * 100) }} %</span>
                                                                @endif
                                                            </a>
                                                        </div>
                                                        <div class="product-content">
                                                            <a href="{{ url('shop/'.$product[$i]['vendor']['username'].'/'.$product[$i]['product_slug'])}}">{{ ucwords($product[$i]['product_name']) }}</a>
                                                            <div class="product-price">
                                                                <span class="new-price">GH¢ {{ $product[$i]['product_selling_price'] - $product[$i]['product_discount'] }}</span>
                                                                @if($product[$i]['product_discount'] > 0)
                                                                    <span class="old-price">GH¢ {{ $product[$i]['product_selling_price'] }}</span>
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
                        {!! $product->render() !!}
                    </div>
                    <!--Pagination End--> 

                </div>
                <!--Shop Product Area End-->
            </div>
        </div>
    </div>
@endsection