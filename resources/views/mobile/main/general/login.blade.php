@extends('mobile.layouts.general')
@section('page-title')Login @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Login to Solushop, Ghana's Most Trusted Store. @endsection
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
                            Login to Solushop Ghana
                        </div>
                    </div>
                </div>

                <div class="about segments-page">
                    <div class="container">
                        <br><br><br>
                        <div class="" style="text-align:center; margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
                            <form class="list" method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="item-input-wrap">
                                    <input type="email" placeholder="Email" name="email" value="{{ old('email') }}"  required>
                                </div>
                                <div class="item-input-wrap no-mb">
                                    <input type="password" placeholder="Password" name="password" required>
                                </div>
                                <button class="button" type="" name="login" value='login' onclick="" style="background-color: #f68b1e">Sign In</button>
                                <br>
                                <div class="link-sign-in-wrapper">
                                    <p style="font-size: 12px">Don't have an account? <a href="{{ url('/register') }}" style="color: #f68b1e; font-weight: 500" class="external">Register</a></p>
                                    <p style="font-size: 12px"><a href="{{ route('customer.reset.password') }}" style="font-weight: 500" class="external">Lost Password ?</a></p>
                                </div>
                                <input type="hidden" name="url" value="{{ URL::previous() }}"/>
                            </form>
                        </div>
                        @if (session()->has('login_error_message')) 
                            <div id="snackbar">{{ session()->get('login_error_message') }}</div>
                        @endif
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection    
    