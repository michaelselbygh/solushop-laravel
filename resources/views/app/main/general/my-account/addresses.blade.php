@extends('app.layouts.my-account')
@section('page-title')Addresses @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Manage your addresses on Solushop Ghana @endsection
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
                                <li>Addresses</li>
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
                    <div class="row" style="">
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-6" style="top: 10px;">
                                    <h3 style="font-weight: 350">
                                        Addresses
                                    </h3>
                                </div>
                                <div class="col-md-6" style="text-align:right">
                                    <a href="{{ route('show.account.add.address') }}">
                                        <button class="form-button" style="font-size:12px; margin-bottom: 7px;">
                                            <i class='fa fa-plus' ></i>
                                        </button>
                                    </a>
                                </div>
                            </div>
                            @if(sizeof($addresses["addresses"]) < 1)
                            <p>
                                You have no addresses yet.
                            </p>
                            @else
                                <div class="goroup-accrodion mb-60">
                                    <div class="panel-group" id="accordion" role="tablist">
                                        @for ($i = 0; $i < sizeof($addresses["addresses"]); $i++)
                                            <!--Single Accrodion Start-->
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title" style="font-size:12px;">
                                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$i}}">
                                                            {{ $addresses["addresses"][$i]["ca_town"] }}, {{ $addresses["addresses"][$i]["ca_address"] }}
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="collapse{{$i}}" class="panel-collapse collapse">
                                                    <div class="panel-body">
                                                        <form action="{{ route("process.account.edit.address") }}" method="POST">
                                                            @csrf
                                                            <div class="col-md-12">
                                                                <div class="form-fild">
                                                                    <select name="address_town" value="" placeholder="Select Town" style="padding-left: 5px; border: 1px solid #e5e5e5; border-radius: 5px; height: 35px;" required>
                                                                        @for ($j = 0; $j < sizeof($addresses["options"]); $j++)
                                                                        <option value="{{$addresses["options"][$j]["sf_town"]}}||{{ $addresses["options"][$j]["sf_region"] }}" @if($addresses["options"][$j]["sf_town"] == $addresses["addresses"][$i]["ca_town"]) selected @endif>
                                                                                  {{  $addresses["options"][$j]["sf_town"] }}
                                                                            </option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                                <div class="form-fild">
                                                                    <input type="text" name="address_details" value="{{ $addresses["addresses"][$i]["ca_address"] }}" placeholder="Enter your address." required>
                                                                </div>
                                                            </div>
                                                            <div class="register-submit" style="text-align:center;">
                                                                <input type="hidden" name="aid" value="{{ $addresses["addresses"][$i]["id"] }}" />
                                                                <button type="" name="update_address" class="form-button" style="margin-top: 15px;">Update Address</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--Single Accrodion End-->
                                        @endfor
                                    </div>
                                </div>
                            @endif
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
                                <li style="background-color: #f68b1e; color:white; border-radius: 10px;">
                                    <a style="margin-left:15px; font-size: 12px; color:white;" href="{{ route('show.account.addresses') }}">
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