@extends('portal.layouts.manager.master')

@section('page-title')
    Customer - {{ $customer["first_name"]." ".$customer["last_name"] }}
@endsection

@section('content-body')
    <div class="row">
        <div class="col-md-12">
            
        </div>
    </div>
   
        
    <div class="row">
        <div class="col-md-7">
            <h5 class="card-title">Edit {{ $customer["first_name"] }}'s details </h5>
            @include('portal.main.success-and-error.message')
            <div class="card" style="">
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form class="form" method="POST" action="{{ route("manager.process.customer", $customer["id"]) }}" enctype="multipart/form-data">
                            <div class="form-body">
                                @csrf
                                <input type="hidden" name="customer_action" value="update_details"/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id">Customer ID</label>
                                            <input id="id" name="id"  class="form-control round" value="{{ $customer["id"] }}" type="text" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="balance">S-Wallet Balance</label>
                                            <input id="balance" name="balance"  class="form-control round" value="GH¢ {{ abs(round(($customer['milk']['milk_value'] * $customer['milkshake']) - $customer['chocolate']['chocolate_value'] * 1, 2)) }}" type="text" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <input id="first_name" name="first_name" class="form-control round" placeholder="Enter first name" value="{{ $customer["first_name"] }}" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input id="last_name" name="last_name"  class="form-control round" placeholder="Enter last name" value="{{ $customer["last_name"] }}" type="text" required> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input id="email" name="email"  class="form-control round" placeholder="Enter email" value="{{ $customer["email"] }}" type="email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone Number</label>
                                            <input id="phone" name="phone"  class="form-control round" placeholder="Enter phone e.g. 0204456789" value="{{ "0".substr($customer["phone"], 3) }}" type="text" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions" style="text-align:center; padding: 20px;">
                                <button type="submit" name="update_details" class="btn btn-success">
                                        Update {{ $customer["first_name"] }}'s Details
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <h5 class="card-title">{{ $customer["first_name"] }}'s Orders</h5>
            <div class="card">
                <div class="card-content collapse show">
                    <div class="card-body card-dashboard">
                        <table class="table table-striped table-bordered zero-configuration" id="customer-orders">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Items Ordered by {{$customer["first_name"] }}</th>
                                    <th>State</th>
                                    <th>Made On</th>
                                    <th>Updated</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody> 
                                @for($i=0; $i<sizeof($customer["orders"]); $i++) 
                                    <tr>
                                        <td>{{ substr($customer["orders"][$i]["id"], 10) }}</td>
                                        <td>
                                            <ul class="list-unstyled users-list m-0">
                                                @for ($j = 0; $j < sizeof($customer["orders"][$i]["order_items"]); $j++) 
                                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="{{ $customer["orders"][$i]["order_items"][$j]["oi_name"] }}" class="avatar avatar-sm pull-up">
                                                        <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius"
                                                        src="{{ url("app/assets/img/products/thumbnails/".$customer["orders"][$i]["order_items"][$j]["sku"]["product"]["images"][0]["pi_path"].".jpg") }}"
                                                        alt="{{ $customer["orders"][$i]["order_items"][$j]["oi_name"] }}">
                                                    </li>
                                                @endfor
                                            </ul>
                                        </td>
                                        <td>
                                            {!! $customer["orders"][$i]["order_state"]["os_user_html"] !!}
                                        </td>
                                        <td>
                                            {{ date('g:ia, l jS F Y', strtotime($customer["orders"][$i]["order_date"])) }}
                                        </td>
                                        <td>
                                            {{ $customer["orders"][$i]["order_date"] }}
                                        </td>
                                        <td>
                                            <a target="new" href="{{ url("portal/manager/order/".$customer["orders"][$i]["id"]) }}">
                                                <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View Order"  style="margin-top: 3px; background-color: black !important; border-color: black !important" class="btn btn-success btn-sm round">
                                                    <i class="ft-eye"></i>
                                                </button>
                                            </a>
                                        </td>
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
        <div class="col-md-5">
            <h5 class="card-title">{{ $customer["first_name"] }}'s Addresses</h5>
            <div class="card">
                <div class="card-content collapse show">
                    <div class="card-body card-dashboard">
                        <table class="table table-striped table-bordered zero-configuration" id="customer-addresses">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @for($i=0; $i<sizeof($customer["addresses"]); $i++) 
                                    <tr>
                                        <td>{{ $customer["addresses"][$i]["id"] }}</td>
                                        <td>{{ $customer["addresses"][$i]["ca_town"]." ".$customer["addresses"][$i]["ca_address"] }}</td>
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
            $('#customer-addresses').dataTable( {
                "order": [
                    [1, 'asc']
                ]
            } );

            $('#customer-orders').dataTable( {
                "order": [
                    [4, 'desc']
                ]
            } );
        })
    </script>
@endsection

