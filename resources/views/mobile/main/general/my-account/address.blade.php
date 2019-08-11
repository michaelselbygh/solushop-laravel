@extends('mobile.layouts.my-account')
@section('page-title')Add Address @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Add an Address on Solushop Ghana @endsection
@section('page-content')
    <div class="page">
        <div class="navbar navbar-page">
            <div class="navbar-inner sliding">
                <div class="left">
                    <a href="{{ route('show.account.addresses') }}" class="link back external">
                        <i class="ti-arrow-left"></i>
                    </a>
                </div>
                <div class="title">
                    Add Address
                </div>
            </div>
        </div>
        <div class="page-content">
            <!-- cart -->
            <div class="cart cart-page segments-page">
                <div class="container">
                    <br>
                    <form class="list" method="POST" action="{{ route("process.account.add.address") }}">
                        @csrf
                        <div class="item-input-wrap">
                            <select name="address_town" value="" placeholder="Select Town" style="padding-left: 18px; border: 1px solid #e5e5e5; border-radius: 5px; height: 35px; background: #eee; font-size:14px;" required>
                                @for ($j = 0; $j < sizeof($address["options"]); $j++)
                                    <option value="{{ $address["options"][$j]["sf_town"] }}||{{ $address["options"][$j]["sf_region"] }}">
                                            {{ $address["options"][$j]["sf_town"] }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="item-input-wrap">
                                <input type="text" name="address_details" value="" placeholder="Address details. Eg Adenta Flats near kfc" required>
                        </div>
                        <input type="hidden" name="checkout_action" value="update_personal_details"/>
                        <button class="button"name="add_address"  type="submit">Add Address</button>
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
    