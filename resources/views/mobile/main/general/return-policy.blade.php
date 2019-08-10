@extends('mobile.layouts.general')
@section('page-title')Return Policy @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')How we handle returns and refunds. @endsection
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
                            Returns &amp; Refunds
                        </div>
                    </div>
                </div>

                <!-- tracking order -->
                <div class="tracking-order segments-page">
                        <div class="container">
                            <p>
                                Thanks for shopping at Solushop Ghana. If you are not entirely satisfied with your purchase, we're here to help.
                            </p>
                            <br> 
                            <div class="accordion-list">                    
                                <div class="accordion-item accordion-item-opened">
                                    <div class="accordion-item-toggle">
                                        Returns
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        <p>
                                            You have 3 calendar days to return an item from the date you received it. To be eligible for a return, your item must be unused and in the same condition that you received it. Your item must be in the original packaging. Your item needs to have the receipt or proof of purchase.
                                        </p>
                                        <br>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle">
                                        Refunds
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        <p>
                                            Once we receive your item, we will inspect it and notify you that we have received your returned item. We will immediately notify you on the status of your refund after inspecting the item.If your return is approved, we will initiate a refund to your solushop wallet.                  
                                        </p>
                                        <br>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle">
                                        Shipping
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        <p>
                                            You will be responsible for paying for your own shipping costs for returning your item. Shipping costs are non¬refundable. If you receive a refund, the cost of return shipping will be deducted from your refund.
                                        </p>
                                        <br>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle">
                                        Contact
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        <p>
                                            If you have any questions on how to return your item to us, <a href="{{ route('show.contact') }}">contact us</a>.
                                        </p>
                                        <br>
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
@endsection    
    