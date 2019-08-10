@extends('mobile.layouts.general')
@section('page-title')Reset Password @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Lost your password? No problem. Let's help you recover it. @endsection
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
                            Reset password
                        </div>
                    </div>
                </div>

                <div class="about segments-page">
                    <div class="container">
                        <div class="" style="text-align:center; margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
                            <form class="list" method="POST" action="{{ route('customer.reset.password') }}">
                                @csrf
                                <div class="item-input-wrap">
                                    <input type="email" placeholder="Email" name="email" value="{{ old('email') }}"  required>
                                </div>
                                <div class="item-input-wrap no-mb">
                                    <input type="text" name="phone" placeholder="Enter Phone e.g 0544000000" value="{{ old('phone') }}" required>
                                </div>
                                <button class="button" type="" name="login" value='login' onclick="" style="background-color: #f68b1e">Reset password</button>
                                <br>
                                <div class="link-sign-in-wrapper">
                                    <p style="font-size: 12px">Don't have an account? <a href="{{ route('register') }}" style="color: #f68b1e; font-weight: 500" class="external">Register</a></p>
                                    <p style="font-size: 12px"><a href="{{ route('login') }}" style="font-weight: 500" class="external">Login here</a></p>
                                </div>
                                <input type="hidden" name="url" value="{{ URL::previous() }}"/>
                            </form>
                        </div>
                        @if (session()->has('error_message')) 
                            <div id="snackbar">{{ session()->get('error_message') }}</div>
                        @elseif(session()->has('success_message')) 
                            <div id="snackbar">{{ session()->get('success_message') }}</div>
                        @endif
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection    
    