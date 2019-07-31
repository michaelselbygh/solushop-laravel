@extends('app.layouts.general')
@section('page-title')
    Checkout
@endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')
    Checkout from Solushop Ghana
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
                                <li>Checkout </li>
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
                    <br>
                </div>
                <!--Shop Product Area End-->
            </div>
        </div>
    </div>

    <div class="checkout-area">
        <div class="container" style="text-align:center">
            <div class="row" style="display: inline-block; width:80%">
                <div class="col-md-6">
                    <h3 style='text-align:left;'>Personal Details</h3>
                    <div class="register-form" style="margin-top: 0px;">
                        <form action="{{ route("process.checkout") }}" method="POST">
                            @csrf
                            <div class="col-md-6">
                                <div class="form-fild">
                                <input type="text" name="first_name" value="{{ Auth::user()->first_name }}" placeholder="e.g. Michael" required>
                                </div>
                                <div class="form-fild">
                                    <input type="text" name="email" value="{{ Auth::user()->email }}" placeholder="e.g. michael.selby@solushop.com.gh" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-fild">
                                    <input type="text" name="last_name" value="{{ Auth::user()->last_name }}" placeholder="e.g. Selby" required>
                                </div>
                                <div class="form-fild">
                                    <input type="text" name="phone" value="{{ "0".substr(Auth::user()->phone, 3) }}" placeholder="e.g 0244000000" required>
                                </div>
                            </div>
                            <div class="register-submit" style="text-align:center;">
                                <button type="" name="UpdateDetails" class="form-button" style="margin-top: 15px;">Update details</button>
                            </div>
                            <input type="hidden" name="checkout_action" value="update_personal_details"/>
                        </form>
                    </div>
                    <h3 style='text-align:left;'>Additional Order Details</h3>
                    <form id="checkout-form" method="POST" action="{{ route('process.checkout') }}">
                        @csrf
                        <input type="hidden" name="checkout_action" value="process_checkout"/>
                        <div class="register-form" style="margin-top: 0px;">
                            <textarea cols="5" rows="2" placeholder="Additional Information, e.g. Popular location close to your address for easy identification" style="border-radius:10px;" class="" value="" name="order_ad"></textarea>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <h3 style='text-align:left; margin-bottom:10px;'>Addresses</h3>
                    @if(sizeof($customer_information["addresses"]) > 0)
                        <form id="" method="POST" action="{{ route('process.checkout') }}">
                            @csrf
                            <div class="shop-table table-responsive">
                                <table style="text-align: left; font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th style="padding: 5px 5px"></th>
                                            <th style="padding: 5px 5px">Region</th>
                                            <th style="padding: 5px 5px">Town/City </th>
                                            <th style="padding: 5px 5px">Address</th>
                                        </tr>
                                    </thead>
                                    <tbody style="text-align: left;">
                                        @for($i=0; $i < sizeof($customer_information["addresses"]); $i++) 
                                            <tr>
                                                <td>
                                                    <input style='height:15px;' type='radio' value='{{ $customer_information["addresses"][$i]['id'] }}' name='default_address' @if($customer_information["addresses"][$i]['id'] == Auth::user()->default_address) checked @endif/>
                                                </td>
                                                <td>{{ $customer_information["addresses"][$i]['ca_region'] }}</td>
                                                <td>{{ $customer_information["addresses"][$i]['ca_town'] }}</td>
                                                <td>{{ $customer_information["addresses"][$i]['ca_address'] }}</td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <input type="hidden" name="checkout_action" value="update_default_address"/>
                            <div class="register-submit" style="text-align:center;">
                                <button type="submit" name="UpdateDefaultAddress" class="form-button">Update Default Address</button>&nbsp;
                                <a href='{{ route("show.account.add.address") }}'>
                                    <button name="add-address" type='submit' class="form-button">Add New Address</button>
                                </a>
                            </div>
                        </form>
                    @else
                        <h4 style='text-align:center; margin-top:50px'>Oops! Looks like you have no addresses set up</h4><br>
                        <td class="product-add-to-cart" >
                            <div style='text-align:center;'>
                                <a href='{{ route("show.account.add.address") }}'><button type="submit" name="add-address" class="form-button">Add New Address</button></a>
                            </div>
                        </td>
                    @endif
                </div>
            </div>
            <div class="row" style="display: inline-block; width:80%">
                    <div class="col-md-12">
                        <div class="checkout-form-area">
                            <div class="ceckout-form">
                                <!--Your Order Fields Start-->
                                <div class="your-order-fields mt-30">
                                    <div class="your-order">
                                        <h3 style='text-align:left;'>Order Summary</h3>
                                    </div>
                                    <div class="your-order-table table-responsive">
                                        <table style="margin: 0 0 20px;">
                                            <tbody> 
                                                @for($i=0; $i < sizeof($checkout['checkout_items']); $i++) 
                                                    <tr class="cart_item">
                                                        <td style="width:70%; text-align: left;" class="product-name"> 
                                                            <span style='font-weight:400; font-size:20px; color:red; cursor:pointer' onclick="removeCheckoutItem('{{ $checkout['checkout_items_id_array'][$i] }}')">
                                                                ×
                                                            </span>
                                                            {{ $checkout["checkout_items"][$i]["product_name"] }}
                                                            @if(trim(strtolower($checkout["checkout_items"][$i]["sku_variant_description"] )) != "none")
                                                                - {{ $checkout["checkout_items"][$i]["sku_variant_description"]  }}
                                                            @endif
                                                                <strong class="product-quantity">( x {{ $checkout['ci_quantity'][$i] }} )</strong>
                                                        </td>
                                                        <td style='text-align:right;' class="product-total">
                                                            <span class="amount">
                                                                GH¢ {{ ($checkout["checkout_items"][$i]["product_selling_price"] - $checkout["checkout_items"][$i]["product_discount"]) * $checkout['ci_quantity'][$i] }}
                                                            </span>
                                                        </td>
                                                        <input type='hidden' name='sku{{$i}}' value="{{ $checkout['checkout_items_id_array'][$i] }}">
                                                    </tr>
                                                @endfor
                                                <form id="remove-checkout-item" method="POST" action="{{ route('process.checkout') }}">
                                                    @csrf
                                                    <input type="hidden" name="checkout_item_sku" id="checkout_item_sku" value="" />
                                                    <input type="hidden" name="checkout_action" value="remove_checkout_item" />
                                                </form>
                                            
                                                @if(isset(Auth::user()->icono) AND Auth::user()->icono != "NULL" AND Auth::user()->icono != NULL)
                                                    <tr class="cart_item">
                                                        <td style="width:70%; text-align: left;" class="product-name">
                                                            1% Discount from Sales Coupon - {{ Auth::user()->icono }} 
                                                            <strong class="product-quantity"></strong>
                                                        </td>
                                                        <td style='text-align:right;' class="product-total">
                                                        <span class="amount"> - GH¢ {{ $checkout["icono_discount"] }}</span> 
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr class="cart-subtotal">
                                                    <th>Delivery</th>
                                                    <td style='text-align:right;'><span class="amount">GH¢ {{ $checkout['shipping'] }}</span></td>
                                                </tr>
                                                <tr class="cart-subtotal">
                                                    <th>Subtotal</th>
                                                    <td style='text-align:right;'><span class="amount">GH¢ {{ $checkout['sub_total'] }}</span></td>
                                                </tr>
                                                @if(isset($checkout['due_from_wallet']))
                                                    <tr class="cart-subtotal">
                                                        <th>To be deducted From S-Wallet</th>
                                                        <td style='text-align:right;'><span class="amount">- GH¢ {{ $checkout['due_from_wallet'] }}</span></td>
                                                    </tr>
                                                @endif                                                
                                                <tr class="order-total">
                                                    <th>Total Due for Payment</th>
                                                    <td style='text-align:right;'>
                                                        <strong><span class="total-amount">GH¢ {{ $checkout['total_due'] }}</span></strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <input type="hidden" name="Shipping" value="0" />
                                <!--Your Order Fields End-->
                                <div class="checkout-payment">
                                    <button class="order-btn" onclick="document.getElementById('checkout-form').submit();">Place order</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function removeCheckoutItem(checkoutItemSKU)
        {
            document.getElementById('checkout_item_sku').value = checkoutItemSKU;
            document.getElementById('remove-checkout-item').submit(); 
        } 
    </script>
@endsection