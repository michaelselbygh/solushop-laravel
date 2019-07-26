@extends('mobile.layouts.general')
@section('page-title')
    {{ $vendor['name'] }}
@endsection
@section('page-image')
    {{ url('app/assets/img/vendor-banners'.$vendor['id'].'.jpg') }}
@endsection
@section('page-description')
    Visit {{ $vendor['name'] }} on Solushop today!
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
                                <div class="content section-wrapper" style="min-height:0px;">
                                    <img src="{{ url('app/assets/img/vendor-banner/'.$vendor['id'].'.jpg') }}" alt="">
                                </div>
                                @for ($i = 0; $i < sizeof($vendor['products']); $i++)
                                    @if ($i > 0 AND $i % 2 == 0)
                                        </div>
                                        <div class="row">
                                    @endif
                                    <div class="col-50">
                                        <a href="{{ url('shop/'.$vendor['username'].'/'.$vendor['products'][$i]['product_slug'])}}" class="external">
                                            <div class="content">
                                                <img src="{{ url('app/assets/img/products/thumbnails/'.$vendor['products'][$i]['images'][0]['pi_path'].'.jpg') }}" alt="">
                                                <div class="text">
                                                    <a href="{{ url('shop/'.$vendor['username'].'/'.$vendor['products'][$i]['product_slug'])}}" class="external">
                                                        <p>{{ ucwords($vendor['products'][$i]['product_name']) }}</p>
                                                    </a>
                                                    <span class="price">
                                                        GH¢ {{ $vendor['products'][$i]['product_selling_price'] - $vendor['products'][$i]['product_discount'] }} 
                                                        @if($vendor['products'][$i]['product_discount'] > 0)
                                                            <span style="color: #a4a4a4; text-decoration: line-through; margin-right: 5px; font-size: 10px;">
                                                                GH¢ {{ $vendor['products'][$i]['product_selling_price'] }}
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
                                {{ $vendor['products']->links('mobile.pagination.links') }}
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
    