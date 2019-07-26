@extends('app.layouts.general')
@section('page-title')
    Our Amazing Vendors 
@endsection
@section('page-image')
    {{ url('app/assets/img/Solushop.jpg') }}
@endsection
@section('page-description')
    Check out our amazing vendors on Solushop Ghana.
@endsection
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
                                <li>Our Amazing Vendors</li>
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
                    @include('app.main.general.success-and-error.message') 
                    
                    <!--Shop Product Area Start-->
                    <div class="shop-product-area">
                        <div class="tab-content">
                            <!--Grid View Start-->
                            <div id="grid-view" class="tab-pane fade in active">
                                <div class="row">
                                    @for($i = 0; $i < sizeof($vendors); $i++)
                                        <div class='col-md-3 col-sm-3' style='text-align: center;'>
                                            <a style='color:#f68b1e' href="{{ url('shop/'.$vendors[$i]->username) }}">
                                                <div class='single-product-quantity' style=' padding: 10px; border-radius: 10px; line-height: 16px; background: #f7f7f7 !important; margin-bottom:20px; height: 210px;'>
                                                    <h4 style='text-align: center; font-size: 14px;'>
                                                        <img src='{{ url('app/assets/img/vendor-banner/'.$vendors[$i]->vendor_id.'.jpg') }}' style='width:100%; height:auto; border-radius: 5px;'/>
                                                        <br><br>
                                                        <a style='color:#f68b1e' href="{{ url('shop/'.$vendors[$i]->username) }}"><span style='font-weight: 400; font-size:13px;'>{{ $vendors[$i]->name }}</span></a>
                                                        <br>
                                                        <span style='font-size: 11px;'>Joined <span style='font-weight: 450'>{{ $vendors[$i]->vendor_date_joined }}</span></span>
                                                    </h4>
                                                    <br>
                    
                                                    @if ($vendors[$i]->vendor_purchases > 0 OR isset($vendors[$i]->vendor_rating_count)) 
                                                        <h4 style='text-align: center; font-size: 13px;'>
                                                            {{ $vendors[$i]->vendor_sales_and_rating_header }}
                                                        </h4>
                                                        <h5 style="text-align: center; font-weight: 300">
                                                            <span style='font-size: 11px;'>
                                                                @if($vendors[$i]->vendor_purchases > 0)
                                                                    <span style='font-weight: 450'>{{ $vendors[$i]->vendor_purchases }}</span> successful 
                                                                    @if($vendors[$i]->vendor_purchases > 1)
                                                                        {{ str_plural('purchase') }}
                                                                    @else
                                                                        purchase
                                                                    @endif
                                                                @endif
                
                                                                @if(isset($vendors[$i]->vendor_rating_count))
                                                                    <br>
                                                                    <span style='padding:2px; border-radius: 5px; color:white; background-color:#f68b1e;'><span style='font-weight: 450'>{{ $vendors[$i]->vendor_rating }} % </span></span>&nbsp; 
                                                                    ( {{ $vendors[$i]->vendor_rating_count }}  
                                                                        @if($vendors[$i]->vendor_rating_count > 1)
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
                                            </a>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            <!--Grid View End-->
                        </div>
                    </div>
                    <!--Shop Product Area End-->
                </div>
                <!--Shop Product Area End-->
            </div>
        </div>
    </div>
@endsection