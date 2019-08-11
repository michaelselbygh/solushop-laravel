@extends('mobile.layouts.general')
@section('page-title')Register @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Register on Solushop, Ghana's Most Trusted Store.@endsection
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
                            Register on Solushop Ghana
                        </div>
                    </div>
                </div>

                <div class="about segments-page">
                    
                    <div class="container">
                        <div class="" style="text-align:center; padding-top:30px; margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
                            <form class="list" method="POST" action="{{ route('login') }}">
                                @csrf
                                <br><br><br><br>
                                <div class="item-input-wrap">
                                    <input type="text" placeholder="First name" name="first_name" value="{{ old('first_name') }}" required>
                                </div>
                                <div class="item-input-wrap">
                                    <input type="text" placeholder="Last name" name="last_name" value="{{ old('last_name') }}" required>
                                </div>
                                <div class="item-input-wrap">
                                    <input type="text" placeholder="Phone e.g 0544000000" name="phone" value="{{ old('phone') }}" required>
                                </div>
                                <div class="item-input-wrap">
                                    <input type="email" placeholder="Email" name="r_email" value="{{ old('r_email') }}" required>
                                </div>
                                <div class="item-input-wrap no-mb">
                                    <input type="password" placeholder="Password" name="r_password" required>
                                </div>
                                <div class="" style="text-align:center; font-size: 11px">
                                    <a href="{{ route('show.terms.and.conditions') }}" style="color: #f68b1e; font-weight: 500" class="external">Terms and Conditions</a><br> 
                                </div>
                                <button class="button" type="submit" name="register" value="register" style="background-color: #f68b1e">Register</button>
                                <br>
                                <div class="link-sign-in-wrapper">
                                    <p style="font-size: 12px">Have an account? <a href="{{ url('/login') }}" style="color: #f68b1e; font-weight: 500" class="external">Login</a></p><br>
                                    
                                </div>
                                <input type="hidden" name="url" value="{{ URL::previous() }}"/>
                            </form>
                        </div>
                        @if (session()->has('register_error_message')) 
                            <div id="snackbar">{{ session()->get('register_error_message') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection    
    