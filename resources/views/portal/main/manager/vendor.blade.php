@extends('portal.layouts.manager.master')

@section('page-title')Vendor - {{ $vendor["name"] }}@endsection

@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <h5 class="card-title">Manage vendor, {{ $vendor["name"] }} </h5>
            @include('portal.main.success-and-error.message')
        </div>
    </div>
   
        
    <div class="row">
        <div class="col-md-7">
            <div class="card" style="">
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form class="form" method="POST" action="{{ route("manager.process.vendor", $vendor["username"]) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input id="name" name="name" class="form-control round" placeholder="Enter Vendor name" value="{{ $vendor["name"] }}" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input id="email" name="email"  class="form-control round" placeholder="Enter email" value="{{ $vendor["email"] }}" type="email" required> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input id="username" name="username" class="form-control round" value="{{ $vendor["username"] }}" type="text" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pin">PIN</label>
                                            <input id="pin" name="pin"  class="form-control round" value="{{ $vendor["passcode"] }}" type="text" readonly> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="main_phone">Main Phone Number</label>
                                            <input id="main_phone" name="main_phone"  class="form-control round" placeholder="Enter main phone e.g. 0204456789" value="{{ "0".substr($vendor["phone"], 3) }}" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="alt_phone">Alternate Phone Number</label>
                                            <input id="alt_phone" name="alt_phone"  class="form-control round" placeholder="Enter alternate phone e.g. 0204456789" value="{{ "0".substr($vendor["alt_phone"], 3) }}" type="text" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mode_of_payment">Mode of Payment</label>
                                            <fieldset class="form-group" >
                                                <select class="form-control" name='mode_of_payment' id="mode_of_payment" style='border-radius:7px;' required>
                                                    <option @if($vendor["mode_of_payment"] == "MTN Mobile Money") selected @endif>MTN Mobile Money</option>
                                                    <option @if($vendor["mode_of_payment"] == "Vodafone Cash") selected @endif>Vodafone Cash</option>
                                                    <option @if($vendor["mode_of_payment"] == "Bank Account") selected @endif>Bank Account</option>
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="payment_details">Payment Details</label>
                                            <input id="payment_details" name="payment_details"  class="form-control round" placeholder="Enter payment details" value="{{ $vendor["payment_details"] }}" type="text" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="header">Current Header Image</label>
                                            <a target="_blank" href="{{ url("app/assets/img/vendor-banner/".$vendor["id"]) }}.jpg">
                                                <input id="header" style="cursor:pointer" name="header"  class="form-control round" value="{{ $vendor["id"] }}.jpg" type="text" readonly>
                                            </a>
                                            </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="header_image">Update Header Image</label>
                                            <input type="file" class="form-control-file" name="header_image" placeholder="Header Image" id="header_image">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="pick_up_address">Pick Up Address</label>
                                    <input id="pick_up_address" name="pick_up_address"  class="form-control round" placeholder="Enter Pick-Up Address" value="{{ $vendor["address"] }}" type="text" required>
                                </div>
                                <div class="form-group">
                                    <label for="shop">Shop URL</label>
                                    <input id="shop" name="shop"  class="form-control round" value="{{ URL::to('/')."/shop/".$vendor["username"] }}" type="text" readonly>
                                </div>
                                <div class="form-actions" style="text-align:center; padding: 20px;">
                                    <input type="hidden" name="vendor_action" value="update_details"/>
                                    <button type="submit" class="btn btn-success">
                                            Update Details
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    
        </div>
        <div class="col-md-5">
            <h5>Account Balance : GH¢ {{ $vendor["balance"] }}</h5>
            <div class="card">
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form method="POST" action="{{ route("manager.process.vendor", $vendor["username"]) }}">
                            @csrf
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-sm-6">
                                    <div class="form-group" style=" margin-bottom:0px;">
                                        <label for="transaction_type">Type</label>
                                        <fieldset class="form-group" >
                                            <select class="form-control" name='transaction_type' id="transaction_type" style='border-radius:7px;' required>
                                                <option>Pay-Out</option>
                                                <option>Penalty</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="text-align: center;"> 
                                    <div class="form-group" style="display: inline-block; margin-bottom:0px;">
                                        <label for="transaction_type">Amount</label>
                                        <input type="hidden" name="vendor_action" value="record_transaction"/>
                                        <input id="pay_out_amount" name="pay_out_amount" class="form-control round" placeholder="0.00" value="{{ $vendor["balance"] }}" type="number" min="0.1" step="0.1" style="width: 100%;" required><br> 
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions" style="text-align:center; padding: 0px;">
                                <button type="submit" name="record_transaction" class="btn btn-success">
                                    Record Transaction 
                                </button>
                            </div>
                        </form>
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
                                @for($i=0; $i<sizeof($vendor["transactions"]); $i++) 
                                    <tr>
                                        <td>{{ $vendor["transactions"][$i]["id"] }}</td>
                                        <td>{{ $vendor["transactions"][$i]["trans_type"] }}</td>
                                        <td>{{ $vendor["transactions"][$i]["trans_amount"] }}</td>
                                        <td>
                                            @if($vendor["transactions"][$i]["trans_debit_account_type"] == 1 AND in_array($vendor["transactions"][$i]["trans_credit_account_type"], [2, 4, 6, 8, 10]))
                                                <img src="{{ url("portal/images/transactions/green-in.png") }}" style="width: 30px;"/>
                                            @elseif($vendor["transactions"][$i]["trans_credit_account_type"] == 1 AND in_array($vendor["transactions"][$i]["trans_debit_account_type"], [2, 4, 6, 8, 10]))
                                                <img src="{{ url("portal/images/transactions/red-out.png") }}" style="width: 30px;"/>
                                            @elseif($vendor["transactions"][$i]["trans_debit_account_type"] == 1 AND in_array($vendor["transactions"][$i]["trans_credit_account_type"], [3, 5, 7, 9]))
                                                <img src="{{ url("portal/images/transactions/yellow-in.png") }}" style="width: 30px;"/>
                                            @elseif($vendor["transactions"][$i]["trans_credit_account_type"] == 1 AND in_array($vendor["transactions"][$i]["trans_debit_account_type"], [3, 5, 7, 9]))
                                                <img src="{{ url("portal/images/transactions/yellow-out.png") }}" style="width: 30px;"/>
                                            @else
                                                <img src="{{ url("portal/images/transactions/neutral.png") }}" style="width: 30px;"/>
                                            @endif
                                        </td>
                                        <td>{{ $vendor["transactions"][$i]["trans_description"] }}</td>
                                        <td>{{ $vendor["transactions"][$i]["trans_date"] }}</td>
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

