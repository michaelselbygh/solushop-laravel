@extends('app.layouts.product')
@section('page-title'){{ $product['product_name'] }}@endsection
@section('page-image'){{ url('/app/assets/img/products/thumbnails/'.$product['images'][0]['pi_path'].'.jpg') }}@endsection
@section('page-description')Buy {{ $product['product_name'] }} from Solushop - Ghana's Most Trusted Online Store @endsection
@section('page-content')
    <div class="heading-banner-area pt-10">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading-banner">
                        <div class="breadcrumbs">
                            <ul>
                                <li><a href="{{ route('home') }}">Home</a><span class="breadcome-separator">></span></li>
                                <li><a href="{{ route('show.shop') }}">Shop</a><span class="breadcome-separator">></span></li>
                                @for ($i = sizeof($product['breadcrumb']) -1; $i >= 0 ; $i--)
                                    <li>
                                        <a href="{{ url($product['breadcrumb'][$i]['url']) }}">
                                            {{ $product['breadcrumb'][$i]['description'] }}
                                        </a>
                                        @if($i != 0)
                                            <span class="breadcome-separator">></span>
                                        @endif
                                    </li>
                                @endfor
                                
                            </ul>
                        </div>
                    </div>
                    @include('app.main.general.success-and-error.message') 
                </div>
            </div>
        </div>
    </div>

    <section class="single-product-area mt-20">
        <div class="container">
            <!--Single Product Info Start-->
            <div class="row">
                <div class="single-product-info mb-50">
                    <!--Single Product Image Start-->
                    <div class="col-md-4 col-sm-4">
                        <!--Product Tab Content Start-->
                        <div class="single-product-tab-content tab-content" style="overflow: hidden;">
                            <div id="w1" class="tab-pane fade in active">
                                <div class="easyzoom easyzoom--overlay">
                                    <a href="{{ url('/app/assets/img/products/main/'.$product['images'][0]['pi_path'].'.jpg') }}">
                                        <img src="{{ url('/app/assets/img/products/main/'.$product['images'][0]['pi_path'].'.jpg') }}" alt="">
                                    </a>
                                </div>
                            </div>
                                
                            @for ($i = 0; $i < sizeof($product['images']); $i++)
                                <div id="w{{ $i + 1 }}" class="tab-pane fade">
                                    <div class="easyzoom easyzoom--overlay">
                                        <a href="{{ url('/app/assets/img/products/main/'.$product['images'][$i]['pi_path'].'.jpg') }}">
                                            <img src="{{ url('/app/assets/img/products/main/'.$product['images'][$i]['pi_path'].'.jpg') }}" alt="">
                                        </a>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        @if(sizeof($product['images']) > 1)
                            <div class="single-product-tab">
                                <div class="single-product-tab-menu owl-carousel"> 
                                    <a data-toggle="tab" href="#w1">
                                        <img src="{{ url('/app/assets/img/products/thumbnails/'.$product['images'][0]['pi_path'].'.jpg') }}" style='border-radius:10px;' alt="">
                                    </a>
                                    @for ($i = 1; $i < sizeof($product['images']); $i++)
                                        <a data-toggle="tab" href="#w{{ $i + 1 }}"><img src="{{ url('/app/assets/img/products/thumbnails/'.$product['images'][$i]['pi_path'].'.jpg') }}" style='border-radius:10px;' alt="">
                                        </a>
                                    @endfor
                                </div>
                            </div>
                        @endif
                        <!--Product Tab Content End-->
                    </div>
                    <!--Single Product Image End-->
                    <div class="col-md-5 col-sm-5">
                        <div class="single-product-content">
                            <div class="form-register-title">
                                <h3>
                                    
                                    {{ ucwords($product['product_name']) }}
                                </h3>
                                
                                <a href='{{ url('my-account/messages/'.$product['vendor']['username'].'/'.$product['product_slug']) }}'>
                                    <i class='fa fa-comments-o' style='font-size:18px; margin-right:3px;'></i> 
                                    Got Questions? Ask the Vendor 
                                </a>
                                <br>
                                <br>
                            </div>

                            <!--Product Price Start-->
                            <div class="single-product-price">
                                @if($product['product_discount'] > 0)
                                    <span class="old-price">GH¢ {{ $product['product_selling_price'] }}</span>
                                    <span class="new-price">GH¢ {{ $product['product_selling_price'] - $product['product_discount'] }}</span>
                                @else
                                    <span class="new-price">GH¢ {{ $product['product_selling_price'] }}</span>
                                @endif
                            </div>
                            <!--Product Price End-->

                            <!--Product Description Start-->
                            <div class="product-description">
                                <p>
                                    @if($product['product_description'] != NULL AND trim($product['product_description']) != "")
                                        {{ $product['product_description'] }}
                                        <br>
                                    @endif
                                    <ul>
                                        @for($i=0; $i < sizeof($product['product_features']); $i++)
                                            <li> <i style='color:#f68b1e' class='ion-ios-checkmark'></i> {{ ucfirst(trim($product['product_features'][$i])) }} </li>
                                        @endfor
                                    </ul>
                                </p>
                            </div>
                            <!--Product Description End-->

                            <!--Product Quantity Start-->
                            <div class="single-product-quantity">
                                <form action="{{ url('shop/'.$product['vendor']['username'].'/'.$product['product_slug']) }}" method="POST">
                                    @csrf
                                    @if ($product['variation_show'] == 0 AND $product['stock_status'] == 0) 
                                        <div class='quantity'>
                                            <label>Quantity</label>
                                            <input class='input-text' style='padding-right:10px;' name='product_quantity' value='1' min='1' max='' type='number'/><span style='padding-left:10px; font-size:13px;'><br><br><label>Select Variation</label>
                                        
                                            <div class="input-group">
                                                <div id="radioBtn" class="btn-group">
                                                    @for ($i = 0; $i < sizeof($product['skus']); $i++)
                                                        @if($product['skus'][$i]['sku_stock_left'] > 0 AND strtolower(trim($product['skus'][$i]['sku_variant_description'])) != "none")
                                                            @if($i == $product['sku_first'])
                                                                <a class="btn rbtn btn-sm active" data-toggle="ProductSKU" data-title="{{ $product['skus'][$i]['id'] }}">
                                                                    {{ $product['skus'][$i]['sku_variant_description'] }}
                                                                </a>
                                                            @else
                                                                <a class="btn rbtn btn-sm notActive" data-toggle="ProductSKU" data-title="{{ $product['skus'][$i]['id'] }}">
                                                                    {{ $product['skus'][$i]['sku_variant_description'] }}
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endfor
                                                    <input type="hidden" name="product_sku" id="ProductSKU" value="{{ $product['skus'][$product['sku_first']]['id'] }}">
                                                </div>
                                            </div>
                                        </span>
                                    </div>
                                    @elseif($product['variation_show'] == 1 AND $product['stock_status'] == 0)
                                        <div class='quantity'>
                                            <label>Quantity</label>
                                                <input class='input-text' style='padding-right:10px;' name='product_quantity' value='1' min='1' max='{{ $product['skus'][0]['sku_stock_left'] }}' type='number'>
                                                @if($product['product_type'] == 0)
                                                    <span style='padding-left:10px; font-size:16px;'>( {{ $product['skus'][0]['sku_stock_left'] }} left )</span>
                                                @endif
                                            <input type='hidden' value = '{{ $product['skus'][0]['id'] }}' name='product_sku'>
                                        </div>
                                    @endif
                                    <span>
                                        @if ($product["stock_status"] == 0) 
                                            @if($product['product_type'] == 0)
                                                <button class="quantity-button" style='float:left; margin-right:10px;' type="submit" name='add_to_cart' value='NoValue'><i style='padding-right:5px; font-size: 18px;' class='ion-android-cart'></i> Add to Cart</button>
                                            @elseif($product['product_type'] == 1)
                                                <button class="quantity-button bts-popup-trigger" style='float:left; margin-right:10px;' type="button"><i style='padding-right:5px; font-size: 18px;' class='ion-android-cart'></i> Add to Cart</button>
                                            @endif
                                            <a href="{{ url("https://api.whatsapp.com/send?phone=233506753093&text=Vendor: ".$product['vendor']['name'].urlencode("\r\n")."Product: ".$product['product_name'].urlencode("\r\n")."Quantity: 1".urlencode("\r\n")."Name: ".$customer_information['whatsapp_name'].urlencode("\r\n")."Address: " ) }}"  target="_blank">
                                                <button class="quantity-button" style='float:left; margin-right:10px; background-color: green' type="button" name='order_via_whatsapp' value='NoValue'><i style='padding-right:5px; font-size: 18px;' class='fa fa-whatsapp'></i>Order Via Whatsapp</button>
                                            </a>
                                            @if (Auth::check()) 
                                                <div class="bts-popup" role="alert" >
                                                    <div class="bts-popup-container"style='background-color:white;'>
                                                        <img src="{{ url('app/assets/img/caution.png') }}" alt="" width="35%" />
                                                        <p style='margin-bottom:0px; padding-bottom:0px;'>
                                                            This item is <b>available only on pre-order.</b><br> We suggest you <b>confirm</b> with the vendor on its <b>availability</b>. At your <b>discretion</b>, you may add item to cart and proceed to checkout.<br><br>
                                                        </p>
                                                        <div class="quantity" style='text-align:center'>
                                                            <a href="{{ url('my-account/messages/'.$product['vendor']['username'].'/'.$product['product_slug']) }}">
                                                                <button class="quantity-button" type='button' style='border-radius:15px; margin-right:20px; display:inline-block;'><i style='padding-right:5px; font-size: 18px;' class='fa fa-comments-o'></i> Ask Vendor</button>
                                                            </a>&nbsp;&nbsp;

                                                            <button class="quantity-button" style='border-radius:15px; display:inline-block;' type="submit" name='add_to_cart' value='NoValue'><i style='padding-right:5px; font-size: 18px;' class='ion-android-cart'></i> Add to Cart</button>
                                                        </div>
                                                        <p>A refund to your <b>Solushop Wallet</b> will be done if ordered item is unavailable after order placement.</p>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <button class="quantity-button" style='float:left; margin-right:10px; background-color:#001337' type="submit" name='unavailable' value='NoValue' disabled><i style='padding-right:5px; font-size: 18px;' class='ion-android-cart'></i>
                                                Out of Stock
                                            </button>
                                        @endif
                                    </span>
                                    <input type="hidden" name="product_action" value="add_to_cart" />
                                    <input type="hidden" name="product_id" value="{{ $product['id'] }}" />
                                </form>
                                <form action="{{ url('shop/'.$product['vendor']['username'].'/'.$product['product_slug']) }}" method="POST">
                                    @csrf
                                    <span>
                                        <input type="hidden" name="product_action" value="add_to_wishlist" />
                                        <input type="hidden" name="product_id" value="{{ $product['id'] }}" />
                                        <button data-toggle="tooltip" style="padding: auto" title="Save For Later" class="sfl-button" type="submit" name='add_to_wishlist'><i style='padding-right:auto; font-size: 20px;' class="ion-android-favorite-outline" ></i></button>
                                    </span>
                                </form>
                            </div>
                            <!--Product Quantity End-->
                        </div>
                    </div>
                    <!--Single Product Content End--> 

                    <!--Vendor Information -->
                    <div class="col-md-3 col-sm-3">
                        <div class="single-product-quantity" style=" padding: 10px; border-radius: 10px; line-height: 16px; background: #f7f7f7 !important;">
                            <h4 style='text-align: center; font-size: 14px;'>
                                <span style='font-size: 12px;'>
                                    Product Availability<br>
                                    @if ($product['product_type'] == 0) 
                                        @if ($product['stock_status'] == 0) 
                                            <span style='color: green; font-weight: 450'>In Stock</span>
                                        @else
                                            <span style='color: red; font-weight: 450'>Out of Stock</span>
                                        @endif
                                    @elseif($product['product_type'] == 1)
                                        Available On <b style='color:red'>Pre-Order</b> only.
                                    @endif
                                </span>
                            </h4><br> 

                            @if($product['purchases'] > 0 OR sizeof($product['reviews']) > 0)
                                <h4 style='text-align: center; font-size: 13px;'>
                                    {{ $product['sales_and_rating_header'] }}
                                </h4>
                                <h5 style="text-align: center; font-weight: 300">
                                    <span style='font-size: 11px;'>
                                        @if($product['purchases'] > 0)
                                            <span style = "text-align: center;">
                                                <span style='font-weight: 450'>{{ $product['purchases'] }}</span> successful 
                                                @if($product['purchases'] > 1)
                                                    {{ str_plural('purchase') }}
                                                @else
                                                    purchase
                                                @endif
                                            </span>
                                        @endif

                                        @if(sizeof($product['reviews']) > 0)
                                            <br><i style="color:#f68b1e" class="ion-ios-star"></i> 
                                            <span style='font-weight: 450'>{{ $product['rating'] }}</span>
                                            ( {{ sizeof($product['reviews']) }}  
                                                @if(sizeof($product['reviews']) > 1)
                                                    {{ str_plural('review') }}
                                                @else
                                                    review
                                                @endif
                                            )
                                        @endif
                                    </span>
                                </h5>
                                <br>
                            @endif

                            <h4 style='text-align: center; font-size: 13px;'>
                                Delivery
                            </h4>

                            <li>
                                <i class='fa fa-clock-o' style='font-size:14px; margin-right:2px; color: #f68b1e;'></i>
                                <span style="font-size: 10px;">
                                    <span style='font-weight: 450'> Avg Shipping Time</span> - {{ $product['avg_dd_estimate'] }}
                                </span>
                            </li>
                            <li><i class='fa fa-truck' style='font-size:14px; margin-right:3px; color: #f68b1e;'></i>
                                <span style="font-size: 10px;">
                                    Arrival between <span style='font-weight: 450'>{{ $product['avg_dd_date_upper'] }}</span> to <span style='font-weight: 450'>{{ $product['avg_dd_date_lower'] }}</span>
                                </span>
                            </li>

                            <li>
                                <i class='fa fa-info-circle' style='font-size:14px; margin-right:3px; color: #f68b1e;'></i>
                                <span style="font-size: 10px;"> Delivery timelines may vary outside Accra.</span>
                            </li>
                        </div><br>

                        <a style='color:#f68b1e' href="{{ url('shop/'.$product['vendor']['username']) }}">
                            <div class='single-product-quantity' style=' padding: 10px; border-radius: 10px; line-height: 16px; background: #f7f7f7 !important; margin-bottom:20px;'>
                                <h4 style='text-align: center; font-size: 14px;'>
                                    <img src='{{ url('app/assets/img/vendor-banner/'.$product['product_vid'].'.jpg') }}' style='width:100%; height:auto; border-radius: 5px;'/>
                                    <br><br>
                                    <span style='font-size: 12px; color:#363f4d;'>Sold By</span> 
                                    <br>
                                    <a style='color:#f68b1e' href="{{ url('shop/'.$product['vendor']['username']) }}"><span style='font-weight: 400; font-size:13px;'>{{ $product['vendor']['name'] }}</span></a>
                                    <br>
                                    <span style='font-size: 11px;'>Joined <span style='font-weight: 450'>{{ $product['vendor_date_joined'] }}</span></span>
                                </h4>
                                <br>

                                @if ($product['vendor_purchases'] > 0 OR isset($product['vendor_rating_count'])) 
                                    <h4 style='text-align: center; font-size: 13px;'>
                                        {{ $product['vendor_sales_and_rating_header'] }}
                                    </h4>
                                    <h5 style="text-align: center; font-weight: 300">
                                        <span style='font-size: 11px;'>
                                            @if($product['vendor_purchases'] > 0)
                                                <span style='font-weight: 450'>{{ $product['vendor_purchases'] }}</span> successful 
                                                @if($product['vendor_purchases'] > 1)
                                                    {{ str_plural('purchase') }}
                                                @else
                                                    purchase
                                                @endif
                                            @endif

                                            @if(isset($product['vendor_rating_count']))
                                                <br>
                                                <span style='padding:2px; border-radius: 5px; color:white; background-color:#f68b1e;'><span style='font-weight: 450'>{{ $product['vendor_rating'] }} % </span></span>&nbsp; 
                                                ( {{ $product['vendor_rating_count'] }}  
                                                    @if($product['vendor_rating_count'] > 1)
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
                    <div>
                </div>
            </div>
            <!--Single Product Info End-->
        </div>
    </section>


    @if(sizeof($product["related_products"]) > 0 AND !(sizeof($product["related_products"]) == 1 AND $product["related_products"][0]['id'] == $product["id"]))
        <section class="related-products-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <!--Section Title1 Start-->
                        <div class="section-title1-border">
                            <div class="section-title1">
                                <h3>Related products</h3>
                            </div>
                        </div> 
                        <!--Section Title1 End-->
                    </div>
                </div>
                <div class="row">
                    <div class="related-products owl-carousel">
                    <!--Single Product Start-->
                    
                        @for($i=0; $i < sizeof($product["related_products"]); $i++) 
                            @if ($product["related_products"][$i]['id'] != $product['id']) 
                                <div class="col-md-12">
                                    <div class="single-product">
                                        <div class="product-img">
                                        <a href="{{ url('shop/'.$product['related_products'][$i]['vendor']['username'].'/'.$product['related_products'][$i]['product_slug'])}}">
                                            <img class="first-img" src="{{ url('app/assets/img/products/thumbnails/'.$product['related_products'][$i]['images'][0]['pi_path'].'.jpg') }}" style='border-radius:10px;' alt="">
                                            @if (sizeof($product['related_products'][$i]['images']) > 1) 
                                                    <img class="hover-img" src="{{ url('app/assets/img/products/thumbnails/'.$product['related_products'][$i]['images'][1]['pi_path'].'.jpg') }}" style='border-radius:10px;' alt="">
                                                @endif

                                                @if($product['related_products'][$i]['product_discount'] > 0)
                                                    <span class="sicker">- {{ ceil($product['related_products'][$i]['product_discount']/$product['related_products'][$i]['product_selling_price'] * 100) }} %</span>
                                                @endif       
                                            </a>
                                        </div>
                                        <div class="product-content">
                                            <a href="{{ url('shop/'.$product['related_products'][$i]['vendor']['username'].'/'.$product['related_products'][$i]['product_slug'])}}">{{ ucwords($product['related_products'][$i]['product_name']) }}</a>
                                            <div class="product-price">
                                                <span class="new-price">GH¢ {{ $product['related_products'][$i]['product_selling_price'] - $product['related_products'][$i]['product_discount'] }}</span>
                                                @if($product['related_products'][$i]['product_discount'] > 0)
                                                    <span class="old-price">GH¢ {{ $product['related_products'][$i]['product_selling_price'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endfor
                        <!--Single Product End-->
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if(sizeof($product["reviews"]) > 0 AND !(sizeof($product["reviews"]) == 1 AND $product['signed_in_customer_review'] == 0))
        <section class="related-products-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <!--Section Title1 Start-->
                        <div class="section-title1-border">
                            <div class="section-title1">
                                <h3>Product Reviews ( {{ sizeof($product["reviews"]) }} )</h3>
                            </div>
                        </div> 
                        <!--Section Title1 End-->
                    </div>
                </div>
                <div class="row">
                    <div class="review-page-comment">
                        <div class="review-comment">
                        <!--Reviews Start-->
                            <ul>
                                @for($i=0; $i < sizeof($product["reviews"]); $i++)
                                    @if (!(Auth::check() AND $product['reviews'][$i]['pr_customer_id'] == Auth::user()->id))
                                    <li>
                                        <div class="product-comment" >
                                                <img src="{{ url('/app/assets/img/comment-author/2.png') }}" class='comment-img' alt="">
                                                <div class="product-comment-content">
                                                    <p><b>{{ $product['reviews'][$i]['customer']['first_name'] }}</b>
                                                        -
                                                        <span>
                                                            {{ $product['reviews'][$i]['pr_date'] }} 
                                                            @if($product['reviews'][$i]['pr_edited'] == 1)
                                                                (Edited)
                                                            @endif
                                                        </span>
                                                        <span class="pro-comments-rating">
                                                            @for($j = 0; $j < $product['reviews'][$i]['pr_rating']; $j++)
                                                                <i class="fa fa-star"></i>
                                                            @endfor
                                                        </span>
                                                    </p>
                                                    @if (!is_null($product['reviews'][$i]['pr_comment']) AND trim($product['reviews'][$i]['pr_comment']) != "") 
                                                        <div class="description">
                                                            <p>{{ $product['reviews'][$i]['pr_comment'] }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                @endfor
                            </ul>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if($product['signed_in_customer_purchase'] == 0)
    <section class="related-products-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <!--Section Title1 Start-->
                        <div class="section-title1-border">
                            <div class="section-title1">
                                <h3>Add a review</h3>
                            </div>
                        </div> 
                        <!--Section Title1 End-->
                    </div>
                </div>
                <div class="row">
                    <div class="review-page-comment">
                        <div class="review-comment">
                        <!--Reviews Start-->
                            <ul>
                                <div class="review-form-wrapper" style="text-align:center;" >
                                    <div class="review-form">
                                        <h3>Add a review </h3><br>
                                        <form action="{{ url('shop/'.$product['vendor']['username'].'/'.$product['product_slug']) }}" method='POST'>
                                            @csrf
                                            <label>Rating</label>
                                            <input type="hidden" name="pid" id='pid' value='{{ $product['id'] }}'/>
                                            <input type="hidden" name="ratingValue" id='ratingValue' value='5'/>
                                            <div class="comment-form-rating">
                                                <div class="stars" data-rating="{{$product['signed_in_customer_review_rating']}}" >
                                                    <span class="star"></span>
                                                    <span class="star"></span>
                                                    <span class="star"></span>
                                                    <span class="star"></span>
                                                    <span class="star"></span>
                                                </div>
                                            </div>
                                            <div class="input-element">
                                                <div class="comment-form-comment">
                                                    <label>Comment</label>
                                                    <textarea name="message" cols="40" rows="8" style="border-radius: 25px;" placeholder="Enter your comments here." required>@if(isset($product['signed_in_customer_review_comment']) AND trim($product['signed_in_customer_review_comment']) != ""){{$product['signed_in_customer_review_comment']}}@endif</textarea>
                                                </div>
                                                <div class="comment-submit" style="text-align: center;">
                                                    <button name='submitRating' type="submit" value="{{ $product['signed_in_customer_review_edited'] }}" class="form-button">Save</button>
                                                </div>
                                            </div>
                                            <input type="hidden" name="product_action" value="add_review" />
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection