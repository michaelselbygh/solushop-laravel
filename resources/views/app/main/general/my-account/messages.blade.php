@extends('app.layouts.my-account')
@section('page-title')
    Messages
@endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')
    Manage your messages on Solushop Ghana
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
                                <li>Messages</li>
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
                    <h3 style="font-weight:350">Messages</h3>
                    <br>

                    @if(sizeof($conversations) < 1)
                        <p>
                            You have no conversations yet.
                        </p>
                    @else
                        @for ($i = 0; $i < sizeof($conversations); $i++)
                            <a href="{{ url("my-account/messages/".$conversations[$i]['vendor']['username']) }}" >
                                <i class='fa fa-check' style='font-size:12px; margin-right:7px; color: #f68b1e;'></i>
                                {{ $conversations[$i]['vendor']['name'] }}
                            
                                @if ($conversations[$i]['unread_messages'] > 0)
                                    <span style='color:white; background-color: red; padding: 2px 6px; border-radius:20px;'>
                                        {{ $conversations[$i]['unread_messages'] }}
                                    </span>
                                @endif
                                <br>
                            </a>
                        @endfor
                    @endif
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