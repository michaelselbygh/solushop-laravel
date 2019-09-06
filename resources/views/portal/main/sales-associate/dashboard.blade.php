@extends('portal.layouts.sales-associate.master')

@section('page-title')Dashboard @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-5">
                        <h5 class="card-title">Transactions</h5>
                    </div>
                    <div class="col-md-7">
                        <h5 class="card-title" style="text-align: right">Balance - <b>GH¢ {{ round(Auth::guard('sales-associate')->user()->balance , 2) }} </b></h5>
                    </div>
                </div>

                
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="transactions">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Value</th>
                                        <th>Cred.</th>
                                        <th>Deb.</th>
                                        <th></th>
                                        <th>Description</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($dashboard["transactions"]); $i++) 
                                        <tr>
                                            <td>{{ $dashboard["transactions"][$i]["id"] }}</td>
                                            <td>{{ $dashboard["transactions"][$i]["trans_type"] }}</td>
                                            <td>{{ $dashboard["transactions"][$i]["trans_amount"] }}</td>
                                            <td>
                                                @if(is_null($dashboard["transactions"][$i]["trans_credit_account"]))
                                                    -
                                                @else
                                                    {{ $dashboard["transactions"][$i]["trans_credit_account"] }}
                                                @endif
                                            </td>
                                            <td>
                                                @if(is_null($dashboard["transactions"][$i]["trans_debit_account"]))
                                                    -
                                                @else
                                                    {{ $dashboard["transactions"][$i]["trans_debit_account"] }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($dashboard["transactions"][$i]["trans_debit_account_type"] == 1 AND in_array($dashboard["transactions"][$i]["trans_credit_account_type"], [2, 4, 6, 8, 10]))
                                                    <img src="{{ url("portal/images/transactions/red-out.png") }}" style="width: 30px;"/>
                                                @elseif($dashboard["transactions"][$i]["trans_credit_account_type"] == 1 AND in_array($dashboard["transactions"][$i]["trans_debit_account_type"], [2, 4, 6, 8, 10]))
                                                    <img src="{{ url("portal/images/transactions/green-in.png") }}" style="width: 30px;"/>
                                                @elseif($dashboard["transactions"][$i]["trans_debit_account_type"] == 1 AND in_array($dashboard["transactions"][$i]["trans_credit_account_type"], [3, 5, 7, 9]))
                                                    <img src="{{ url("portal/images/transactions/yellow-out.png") }}" style="width: 30px;"/>
                                                @elseif($dashboard["transactions"][$i]["trans_credit_account_type"] == 1 AND in_array($dashboard["transactions"][$i]["trans_debit_account_type"], [3, 5, 7, 9]))
                                                    <img src="{{ url("portal/images/transactions/yellow-in.png") }}" style="width: 30px;"/>
                                                @else
                                                    <img src="{{ url("portal/images/transactions/neutral.png") }}" style="width: 30px;"/>
                                                @endif
                                            </td>
                                            <td>{{ $dashboard["transactions"][$i]["trans_description"] }}</td>
                                            <td>{{ $dashboard["transactions"][$i]["trans_date"] }}</td>
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
            <div class="col-md-4">
                <h5 class="card-title">Badge</h5>
                <div class="card">
                    <div class="row">
                        <div class="col-md-12" style="padding-top: 10px;">
                            <div style="text-align:center">
                                    <img style="width:155px; height:auto; padding:10px;" src="{{url("portal/images/s-team-badges/".$sales_associate["badge_info"]["sab_image"]) }}"/>
                            </div>
                        </div>
                    </div>
                </div>
                <h5 class="card-title">Details</h5>
                <div class="card">
                    <div class="row">
                        <div class="col-md-12" style="padding-top: 30px; padding-left:30px;">
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-4" style="font-weight: 600;">
                                    ID : <br>
                                    Status : <br>
                                    Coupon : <br>
                                    Commision  : <br>
                                    Total Sales : <br><br><br>
                                </div>
                                <div class="col-7">
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
