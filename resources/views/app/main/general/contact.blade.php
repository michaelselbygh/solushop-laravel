@extends('app.layouts.general')
@section('page-title')
    Let's get talking.
@endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')
    Questions or suggestions? Let's talk. We are always happy to hear from you.
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
                                <li><a href="{{ route('home') }}">Home</a><span class="breadcome-separator">></span></li>
                                <li>Let's get talking</li>
                            </ul>
                        </div><br>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Heading Banner Area End-->

    <section class="contact-form-area mt-50 mb-50">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-6" style="text-align: right;">
                    <img src="{{ url('app/assets/img/contact.jpg') }}" style="border-radius:20px; width:350px;" />
                </div>
                <div class="col-md-6 col-sm-6">
                    <div style="width: 350px;">
                        <h4 style="text-align:left; margin-top:30px;">Let's get talking.</h4>
                        <br>
                        <p>
                            Our Team of ever ready and able personnel are always here to attented to your needs.<br><br>Our most common questions have been answered <a href="{{ route('show.frequently.asked.questions') }}"> here on frequently asked questions</a>. You can also ask a question if it's not already addressed. 
                        </p>  
                        <div class="contact">
                            <p>
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
                </div>
            </div>
            <br><br>
        </div>
    </section>
@endsection