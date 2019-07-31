@extends('mobile.layouts.general')
@section('page-title')
    Cart
@endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')
    Your cart on Solushop Ghana.
@endsection
@section('page-content')
    <div class="page">
        @include('mobile.main.general.includes.toolbar')
        <div class="navbar navbar-page">
            <div class="navbar-inner sliding">
                <div class="left">
                    <a href="{{ URL::previous() }}" class="link back">
                        <i class="ti-arrow-left"></i>
                    </a>
                </div>
                <div class="title">
                    Cart
                </div>
            </div>
        </div>
        <div class="page-content">
            <!-- cart -->
            <div class="cart cart-page segments-page">
                <div class="container">
                    @if(!Auth::check())
                        <div class="content" style="text-align:center; margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
                            
                            <div class="error-message">
                                <h6>
                                    Looks like you're not logged in.
                                    <br>
                                    Login below to access your cart.
                                </h6>
                            </div>
                            <a href="{{ route("login") }}" class="external">
                                <button class="button" style="background-color:#f68b1e; width:100%">Login here</button>
                            </a>
                        </div>
                    @else
                        @if(!isset($cart['cart_items']) OR sizeof($cart['cart_items']) < 1)
                            <div class="content" style="text-align:center; margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
                                <div class="error-message">
                                    <h6>
                                        Your cart is empty.
                                    </h6>
                                </div>
                                <a href="{{ route("show.shop") }}" class="external">
                                    <button class="button" style="background-color:#f68b1e; width:100%">Start Shopping here</button>
                                </a>
                            </div>
                        @else
                            <form method="POST" action="{{ route("process.cart.action") }}">
                                @csrf
                                @for($i = 0; $i < sizeof($cart['cart_items']); $i++)
                                    <div class="wrap-content">
                                        <div class="row">
                                            <div class="col-25">
                                                <div class="content-image">
                                                    <a href="{{ url("shop/".$cart["cart_items"][$i]["username"]."/".$cart["cart_items"][$i]["product_slug"]) }}" class="external">
                                                        <img src="{{ url("app/assets/img/products/thumbnails/".$cart["cart_items"][$i]["product_images"][0]["pi_path"].".jpg") }}" alt="">
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-55">
                                                <div class="content-text">
                                                    <a href="{{ url("shop/".$cart["cart_items"][$i]["username"]."/".$cart["cart_items"][$i]["product_slug"]) }}" class="external">
                                                        <p>
                                                            {{ $cart["cart_items"][$i]["product_name"] }}
                                                            @if(trim(strtolower($cart["cart_items"][$i]["sku_variant_description"] )) != "none")
                                                                - {{ $cart["cart_items"][$i]["sku_variant_description"]  }}
                                                            @endif
                                                            <br>
                                                        </p>
                                                    </a>
                                                    <span onclick="removeCartItem('{{ $cart['cart_items_id_array'][$i] }}')" style="font-size: 12px; color: red; cursor: pointer;">× Remove</span>
                                                </div>
                                            </div>
                                            <div class="col-20">
                                                <div class="content-info">
                                                    <span class="price">GH¢ {{ ($cart["cart_items"][$i]["product_selling_price"] - $cart["cart_items"][$i]["product_discount"]) * $cart["ci_quantity"][$i] }}</span>
                                                    <br>
                                                </div>
                                                <input class="input-text" style="text-align:center; width:50px; height:30px; border: 1px solid #ebebeb; border-radius:10px;" name="quantity{{$i}}" value="{{ $cart["ci_quantity"][$i] }}" min="1" max="{{ $cart["cart_items"][$i]["sku_stock_left"] }}" type="number">
                                            </div>                        
                                        </div>
                                    </div>
                                    <input type='hidden' name='sku{{$i}}' value="{{ $cart['cart_items_id_array'][$i] }}">
                                    <!-- small divider -->
                                    <div class="small-divider"></div>
                                    <!-- end  small divider -->
                                @endfor
                                @if (isset(Auth::user()->icono) AND Auth::user()->icono != "NULL" AND Auth::user()->icono != NULL)
                                <div class="wrap-content">
                                        <div class="row">
                                            <div class="col-25">
                                                <div class="content-image">
                                                    <img src="{{ url("app/assets/img/discount-badges/Rookie.png") }}" alt="">
                                                </div>
                                            </div>
                                            <div class="col-55">
                                                <div class="content-text">
                                                    <p>
                                                        1 % Discount from Sales Coupon
                                                        <br>
                                                        <span onclick="document.getElementById('remove-icono').submit();" style="font-size: 12px; color: red; cursor: pointer">× Remove</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-20">
                                                <div class="content-info">
                                                    <span class="price">GH¢ {{ $cart['icono_discount'] }}</span>
                                                    <br>
                                                </div>
                                            </div>                        
                                        </div>
                                    </div>
                                    <!-- small divider -->
                                    <div class="small-divider"></div>
                                @endif
                                <button class="button" type="submit" style="">Update Cart</button>
                                <div class="small-divider"></div>
                                <br>
                                <input type="hidden" name="cart_action" value="update_cart" />
                                <input type='hidden' name='cart_count' value="{{ $customer_information["cart_count"] }}">
                            </form>
                            <form id="remove-icono"  method="POST" action="{{ route("process.cart.action") }}">
                                @csrf
                                <input type="hidden" name="cart_action" value="remove_icono" />
                            </form>
                            <form id="remove-cart-item"  method="POST" action="{{ route("process.cart.action") }}">
                                @csrf
                                <input type="hidden" name="cart_action" value="remove_cart_item" />
                                <input type="hidden" name="cart_item_sku" id="cart_item_sku" value="" />
                            </form>
                        @endif
                        <div class="description-product-wrapper section-wrapper">
                            <div class="wrap-title">
                                <h3>Coupons</h3>
                            </div>
                            <form class="list" method="POST" action="{{ route("process.cart.action") }}">
                                @csrf
                                <input type="hidden" name="cart_action" value="apply_coupon" />
                                <div class="item-input-wrap no-mb">
                                    <input type="text" placeholder="Enter Coupon Code" name="coupon_code" required>
                                </div>
                                <button class="button" type="submit" style="">Apply Coupon</button>
                            </form>
                            <br>
                            @if(isset($cart['sub_total']))
                                <div class="small-divider"></div>
                                <div class="wrap-title">
                                    <h3>Checkout</h3>
                                </div>
                                <a href="{{ route('show.checkout') }}" class="external">
                                    
                                    <button class="button" style="background-color: #f68b1e">Proceed To Checkout ( GH¢ {{ $cart['sub_total'] }} )</button>
                                    
                                </a>
                            @endif
                        </div>
                        <br><br>
                    @endif
                    

                </div>
            </div>
            @if (session()->has('error_message')) 
                <div id="snackbar">{{ session()->get('error_message') }}</div>
            @elseif (session()->has('success_message')) 
                <div id="snackbar">{{ session()->get('success_message') }}</div>
            @endif
            <!-- end cart -->
        </div>
    </div>
    <script>
        function removeCartItem(cartItemSKU)
        {
            document.getElementById('cart_item_sku').value = cartItemSKU;
            document.getElementById('remove-cart-item').submit(); 
        } 
    </script>
    
@endsection    
    