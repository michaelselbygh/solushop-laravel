@extends('portal.layouts.manager.master')

@section('page-title')
    Sales Associate - {{ $sales_associate["first_name"]." ".$sales_associate["last_name"] }}
@endsection

@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <h5 class="card-title">Manage sales associate, {{ $sales_associate["first_name"]." ".$sales_associate["last_name"] }} </h5>
            @include('portal.main.success-and-error.message')
        </div>
    </div>
   
        
    <div class="row">
        <div class="col-md-7">
            <div class="card" style="">
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form class="form" method="POST" action="{{ route("manager.process.sales.associate", $sales_associate["id"]) }}" enctype="multipart/form-data">
                            <div class="form-body">
                                @csrf
                                <input type="hidden" name="sa_action" value="update_details"/>
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
                                <button type="submit" name="update_details" class="btn btn-success">
                                        Update {{ $sales_associate["first_name"] }}'s Details
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="row">
                    <div class="col-md-4" stle="padding:40px;">
                        <img style="width:155px; height:auto; padding:10px; margin-left: 20px;" src="{{url("portal/images/s-team-badges/".$sales_associate["badge_info"]["sab_image"]) }}"/>
                    </div>
                    <div class="col-md-8" style="padding-top: 30px; padding-left:30px;">
                        <div class="row">
                            <div class="col-md-4" style="font-weight: 600;">
                                ID : <br>
                                Status : <br>
                                Coupon : <br>
                                Commision  : <br>
                                Total Sales : <br>
                            </div>
                            <div class="col-md-8">
                                STM-{{ $sales_associate["id"] }} <br>
                                {{ $sales_associate["badge_info"]["sab_description"] }} <br>
                                {{ substr($sales_associate["id_file"], 0, 24) }} <br>
                                {{ $sales_associate["badge_info"]["sab_commission"] * 100 }} % <br>
                                GH¢ {{ round($sales_associate["sales"], 2) }} <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
        </div>
        <div class="col-md-5">
            <h5>Account Balance : GH¢ {{ $sales_associate["balance"] }}</h5>
            <div class="card">
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-sm-3">
                            </div>
                            <div class="col-sm-6" style="text-align: center;"> 
                                <form method="POST" action="{{ route("manager.process.sales.associate", $sales_associate["id"]) }}">
                                    @csrf
                                    <div class="form-group" style="display: inline-block">
                                        <input type="hidden" name="sa_action" value="record_payout"/>
                                        <input id="pay_out_amount" name="pay_out_amount" class="form-control round" placeholder="0.00" value="{{ $sales_associate["balance"] }}" type="number" min="0.1" max="{{ $sales_associate["balance"] }}" step="0.1" style="width: 100%;" required><br>
                                        <button type="submit" name="record_payout" class="btn btn-success">
                                                Record Payout
                                        </button> 
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="card-title">Transactions</h5>
            <div class="card">
                <div class="card-content collapse show">
                    <div class="card-body card-dashboard">
                        <table class="table table-striped table-bordered zero-configuration" id="transactions">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th></th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @for($i=0; $i<sizeof($sales_associate["transactions"]); $i++) 
                                    <tr>
                                        <td>{{ $sales_associate["transactions"][$i]["id"] }}</td>
                                        <td>{{ $sales_associate["transactions"][$i]["trans_type"] }}</td>
                                        <td>{{ $sales_associate["transactions"][$i]["trans_amount"] }}</td>
                                        <td>
                                            @if($sales_associate["transactions"][$i]["trans_debit_account_type"] == 1 AND in_array($sales_associate["transactions"][$i]["trans_credit_account_type"], [2, 4, 6, 8, 10]))
                                                <img src="{{ url("portal/images/transactions/green-in.png") }}" style="height: 30px;"/>
                                            @elseif($sales_associate["transactions"][$i]["trans_credit_account_type"] == 1 AND in_array($sales_associate["transactions"][$i]["trans_debit_account_type"], [2, 4, 6, 8, 10]))
                                                <img src="{{ url("portal/images/transactions/red-out.png") }}" style="height: 30px;"/>
                                            @elseif($sales_associate["transactions"][$i]["trans_debit_account_type"] == 1 AND in_array($sales_associate["transactions"][$i]["trans_credit_account_type"], [3, 5, 7, 9]))
                                                <img src="{{ url("portal/images/transactions/yellow-in.png") }}" style="height: 30px;"/>
                                            @elseif($sales_associate["transactions"][$i]["trans_credit_account_type"] == 1 AND in_array($sales_associate["transactions"][$i]["trans_debit_account_type"], [3, 5, 7, 9]))
                                                <img src="{{ url("portal/images/transactions/yellow-out.png") }}" style="height: 30px;"/>
                                            @else
                                                <img src="{{ url("portal/images/transactions/neutral.png") }}"/>
                                            @endif
                                        </td>
                                        <td>{{ $sales_associate["transactions"][$i]["trans_description"] }}</td>
                                        <td>{{ $sales_associate["transactions"][$i]["trans_date"] }}</td>
                                    </tr>
                                @endfor
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('#transactions').dataTable( {
                "order": [
                    [0, 'desc']
                ]
            } );
        })
    </script>
@endsection

