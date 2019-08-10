
        <!-- BEGIN: Main Menu-->

        <div class="main-menu menu-fixed menu-dark menu-accordion    menu-shadow " data-scroll-to-active="true">
                <div class="main-menu-content">
                        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                            <li class="nav-item">
                                <a href="{{ route('sales-associate.dashboard') }}">
                                    <i class="la la-user" style="color:#f68b1e;"></i>
                                    <span class="menu-title">Dashboard</span>
                                </a>
                            </li>
                            <li class=" nav-item"><a href="{{ route('sales-associate.show.customers') }}"><i class="la la-users" style="color:#f68b1e;"></i><span class="menu-title">Customers</span></a>
                                <ul class="menu-content">
                                    <li>
                                        <a class="menu-item" href="{{ route('sales-associate.show.customers') }}">
                                            <span>Manage Customers</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route('sales-associate.show.add.customer') }}">
                                            <span>Add Customer</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class=" nav-item"><a href="{{ route('sales-associate.show.orders') }}"><i class="la la-shopping-cart" style="color:#f68b1e;"></i><span class="menu-title">Orders</span></a>
                                <ul class="menu-content">
                                    <li>
                                        <a class="menu-item" href="{{ route('sales-associate.show.orders') }}">
                                            <span>Manage Orders</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route('sales-associate.show.add.order.step-1') }}">
                                            <span>Initiate Order</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sales-associate.show.terms.of.use') }}">
                                    <i class="la la-exclamation-circle" style="color:#f68b1e;"></i>
                                    <span class="menu-title">Terms of Use</span>
                                </a>
                            </li>
                            @if (Auth::guard('sales-associate')->user())
                                <li class="nav-item">
                                    <a href="{{ route("sales-associate.logout") }}">
                                        <i class="la la-lock" style="color:#f68b1e;"></i>
                                        <span class="menu-title">Logout</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                </div>
            </div>
    