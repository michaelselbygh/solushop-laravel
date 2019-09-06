@extends('mobile.layouts.my-account')
@section('page-title')Orders @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Manage your orders on Solushop Ghana @endsection
@section('page-content')
    <div class="page">
        <div class="navbar navbar-page">
            <div class="navbar-inner sliding">
                <div class="left">
                    <a href="{{ route('show.account.dashboard') }}" class="link back external">
                        <i class="ti-arrow-left"></i>
                    </a>
                </div>
                <div class="title">
                    Orders
                </div>
            </div>
        </div>
        <div class="page-content">
            <!-- cart -->
            <div class="tracking-order product segments-page">
                <div class="container">
                    @if(sizeof($orders) < 1)
                        <div class="content" style="text-align:center; margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
                            <div class="error-message">
                                <h6>
                                    No orders yet.
                                </h6>
                            </div>
                        </div>
                    @else
                        <div class="accordion-list">
                            @foreach ($orders as $order)
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle" style="font-size: 12px; background-color: #f68b1e; color: white">
                                        <b>{{ $order->id }}</b> - {{ date('g:ia, jS F Y', strtotime($order->order_date)) }}
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        <div class="row">
                                            <div class="col-40" style="padding-right:0px; padding-left:0px; ">
                                            </div>
                                            <div class="col-60" style="padding-right:0px; padding-left:0px; text-align:right;">
                                                <h6>Status - 
                                                    @switch($order->order_state)
                                                        @case(1)
                                                            Unpaid
                                                            @break
                                                        @case(2)
                                                            Pending Confirmation
                                                            @break
                                                        @case(3)
                                                            Confirmed, processing.
                                                            @break
                                                        @case(4)
                                                            Ready for Delivery
                                                            @break
                                                        @case(5)
                                                            Delivering
                                                            @break
                                                        @case(6)
                                                            Completed
                                                            @break
                                                        @case(7)
                                                            Cancelled
                                                            @break
                                                        @default
                                                            
                                                    @endswitch
                                                </h6>
                                            </div>
                                        </div>
                                        <div class="your-order-table table-responsive">
                                            <table style="margin: 0 0 20px; width:100%">
                                                <tbody> 
                                                    @for ($j=0; $j < sizeof($order->order_items); $j++) 
                                                        <tr class="cart_item">
                                                            <td style="width:70%; text-align: left;" class="product-name"> 
                                                                <strong class="product-quantity">
                                                                    {{  $order->order_items[$j]->oi_quantity }}
                                                                </strong>
                                                                {{  $order->order_items[$j]->oi_name }}
                                                            </td>
                                                            <td style='text-align:right;' class="product-total">
                                                                <span class="amount">
                                                                    GH¢ {{  $order->order_items[$j]->oi_quantity * ($order->order_items[$j]->oi_selling_price - $order->order_items[$j]->oi_discount) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                    @if(isset(Auth::user()->icono) AND Auth::user()->icono != "NULL" AND Auth::user()->icono != NULL)
                                                        <tr class="cart_item">
                                                            <td style="width:70%; text-align: left;" class="product-name">
                                                                1% Discount from Sales Coupon - {{ $order->order_scoupon }} 
                                                                <strong class="product-quantity"></strong>
                                                            </td>
                                                            <td style='text-align:right;' class="product-total">
                                                            <span class="amount"> - GH¢ {{ 0.01 * $order->order_subtotal }}</span> 
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                                <tfoot>
                                                    <tr class="cart-subtotal">
                                                        <th style="text-align: left; font-weight: 400;"></th>
                                                        <td style="text-align:right;"><span class="amount"><br></span></td>
                                                    </tr>
                                                    <tr class="cart-subtotal">
                                                        <th style="text-align: left;">Subtotal</th>
                                                        <td style='text-align:right;'>
                                                            <span class="amount">
                                                                GH¢ 
                                                                @if($order->order_scoupon != NULL OR $order->order_scoupon != "NULL")
                                                                    {{ 0.99 * $order->order_subtotal }}
                                                                @else
                                                                    {{ $order->order_subtotal }}
                                                                @endif
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr class="cart-subtotal">
                                                        <th style="text-align: left;">Delivery</th>
                                                        <td style='text-align:right;'><span class="amount">GH¢ {{ $order->order_shipping }}</span></td>
                                                    </tr>
                                                    @if ($order->order_state == 1 AND $customer_information["wallet_balance"] > 0)
                                                        <tr class="cart-subtotal">
                                                            <th style="text-align: left;">To be deducted From S-Wallet</th>
                                                            <td style='text-align:right;'>
                                                                <span class="amount">
                                                                    - GH¢ 
                                                                    @if($order->order_scoupon != NULL AND trim($order->order_scoupon) != "NULL")
                                                                        @if ($customer_information["wallet_balance"] >= (0.99 * $order->order_subtotal) + $order->order_shipping)
                                                                            {{(0.99 * $order->order_subtotal) + $order->order_shipping}}
                                                                        @else 
                                                                            {{$customer_information["wallet_balance"]}}
                                                                        @endif
                                                                    @else
                                                                        @if ($customer_information["wallet_balance"] >= ($order->order_subtotal) + $order->order_shipping)
                                                                            {{($order->order_subtotal) + $order->order_shipping}}
                                                                        @else
                                                                            {{$customer_information["wallet_balance"]}}
                                                                        @endif
                                                                    @endif
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endif                                                
                                                    <tr class="order-total">
                                                        <th style="text-align: left;">
                                                            Total
                                                            @if($order->order_state == 1)
                                                                Due
                                                            @else
                                                                Paid
                                                            @endif
                                                        </th>
                                                        <td style='text-align:right;'>
                                                            <strong>
                                                                <span class="total-amount">
                                                                    GH¢ 
                                                                    @if($order->order_state == 1)
                                                                        @if($order->order_scoupon != NULL AND $order->order_scoupon != "NULL")
                                                                            @if ($customer_information["wallet_balance"] >= (0.99 * $order->order_subtotal) + $order->order_shipping)
                                                                                0.00
                                                                            @else
                                                                                {{ (0.99 * $order->order_subtotal) + $order->order_shipping - $customer_information["wallet_balance"] }}
                                                                            @endif
                                                                        @else
                                                                            @if ($customer_information["wallet_balance"] >= ($order->order_subtotal) + $order->order_shipping)
                                                                                0.00
                                                                            @else
                                                                                {{ ($order->order_subtotal) + $order->order_shipping - $customer_information["wallet_balance"] }}
                                                                            @endif
                                                                        @endif
                                                                    
                                                                    @else 
                                                                        @if($order->order_scoupon != NULL AND $order->order_scoupon != "NULL")
                                                                            {{ (0.99 * $order->order_subtotal) + $order->order_shipping}}
                                                                        @else 
                                                                            {{ ($order->order_subtotal) + $order->order_shipping}}
                                                                        @endif
                                                                    @endif
                                                                </span>
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        @if($order->order_state == 1)
                                            <button class="button" onclick="initiateOrder('{{$order->id}}')" >Pay to Initiate Order</button>
                                        @endif
                                        <br>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <form id="initiate-order-form" method="POST" action="{{ route("process.account.orders") }}">
                            @csrf
                            <input type="hidden" name="oid" id="oid"/>
                        </form>
                    @endif
                    
                    <div class="pagination" style="text-align: center;">
                        {{ $orders->links('mobile.pagination.links') }}
                    </div>
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
        function initiateOrder(orderID)
        {
            document.getElementById('oid').value = orderID;
            document.getElementById('initiate-order-form').submit(); 
        } 
    </script>
@endsection    
    