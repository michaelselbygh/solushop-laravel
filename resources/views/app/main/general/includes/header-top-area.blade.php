<!--Header Top Area Start--> 
<div class="header-top-area">
    <div class="container">
        <div class="row">
            <!--Header Top Left Area Start-->
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="header-top-menu">
                    <ul>
                        <li style="padding-right:10px; margin-right:0px;"><a href="shop.php"><i class="fa fa-shopping-cart"></i> &nbsp;Shop</a></li>
                        @if (Auth::check())
                            <li style="padding-right:10px; margin-right:0px;" class="">
                                <a href="my-account/"><i class="fa fa-user"></i>&nbsp;&nbsp; My Account </a>
                            </li>
                            <li style="padding-right:10px; margin-right:0px;" class="">
                                <a href="{{ route('customer.logout') }}">&nbsp;&nbsp; Logout </a>
                            </li>
                        @else
                            {{-- In processing, redirect back to either previous if any, or shop page. --}}
                            <li style="padding-right:10px; margin-right:0px;" class="">
                                <a href="{{ route('login') }}"><i class="fa fa-user"></i>&nbsp;&nbsp; Register / Login </a>
                            </li>
                        @endif
                        <li>
                            <a href=""><img style="height:22px;" src="{{ url('app/assets/img/payment/payment.png') }}" alt=""></a>
                        </li>
                    </ul>
                </div>
            </div>
            <!--Header Top Left Area End-->
            <!--Header Top Right Area Start-->
            <div class="col-md-6 col-sm-6 hidden-xs text-right">
                <div class="header-top-menu">
                    <ul>
                        <li class="fa fa-phone" style="font-size: 12px; padding-right: 10px;"><a href="{{ url('tel:233506753093') }}" ><span>&nbsp; 0506753093 </span></a></li>
                        <li class="fa fa-whatsapp" style="font-size: 12px; padding-right:10px"><a href="{{ url('https://api.whatsapp.com/send?phone=233506753093') }}" target="_blank"><span>&nbsp;&nbsp;0506753093 </span></a></li>
                        @if (Auth::check())
                            {{-- Return wallet balance from controller --}}
                            <li class="wallet">
                                <a href="my-account/wallet.php">
                                    <span style='color:white; padding-right: 10px;'>
                                        <b>¢ 0.00 </b>
                                    </span>
                                </a>
                            </li>

                            <li class="account">
                                <a href=""> {{ Auth::user()->first_name }} <i class="fa fa-angle-down"></i></a>
                                <ul class="ht-dropdown">
                                    <li>
                                        <a href="checkout.php"> Checkout</a>
                                    </li>
                                    <li>
                                        <a href="my-account/messages-area.php">
                                            <span style='color:white; background-color: red; padding: 4px 4px 4px 8px; border-radius:20px; margin-right:3px;'> 2 </span> Messages
                                        </a>
                                    </li>
                                    <li><a href="my-account/">My Account</a></li>
                                    <li><a href="my-account/wallet.php">Wallet: <b>¢ 0.00</b></a></li>
                                    <li><a href="{{ route('logout') }}">Logout</a></li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            <!--Header Top Right Area End-->
        </div>
    </div>
</div>
<!--Header Top Area End-->