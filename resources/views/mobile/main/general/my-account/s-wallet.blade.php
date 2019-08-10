@extends('mobile.layouts.my-account')
@section('page-title')Wallet @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Manage your wallet on Solushop Ghana @endsection
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
                    S-Wallet ( Balance : <b>GH¢ {{ abs($customer_information["wallet_balance"]) }}</b> )
                </div>
            </div>
        </div>
        <div class="page-content">
            <!-- cart -->
            <div class="cart cart-page segments-page">
                <div class="container">
                    <br>
                    <span>The Solushop Wallet or S-Wallet allows you to top-up money and make purchases directly and super fast on Solushop.</span>
                    <br><br>
                    <form class="list" method="POST" action="{{ route("process.account.wallet") }}">
                        @csrf
                        <div class="item-input-wrap">
                            <select name="wtup_id" placeholder="Select Package" style="padding-left: 18px; border: 1px solid #e5e5e5; border-radius: 5px; height: 35px; background: #eee; font-size:14px;" required>
                                @for ($j = 0; $j < sizeof($wallet["options"]); $j++)
                                    <option value="{{ $wallet["options"][$j]["id"] }}">
                                            {{ $wallet["options"][$j]["wtu_package_description"] }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <button class="button"name="top_up_wallet"  type="submit">Top Up Wallet</button>
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
    