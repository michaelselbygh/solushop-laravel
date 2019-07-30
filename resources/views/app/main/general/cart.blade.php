@extends('app.layouts.general')
@section('page-title')
    Cart
@endsection
@section('page-image')
    {{ url('app/assets/img/Solushop.jpg') }}
@endsection
@section('page-description')
    Your cart on Solushop Ghana.
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
                                <li>Cart</li>
                            </ul>
                        </div><br>
                    </div>
                    @include('app.main.general.success-and-error.message') 
                    <br>
                </div>
            </div>
        </div>
    </section>
    <!--Heading Banner Area End-->

    <section class="contact-form-area">
        <div class="container">
            <div class="row">
                @if(!Auth::check())
                    <div class="col-sm-6 col-sm-offset-3  "> 
                        <div class="search-form-wrapper mtb-70" style="text-align: center;">
                            <div class="error-message">
                                <p style="font-size: 16px;">
                                    Looks like you're not logged in.
                                    <br>
                                    Login below to access your cart.
                                </p>
                            </div>
                            <div class="search-form">
                                <div class="back-to-home">
                                    <a href="{{ route('login') }}">Login Here</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @if(!isset($cart) OR sizeof($cart['cart_items']) < 1)
                        <div class="col-sm-6 col-sm-offset-3"> 
                            <div class="search-form-wrapper mtb-70" style="text-align: center;">
                                <div class="error-message">
                                    <p style="font-size: 16px;">
                                        Yikes, your cart is empty.
                                    </p>
                                </div>
                                <div class="search-form">
                                    <div class="back-to-home">
                                        <a href="{{ route('show.shop') }}">Start shopping</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <table style="width: 80%; margin:auto;">
                            <thead>
                                <tr>
                                    <th class="product-name">
                                        <span class="nobr"> Product</span>
                                    </th>
                                    <th class="product-quantity">
                                        <span class="nobr">Quantity</span>
                                    </th>
                                    <th class="product-price">
                                        <span class="nobr"> Unit Price </span>
                                    </th>
                                    <th class="product-total-price">
                                        <span class="nobr"> Total Price </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <form id="update-cart-form" method="POST" action="{{ route("process.cart.action") }}">
                                    @csrf
                                    @for ($i = 0; $i < sizeof($cart['cart_items']); $i++)
                                        <tr>
                                            <td class="product-name" style='text-align:left;'>
                                                <a href="{{ url("shop/".$cart["cart_items"][$i]["username"]."/".$cart["cart_items"][$i]["product_slug"]) }}">
                                                    <img src="{{ url("app/assets/img/products/thumbnails/".$cart["cart_items"][$i]["product_images"][0]["pi_path"].".jpg") }}" width='60px;' height='auto' style='border-radius:5px;' alt="">
                                                    &nbsp;&nbsp;
                                                    {{ $cart["cart_items"][$i]["product_name"] }}
                                                    @if(trim(strtolower($cart["cart_items"][$i]["sku_variant_description"] )) != "none")
                                                        - {{ $cart["cart_items"][$i]["sku_variant_description"]  }}
                                                    @endif
                                                    &nbsp;&nbsp;
                                                </a>
                                                <span onclick="removeCartItem('{{ $cart['cart_items_id_array'][$i] }}')" style="font-size: 22px; color: red; cursor: pointer;">×</span>
                                            </td>
                                            <td class="product-price">
                                                <span><input class="input-text" style="text-align:center; width:50px; height:30px; border: 1px solid #ebebeb; border-radius:10px;" name="quantity{{$i}}" value="{{ $cart["ci_quantity"][$i] }}" min="1" max="{{ $cart["cart_items"][$i]["sku_stock_left"] }}" type="number"></span>
                                            </td>
                                            <td class="product-total-price">
                                                <span>GH¢ {{ $cart["cart_items"][$i]["product_selling_price"] - $cart["cart_items"][$i]["product_discount"] }}</span>
                                            </td>
                                            <td class="product-price">
                                                <span>GH¢ {{ ($cart["cart_items"][$i]["product_selling_price"] - $cart["cart_items"][$i]["product_discount"]) * $cart["ci_quantity"][$i] }}</span>
                                            </td>
                                            <input type='hidden' name='sku{{$i}}' value="{{ $cart['cart_items_id_array'][$i] }}">
                                        </tr>
                                    @endfor
                                    <input type='hidden' name='cart_count' value="{{ $customer_information["cart_count"] }}">
                                    
                                    @if(isset(Auth::user()->icono) AND Auth::user()->icono != "NULL" AND Auth::user()->icono != NULL) 
                                    <tr>
                                            <td class="product-name" style='text-align:left;'>
                                                <img src="{{ url("app/assets/img/discount-badges/Rookie.png") }}" width='60px;' height='auto' style='border-radius:5px;' alt="">
                                                &nbsp;&nbsp;
                                                    1 % Discount from Sales Coupon
                                                &nbsp;&nbsp;
                                                <span onclick="document.getElementById('remove-icono').submit();" style="font-size: 22px; color: red; cursor: pointer">×</span>
                                            </td>
                                            <td class="product-price">
                                                <span>
                                                    N/A
                                                </span>
                                            </td>
                                            <td class="product-total-price">
                                                N/A
                                            </td>
                                            <td class="product-price">
                                            <span>GH¢ {{ $cart['icono_discount'] }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                    <input type="hidden" name="cart_action" value="update_cart" />
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
                            </tbody>
                        </table>
                    @endif
                @endif
            </div>
        </div>

        <div class="container" style="padding: 0px;">
            <br><br>
            <div class="row" style="width: 80%; margin:auto;">
                <div class="col-md-6 col-sm-6" style="padding-left: 0px;">
                    <div class="cart-collaterals">
                        <div class="cart-cuppon">
                            @if (Auth::check()) 
                                <div class='coupon'>
                                <form  method="POST" action="{{ route("process.cart.action") }}">
                                    @csrf
                                    <input type="hidden" name="cart_action" value="apply_coupon" />
                                    <input name='coupon_code' class='input-copun' value='' placeholder='Coupon code' type='text'>
                                    <button type='submit' name='applyCoupon' class='wishlist-btn shopping-btn'>Apply Coupon</button>
                                </form>
                                </div>
                                
                                <button class='update-btn' onclick="document.getElementById('update-cart-form').submit();" >Update cart</button>
                                <br><br><br>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6" style="padding-right: 0px;">
                    <div class="shopping-cart-total">
                        <h2>Cart Totals</h2>
                        <div class="shop-table table-responsive">
                            <table>
                                <tbody>
                                    <tr class="order-total">
                                        <td data-title="Sub-Total">
                                            <span>
                                                <strong>
                                                    @if(isset($cart['sub_total']))
                                                        GH¢ {{ $cart['sub_total'] }}
                                                    @else
                                                        GH¢ 0.00
                                                    @endif
                                                </strong>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @if (isset($cart['cart_items']) AND sizeof($cart['cart_items']) > 0) 
                            <div class="proceed-to-checkout">
                                <a class="checkout-button" href="{{ route('show.checkout') }}">Proceed to Checkout</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function removeCartItem(cartItemSKU)
        {
            document.getElementById('cart_item_sku').value = cartItemSKU;
            document.getElementById('remove-cart-item').submit(); 
        } 
    </script>

@endsection