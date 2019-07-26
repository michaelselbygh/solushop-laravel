
    <div class="toolbar tabbar tabbar-labels toolbar-bottom">
        <div class="toolbar-inner">
            <a href="{{ route('home') }}" class="tab-link tab-link-active external">
                <i class="ti-home"></i>
                <span class="tabbar-label">Home</span>
            </a>
            <a href="{{ route('show.shop') }}" class="tab-link external">
                <i class="ti-gift"></i>
                <span class="tabbar-label">Shop</span>
            </a>
            <a href="/cart" class="tab-link external">
                <i class="ti-shopping-cart"></i>
                <span class="tabbar-label">Cart</span>
            </a>
            <a href="/wishlist" class="tab-link external">
                <i class="ti-heart "></i>
                <span class="tabbar-label">Wishlist</span>
            </a>
            
            @if(Auth::check())
                <a href="/my-account/" class="tab-link external">
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