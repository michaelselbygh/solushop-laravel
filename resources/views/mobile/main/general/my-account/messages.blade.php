@extends('mobile.layouts.my-account')
@section('page-title')Messages @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Manage your messages on Solushop Ghana @endsection
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
                    Messages
                </div>
            </div>
        </div>
        <div class="page-content">
            <!-- cart -->
            <div class="cart cart-page segments-page">
                <div class="container">
                    @if(sizeof($conversations) < 1)
                        <div class="content" style="text-align:center; margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
                            <div class="error-message">
                                <h6>
                                    No conversations yet.
                                </h6>
                            </div>
                        </div>
                    @else
                        @for ($i = 0; $i < sizeof($conversations); $i++)
                            <a href="{{ url("my-account/messages/".$conversations[$i]['vendor']['username']) }}" class="external" style="font-size:16px;">
                                <i class='ti-email' style='font-size:12px; margin-right:4px; color: #f68b1e;'></i>
                                {{ $conversations[$i]['vendor']['name'] }}
                            
                                @if ($conversations[$i]['unread_messages'] > 0)
                                    <span style='color:white; background-color: red; padding: 2px 6px; border-radius:20px;'>
                                        {{ $conversations[$i]['unread_messages'] }}
                                    </span>
                                @endif
                                <br>
                            </a>
                        @endfor
                    @endif
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
    