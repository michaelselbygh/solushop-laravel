@extends('app.layouts.my-account')
@section('page-title')
    Orders
@endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')
    Manage your orders on Solushop Ghana
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
                                <li><a href="{{ route('show.account.dashboard') }}">My Account</a><span class="breadcome-separator">></span></li>
                                <li>Orders</li>
                            </ul>
                        </div>
                        @include('app.main.general.success-and-error.message') 
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Heading Banner Area End-->

    <div class="product-list-grid-view-area mt-20">
        <div class="container" style="text-align: center;">
            <div class="row" style="text-align: left; display: inline-block; width: 70%; min-height:450px;">
                <!--Shop Product Area Start-->
                <div class="col-md-8 col-md-push-4">
                    <br>
                    <h3 style="font-weight: 350">Orders</h3>
                    <br>
                    @if(sizeof($orders) > 0)
                        <div class="panel-group" id="accordion" role="tablist">
                            <?php $i = 0 ?>
                            @foreach ($orders as $order)
                                <!--Single Accrodion Start-->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h5 class="panel-title">
                                            <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$i}}" style="font-size:12px;">
                                                <span style="font-weight: 400"> {{ $order->id }}</span> - {{ date('g:ia, l jS F Y', strtotime($order->order_date)) }}
                                            </a>
                                        </h5>
                                    </div>
                                    <div id="collapse{{$i}}" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="col-lg-6" style="padding-right:0px; padding-left:0px; ">
                                            </div>
                                            <div class="col-lg-6" style="padding-right:0px; padding-left:0px; text-align:right;">
                                                <h4>Status - 
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
                                                </h4>
                                            </div>
                                            <div class="checkout-form-area" style = "margin-bottom: 0px;">
                                                <div class="ceckout-form">
                                                    <!--Your Order Fields Start-->
                                                    <div class="your-order-fields mt-30">
                                                        <div class="your-order">
                                                        </div>
                                                        <div class="your-order-table table-responsive">
                                                            <table style="margin-bottom: 0px;">
                                                                <tbody>
                                                                    @for ($j=0; $j < sizeof($order->order_items); $j++) 
                                                                        <tr class="cart_item">
                                                                            <td style="width:70%" class="product-name">
                                                                                <strong class="product-quantity">
                                                                                    {{  $order->order_items[$j]->oi_quantity }}
                                                                                </strong>
                                                                                {{  $order->order_items[$j]->oi_name }}
                                                                            </td>
                                                                            <td style='text-align:right;' class="product-total">
                                                                                <span class="amount">GH¢  
                                                                                    {{  $order->order_items[$j]->oi_quantity * ($order->order_items[$j]->oi_selling_price - $order->order_items[$j]->oi_discount) }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @endfor
                                                                    @if($order->order_scoupon != NULL AND trim($order->order_scoupon) != "NULL")
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
                                                                        <th>Subtotal</th>
                                                                        <td style='text-align:right;'>
                                                                            <span class="amount">
                                                                                    GH¢
                                                                                @if($order->order_scoupon != NULL AND $order->order_scoupon != "NULL")
                                                                                    {{ 0.99 * $order->order_subtotal }}
                                                                                @else
                                                                                    {{ $order->order_subtotal }}
                                                                                @endif
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="shipping">
                                                                        <th>Delivery </th>
                                                                        <td style='text-align:right;' data-title="Delivery">
                                                                            <span class="amount"> GH¢ {{ $order->order_shipping }}</span>
                                                                        </td>
                                                                    </tr>
                                                                    @if ($order->order_state == 1 AND $customer_information["wallet_balance"] > 0)
                                                                        <tr class="shipping">
                                                                            <th>To be deducted from S-Wallet </th>
                                                                            <td style='text-align:right;' data-title="Delivery">
                                                                                <span class="amount">
                                                                                    -  GH¢
                                                                                    @if($order->order_scoupon != NULL AND $order->order_scoupon != "NULL")
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
                                                                        <th>
                                                                            Total
                                                                            @if($order->order_state == 1)
                                                                                Due
                                                                            @else
                                                                                Paid
                                                                            @endif                                                                            
                                                                        </th>
                                                                        <td style='text-align:right;'>
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
                                                                        </td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <!--Your Order Fields End-->
                                                </div>
                                                @if($order->order_state == 1)
                                                    <div style="text-align:center";>
                                                        <div class="register-submit" style="text-align:center;">
                                                            <button onclick="initiateOrder('{{$order->id}}')" class="form-button" style="margin-top: 15px;">
                                                                Pay to Initiate
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php $i++ ?>
                                <!--Single Accrodion End-->
                            @endforeach
                        </div>
                        <form id="initiate-order-form" method="POST" action="{{ route("process.account.orders") }}">
                            @csrf
                            <input type="hidden" name="oid" id="oid"/>
                        </form>
                    @else
                        <p>
                            You have no orders yet.
                        </p>
                    @endif
                    <!--Pagination Start-->
                    <div style="width: 100%; text-align: center;">
                        {!! $orders->render() !!}
                    </div>
                    <!--Pagination End--> 
                </div>
                <!--Shop Product Area End-->
                <!--Left Sidebar Start-->
                <div class="col-md-4 col-md-pull-8">
                    <div class="widget widget-shop-categories" style="margin-bottom:50px; border-radius:20px;">
                        <div class="widget-content">
                            <ul class="product-categories">
                                <li>
                                    <a style="margin-left:15px;font-size: 12px;" href="{{ route('show.account.dashboard') }}">
                                        <i class='fa fa-dashboard' style='font-size:18px; margin-right:7px;'></i> 
                                        Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a style="margin-left:15px; font-size: 12px;" href="{{ route('show.account.messages') }}">
                                        <i class='fa fa-comments-o' style='font-size:18px; margin-right:7px;'></i> 
                                        Messages 
                                        @if(isset($customer_information["unread_messages"]) and $customer_information["unread_messages"]>0)
                                            <span style='color:white; background-color: red; padding: 4px 8px; border-radius:20px; margin-left:5px;'>
                                                {{ $customer_information["unread_messages"] }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a style="margin-left:15px; font-size: 12px;" href="{{ route('show.account.personal.details') }}">
                                        <i class='fa fa-user' style='font-size:18px; margin-right:7px;'></i> 
                                        Personal Details
                                    </a>
                                </li>
                                <li  style="background-color: #f68b1e; color:white; border-radius: 10px;">
                                    <a style="margin-left:15px; font-size: 12px; color:white;" href="{{ route('show.account.orders') }}">
                                        <i class='fa fa-shopping-bag' style='font-size:18px; margin-right:7px;'></i> 
                                        Your Orders
                                    </a>
                                </li>
                                <li>
                                    <a style="margin-left:15px; font-size: 12px;" href="{{ route('show.account.login.and.security') }}">
                                        <i class='fa fa-lock' style='font-size:18px; margin-right:7px;'></i> 
                                        Login &amp; Security
                                    </a>
                                </li>
                                <li>
                                    <a style="margin-left:15px; font-size: 12px;" href="{{ route('show.account.addresses') }}">
                                        <i class='fa fa-address-card-o' style='font-size:18px; margin-right:7px;'></i> 
                                        Addresses
                                    </a>
                                </li>
                                <li>
                                    <a style="margin-left:15px; font-size: 12px;" href="{{ route('show.account.wallet') }}">
                                        <i class='fa fa-money' style='font-size:18px; margin-right:7px;'></i> 
                                        Wallet
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--Left Sidebar End-->
            </div>
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