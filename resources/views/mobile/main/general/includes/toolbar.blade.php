
    <div class="toolbar tabbar tabbar-labels toolbar-bottom">
        <div class="toolbar-inner">
            <a href="{{ route('home') }}" class="tab-link tab-link-active external">
                <i class="ti-home"></i>
                <span class="tabbar-label">Home</span>
            </a>
            <a href="{{ route('show.shop') }}" class="tab-link external">
                <i class="ti-gift"></i>
                <span class="tabbar-label">Shop </span>
            </a>
            <a href="{{ route('show.cart') }}" class="tab-link external">
                <i class="ti-shopping-cart"></i>
                <span class="tabbar-label">Cart @if($customer_information['cart_count'] > 0)({{$customer_information['cart_count']}})@endif</span>
            </a>
            <a href="{{ route('show.wishlist') }}" class="tab-link external">
                <i class="ti-heart "></i>
                <span class="tabbar-label">Wishlist @if($customer_information['wishlist_count'] > 0)({{$customer_information['wishlist_count']}})@endif</span>
            </a>
            
            @if(Auth::check())
                <a href="{{ route('show.account.dashboard') }}" class="tab-link external">
                    <i class="ti-user"></i>
                    <span class="tabbar-label">Account</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="tab-link external">
                    <i class="ti-user"></i>
                    <span class="tabbar-label">Login</span>
                </a>
            @endif
            
        </div>
    </div>