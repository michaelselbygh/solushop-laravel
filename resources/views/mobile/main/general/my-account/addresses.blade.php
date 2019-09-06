@extends('mobile.layouts.my-account')
@section('page-title')Addresses @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Manage your addresses on Solushop Ghana @endsection
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
                    Addresses
                </div>
            </div>
        </div>
        <div class="page-content">
            <!-- cart -->
            <div class="tracking-order segments-page">
                <div class="container">
                    @if(sizeof($addresses["addresses"]) < 1)
                        <div class="content" style="text-align:center; margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
                            <div class="error-message">
                                <h6>
                                    No addresses yet.
                                </h6>
                            </div>
                            <a href="{{ route('show.account.add.address') }}" class="external">
                                <button class="button" style="background-color:#f68b1e; width:100%">Add Address</button>
                            </a>
                        </div>
                    @else
                        <div class="accordion-list"> 
                            @for ($i = 0; $i < sizeof($addresses["addresses"]); $i++)
                                <div class="accordion-item">
                                    <div class="accordion-item-toggle" style="background-color: #f68b1e; color:white">
                                        @if(strlen($addresses["addresses"][$i]["ca_town"].", ".$addresses["addresses"][$i]["ca_address"]) > 40)
                                            {{ substr($addresses["addresses"][$i]["ca_town"].", ".$addresses["addresses"][$i]["ca_address"], 0, 40)." . . ." }}
                                        @else
                                            {{ substr($addresses["addresses"][$i]["ca_town"].", ".$addresses["addresses"][$i]["ca_address"], 0, 40) }}
                                        @endif
                                    </div>
                                    <div class="accordion-item-content">
                                        <br>
                                        <form class="list" method="POST" action="{{ route("process.account.edit.address") }}">
                                            @csrf
                                            <div class="item-input-wrap">
                                                <select name="address_town" value="" placeholder="Select Town" style="padding-left: 18px; border: 1px solid #e5e5e5; border-radius: 5px; height: 35px; background: #eee; font-size:14px;" required>
                                                    @for ($j = 0; $j < sizeof($addresses["options"]); $j++)
                                                        <option value="{{ $addresses["options"][$j]["sf_town"] }}||{{ $addresses["options"][$j]["sf_region"] }}"
                                                        @if($addresses["options"][$j]["sf_town"] == $addresses["addresses"][$i]["ca_town"]) selected @endif>
                                                                {{ $addresses["options"][$j]["sf_town"] }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="item-input-wrap">
                                                    <input type="text" name="address_details" value="{{ $addresses["addresses"][$i]["ca_address"] }}" placeholder="Enter address detaills." required>
                                            </div>
                                            <input type="hidden" name="aid" value="{{ $addresses["addresses"][$i]["id"] }}" />
                                            <button class="button"name="update_address"  type="submit">Update Address</button>
                                            <br>
                                            
                                        </form>
                                        <br>
                                    </div>
                                </div>
                            @endfor
                        </div>
                        <div class="content" style="text-align:center; margin: 0; position: absolute; left: 50%; -ms-transform: translateX(-50%); transform: translateX(-50%);">
                            <a href="{{ route('show.account.add.address') }}" class="external">
                                <button class="button" style="background-color:#f68b1e; width:100%">Add Address</button>
                            </a>
                        </div>
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
    