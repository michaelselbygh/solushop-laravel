@extends('mobile.layouts.general')
@section('page-title')Page not found @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Resource unaivailable or moved to different location. @endsection
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
                            Oops!
                        </div>
                    </div>
                </div>

                <div class="about segments-page">
                    <div class="container">
                        <div class="content" style="text-align:center; margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
                            <p>Resource not found.</p>
                            <div class="error-message">
                                <h4>We can't seem to find what you're looking for.</h4>
                            </div>
                            <a href="{{ route("show.shop") }}" class="external">
                                <button class="button" style="background-color:#f68b1e; width:100%">Back to Shop</button>
                            </a>
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
    