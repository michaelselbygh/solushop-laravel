@extends('mobile.layouts.general')
@section('page-title')
    Checkout
@endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')
    Checkout from Solushop Ghana
@endsection
@section('page-description')
    Check out these frequently asked questions on Solushop Ghana.
@endsection
@section('page-content')
    <div class="page page-home">
        <div class="tabs page-content">
            <div id="tab-1" class="tab tab-active">
                <!-- home -->

                <div class="navbar navbar-page">
                    <div class="navbar-inner sliding">
                        <div class="left">
                            <a href="{{ route("show.cart") }}" class="link back external">
                                <i class="ti-arrow-left"></i>
                            </a>
                        </div>
                        <div class="title">
                            Checkout
                        </div>
                    </div>
                </div>

                <div class="page-content" style="padding-top:10px;">
                    <!-- tracking order -->
                    <div class="tracking-order segments-page">
                        <div class="container">
                            <div class="accordion-list">                    
                                <div class="accordion-item accordion-item-opened">
                                    <div class="accordion-item-toggle" style="background-color: #f68b1e; ">
                                        Personal Details
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        <form class="list" method="POST" action="{{ route('process.checkout') }}">
                                            @csrf
                                            <div class="item-input-wrap">
                                                <input type="text" placeholder="First name" name="first_name" value="{{ Auth::user()->first_name }}" required>
                                            </div>
                                            <div class="item-input-wrap">
                                                <input type="text" placeholder="Last name" name="last_name" value="{{ Auth::user()->last_name }}" required>
                                            </div>
                                            <div class="item-input-wrap">
                                                <input type="text" placeholder="Phone e.g 0544000000" name="phone" value="{{ "0".substr(Auth::user()->phone, 3) }}" required>
                                            </div>
                                            <div class="item-input-wrap">
                                                <input type="email" placeholder="Email" name="email" value="{{ Auth::user()->email }}" required>
                                            </div>
                                            <input type="hidden" name="checkout_action" value="update_personal_details"/>
                                            <button class="button" type="submit">Update Details</button>
                                            <br>
                                            
                                        </form>
                                        <br>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle" style="background-color: #f68b1e; ">
                                        Delivery Address
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        @if(sizeof($customer_information["addresses"]) > 0)
                                            <form id="" method="POST" action="{{ route('process.checkout') }}">
                                                @csrf
                                                <div class="shop-table table-responsive">
                                                    <table style="text-align: left; font-size: 12px;">
                                                        <tbody style="text-align: left;">
                                                            @for($i=0; $i < sizeof($customer_information["addresses"]); $i++) 
                                                                <tr>
                                                                    <td>
                                                                        <input style='height:15px;' type='radio' value='{{ $customer_information["addresses"][$i]['id'] }}' name='default_address' @if($customer_information["addresses"][$i]['id'] == Auth::user()->default_address) checked @endif/>
                                                                    </td>
                                                                    <td>{{ $customer_information["addresses"][$i]['ca_town'] }} | {{ $customer_information["addresses"][$i]['ca_address'] }}</td>
                                                                </tr>
                                                            @endfor
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <input type="hidden" name="checkout_action" value="update_default_address"/>
                                                <div class="row">
                                                    <div class="col-50">
                                                            <button class="button" type="submit">Update Default</button>
                                                    </div>
                                                    <div class="col-50">
                                                        <a href='my-account/add-address.php'>
                                                            <button class="button" >Add New Address</button>
                                                        </a>
                                                    </div>
                                                </div>
                                            </form>
                                        @else
                                            <h5 style='text-align:center; margin-top:50px'>Oops! Looks like you have no addresses set up</h5>
                                            <td class="product-add-to-cart" >
                                                <div style='text-align:center;'>
                                                    <a href='my-account/add-address.php'>
                                                        <button class="button" >Add New Address</button>
                                                    </a>
                                                    <br><br>
                                                </div>
                                            </td>
                                        @endif
                                        <br>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle" style="background-color: #f68b1e; ">
                                       Order Summary
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        <div class="your-order-table table-responsive">
                                            <table style="margin: 0 0 20px; width:100%">
                                                <tbody> 
                                                    @for($i=0; $i < sizeof($checkout['checkout_items']); $i++) 
                                                        <tr class="cart_item">
                                                            <td style="width:70%; text-align: left;" class="product-name"> 
                                                                <span style='font-weight:400; font-size:20px; color:red; cursor:pointer' onclick="removeCheckoutItem('{{ $checkout['checkout_items_id_array'][$i] }}')">
                                                                    ×
                                                                </span>
                                                                <strong class="product-quantity">{{ $checkout['ci_quantity'][$i] }}</strong>
                                                                {{ $checkout["checkout_items"][$i]["product_name"] }}
                                                                @if(trim(strtolower($checkout["checkout_items"][$i]["sku_variant_description"] )) != "none")
                                                                    - {{ $checkout["checkout_items"][$i]["sku_variant_description"]  }}
                                                                @endif
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
                                                        <tr class="cart_item" >
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
                                                <tfoot style="">
                                                    <tr class="cart-subtotal" >
                                                        <th style="text-align: left; font-weight: 400;"></th>
                                                        <td style='text-align:right;'><span class="amount"><br></span></td>
                                                    </tr>
                                                    <tr class="cart-subtotal" >
                                                        <th style="text-align: left; font-weight: 400;">Delivery</th>
                                                        <td style='text-align:right;'><span class="amount">GH¢ {{ $checkout['shipping'] }}</span></td>
                                                    </tr>
                                                    <tr class="cart-subtotal" >
                                                        <th style="text-align: left; font-weight: 400;">Subtotal</th>
                                                        <td style='text-align:right;'><span class="amount">GH¢ {{ $checkout['sub_total'] }}</span></td>
                                                    </tr>
                                                    @if(isset($checkout['due_from_wallet']))
                                                        <tr class="cart-subtotal">
                                                            <th style="text-align: left; font-weight: 400;">To be deducted From S-Wallet</th>
                                                            <td style='text-align:right;'><span class="amount">- GH¢ {{ $checkout['due_from_wallet'] }}</span></td>
                                                        </tr>
                                                    @endif                                                
                                                    <tr class="order-total" >
                                                        <th style="text-align: left; font-weight: 400;">Total Due for Payment</th>
                                                        <td style='text-align:right;'>
                                                            <strong><span class="total-amount">GH¢ {{ $checkout['total_due'] }}</span></strong>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle" style="background-color: #f68b1e; ">
                                        Additional Order Details (Optional)
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        <form id="checkout-form" method="POST" action="{{ route('process.checkout') }}">
                                            @csrf
                                            <input type="hidden" name="checkout_action" value="process_checkout"/>
                                            <textarea placeholder="Additional Information, e.g. Popular location close to your address for easy identification" style="border:#f68b1e 1px solid; border-radius:10px; width:100%; height:150px; padding:20px;" class="" value="" name="order_ad"></textarea>
                                        </form>
                                        <br>
                                    </div>
                                </div>
                                <button class="button" style="background-color:green" onclick="document.getElementById('checkout-form').submit();">Place order</button>
                            </div>
                            @if (session()->has('success_message')) 
                                <div id="snackbar">{{ session()->get('success_message') }}</div>
                            @elseif (session()->has('error_message')) 
                                <div id="snackbar">{{ session()->get('error_message') }}</div>
                            @endif
                        </div>
                    </div>
                    <!-- end tracking order -->
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
    