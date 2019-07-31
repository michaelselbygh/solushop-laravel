@extends('mobile.layouts.general')
@section('page-title')
    About Solushop
@endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')
    Solushop is Ghana&#039;s most trusted Online Shopping Mall ➜Shop electronics, accessories, books, fashion &amp; more online ✔ Great customer care ✔ Top quality products ✓ super fast shipping ✓ Order now and enjoy a revolutionary shopping experience!
@endsection
@section('page-content')
    <div class="page page-home">
        @include('mobile.main.general.includes.toolbar')
        <div class="tabs page-content">
            <div id="tab-1" class="tab tab-active">
                <!-- home -->

                <div class="navbar navbar-page">
                    <div class="navbar-inner sliding">
                        <div class="left">
                            <a href="{{ URL::previous() }}" class="link back external">
                                <i class="ti-arrow-left"></i>
                            </a>
                        </div>
                        <div class="title">
                            About Solushop
                        </div>
                    </div>
                </div>

                <div class="about segments-page">
                    <div class="container">
                        <div class="content">
                            <img src="{{ url('app/assets/img/about/2.jpg') }}" style="border-radius:20px; width:100%"><br>
                            <p>Solushop is a community of driven vendors and satisfied customers. Provision of quality products, services and satisfaction of our customers is our hallmark. We seek to ensure that our customers shop with great passion and happiness. All products are subjected to strict quality checks to guarantee that customers’ expectations are met.</p><br>

                            <img src="{{ url('app/assets/img/about/1.jpg') }}" style="border-radius:20px; width:100%">
                            <h5 style="text-align:center; margin-top:40px;">Our vision here at Solushop is<br><br><i style="font-size:14px; padding:10px; font-weight: 350">"To be the No. 1 Online Store in Ghana, and Africa with happy and satisfied buyers and vendors."</i></h5><br><br>

                            <img src="{{ url('app/assets/img/about/3.jpg') }}" style="border-radius:20px; width:100%"><br>
                            <p>Buying on Solushop is extremely easy. Simply sign up, add products to your cart and pay to initiate your order. Confirmation will be made via phone and your order will be processed and delivered within a week! Amazing right? Start shopping now.</p><br>
                            
                            <img src="{{ url('app/assets/img/about/4.jpg') }}" style="border-radius:20px; width:100%"><br>
                            <p>To become a vendor on Solushop, you need to have an authentic and verified product source, and a pickup point in Accra. You can apply via whatsapp on 0506753093 or email management@solushop.com.gh. Terms and Conditions Apply</p><br>
                        </div>
                        <div class="social-media-wrapper">
                            <ul>
                                <li><a href="{{ url('https://www.facebook.com/solushopghana/') }}" target="_blank" class="external"><i class="ti-facebook"></i></a></li>
                                <li><a href="{{ url('https://twitter.com/SolushopGhana') }}" target="_blank" class="external"><i class="ti-twitter"></i></a></li>
                                <li><a href="{{ url('https://www.instagram.com/solushopghana/') }}" target="_blank" class="external"><i class="ti-instagram"></i></a></li>
                            </ul>
                            <br>Let's connect.<br><br>
                        </div>
                        @if (session()->has('welcome_message')) 
                            <div id="snackbar">{{ session()->get('welcome_message') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection    
    