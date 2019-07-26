@extends('mobile.layouts.general')
@section('page-title')
    Shop
@endsection
@section('page-image')
    {{ url('app/assets/img/Solushop.jpg') }}
@endsection
@section('page-description')
    Solushop is Ghana&#039;s most trusted Online Shopping Mall ➜Shop electronics, accessories, books, fashion &amp; more online ✔ Great customer care ✔ Top quality products ✓ super fast shipping ✓ Order now and enjoy a revolutionary shopping experience!
@endsection
@section('page-content')
    <div class="page page-home">
        @include('mobile.main.general.includes.toolbar')
        @include('mobile.main.general.includes.navbar')
        <div class="page-content">
            <!-- product -->
            <div class="product segments-page">
                <div class="tabs">
                    <div id="tab-11" class="tab tab-active">
                        @include('mobile.main.general.includes.sidebar')
                        <div class="container">
                            <div class="row">
                                @for ($i = 0; $i < sizeof($product); $i++)
                                    @if ($i > 0 AND $i % 2 == 0)
                                        </div>
                                        <div class="row">
                                    @endif
                                    <div class="col-50">
                                        <a href="{{ url('shop/'.$product[$i]['vendor']['username'].'/'.$product[$i]['product_slug'])}}" class="external">
                                            <div class="content">
                                                <img src="{{ url('app/assets/img/products/thumbnails/'.$product[$i]['images'][0]['pi_path'].'.jpg') }}" alt="">
                                                <div class="text">
                                                    <a href="{{ url('shop/'.$product[$i]['vendor']['username'].'/'.$product[$i]['product_slug'])}}" class="external">
                                                        <p>{{ ucwords($product[$i]['product_name']) }}</p>
                                                    </a>
                                                    <span class="price">
                                                        GH¢ {{ $product[$i]['product_selling_price'] - $product[$i]['product_discount'] }} 
                                                        @if($product[$i]['product_discount'] > 0)
                                                            <span style="color: #a4a4a4; text-decoration: line-through; margin-right: 5px; font-size: 10px;">
                                                                GH¢ {{ $product[$i]['product_selling_price'] }}
                                                            </span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endfor
                            </div>
                            <div class="pagination" style="text-align: center;">
                                {{ $product->links('mobile.pagination.links') }}
                            </div>
                            @if (session()->has('welcome_message')) 
                                <div id="snackbar">{{ session()->get('welcome_message') }}</div>
                            @endif
                            <br><br><br>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end product -->
        </div>
    </div>
@endsection    
    