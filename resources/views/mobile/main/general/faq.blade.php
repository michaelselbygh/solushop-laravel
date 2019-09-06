@extends('mobile.layouts.general')
@section('page-title')Frequently asked questions. @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Check out these frequently asked questions on Solushop Ghana. @endsection
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
                            Frequently Asked Questions.
                        </div>
                    </div>
                </div>

                <div class="page-content" style="padding-top:10px;">
                    <!-- tracking order -->
                    <div class="tracking-order segments-page">
                        <div class="container">
                            <div class="accordion-list">                    
                                <div class="accordion-item accordion-item-opened">
                                    <div class="accordion-item-toggle">
                                        Are all products on Solushop genuine?
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        Yes. We are committed to offering our customers only 100% genuine and original products. We also take all necessary actions to ensure that any supplier found to be supplying non-genuine products to us is immediately delisted from Solushop.
                                        <br><br>
                                        Please send an email to support@solushop.com.gh if you think a product listed on our website does not meet these standards.
                                        <br><br>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle">
                                        Do I need an account to shop on Solushop?
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        No you don't need an account to surf products and compare prices. However to ensure you get the best delivery and checkout experience, we require that you sign up when making purchases.
                                        <br><br>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle">
                                        How do I place an order?
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        Shopping on Solushop is easy! Once you have found the product you want to buy, just follow the steps below:<br>
                                        <b>
                                            <ol>
                                                <li>Sign up to unlock your carting feature.</li>
                                                <li>Add products to cart.</li>
                                                <li>Confirm cart and proceed to checkout.</li>
                                                <li> Pay and that's it!</li>
                                            </ol>
                                        </b>
                                        Once your order is placed, we will either automatically confirm it by notifying you via email, or we will call you for confirmation in case we need more details. <br><br>
                                        Please note that this confirmation is a mandatory step before we ship your order. If you are unsure of whether your order has been confirmed or not, please contact our Customer Service Call Center on 
                                        <a href="{{ url('tel:233506753093') }}" >
                                            <span> 0506753093 </span>
                                        </a> an hour or two after your order placement.
                                        <br><br>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle">
                                       How do I pay on Solushop?
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        You can choose from the different payment methods available on Solushop. Please find below the list of available payment methods.<br>
                                        <b>
                                            <ol>
                                                <li>Visa</li>
                                                <li>Mastercard</li>
                                                <li>Slydepay</li>
                                                <li>MTN Mobile Money</li>
                                                <li>Tigo Cash</li>
                                                <li>Airtel Money</li>
                                            </ol>
                                        </b>
                                        You can find the accepted payment methods at the bottom of all the pages.
                                        <br><br>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle">
                                        Can someone collect my package on my behalf?
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        Yes, simply inform our delivery agents when they call on the day of delivery and provide a name and telephone number of the person collecting on your behalf.
                                        <br><br>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle">
                                       Will I receive all the items in my order at once?
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        You may receive items in your order separately because they are supplied by different vendors. We ship as quickly as items are made available by vendors.
                                        <br><br>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle">
                                        Any hidden costs or charges?
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        There are no hidden costs or charges when you order from Solushop. All costs are 100% visible at the end of the checkout process.
                                        <br><br>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle">
                                        How can I track my order?
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        We will send you regular updates about the status of your order via emails and SMS.
                                        <br><br>
                                    </div>
                                </div>
                            </div>
                            @if (session()->has('welcome_message')) 
                                <div id="snackbar">{{ session()->get('welcome_message') }}</div>
                            @endif
                        </div>
                    </div>
                    <!-- end tracking order -->
                </div>
            </div>
        </div>
    </div>
@endsection    
    