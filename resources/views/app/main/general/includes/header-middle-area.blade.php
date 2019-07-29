<!--Header Middel Area Start-->
<div class="header-middel-area">
    <div class="container">
        <div class="row">
            <!--Logo Start-->
            <div class="col-md-2 col-sm-3 col-xs-12">
                <div class="logo">
                    <a href="{{ route('home') }}"><img src="{{ url('app/assets/img/logo/logo.png') }}" alt="Solushop Logo" style="width: 120px;
                        height: auto;"></a>
                </div>
            </div>
            <!--Logo End-->
            <!--Search Box Start-->
            <div class="col-md-7 col-sm-5 col-xs-12">
                <div class="search-box-area">
                    <form action="{{ route('show.shop.search') }}" method="POST">
                        @csrf
                        <div class="search-box">
                            <input type="text" name="search_query_string" id="search" placeholder="Search for something e.g. Perfumes" value=''>
                            <button name="Search" type="submit"><i class="ion-ios-search-strong"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <!--Search Box End-->
            <!--Mini Cart Start-->
            <div class="col-md-3 col-sm-4 col-xs-12">
                <div class="mini-cart-area" style="text-align: center;">
                    <ul>
                        <li >
                            <a href="{{ route('show.wishlist') }}">
                                <i class="ion-android-star" style="color:white;"></i>
                                <span class="cart-add" style="color:white;">
                                    {{ $customer_information["wishlist_count"] }}
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('show.cart') }}">
                                <i class="ion-android-cart"></i>
                                <span class="cart-add">
                                   {{ $customer_information["cart_count"] }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!--Mini Cart End-->
        </div>
    </div>
</div>
<!--Header Middel Area End-->