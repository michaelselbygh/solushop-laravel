@extends('app.layouts.general')
@section('page-title')Return Policy @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')How we handle returns and refunds. @endsection
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
                                <li>Returns and Refunds</li>
                            </ul>
                        </div><br>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Heading Banner Area End-->

    <section class="contact-form-area ">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">    
                    <p>
                        Thanks for shopping at Solushop Ghana. If you are not entirely satisfied with your purchase, we're here to help.
                    </p>
                    <br>       
                    <div class="goroup-accrodion mb-60">
                        <div class="panel-group" id="accordion" role="tablist">
                            <!--Single Accrodion Start-->
                            <div class="panel panel-default active">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                                            Returns
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse1" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <p>
                                            You have 3 calendar days to return an item from the date you received it. To be eligible for a return, your item must be unused and in the same condition that you received it. Your item must be in the original packaging. Your item needs to have the receipt or proof of purchase.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!--Single Accrodion End-->
                            <!--Single Accrodion Start-->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                                            Refunds
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse2" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <p>
                                            Once we receive your item, we will inspect it and notify you that we have received your returned item. We will immediately notify you on the status of your refund after inspecting the item.If your return is approved, we will initiate a refund to your solushop wallet.                  
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!--Single Accrodion End-->
                            <!--Single Accrodion Start-->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse3">
                                            Shipping
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse3" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <p>
                                            You will be responsible for paying for your own shipping costs for returning your item. Shipping costs are non¬refundable. If you receive a refund, the cost of return shipping will be deducted from your refund.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!--Single Accrodion End-->
                            <!--Single Accrodion Start-->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse4">
                                            Contact
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse4" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <p>
                                            If you have any questions on how to return your item to us, <a href="{{ route('show.contact') }}">contact us</a>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!--Single Accrodion End-->
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection