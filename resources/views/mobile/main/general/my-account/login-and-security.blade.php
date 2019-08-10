@extends('mobile.layouts.my-account')
@section('page-title')Login and Security @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Manage your password on Solushop Ghana @endsection
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
                    Change Password
                </div>
            </div>
        </div>
        <div class="page-content">
            <!-- cart -->
            <div class="cart cart-page segments-page">
                <div class="container">
                    <form class="list" method="POST" action="{{ route("process.account.login.and.security") }}">
                        @csrf
                        <div class="item-input-wrap">
                            <input type="password" name="current_password" value="" placeholder="Enter current password" required>
                        </div>
                        <div class="item-input-wrap">
                            <input type="password" name="new_password" value="" placeholder="Enter new password" required>
                        </div>
                        <div class="item-input-wrap">
                            <input type="password" name="confirm_new_password" value="" placeholder="Confirm new password" required>
                        </div>
                        <button class="button" type="submit">Update Password</button>
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
    