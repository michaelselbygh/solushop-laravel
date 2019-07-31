<!-- sidebar -->
<div class="panel panel-left panel-cover">
    <div class="list links-list">
        <ul>
            <li>
                {{-- change url to showcategories for more control --}}
                <a href="{{ route('show.vendors') }}" class="panel-close external">
                    <div class="item-media">
                        <i class="ti-link"></i>
                    </div>
                    <div class="item-inner">
                        <div class="item-title">
                            Vendors
                        </div>
                    </div>
                </a>
            <li>
                <a href="{{ route('show.about') }}" class="panel-close external">
                    <div class="item-media">
                        <i class="ti-shopping-cart-full"></i>
                    </div>
                    <div class="item-inner">
                        <div class="item-title">
                            About Solushop
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('show.contact') }}" class="panel-close external">
                    <div class="item-media">
                        <i class="ti-headphone-alt"></i>
                    </div>
                    <div class="item-inner">
                        <div class="item-title">
                            Contact Us
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('show.frequently.asked.questions') }}" class="panel-close external">
                    <div class="item-media">
                        <i class="ti-help-alt"></i>
                    </div>
                    <div class="item-inner">
                        <div class="item-title">
                            FAQs
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('show.privacy.policy') }}" class="panel-close external">
                    <div class="item-media">
                        <i class="ti-rss"></i>
                    </div>
                    <div class="item-inner">
                        <div class="item-title">
                            Privacy Policy
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('show.return.policy') }}" class="panel-close external">
                    <div class="item-media">
                        <i class="ti-loop"></i>
                    </div>
                    <div class="item-inner">
                        <div class="item-title">
                            Return Policy
                        </div>
                    </div>
                </a>
            <li>
                <a href="{{ route('show.terms.and.conditions') }}" class="panel-close external">
                    <div class="item-media">
                        <i class="ti-thumb-up"></i>
                    </div>
                    <div class="item-inner">
                        <div class="item-title">
                            T &amp; Cs
                        </div>
                    </div>
                </a>
            </li>
            @if(Auth::check())
                <li>
                    <a href="{{ route('show.account.dashboard') }}" class="panel-close external">
                        <div class="item-media">
                            <i class="ti-settings"></i>
                        </div>
                        <div class="item-inner">
                            <div class="item-title">
                                Settings
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
            @else
                <li>
                    <a href="{{ route('login') }}" class="panel-close external">
                        <div class="item-media">
                            <i class="ti-shift-right"></i>
                        </div>
                        <div class="item-inner">
                            <div class="item-title">
                                Sign In
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('register') }}" class="panel-close external">
                        <div class="item-media">
                            <i class="ti-plus"></i>
                        </div>
                        <div class="item-inner">
                            <div class="item-title">
                                Sign Up
                            </div>
                        </div>
                    </a>
                </li>
                
            @endif
        </ul>
    </div>
</div>
<!-- end sidebar -->