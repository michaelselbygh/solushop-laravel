<div class="main-menu menu-fixed menu-dark menu-accordion    menu-shadow " data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item">
                <a href="{{ route('delivery-partner.dashboard') }}">
                    <i class="la la-home" style="color:#f68b1e;"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('delivery-partner.show.pick.ups') }}">
                    <i class="la la-reply" style="color:#f68b1e;"></i>
                    <span class="menu-title">Pick-Ups</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('delivery-partner.show.deliveries') }}">
                    <i class="la la-share" style="color:#f68b1e;"></i>
                    <span class="menu-title">Deliveries</span>
                </a>
            </li>
            @if (Auth::guard('delivery-partner')->user())
                <li class="nav-item">
                    <a href="{{ route("delivery-partner.logout") }}">
                        <i class="la la-lock" style="color:#f68b1e;"></i>
                        <span class="menu-title">Logout</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>
    