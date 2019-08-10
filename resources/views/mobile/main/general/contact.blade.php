@extends('mobile.layouts.general')
@section('page-title')Let's get talking. @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Questions or suggestions? Let's talk. We are always happy to hear from you. @endsection
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
                            Let's get talking.
                        </div>
                    </div>
                </div>

                <div class="about segments-page">
                    <div class="container">
                        <div class="content">
                            <img src="{{ url('app/assets/img/contact.jpg') }}" style="border-radius:20px; width:100%"><br><br>
                            <p style="text-align: left"> Our Team of ever ready and able personnel are always here to attented to your needs.<br><br>Our most common questions have been answered <a href="{{ route('show.frequently.asked.questions') }}"> here on frequently asked questions</a>. You can also ask a question if it's not already addressed.</p><br>
                        </div>
                        <div class="social-media-wrapper">
                            <p>
                                <a href="mailto:hello@solushop.com.gh">hello@solushop.com.gh</a><br>
                                <i class="ti-headphone-alt" style="color: #f68b1e; margin-right: 3px;"></i> 
                                <a href="{{ url('tel:233506753093') }}" >
                                    <span> 0506753093 </span>
                                </a>
                                &nbsp;|&nbsp;&nbsp; 
                                <i class="ti-comments" style="font-size: 12px; color: #f68b1e; margin-right: 3px;"></i>
                                <a href="{{ url('https://api.whatsapp.com/send?phone=233506753093') }}" target="_blank">
                                    <span> 0506753093 </span>
                                </a>
                                
                            </p>
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
    