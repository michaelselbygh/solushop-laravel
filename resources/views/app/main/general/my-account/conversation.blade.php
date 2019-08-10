@extends('app.layouts.my-account') 
@section('page-title')Conversation with {{ $conversation["vendor"]["name"] }} @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Send {{ $conversation["vendor"]["name"] }} a message on Solushop Ghana. @endsection
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
                                <li><a href="{{ route('show.account.messages') }}">Messages</a><span class="breadcome-separator">></span></li>
                                <li>{{ $conversation["vendor"]["name"] }}</li>
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
                    <h3 style="font-weight:350">Conversation with {{ $conversation["vendor"]["name"] }}</h3>
                    <div style='border: 1px solid #ebebeb; border-radius:10px; height:400px; padding:10px;overflow: hidden;'>
                        <div class="child" id='Child'>
                            @if (sizeof($conversation["messages"]) < 1 ) 
                                <h4 style='text-align:center; margin-top:17%; font-weight: 300'>
                                    No messages yet
                                </h4>
                                <br>
                                <h5 style='text-align:center; font-weight:300'>
                                    Enter a message below.
                                </h5>
                                <br>
                                <h5 style='text-align:center; font-weight:300 '>
                                    Keep conversations strictly professional.<br>
                                    Do not exchange contact details<br>
                                    All communication must be within Solushop<br><br>
                                    
                                    To ensure safety of both parties,<br>
                                    we supervise all conversation between customers and vendors.<br>
                                    Please ensure that all commuication is done here.
                                </h5>
                                <br><br><br><br>
                            @else
                                @for ($i=0; $i < sizeof($conversation["messages"]); $i++)  
                                    @if ($conversation["messages"][$i]["message_sender"] == Auth::user()->id) 
                                        <div class='chat-row' style='text-align: right; word-wrap: break-word'>
                                            <div class='chat-item' style='color: #fff; background-color: #001337; border-radius:10px; padding:7px; max-width:400px; display: inline-block; text-align: left; font-size:12px;'>
                                                {{ $conversation["messages"][$i]["message_content"] }}
                                            </div>
                                            <br>
                                            <span style='font-size:11px; font-weight:300'>
                                                {{ Auth::user()->first_name." - ".$conversation["messages"][$i]["message_timestamp"] }}
                                            </span>
                                        </div>
                                        <br>
                                    @elseif($conversation["messages"][$i]["message_sender"] == $conversation["vendor"]["id"])
                                        <div class='chat-row' style='text-align: left; '>
                                            <div class='chat-item' style='color: #001337; background-color: #edeef0; border-radius:10px; padding:7px; max-width:400px; display: inline-block; text-align: left; font-size: 12px'>
                                                {{ $conversation["messages"][$i]["message_content"] }}
                                            </div>
                                            <br>
                                            <span style='font-size:10px; font-weight:300'>
                                                {{ $conversation["vendor"]["name"]." - ".$conversation["messages"][$i]["message_timestamp"] }}
                                            </span>
                                        </div>
                                        <br>
                                    @elseif($conversation["messages"][$i]["message_sender"] == "MGMT")
                                        <div class='chat-row' style='text-align: left; '>
                                            <div class='chat-item' style='color: #001337; background-color: #edeef0; border-radius:10px; padding:7px; max-width:400px; display: inline-block; text-align: left; font-size:12px;'>
                                                {{ $conversation["messages"][$i]["message_content"] }}
                                            </div>
                                            <br>
                                            <span style='font-size:10px; font-weight:300'>
                                                Solushop Management - {{ $conversation["messages"][$i]["message_timestamp"] }}
                                            </span>
                                        </div>
                                        <br>
                                    @endif
                                @endfor
                            @endif
                        </div>
                    </div>
                    <div style='padding:10px;'>
                        <div class="register-form" style="margin-top: 0px; padding: 0px; margin: 0px;">
                            <form action="{{ route("process.account.conversation", $conversation["vendor"]["username"]) }}" method="POST">
                                @csrf
                                <div class="form-fild">
                                    @if(isset($conversation["product"]))
                                        <input style="width:100%" type="text" name="message_content" value="Product : {{ $conversation["product"]["product_name"] }}. Hi {{ $conversation["vendor"]["name"] }} ..." placeholder="Enter message here" required>
                                    @else
                                        <input style="width:100%" type="text" name="message_content" value="" placeholder="Enter message here" required>
                                    @endif
                                </div>
                                <input type="hidden" name="mci" value="AK39SA{{ $conversation["details"]["id"] }}"/>
                                <div class="register-submit" style="text-align:center; margin-bottom:0px;">
                                    <button type="submit" name="send_message" class="form-button">Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                <!--Shop Product Area End-->

                <!--Left Sidebar Start-->
                <div class="col-md-4 col-md-pull-8">
                    <div class="widget widget-shop-categories" style="margin-bottom:50px; border-radius:20px;">
                        <div class="widget-content">
                            <ul class="product-categories">
                                <li>
                                    <a style="margin-left:15px; font-size: 12px;" href="{{ route('show.account.dashboard') }}">
                                        <i class='fa fa-dashboard' style='font-size:18px; margin-right:7px;'></i> 
                                        Dashboard
                                    </a>
                                </li>
                                <li style="background-color: #f68b1e;  border-radius: 10px;">
                                    <a style="margin-left:15px; color:white; font-size: 12px;" href="{{ route('show.account.messages') }}">
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
                                <li>
                                    <a style="margin-left:15px; font-size: 12px;" href="{{ route('show.account.orders') }}">
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
@endsection