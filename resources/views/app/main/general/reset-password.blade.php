@extends('app.layouts.general')
@section('page-title')Reset Password @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Lost your password? No problem. Let's help you recover it. @endsection
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
                                <li>Reset Password</li>
                            </ul>
                        </div><br>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Heading Banner Area End--> 
    <!--My Account Area Start-->
    <section class="my-account-area mt-20">
        <div class="container">
            <div class="row justify-content-md-center">
                <div class="col-sm-4 col-sm-offset-4 text-center customFadeIn" style="border-radius: 10px; border: 1px solid #f68c1e; padding: 20px; margin-bottom: 40px;">
                    <div class="customer-login-register customFadeIn" id="login">
                        <div class="form-login-title">
                            <h3 style="text-align:center;">Lost your password?<br> No problem.</h3><br>
                            <div style="padding-left: 20px; padding-right: 20px">
                                @include('app.main.general.success-and-error.message') 
                            </div>
                        </div>
                        <div class="login-form">
                            <form action="{{ route('customer.reset.password') }}" method="POST">
                                @csrf
                                <div class="form-fild">
                                    <input type="text" name="email" placeholder="Enter Email" value="{{ old('email') }}" required>
                                </div>
                                <div class="form-fild">
                                    <input type="text" name="phone" placeholder="Enter Phone e.g 0544000000" value="{{ old('phone') }}" required>
                                    <br><br>
                                </div>
                                <div class="login-submit" style="text-align:center;">
                                    <button type="submit" name="solushop_rp" value='login' class="form-button">Reset Password</button>
                                </div>
                                <div class="lost-password" style="text-align:center;">
                                        <a href="{{ route('login') }}">Login</a>
                                        <br>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--My Account Area End-->
@endsection