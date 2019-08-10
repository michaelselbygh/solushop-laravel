@extends('mobile.layouts.my-account')
@section('page-title')Conversation with {{ $conversation["vendor"]["name"] }} @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Send {{ $conversation["vendor"]["name"] }} a message on Solushop Ghana. @endsection
@section('page-content')
    <div class="page">
        <div class="navbar navbar-page">
            <div class="navbar-inner sliding">
                <div class="left">
                    <a href="{{ route('show.account.messages') }}" class="link back external">
                        <i class="ti-arrow-left"></i>
                    </a>
                </div>
                <div class="title">
                    {{ $conversation["vendor"]["name"] }}
                </div>
            </div>
        </div>
        <div class="page-content">
            <!-- cart -->
            <div class="cart cart-page segments-page">
                <div class="container">
                    <div style='border: 1px solid #ebebeb; border-radius:10px; height:470px; padding:10px;overflow: hidden;'>
                        <div class="child" id='Child'>
                            @if (sizeof($conversation["messages"]) < 1 ) 
                                <h5 style='text-align:center; margin-top:17%; font-weight: 300'>
                                    No messages yet
                                </h5>
                                <br>
                                <h6 style='text-align:center; font-weight:300'>
                                    Enter a message below.
                                </h6>
                                <br>
                                <h6 style='text-align:center; font-weight:300 '>
                                    Keep conversations strictly professional.<br>
                                    Do not exchange contact details<br>
                                    All communication must be within Solushop<br><br>
                                    
                                    To ensure safety of both parties,<br>
                                    we supervise all conversation<br> between customers and vendors.<br>
                                    Please ensure that all commuication is done here.
                                </h6>
                                <br><br><br><br>
                            @else
                                @for ($i=0; $i < sizeof($conversation["messages"]); $i++)  
                                    @if ($conversation["messages"][$i]["message_sender"] == Auth::user()->id) 
                                        <div class='chat-row' style='text-align: right; word-wrap: break-word'>
                                            <div class='chat-item' style='color: #fff; background-color: #001337; border-radius:10px; padding:7px; max-width:400px; display: inline-block; text-align: left; font-size:12px;'>
                                                {{ $conversation["messages"][$i]["message_content"] }}
                                            </div>
                                            <br>
                                            <span style='font-size:11px; font-weight:300'>
                                                {{ Auth::user()->first_name." - ".$conversation["messages"][$i]["message_timestamp"] }}
                                            </span>
                                        </div>
                                        <br>
                                    @elseif($conversation["messages"][$i]["message_sender"] == $conversation["vendor"]["id"])
                                        <div class='chat-row' style='text-align: left; '>
                                            <div class='chat-item' style='color: #001337; background-color: #edeef0; border-radius:10px; padding:7px; max-width:400px; display: inline-block; text-align: left; font-size: 12px'>
                                                {{ $conversation["messages"][$i]["message_content"] }}
                                            </div>
                                            <br>
                                            <span style='font-size:10px; font-weight:300'>
                                                {{ $conversation["vendor"]["name"]." - ".$conversation["messages"][$i]["message_timestamp"] }}
                                            </span>
                                        </div>
                                        <br>
                                    @elseif($conversation["messages"][$i]["message_sender"] == "MGMT")
                                        <div class='chat-row' style='text-align: left; '>
                                            <div class='chat-item' style='color: #001337; background-color: #edeef0; border-radius:10px; padding:7px; max-width:400px; display: inline-block; text-align: left; font-size:12px;'>
                                                {{ $conversation["messages"][$i]["message_content"] }}
                                            </div>
                                            <br>
                                            <span style='font-size:10px; font-weight:300'>
                                                Solushop Management - {{ $conversation["messages"][$i]["message_timestamp"] }}
                                            </span>
                                        </div>
                                        <br>
                                    @endif
                                @endfor
                            @endif
                        </div>
                    </div>
                    <div style='padding:10px;'>
                        <div class="register-form" style="margin-top: 0px; padding: 0px; margin: 0px;">
                            <form class="list" action="{{ route("process.account.conversation", $conversation["vendor"]["username"]) }}" method="POST">
                                @csrf
                                <div class="item-input-wrap">
                                    @if(isset($conversation["product"]))
                                        <input style="width:100%" type="text" name="message_content" value="Product : {{ $conversation["product"]["product_name"] }}. Hi {{ $conversation["vendor"]["name"] }} ..." placeholder="Enter message here" required>
                                    @else
                                        <input style="width:100%" type="text" name="message_content" value="" placeholder="Enter message here" required>
                                    @endif
                                </div>
                                <input type="hidden" name="mci" value="AK39SA{{ $conversation["details"]["id"] }}"/>
                                    <button class="button"name="send_message"  type="submit">Send Message</button>
                                <br>
                                
                            </form>
                        </div>
                    </div>
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
    