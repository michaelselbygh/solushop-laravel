@extends('mobile.layouts.my-account')
@section('page-title')Personal Details @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Manage your personal details on Solushop Ghana @endsection
@section('page-content')
    <div class="page">
        <div class="navbar navbar-page">
            <div class="navbar-inner sliding">
                <div class="left">
                    <a href="{{ route('show.account.dashboard') }}" class="link back external">
                        <i class="ti-arrow-left"></i>
                    </a>
                </div>
                <div class="title">
                    Personal Details
                </div>
            </div>
        </div>
        <div class="page-content">
            <!-- cart -->
            <div class="cart cart-page segments-page">
                <div class="container">
                    <form class="list" method="POST" action="{{ route("process.account.personal.details") }}">
                        @csrf
                        <div class="item-input-wrap">
                            <input type="text" placeholder="First name" name="first_name" value="{{ Auth::user()->first_name }}" required>
                        </div>
                        <div class="item-input-wrap">
                            <input type="text" placeholder="Last name" name="last_name" value="{{ Auth::user()->last_name }}" required>
                        </div>
                        <div class="item-input-wrap">
                            <input type="text" placeholder="Phone e.g 0544000000" name="phone" value="{{ "0".substr(Auth::user()->phone, 3) }}" required>
                        </div>
                        <div class="item-input-wrap">
                            <input type="email" placeholder="Email" name="email" value="{{ Auth::user()->email }}" required>
                        </div>
                        <button class="button" type="submit">Update Details</button>
                        <br>
                    </form>
                </div>
            </div>
            @if (session()->has('error_message')) 
                <div id="snackbar">{{ session()->get('error_message') }}</div>
            @elseif (session()->has('success_message')) 
                <div id="snackbar">{{ session()->get('success_message') }}</div>
            @endif
            <!-- end cart -->
        </div>
    </div>
    
@endsection    
    