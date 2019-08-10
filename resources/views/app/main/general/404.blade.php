@extends('app.layouts.general')
@section('page-title')Page not found @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Resource unaivailable or moved to different location. @endsection
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
                                <li>Resource Not Found</li>
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
                <div class="col-sm-6 col-sm-offset-3"> 
                    <div class="search-form-wrapper" style="text-align: center;">
                        <h1>Oops!</h1>
                        <h4>Resource not found.</h4>
                        <div class="error-message">
                            <p>We can't seem to find what you're looking for.</p>
                        </div>
                        <div class="search-form">
                            <div class="back-to-home">
                                <a href="{{ route('show.shop') }}">Take me back to the shop</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br><br>
        </div>
    </section>
@endsection