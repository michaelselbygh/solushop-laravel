@extends('app.layouts.general')
@section('page-title')
    About Solushop
@endsection
@section('page-image')
    {{ url('app/assets/img/Solushop.jpg') }}
@endsection
@section('page-description')
    Solushop is Ghana&#039;s most trusted Online Shopping Mall ➜Shop electronics, accessories, books, fashion &amp; more online ✔ Great customer care ✔ Top quality products ✓ super fast shipping ✓ Order now and enjoy a revolutionary shopping experience!
@endsection
@section('page-content')
    <!--Heading Banner Area Start-->
    <section class="heading-banner-area pt-10">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading-banner">
                        <div class="breadcrumbs">
                            <ul>
                                <li><a href="index.php">Home</a><span class="breadcome-separator">></span></li>
                                <li>About</li>
                            </ul>
                        </div><br>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Heading Banner Area End-->

    <section class="contact-form-area mt-20">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-6" style="text-align: right;">
                    <img src="{{ url('app/assets/img/about/2.jpg') }}" style="border-radius:20px; width:350px;" />
                </div>
                <div class="col-md-6 col-sm-6">
                    <div style="width: 350px;">
                        <h4 style="text-align:left;">We are more than a marketplace</h4>
                        <br>
                        <p style="color:#363f4d; font-size:12px; text-align:left;">Solushop is a community of driven vendors and satisfied customers. Provision of quality products, services and satisfaction of our customers is our hallmark. We seek to ensure that our customers shop with great passion and happiness. All products are subjected to strict quality checks to guarantee that customers’ expectations are met.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6" style="text-align: right;">
                    <div style="width: 350px; display: inline-block">
                        <h4 style="text-align:right; margin-top:50px;">Our vision here at Solushop is<br><br><i style="font-size:12px;  font-weight: 300">To be the No. 1 Online Store in Ghana,<br> and Africa with happy and satisfied buyers and vendors.</i></h4>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6" style="text-align: left;">
                    <img src="{{ url('app/assets/img/about/1.jpg') }}" style="border-radius:20px; width: 350px;" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6" style="text-align: right;">
                    <img src="{{ url('app/assets/img/about/3.jpg') }}" style="border-radius:20px;  width: 350px;" />
                </div>
                <div class="col-md-6 col-sm-6" style="text-align: left;">
                    <div style="width: 350px; display: inline-bloxk">
                        <h4 style="text-align:left; margin-top:40px;">Buying on Solushop</h4>
                        <br>
                        <p style="color:#363f4d; font-size:12px; text-align:left;">Buying on Solushop is extremely easy. Simply sign up, add products to your cart and pay to initiate your order. Confirmation will be made via phone and your order will be processed and delivered within a week! Amazing right? Start shopping now.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6" style="text-align: right;">
                    <div style="width: 350px; display:inline-block">
                        <h4 style="text-align:right; margin-top:30px;">Selling on Solushop</h4>
                        <br>
                        <p style="color:#363f4d; font-size:12px; text-align:right;">To become a vendor on Solushop, you need to have an authentic and verified product source, and a pickup point in Accra. You can apply via whatsapp on 0506753093 or email management@solushop.com.gh. Terms and Conditions Apply</p>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6" style="text-align: left;  width: 350px;">
                    <img src="{{ url('app/assets/img/about/4.jpg') }}" style="border-radius:20px;" />
                </div>
            </div>
            <br><br>
        </div>
    </section>
@endsection