@extends('mobile.layouts.my-account')
@section('page-title')My Account @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Manage your account on Solushop Ghana @endsection
@section('page-content')
    <div class="page">
        @include('mobile.main.general.includes.toolbar')
        <div class="navbar navbar-page">
            <div class="navbar-inner sliding">
                <div class="title">
                    My Account
                </div>
            </div>
        </div>
        <div class="page-content">
            <!-- account buyer -->
            <div class="account-buyer">
                <div class="header">
                    <div class="mask"></div>
                    <img src="{{ url("mobile/images/banner2.jpg") }}" alt="">
                    <div class="user-caption">
                        <img src="{{ url('/app/assets/img/comment-author/2.png') }}" alt="">
                        <div class="title-name">
                            <br>
                            <h5>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h5>
                        </div>
                    </div>
                </div>
                <div class="info-balance segments">
                    <div class="container">
                        <div class="row">
                            <div class="col-50">
                                <div class="content">
                                    <span>Your Balance</span>
                                    <h5>GH¢ {{ abs($customer_information["wallet_balance"]) }}</h5>
                                </div>
                            </div>
                            <div class="col-50">
                                <div class="content">
                                    <a href="{{ route('show.account.wallet') }}" class="external">
                                        <button class="button">Top Up</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
                <div class="account-menu">
                    <div class="list links-list">
                        <ul>
                            <li>
                                <a href="{{ route('show.account.messages') }}" class="panel-close external">
                                    <div class="item-media">
                                        <i class="ti-email"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Messages
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('show.account.personal.details') }}" class="panel-close external">
                                    <div class="item-media">
                                        <i class="ti-user"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Personal Details
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('show.account.orders') }}" class="panel-close external">
                                    <div class="item-media">
                                        <i class="ti-bag"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Orders
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('show.account.login.and.security') }}" class="panel-close external">
                                    <div class="item-media">
                                        <i class="ti-unlock"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Login &amp; Security
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('show.account.addresses') }}" class="panel-close external">
                                    <div class="item-media">
                                        <i class="ti-location-pin"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Addresses
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('customer.logout') }}" class="panel-close external">
                                    <div class="item-media">
                                        <i class="ti-power-off"></i>
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-title">
                                            Logout
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- end account buyer -->
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
    