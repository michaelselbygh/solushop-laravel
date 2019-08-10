@extends('portal.layouts.manager.master')

@section('page-title')Accounts @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-8">
                <h5 class="card-title">Transactions</h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="transactions">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Cred.</th>
                                        <th>Deb.</th>
                                        <th></th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Recorder</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($accounts["transactions"]); $i++) 
                                        <tr>
                                            <td>{{ $accounts["transactions"][$i]["id"] }}</td>
                                            <td>{{ $accounts["transactions"][$i]["trans_type"] }}</td>
                                            <td>
                                                @if(is_null($accounts["transactions"][$i]["trans_credit_account"]))
                                                    -
                                                @else
                                                    {{ $accounts["transactions"][$i]["trans_credit_account"] }}
                                                @endif
                                            </td>
                                            <td>
                                                @if(is_null($accounts["transactions"][$i]["trans_debit_account"]))
                                                    -
                                                @else
                                                    {{ $accounts["transactions"][$i]["trans_debit_account"] }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($accounts["transactions"][$i]["trans_debit_account_type"] == 1 AND in_array($accounts["transactions"][$i]["trans_credit_account_type"], [2, 4, 6, 8, 10]))
                                                    <img src="{{ url("portal/images/transactions/green-in.png") }}" style="width: 30px;"/>
                                                @elseif($accounts["transactions"][$i]["trans_credit_account_type"] == 1 AND in_array($accounts["transactions"][$i]["trans_debit_account_type"], [2, 4, 6, 8, 10]))
                                                    <img src="{{ url("portal/images/transactions/red-out.png") }}" style="width: 30px;"/>
                                                @elseif($accounts["transactions"][$i]["trans_debit_account_type"] == 1 AND in_array($accounts["transactions"][$i]["trans_credit_account_type"], [3, 5, 7, 9]))
                                                    <img src="{{ url("portal/images/transactions/yellow-in.png") }}" style="width: 30px;"/>
                                                @elseif($accounts["transactions"][$i]["trans_credit_account_type"] == 1 AND in_array($accounts["transactions"][$i]["trans_debit_account_type"], [3, 5, 7, 9]))
                                                    <img src="{{ url("portal/images/transactions/yellow-out.png") }}" style="width: 30px;"/>
                                                @else
                                                    <img src="{{ url("portal/images/transactions/neutral.png") }}" style="width: 30px;"/>
                                                @endif
                                            </td>
                                            <td>{{ $accounts["transactions"][$i]["trans_description"] }}</td>
                                            <td>{{ $accounts["transactions"][$i]["trans_date"] }}</td>
                                            <td>{{ $accounts["transactions"][$i]["trans_recorder"] }}</td>
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
            <div class="col-4">
                <h5 class="card-title">Balance</h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Total : </h6>
                                    <h6>Due Vendors : </h6>
                                    <h6>Due Sales Associates : </h6>
                                    <h6>Available : </h6>
                                </div>
                                <div class="col-md-6" >
                                    <h6><b>GH¢ {{ round($accounts["balance"]["total"], 2) }}</b></h6>
                                    <h6 style="color: #fbae17;"><b>GH¢ {{ round($accounts["balance"]["vendors"], 2) }}</b></h6>
                                    <h6 style="color: #fbae17;"><b>GH¢ {{ round($accounts["balance"]["sales-associates"], 2) }}</b></h6>
                                    <h6 style="color: green;"><b>GH¢ {{ round($accounts["balance"]["available"], 2) }}</b></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="card-title">Record Payment</h5>
                @include('portal.main.success-and-error.message')
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <form method="POST" action={{ route("manager.process.accounts")}}>
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="payment_type">Type</label>
                                            <select class="form-control" name='payment_type' id="payment_type" style='border-radius:7px;' required>
                                                <option value='Pay-Out'>Pay-Out</option>
                                                <option value='Pay-In'>Pay-In</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6" >
                                        <div class="form-group">
                                            <label for="payment_amount">Amount</label>
                                            <input id="payment_amount" name="payment_amount" value="0.01" class="form-control round" type="number" step="0.01" min="0.01" max="{{ round($accounts["balance"]["available"], 2) }}" required> 
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="payment_description">Description</label>
                                    <input id="payment_description" name="payment_description"  class="form-control round" placeholder="Describe the payment." value="" type="text" required> 
                                </div>
                                <div class="form-actions" style="text-align:center;">
                                    <button type="submit" name="record_payment" class="btn btn-success">
                                            Record Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


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

