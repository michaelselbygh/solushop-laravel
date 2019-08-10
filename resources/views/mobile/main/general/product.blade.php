@extends('mobile.layouts.product')
@section('page-title'){{ $product['product_name'] }}@endsection
@section('page-image'){{ url('/app/assets/img/products/thumbnails/'.$product['images'][0]['pi_path'].'.jpg') }}@endsection
@section('page-description') Buy {{ $product['product_name'] }} from Solushop - Ghana's Most Trusted Online Store @endsection
@section('page-content')
    <div class="page">
        <div class="page-content">
            <div id="tab-1" class="tab tab-active">
                <!-- home -->
                @include('mobile.main.general.includes.sidebar')
                <!-- link back -->
                <a href="{{ URL::previous() }}" class="link back nav-back external">
                    <i class="ti-arrow-left"></i>
                </a>
                <!-- end link back -->
    
                <!-- product details -->
                <div class="product-details">
                    {{-- <div class="wrap-action">
                        <div class="row">
                            <div class="col-20" style="text-align:center">
                                <div class="content-icon">
                                    <a href="" onclick="document.getElementById('wishlist-form').submit();"><i class="ti-heart"></i></a> 
                                </div>
                            </div>
                            <div class="col-80">
                                <div class="content-button">
                                    @if ($product["stock_status"] == 0) 
                                        @if($product['product_type'] == 0)
                                            <button class="button" onclick="document.getElementById('cart-form').submit();">Add to Cart - GH¢ {{ $product['product_selling_price'] - $product['product_discount'] }}</button>
                                        @elseif($product['product_type'] == 1)
                                            <button class="button bts-popup-trigger" >Add to Cart - GH¢ {{ $product['product_selling_price'] - $product['product_discount'] }}</button>
                                        @endif
                                        
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
                                                        <button class="quantity-button" style='border-radius:15px; display:inline-block;' onclick="document.getElementById('cart-form').submit();"><i style='padding-right:5px; font-size: 18px;' class='ion-android-cart'></i> Add to Cart</button>
                                                    </div>
                                                    <p>A refund to your <b>Solushop Wallet</b> will be done if ordered item is unavailable after order placement.</p>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <button class="button" style="background-color: red" disabled>Out of Stock</button>
                                    @endif
                                    
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="header">
                        <div data-pagination='{"el": ".swiper-pagination"}' data-space-between="0" class="swiper-container swiper-init swiper-container-horizontal">
                            <div class="swiper-pagination"></div>
                            <div class="swiper-wrapper">
                                @for ($i = 0; $i < sizeof($product['images']); $i++)
                                    <div class="swiper-slide">
                                        <div class="content">
                                            <div class="mask"></div>
                                            <img src="{{ url('/app/assets/img/products/main/'.$product['images'][$i]['pi_path'].'.jpg') }}" alt="">
                                        </div>
                                    </div> 
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="wrap-content">
                        <h4>
                            {{ ucwords($product['product_name']) }}
                        </h4>
                        <span class="price">
                                @if($product['product_discount'] > 0)
                                <span class="old-price" style="color: #a4a4a4; text-decoration: line-through; margin-right: 5px; font-size: 10px; font-weight: 300">GH¢ {{ $product['product_selling_price'] }}</span>
                                <span class="new-price">GH¢ {{ $product['product_selling_price'] - $product['product_discount'] }}</span>
                            @else
                                <span class="new-price">GH¢ {{ $product['product_selling_price'] }}</span>
                            @endif
                        </span>
    
                        <!-- small divider -->
                        <div class="small-divider"></div>
                        <!-- end  small divider -->
    
                        <div class="description-product-wrapper section-wrapper">
                            <div class="wrap-title">
                                <h3>Description</h3>
                            </div>
                            @if($product['product_description'] != NULL AND trim($product['product_description']) != "")
                                <p>
                                    {{ $product['product_description'] }}
                                </p>
                                <br>
                            @endif
                            <ul>
                                @for($i=0; $i < sizeof($product['product_features']); $i++)
                                    <li> <i style='color:#f68b1e' class='ti-arrow-circle-right'></i> {{ ucfirst(trim($product['product_features'][$i])) }} </li>
                                @endfor
                            </ul>
                        </div>
                        <div class="description-product-wrapper section-wrapper">
                            <form action="{{ url('shop/'.$product['vendor']['username'].'/'.$product['product_slug']) }}" method="POST" id="cart-form">
                                @csrf
                                @if ($product['variation_show'] == 0 AND $product['stock_status'] == 0) 
                                    <div class="wrap-title">
                                        <h3>Select Variation</h3>
                                    </div>
                                
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
                                    <br>
                                @elseif($product['variation_show'] == 1 AND $product['stock_status'] == 0)
                                    <input type='hidden' value = '{{ $product['skus'][0]['id'] }}' name='product_sku'>
                                @endif

                                <input type='hidden' value = '{{ $product['id'] }}' name='product_id'>
                                <input type='hidden' value = '1' name='product_quantity'>
                                <input type="hidden" name="product_action" value="add_to_cart" />
                            </form>
                            <form action="{{ url('shop/'.$product['vendor']['username'].'/'.$product['product_slug']) }}" method="POST" id='wishlist-form'>
                                @csrf
                                <input type="hidden" name="product_action" value="add_to_wishlist" />
                                <input type="hidden" name="product_id" value="{{ $product['id'] }}" />
                            </form>
                        </div>
                        <div class="row">
                            <div class="col-80">
                                <div class="content-button">
                                    @if ($product["stock_status"] == 0) 
                                        @if($product['product_type'] == 0)
                                            <button class="button" onclick="document.getElementById('cart-form').submit();">Add to Cart - GH¢ {{ $product['product_selling_price'] - $product['product_discount'] }}</button>
                                        @elseif($product['product_type'] == 1)
                                            <button class="button bts-popup-trigger" >Add to Cart - GH¢ {{ $product['product_selling_price'] - $product['product_discount'] }}</button>
                                        @endif
                                        
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
                                                        <button class="quantity-button" style='border-radius:15px; display:inline-block;' onclick="document.getElementById('cart-form').submit();"><i style='padding-right:5px; font-size: 18px;' class='ion-android-cart'></i> Add to Cart</button>
                                                    </div>
                                                    <p>A refund to your <b>Solushop Wallet</b> will be done if ordered item is unavailable after order placement.</p>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <button class="button" style="background-color: red" disabled>Out of Stock</button>
                                    @endif
                                    
                                </div>
                            </div>
                            <div class="col-20" style="text-align:center">
                                <button class="button" onclick="document.getElementById('wishlist-form').submit();" style="background-color: #f68b1e" ><i class="ti-heart" style="margin-right: 0px;"></i></button>
                            </div>
                        </div>
                        <div class="information-product-wrapper section-wrapper">   
                            <div class="wrap-title">
                                <h3>Availability &amp; Delivery</h3>
                            </div>
                            <ul>
                                <li>Status 
                                    <span>
                                        @if ($product['stock_status'] == 0) 
                                            <span style='color: green; font-weight: 450'>In Stock</span>
                                        @else
                                            <span style='color: red; font-weight: 450'>Out of Stock</span>
                                        @endif
                                    </span>
                                </li>
                                <li>Avg. Delivery Duration 
                                    <span>
                                        {{ $product['avg_dd_estimate'] }}
                                    </span>
                                </li>
                                <li>
                                    Estimated Arrival 
                                    <span>
                                        Between {{ $product['avg_dd_date_upper'] }} to {{ $product['avg_dd_date_lower'] }}
                                    </span>
                                </li>
                            </ul>
                        </div>
    
                        <div class="author-wrapper">
                            <a  href="{{ url('shop/'.$product['vendor']['username']) }}" class="external">
                            <img src="{{ url('app/assets/img/icon/vendor.png') }}" alt="">
                                <div class="title-name">
                                    <span class="location" style="font-size: 12px;">Sold by</span>
                                    <h4>{{ $product['vendor']['name'] }}</h4>
                                    <span class="location" style="font-size: 12px;">Joined <span style='font-weight: 450'>{{ $product['vendor_date_joined'] }}</span></span>
                                </div>
                            </a>
                        </div>
                        <a href="{{ url('my-account/messages/'.$product['vendor']['username'].'/'.$product['product_slug']) }}" class="external">
                            <button class="button" style="font-weight: 400"> Ask {{ $product['vendor']['name'] }} a question.</button>
                        </a>
                        <br><br>
                    </div>
                    
                    @if(sizeof($product["related_products"]) > 0 AND !(sizeof($product["related_products"]) == 1 AND $product["related_products"][0]['id'] == $product["id"]))
                        <div class="related-product">
                            <div class="wrap-title">
                                <h3>Related Products</h3>
                            </div>
                            <div data-space-between="10" data-slides-per-view="auto" class="swiper-container swiper-init demo-swiper-auto">
                                <div class="swiper-wrapper">
                                    @for ($i = 0; $i < sizeof($product["related_products"]); $i++)
                                        @if($product["related_products"][$i]["id"] != $product["id"]) 
                                            <div class="swiper-slide">
                                                <a href="{{ url('shop/'.$product['related_products'][$i]['vendor']['username'].'/'.$product['related_products'][$i]['product_slug'])}}" class="external">
                                                    <div class="content">
                                                        <img src="{{ url('app/assets/img/products/thumbnails/'.$product['related_products'][$i]['images'][0]['pi_path'].'.jpg') }}" alt="">
                                                        <div class="text">
                                                            <a href="{{ url('shop/'.$product['related_products'][$i]['vendor']['username'].'/'.$product['related_products'][$i]['product_slug'])}}" class="external"><p>{{ ucwords($product['related_products'][$i]['product_name']) }}</p></a>
                                                            <span class="price">
                                                                GH¢ {{ $product['related_products'][$i]['product_selling_price'] - $product['related_products'][$i]['product_discount'] }} 
                                                                @if($product['related_products'][$i]['product_discount'] > 0)
                                                                    <span style="color: #a4a4a4; text-decoration: line-through; margin-right: 5px; font-size: 10px;">
                                                                        GH¢ {{ $product['related_products'][$i]['product_selling_price'] }}
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="container">
                        <a href="{{ url("/shop") }}" class="external">
                            <button class="button" style="background-color:#f68b1e;">Back to Shop</button>
                        </a>
                    </div>
                    <!-- small divider -->
                    <div class="small-divider"></div>
                    <!-- end  small divider -->
    
                    @if(sizeof($product["reviews"]) > 0)
                        <div class="container">
                            <div class="review-product-wrapper section-wrapper">
                                <div class="wrap-title">
                                    <h3>Product Reviews</h3>
                                </div>
                                @if(sizeof($product["reviews"]) > 0 AND !(sizeof($product["reviews"]) == 1 AND $product['signed_in_customer_review'] == 0))
                                    @for($i=0; $i < sizeof($product["reviews"]); $i++)
                                    <div class="content">
                                        <img src="{{ url('/app/assets/img/comment-author/2.png') }}" alt="">
                                        <div class="text">
                                            <h5>{{ $product['reviews'][$i]['customer']['first_name'] }}</h5>
                                            <span>{{ $product['reviews'][$i]['pr_date'] }}</span>
                                            @if (!is_null($product['reviews'][$i]['pr_comment']) AND trim($product['reviews'][$i]['pr_comment']) != "")
                                                <p>{{ $product['reviews'][$i]['pr_comment'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @endfor
                                @endif
                            </div>
                            
                            <div class="comment-form-wrapper section-wrapper">
                                @if($product['signed_in_customer_purchase'] == 0)
                                    <div class="wrap-title">
                                        <h3>Leave a review</h3>
                                    </div>
                                    <form class="list">
                                        <label>Rating</label>
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
                                        <div class="item-input-wrap">
                                            <textarea placeholder="Comment*" required></textarea>
                                        </div>
                                        <button class="button">Save Review</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="container">
                            <div class="review-product-wrapper section-wrapper">
                            </div>
                            <br><br>
                        </div>
                    @endif
                    @if (session()->has('welcome_message')) 
                        <div id="snackbar">{{ session()->get('welcome_message') }}</div>
                    @endif
                </div>
                
                <!-- end product details -->
            </div>
            @if (session()->has('error_message')) 
                <div id="snackbar">{{ session()->get('error_message') }}</div>
            @elseif (session()->has('success_message')) 
                <div id="snackbar">{{ session()->get('success_message') }}</div>
            @endif
        </div>
    </div>
@endsection