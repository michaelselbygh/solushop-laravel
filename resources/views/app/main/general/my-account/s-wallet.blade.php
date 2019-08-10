@extends('app.layouts.my-account')
@section('page-title')Wallet @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Manage your wallet on Solushop Ghana @endsection
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
                                <li>S-Wallet</li>
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
                    <h3 style="font-weight: 350">S-Wallet ( Balance : <b>GH¢ {{ abs($customer_information["wallet_balance"]) }}</b> )</h3>
                    <br>
                    <div class="row" style="">
                        <div class="col-md-10">
                            <span>The Solushop Wallet or S-Wallet allows you to top-up money and make purchases directly and super fast on Solushop.</span>
                            <div class="register-form" style="margin-top: 0px;">
                                <form action="{{ route("process.account.wallet") }}" method="POST">
                                    @csrf
                                    <div class="col-md-12">
                                        <div class="form-fild">
                                            <select name="wtup_id" placeholder="Select Package" style="padding-left: 5px; border: 1px solid #e5e5e5; border-radius: 5px; height: 35px;" required>
                                                @for ($j = 0; $j < sizeof($wallet["options"]); $j++)
                                                    <option value="{{ $wallet["options"][$j]["id"] }}">
                                                            {{ $wallet["options"][$j]["wtu_package_description"] }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="register-submit" style="text-align:center;">
                                        <button type="" name="top_up_wallet" class="form-button" style="margin-top: 15px;">Top Up Wallet</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Shop Product Area End-->
                <!--Left Sidebar Start-->
                <div class="col-md-4 col-md-pull-8">
                    <div class="widget widget-shop-categories" style="margin-bottom:50px; border-radius:20px;">
                        <div class="widget-content">
                            <ul class="product-categories">
                                <li style="">
                                    <a style="margin-left:15px; font-size: 12px;" href="{{ route('show.account.dashboard') }}">
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
                                <li style="background-color: #f68b1e; color:white; border-radius: 10px;">
                                    <a style="margin-left:15px; font-size: 12px; color:white;" href="{{ route('show.account.wallet') }}">
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