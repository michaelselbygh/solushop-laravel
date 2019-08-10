@extends('mobile.layouts.general')
@section('page-title')Our Amazing Vendors @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Check out our amazing vendors on Solushop Ghana. @endsection
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
                            Our amazing Vendors
                        </div>
                    </div>
                </div>

                <div class="categories segments-page">
                    <div class="container">
                        @for ($i = 0; $i < sizeof($vendors); $i++)
                            <a href="{{ url('shop/'.$vendors[$i]->username) }}" class="external">
                            <div class="content section-wrapper">
                                <div class="mask"></div>
                                <img src="{{ url('app/assets/img/vendor-banner/'.$vendors[$i]->vendor_id.'.jpg') }}" alt="">
                                <div class="title">
                                    <h4 style="color:white; font-weight: 400">{{ $vendors[$i]->name }}</h4>
                                </div>
                            </div>
                        </a>
                        @endfor
                    </div>
                    @if (session()->has('welcome_message')) 
                        <div id="snackbar">{{ session()->get('welcome_message') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection    
    