@extends('portal.layouts.vendor.master')

@section('page-title')Conversation with {{ $conversation["customer"]["first_name"]." ".$conversation["customer"]["last_name"] }}@endsection

@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <h5 class="card-title">Conversation with {{ $conversation["customer"]["first_name"]." ".$conversation["customer"]["last_name"] }} </h5>
        </div>
    </div>
   
        
    <div class="row">
        <div class="col-md-7">
            @include('portal.main.success-and-error.message')
            <div class="card" style="margin-bottom:5px;">
                <div class="card-content collapse show">
                    <div class="card-body" style="height: 520px; overflow: hidden;">
                        <div class="child" id='Child'>
                            @if (sizeof($conversation["messages"]) < 1 ) 
                                <h4 style='text-align:center; margin-top:220px; font-weight: 300'>
                                    No messages yet
                                </h4>
                            @else
                                @for ($i=0; $i < sizeof($conversation["messages"]); $i++)  
                                    @if ($conversation["messages"][$i]["message_sender"] == $conversation["customer"]["id"]) 
                                        <div class='chat-row' style='text-align: left; '>
                                            <div class='chat-item' style='color: #001337; background-color: #edeef0; border-radius:10px; padding:7px; max-width:400px; display: inline-block; text-align: left; font-size: 12px'>
                                                {{ $conversation["messages"][$i]["message_content"] }}
                                            </div>
                                            <br>
                                            <span style='font-size:10px; font-weight:300'>
                                                {{ $conversation["customer"]["first_name"]." - ".$conversation["messages"][$i]["message_timestamp"] }}
                                            </span>
                                        </div>
                                        <br>
                                    @elseif($conversation["messages"][$i]["message_sender"] == "MGMT")
                                        <div class='chat-row' style='text-align: left; '>
                                            <div class='chat-item' style='color: #001337; background-color: #edeef0; border-radius:10px; padding:7px; max-width:400px; display: inline-block; text-align: left; font-size: 12px'>
                                                {{ $conversation["messages"][$i]["message_content"] }}
                                            </div>
                                            <br>
                                            <span style='font-size:10px; font-weight:300'>
                                                    {{ "Solushop Management - ".$conversation["messages"][$i]["message_timestamp"] }}
                                            </span>
                                        </div>
                                        <br>
                                    @elseif($conversation["messages"][$i]["message_sender"] == Auth::guard('vendor')->user()->id)
                                        <div class='chat-row' style='text-align: right; word-wrap: break-word'>
                                            <div class='chat-item' style='color: #fff; background-color: #001337; border-radius:10px; padding:7px; max-width:400px; display: inline-block; text-align: left; font-size:12px;'>
                                                {{ $conversation["messages"][$i]["message_content"] }}
                                            </div>
                                            <br>
                                            <span style='font-size:11px; font-weight:300'>
                                                {{ Auth::guard('vendor')->user()->name." - ".$conversation["messages"][$i]["message_timestamp"] }}
                                            </span>
                                        </div>
                                        <br>
                                    @endif
                                @endfor
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('vendor.process.conversation', $conversation['record']['id'] )}}">
                @csrf
                <div class="form-group" style="margin-bottom: 10px;">
                    <input id="message" name="message"  class="form-control round" value="" type="text" required>
                </div>
                <div class="form-actions" style="text-align:center; padding: 0px;">
                    <button type="submit" class="btn btn-success">
                            Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        window.onload=function () {
            var objDiv = document.getElementById("Child");
            objDiv.scrollTop = objDiv.scrollHeight;
        }
    </script>
@endsection

