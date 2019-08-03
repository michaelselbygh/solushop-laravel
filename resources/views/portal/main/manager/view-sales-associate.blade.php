@extends('portal.layouts.manager.master')

@section('page-title')
    Sales Associate - {{ $sales_associate["first_name"]." ".$sales_associate["last_name"] }}
@endsection

@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <h4 class="card-title">Manage sales associate, {{ $sales_associate["first_name"]." ".$sales_associate["last_name"] }} </h4>
            @include('portal.main.success-and-error.message')
        </div>
    </div>
    <form class="form" method="POST" action="{{ route("manager.process.sales.associate", $sales_associate["id"]) }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-7">
                <div class="card" style="">
                    <div class="card-content collapse show">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <input id="first_name" name="first_name" class="form-control round" placeholder="Enter first name" value="{{ $sales_associate["first_name"] }}" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input id="last_name" name="last_name"  class="form-control round" placeholder="Enter last name" value="{{ $sales_associate["last_name"] }}" type="text" required> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input id="email" name="email"  class="form-control round" placeholder="Enter email" value="{{ $sales_associate["email"] }}" type="email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">PIN</label>
                                            <input id="passcode" name="passcode"  class="form-control round" value="{{ $sales_associate["passcode"] }}" type="text" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone Number</label>
                                            <input id="phone" name="phone"  class="form-control round" placeholder="Enter phone e.g. 0204456789" value="{{ "0".substr($sales_associate["phone"], 3) }}" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="identification">Identification</label>
                                            <a target="_blank" href="{{ url("portal/s-team-member-id/".$sales_associate["id_file"]) }}">
                                                <input id="identification" style="cursor:pointer" name="identification"  class="form-control round" value="{{ $sales_associate["id_type"] }}" type="text" readonly>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mode_of_payment">Mode of Payment</label>
                                            <fieldset class="form-group" >
                                                <select class="form-control" name='mode_of_payment' id="mode_of_payment" style='border-radius:7px;' required>
                                                    <option @if($sales_associate["mode_of_payment"] == "MTN Mobile Money") selected @endif>MTN Mobile Money</option>
                                                    <option @if($sales_associate["mode_of_payment"] == "Vodafone Cash") selected @endif>Vodafone Cash</option>
                                                    <option @if($sales_associate["mode_of_payment"] == "Bank Account") selected @endif>Bank Account</option>
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="payment_details">Payment Details</label>
                                            <input id="payment_details" name="payment_details"  class="form-control round" placeholder="Enter payment details" value="{{ $sales_associate["payment_details"] }}" type="text" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="residential_address">Residential Address</label>
                                    <input id="residential_address" name="residential_address"  class="form-control round" placeholder="Enter Residential Address" value="{{ $sales_associate["address"] }}" type="text" required>
                                </div>
                            </div>
                            <div class="form-actions" style="text-align:center; padding: 20px;">
                                <button type="submit" name="AddSTeamMember" class="btn btn-success">
                                        Update {{ $sales_associate["first_name"] }}'s Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
            </div>
        </div>
        <!-- Save button stuff goes here -->
        <div class="card">
            
        </div>
    </form>
@endsection

