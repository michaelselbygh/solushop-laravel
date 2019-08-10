@extends('app.layouts.general')
@section('page-title')Login or Register @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Login or Register to Solushop, Ghana's Most Trusted Store. @endsection
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
                                <li>Login or Register</li>
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
                    <div class="customer-login-register customFadeIn" id="register"
                     @if (session()->has('register_error_message')) 
                        style="display:block;" 
                    @elseif (session()->has('login_error_message'))
                        style="display:none;"
                    @endif >
                        <h3 style="text-align:center;">Join Our Happy Community</h3>
                        <div style="padding-left: 20px; padding-right: 20px"><br>
                            @if (session()->has('register_error_message')) 
                                <div class="alert alert-danger alert-dismissible mb-2" role="alert" style='border-radius: 10px; text-align: left; margin-bottom: 0px;'>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    {{ session()->get('register_error_message') }}
                                </div>
                            @endif
                        </div>
                        <div class="register-form">
                            <form action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="col-lg-6" style="padding-left: 0px; padding-right: 5px;">
                                    <div class="form-fild">
                                        <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col-lg-6" style="padding-right: 0px; padding-left: 5px;">
                                    <div class="form-fild">
                                            <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name">
                                    </div>
                                </div>
                                <div class="col-lg-12" style="padding-right: 0px; padding-left: 0px;">
                                    <div class="form-fild">
                                        <input type="text" name="r_email" value="{{ old('r_email') }}" placeholder="Email e.g. ekowyeboah@gmail.com">
                                    </div>
                                    <div class="form-fild">
                                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Phone e.g 0544000000">
                                    </div>
                                    <div class="form-fild">
                                        <input type="password" id="Password" name="r_password" value="" placeholder="Enter password" >
                                    </div>
                                    <div class="register-submit" style="text-align:center;">
                                        <div class="lost-password" style="text-align:center; font-size: 11px">
                                            By clicking on register you  agree to the <br><a target="_blank" href="{{ route('show.terms.and.conditions') }}">Terms and Conditions</a><br> binding the use of this platform.
                                        </div>
                                        <button type="submit" name="register" value="register" class="form-button" onclick="gtag_report_conversion()">Register</button>
                                        
                                        <div class="lost-password" style="text-align:center; font-size: 11px">
                                            <br>
                                            Already a member? <a id="toggleLogin" style="cursor:pointer;">Login here</a>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="url" value="{{ URL::previous() }}"/>
                            </form>
                        </div>
                    </div>
                    <div class="customer-login-register customFadeIn" id="login"
                    @if (session()->has('login_error_message')) 
                        style="display:block;" 
                    @elseif (session()->has('register_error_message'))
                        style="display:none;"
                    @endif >
                        <div class="form-login-title">
                            <h3 style="text-align:center;">Hi there, welcome back.</h3><br>
                            <div style="padding-left: 20px; padding-right: 20px">
                                @if (session()->has('login_error_message')) 
                                    <div class="alert alert-danger alert-dismissible mb-2" role="alert" style='border-radius: 10px; text-align: left; margin-bottom: 0px;'>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                        {{ session()->get('login_error_message') }}
                                    </div>
                                @endif 
                            </div>
                        </div>
                        <div class="login-form">
                        <form action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="form-fild">
                                    <input type="text" name="email" placeholder="Enter Email" value="{{ old('email') }}" required>
                                </div>
                                <div class="form-fild">
                                    <input type="password" name="password" placeholder="Enter Password" required>
                                    <br><br>
                                </div>
                                <div class="login-submit" style="text-align:center;">
                                    <button type="submit" name="login" value='login' class="form-button">Login</button>
                                </div>
                                <div class="lost-password" style="text-align:center;">
                                    <a href="{{ route('customer.reset.password') }}">Lost your password?</a>
                                    <br>
                                    <span style="font-size: 11px">Not registered yet? <a id="toggleRegister" style="color: #f68c1e; cursor:pointer;">Register here</a></span>
                                </div>
                                <input type="hidden" name="url" value="{{ URL::previous() }}"/>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--My Account Area End-->
@endsection