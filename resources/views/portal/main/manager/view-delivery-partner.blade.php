@extends('portal.layouts.manager.master')

@section('page-title')Delivery Partner - {{ $delivery_partner["first_name"]." ".$delivery_partner["last_name"] }}@endsection

@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <h5 class="card-title">Manage delivery partner, {{ $delivery_partner["first_name"]." ".$delivery_partner["last_name"] }} </h5>
            @include('portal.main.success-and-error.message')
        </div>
    </div>
   
        
    <div class="row">
        <div class="col-md-7">
            <div class="card" style="">
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form class="form" method="POST" action="{{ route("manager.process.delivery.partner", $delivery_partner["id"]) }}" enctype="multipart/form-data">
                            <div class="form-body">
                                @csrf
                                <input type="hidden" name="sa_action" value="update_details"/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <input id="first_name" name="first_name" class="form-control round" placeholder="Enter first name" value="{{ $delivery_partner["first_name"] }}" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input id="last_name" name="last_name"  class="form-control round" placeholder="Enter last name" value="{{ $delivery_partner["last_name"] }}" type="text" required> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="dp_company">Company</label>
                                            <input id="dp_company" name="dp_company"  class="form-control round" value="{{ $delivery_partner["dp_company"] }}" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input id="email" name="email"  class="form-control round" placeholder="Enter email" value="{{ $delivery_partner["email"] }}" type="email" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="payment_details">Payment Details</label>
                                    <input id="payment_details" name="payment_details"  class="form-control round" placeholder="Enter Payment Details" value="{{ $delivery_partner["payment_details"] }}" type="text" required>
                                </div>
                            </div>
                            <div class="form-actions" style="text-align:center; padding: 20px;">
                                <button type="submit" name="update_details" class="btn btn-success">
                                        Update {{ $delivery_partner["first_name"] }}'s Details
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-5">
            <h5>Account Balance : GH¢ {{ $delivery_partner["balance"] }}</h5>
            <div class="card">
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-sm-3">
                            </div>
                            <div class="col-sm-6" style="text-align: center;"> 
                                <form method="POST" action="{{ route("manager.process.delivery.partner", $delivery_partner["id"]) }}">
                                    @csrf
                                    <div class="form-group" style="display: inline-block">
                                        <input type="hidden" name="sa_action" value="record_payout"/>
                                        <input id="pay_out_amount" name="pay_out_amount" class="form-control round" placeholder="0.00" value="{{ $delivery_partner["balance"] }}" type="number" min="0.1" max="{{ $delivery_partner["balance"] }}" step="0.1" style="width: 100%;" required><br>
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
                                @for($i=0; $i<sizeof($delivery_partner["transactions"]); $i++) 
                                    <tr>
                                        <td>{{ $delivery_partner["transactions"][$i]["id"] }}</td>
                                        <td>{{ $delivery_partner["transactions"][$i]["trans_type"] }}</td>
                                        <td>{{ $delivery_partner["transactions"][$i]["trans_amount"] }}</td>
                                        <td>
                                            @if($delivery_partner["transactions"][$i]["trans_debit_account_type"] == 1 AND in_array($delivery_partner["transactions"][$i]["trans_credit_account_type"], [2, 4, 6, 8, 10]))
                                                <img src="{{ url("portal/images/transactions/green-in.png") }}" style="width: 30px;"/>
                                            @elseif($delivery_partner["transactions"][$i]["trans_credit_account_type"] == 1 AND in_array($delivery_partner["transactions"][$i]["trans_debit_account_type"], [2, 4, 6, 8, 10]))
                                                <img src="{{ url("portal/images/transactions/red-out.png") }}" style="width: 30px;"/>
                                            @elseif($delivery_partner["transactions"][$i]["trans_debit_account_type"] == 1 AND in_array($delivery_partner["transactions"][$i]["trans_credit_account_type"], [3, 5, 7, 9]))
                                                <img src="{{ url("portal/images/transactions/yellow-in.png") }}" style="width: 30px;"/>
                                            @elseif($delivery_partner["transactions"][$i]["trans_credit_account_type"] == 1 AND in_array($delivery_partner["transactions"][$i]["trans_debit_account_type"], [3, 5, 7, 9]))
                                                <img src="{{ url("portal/images/transactions/yellow-out.png") }}" style="width: 30px;"/>
                                            @else
                                                <img src="{{ url("portal/images/transactions/neutral.png") }}" style="width: 30px;"/>
                                            @endif
                                        </td>
                                        <td>{{ $delivery_partner["transactions"][$i]["trans_description"] }}</td>
                                        <td>{{ $delivery_partner["transactions"][$i]["trans_date"] }}</td>
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

