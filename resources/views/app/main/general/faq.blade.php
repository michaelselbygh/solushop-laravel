@extends('app.layouts.general')
@section('page-title')Frequently asked questions. @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Check out these frequently asked questions on Solushop Ghana. @endsection
@section('page-content')
    <!--Heading Banner Area Start-->
    <section class="heading-banner-area pt-10">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading-banner">
                        <div class="breadcrumbs">
                            <ul>
                                <li><a href="{{ route('home') }}">Home</a><span class="breadcome-separator">></span></li>
                                <li>Frequently Asked Questions</li>
                            </ul>
                        </div><br>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Heading Banner Area End-->

    <section class="frequently-question">
        <div class="container">
            <div class="row">
                <!--Frequently Accordion Start-->
                <div class="col-sm-6 col-sm-offset-3">           
                    <div class="goroup-accrodion mb-60">
                        <div class="panel-group" id="accordion" role="tablist">
                            <!--Single Accrodion Start-->
                            <div class="panel panel-default active">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                                        Are all products on Solushop original and genuine?</a>
                                    </h4>
                                </div>
                                <div id="collapse1" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        Yes. We are committed to offering our customers only 100% genuine and original products. We also take all necessary actions to ensure that any supplier found to be supplying non-genuine products to us is immediately delisted from Solushop.
                                        <br><br>
                                        Please send an email to support@solushop.com.gh if you think a product listed on our website does not meet these standards.
                                    </div>
                                </div>
                            </div>
                            <!--Single Accrodion End-->
                            <!--Single Accrodion Start-->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                                        Do I need an account to shop on Solushop?</a>
                                    </h4>
                                </div>
                                <div id="collapse2" class="panel-collapse collapse">
                                    <div class="panel-body">No you don't need an account to surf products and compare prices. However to ensure you get the best delivery and checkout experience, we require that you sign up when making purchases.</div>
                                </div>
                            </div>
                            <!--Single Accrodion End-->
                            <!--Single Accrodion Start-->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse3">
                                        How do I place an order?</a>
                                    </h4>
                                </div>
                                <div id="collapse3" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        Shopping on Solushop is easy! Once you have found the product you want to buy, just follow the steps below:<br><br>
                                        <b>
                                            <ol>
                                                <li>1. Sign up to unlock your carting feature.</li>
                                                <li>2. Add products to cart.</li>
                                                <li>3. Confirm cart and proceed to checkout.</li>
                                                <li>4. Pay and that's it!</li>
                                            </ol>
                                        </b>
                                        Once your order is placed, we will either automatically confirm it by notifying you via email, or we will call you for confirmation in case we need more details. <br>
                                        Please note that this confirmation is a mandatory step before we ship your order. If you are unsure of whether your order has been confirmed or not, please contact our Customer Service Call Center on 0506753093 or support@solushop.com.gh a few hours after your order placement.
                                    </div>
                                </div>
                            </div>
                            <!--Single Accrodion End-->
                            <!--Single Accrodion Start-->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse4">
                                        How do I pay on Solushop?</a>
                                    </h4>
                                </div>
                                <div id="collapse4" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        You can choose from the different payment methods available on Solushop. Please find below the list of available payment methods.<br><br>
                                        <b>
                                            <ol>
                                                <li>1. Visa</li>
                                                <li>2. Mastercard</li>
                                                <li>3. Slydepay</li>
                                                <li>4. MTN Mobile Money</li>
                                                <li>5. Tigo Cash</li>
                                                <li>6. Airtel Money</li>
                                            </ol>
                                        </b>
                                        You can find the accepted payment methods at the bottom of all the pages.
                                    </div>
                                </div>
                            </div>
                            <!--Single Accrodion End-->
                            <!--Single Accrodion Start-->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse5">
                                        Can someone collect my package on my behalf?</a>
                                    </h4>
                                </div>
                                <div id="collapse5" class="panel-collapse collapse">
                                    <div class="panel-body">Yes, simply inform our delivery agents when they call on the day of delivery and provide a name and telephone number of the person collecting on your behalf.</div>
                                </div>
                            </div>
                            <!--Single Accrodion End-->
                            <!--Single Accrodion Start-->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse6">
                                        Will I receive all the items in my order at once?</a>
                                    </h4>
                                </div>
                                <div id="collapse6" class="panel-collapse collapse">
                                    <div class="panel-body">You may receive items in your order separately because they are supplied by different vendors. We ship as quickly as items are made available by vendors.</div>
                                </div>
                            </div>
                            <!--Single Accrodion End-->
                            <!--Single Accrodion Start-->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse7">
                                        Any hidden costs or charges?</a>
                                    </h4>
                                </div>
                                <div id="collapse7" class="panel-collapse collapse">
                                    <div class="panel-body">There are no hidden costs or charges when you order from Solushop. All costs are 100% visible at the end of the checkout process.</div>
                                </div>
                            </div>
                            <!--Single Accrodion End-->
                            <!--Single Accrodion Start-->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse8">
                                        How can I track my order?</a>
                                    </h4>
                                </div>
                                <div id="collapse8" class="panel-collapse collapse">
                                    <div class="panel-body">We will send you regular updates about the status of your order via emails and SMS</div>
                                </div>
                            </div>
                            <!--Single Accrodion End-->
                        </div>
                    </div>
                </div>
                <!--Frequently Accordion End-->
            </div>
        </div>
    </section>
@endsection