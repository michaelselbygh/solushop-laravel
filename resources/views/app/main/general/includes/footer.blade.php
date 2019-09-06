<!--Footer Area Start-->
<footer>
    <div class="footer-container white-bg">
        <!--Footer Top Area Start-->
        <div class="footer-top-area ptb-50">
            <div class="container">
                <div class="row">
                    <!--Single Footer Start-->
                    <div class="col-md-4 col-sm-6">
                        <div class="single-footer">
                            <!--Footer Logo Start-->
                            <div class="footer-logo">
                                
                            </div>
                            <!--Footer Logo End-->
                            <!--Footer Content Start-->
                            <div class="footer-content">
                                <div><a href="{{ route('home') }}"><img src="{{ url('app/assets/img/logo/logo2.png') }}" alt="Solushop Logo" style="width: 130px;
                                    height: auto;"></a>
                                </div>
                                <p>Ghana's Most Trusted Online Store.</p>
                                
                                <div class="contact">
                                    <p>
                                        Accra, Ghana.<br>
                                        <a href="mailto:hello@solushop.com.gh">hello@solushop.com.gh</a><br>
                                        <i class="fa fa-phone" style="color: #f68b1e;"></i> 
                                        <a href="{{ url('tel:233506753093') }}" >
                                            <span> 0506753093 </span>
                                        </a>
                                        &nbsp;|&nbsp;&nbsp; 
                                        <i class="fa fa-whatsapp" style="font-size: 14px; color: #f68b1e;"></i>
                                        <a href="{{ url('https://api.whatsapp.com/send?phone=233506753093') }}" target="_blank">
                                            <span> 0506753093 </span>
                                        </a>
                                        
                                    </p>
                                </div>
                            </div>
                            <!--Footer Content End-->
                        </div>
                    </div>
                    <!--Single Footer End-->
                    <!--Single Footer Start-->
                    <div class="col-md-2 col-sm-6">
                        <div class="single-footer mt-30">
                            <ul class="footer-info">
                                <li><a href="{{ route('show.about') }}">About Us</a></li>
                                <li><a href="{{ route('show.contact') }}">Contact</a></li>
                                <li><a href="{{ route('show.frequently.asked.questions') }}">FAQ</a></li>
                                <li><a href="{{ route('show.privacy.policy') }}">Privacy Policy</a></li>
                                <li><a href="{{ route('show.return.policy') }}">Return Policy</a></li>
                                <li><a href="{{ route('show.terms.and.conditions') }}">T &amp; Cs</a></li>
                            </ul>
                        </div>
                    </div>
                    <!--Single Footer End-->
                    <!--Single Footer Start-->
                    <div class="col-md-2 col-sm-6">
                        <div class="single-footer mt-30">
                            <ul class="footer-info">
                                   @if (Auth::check())
                                        <li><a href="{{ route('show.account.dashboard') }}">My Account</a></li>
                                    @else
                                        <li><a href="{{ route('login') }}">Login / Register</a></li>
                                    @endif
                                <li><a href="{{ route('show.wishlist') }}">Wishlist</a></li>
                                <li><a href="{{ route('show.cart') }}">Shopping Cart</a></li>
                                <li><a href="{{ route('show.checkout') }}">Checkout</a></li>
                            </ul>
                        </div>
                    </div>
                    <!--Single Footer End-->
                    <!--Single Footer Start-->
                    <div class="col-md-4 col-sm-6">
                        <div class="single-footer mt-30">
                            <div class="footer-title">
                                <h3>Follow Us</h3>
                            </div>
                            <ul class="socil-icon mb-10">
                                <li><a href="{{ url('https://twitter.com/SolushopGhana') }}" data-toggle="tooltip" title="Twitter"><i class="ion-social-twitter" style="color: #fff;"></i></a></li>
                                <li><a href="{{ url('https://www.facebook.com/solushopghana/') }}" data-toggle="tooltip" title="Facebook"><i class="ion-social-facebook" style="color: #fff; font-size: 18px;"></i></a></li>
                                <li><a href="{{ url('https://www.instagram.com/solushopghana/') }}" style="color:purple" data-toggle="tooltip" title="Instagram"><i class="ion-social-instagram" style="color: #fff; font-size: 18px;"></i></a></li>
                                <li><a href="{{ url('https://www.youtube.com/channel/UC6RdTmn3tuhoK2NI5Zj-4yA') }}" style="color:red" data-toggle="tooltip" title="Youtube"><i class="ion-social-youtube" style="color: #fff;"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <!--Single Footer End-->
                </div>
            </div>
        </div>
        <!--Footer Top Area End-->
        <!--Footer Bottom Area Start-->
        <div class="footer-bottom-area">
            <div class="container">
                <div class="row">
                    <!--Footer Left Content Start-->
                    <div class="col-md-6 col-sm-6">
                        <div class="copyright-text">
                            <p>Copyright © {{ date("Y") }} <a target="_blank">Solushop Ghana Limited</a> All Rights Reserved.</p>
                        </div>
                    </div>
                    <!--Footer Left Content End-->
                    <!--Footer Right Content Start-->
                    <div class="col-md-6 col-sm-6">
                        <div class="payment-img text-right" id="accepted-payment">
                            <a href="#"><img src="{{ url('app/assets/img/payment/payment.png') }}" alt="Solushop Payment Options" style="height: 25px;"></a>
                        </div>
                    </div>
                    <!--Footer Right Content End-->
                </div>
            </div>
        </div>
        <!--Footer Bottom Area End-->
    </div>
</footer>
<!--Footer Area End-->