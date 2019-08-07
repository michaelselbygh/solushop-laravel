
        <!-- BEGIN: Main Menu-->

        <div class="main-menu menu-fixed menu-dark menu-accordion    menu-shadow " data-scroll-to-active="true">
                <div class="main-menu-content">
                        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                            <li class="nav-item">
                                <a href="{{ route('manager.dashboard') }}">
                                    <i class="la la-home" style="color:#f68b1e;"></i>
                                    <span class="menu-title">Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('manager.show.customers') }}">
                                    <i class="la la-users" style="color:#f68b1e;"></i>
                                    <span class="menu-title">Customers</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('manager.show.orders') }}">
                                    <i class="la la-shopping-cart" style="color:#f68b1e;"></i>
                                    <span class="menu-title">Orders</span>
                                </a>
                            </li>
                            <li class=" nav-item"><a href="{{ route('manager.show.messages') }}"><i class="la la-comments" style="color:#f68b1e;"></i><span class="menu-title">Messages</span></a>
                                <ul class="menu-content">
                                    <li>
                                        <a class="menu-item" href="{{ route('manager.show.messages') }}">
                                            <span>Conversations</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route('manager.show.flagged.messages') }}">
                                            <span>Flags</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route('manager.show.deleted.messages') }}">
                                            <span>Deleted Messages</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class=" nav-item"><a href="{{ route('manager.show.products') }}"><i class="la la-archive" style="color:#f68b1e;"></i><span class="menu-title">Products</span></a>
                                <ul class="menu-content">
                                    <li>
                                        <a class="menu-item" href="{{ route('manager.show.products') }}">
                                            <span>Manage Products</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route('manager.show.add.product') }}">
                                            <span>Add Product</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route('manager.show.deleted.products') }}">
                                            <span>Deleted Products</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item"><a href="{{ route('manager.show.vendors') }}"><i class="la la-suitcase" style="color:#f68b1e;"></i><span class="menu-title">Vendors</span></a>
                                <ul class="menu-content">
                                    <li>
                                        <a class="menu-item" href="{{ route('manager.show.vendors') }}">
                                            <span>Manage Vendors</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route('manager.show.add.vendor') }}">
                                            <span>Add Vendor</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class=" nav-item"><a><i class="la la-plane" style="color:#f68b1e;"></i><span class="menu-title">Logistics</span></a>
                                <ul class="menu-content">
                                    <li>
                                        <a class="menu-item" href="{{ route("manager.show.active.pick.ups") }}">
                                            <span>Active Pick Ups</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route("manager.show.active.deliveries") }}">
                                            <span>Active Deliveries</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route("manager.show.pick.ups.history") }}">
                                            <span>Pick-Up History</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route("manager.show.deliveries.history") }}">
                                            <span>Delivery History</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class=" nav-item"><a href="{{ route("manager.show.delivery.partners") }}"><i class="la la-gift" style="color:#f68b1e;"></i><span class="menu-title">Delivery Partners</span></a>
                                <ul class="menu-content">
                                    <li>
                                        <a class="menu-item" href="{{ route("manager.show.delivery.partners") }}">
                                            <span>Manage Delivery Partners</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route("manager.show.add.delivery.partner") }}">
                                            <span>Add Delivery Partner</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class=" nav-item"><a href="{{ route("manager.show.coupons") }}"><i class="la la-ticket" style="color:#f68b1e;"></i><span class="menu-title">Coupons</span></a>
                                <ul class="menu-content">
                                    <li>
                                        <a class="menu-item" href="{{ route("manager.show.coupons") }}">
                                            <span>Manage Coupons</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route("manager.show.generate.coupon") }}">
                                            <span>Generate Coupon</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class=" nav-item"><a href="{{ route("manager.show.sales.associates") }}"><i class="la la-signal" style="color:#f68b1e;"></i><span class="menu-title">Sales Associates</span></a>
                                <ul class="menu-content">
                                    <li>
                                        <a class="menu-item" href="{{ route("manager.show.sales.associates") }}">
                                            <span>Manage Sales Associates</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route("manager.show.add.sales.associate") }}">
                                            <span>Add Sales Associate</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route("manager.subscriptions") }}">
                                    <i class="la la-tag" style="color:#f68b1e;"></i>
                                    <span class="menu-title">Subscriptions</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route("manager.show.accounts") }}">
                                    <i class="la la-money" style="color:#f68b1e;"></i>
                                    <span class="menu-title">Accounts</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route("manager.sms.report") }}" >
                                    <i class="la la-envelope" style="color:#f68b1e;"></i>
                                    <span class="menu-title">SMS Report</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route("manager.activity.log") }}">
                                    <i class="la la-server" style="color:#f68b1e;"></i>
                                    <span class="menu-title">Activity Log</span>
                                </a>
                            </li>
                            @if (Auth::user())
                                <li class="nav-item">
                                    <a href="{{ route("manager.logout") }}">
                                        <i class="la la-lock" style="color:#f68b1e;"></i>
                                        <span class="menu-title">Logout</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                </div>
            </div>
    