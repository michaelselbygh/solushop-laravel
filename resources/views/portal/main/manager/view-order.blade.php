@extends('portal.layouts.manager.master')

@section('page-title')Order {{ $order["id"] }}@endsection

@section('content-body')
    <div class="row">
        <div class="col-md-12">
            
        </div>
    </div>
   
        
    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-8" style="margin-top : 10px;">
                    <h5 class="card-title">Order Summary - <b>{{ $order["id"] }}</b></h5>
                </div>
                <div class="col-md-4" style="text-align: right; margin-bottom:5px;">
                    <form method="POST" action="{{ route("manager.process.order", $order["id"]) }}">
                        @csrf
                        @if ( in_array($order["order_state"]["id"], [1]) )
                            <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Confirm Payment Received"  style="margin-top: 3px;" class="btn btn-success btn-sm round" type="submit" name="order_action" value="confirm_order_payment">
                                <i class="ft-check"></i>
                            </button>
                            <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Cancel Order ( No Refund )"  style="margin-top: 3px; background-color: black !important; border-color: black !important" class="btn btn-success btn-sm round" type="submit" name="order_action" value="cancel_order_no_refund">
                                <i class="ft-x"></i>
                            </button>
                        @elseif(in_array($order["order_state"]["id"], [2]))
                            <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Confirm Order"  style="margin-top: 3px;" class="btn btn-info btn-sm round" type="submit" name="order_action" value="confirm_order">
                                <i class="ft-check"></i>
                            </button>
                            <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Cancel Order ( Order Items Refund Only )"  style="margin-top: 3px;" class="btn btn-warning btn-sm round" type="submit" name="order_action" value="cancel_order_partial_refund">
                                    <i class="ft-x"></i>
                                </button>
                            <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Cancel Order ( Full Refund  )"  style="margin-top: 3px; " class="btn btn-danger btn-sm round" type="submit" name="order_action" value="cancel_order_full_refund">
                                <i class="ft-x"></i>
                            </button>
                            <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Cancel Order ( No Refund )"  style="margin-top: 3px; background-color: black !important; border-color: black !important" class="btn btn-success btn-sm round" type="submit" name="order_action" value="cancel_order_no_refund">
                                <i class="ft-x"></i>
                            </button>
                        @elseif(in_array($order["order_state"]["id"], [3, 4, 5]))
                            <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Cancel Order ( Order Items Refund Only )"  style="margin-top: 3px;" class="btn btn-warning btn-sm round" type="submit" name="order_action" value="cancel_order_partial_refund">
                                <i class="ft-x"></i>
                            </button>
                            <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Cancel Order ( Full Refund )"  style="margin-top: 3px;" class="btn btn-danger btn-sm round" type="submit" name="order_action" value="cancel_order_full_refund">
                                <i class="ft-x"></i>
                            </button>
                            <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Cancel Order ( No Refund )"  style="margin-top: 3px; background-color: black !important; border-color: black !important" class="btn btn-success btn-sm round" type="submit" name="order_action" value="cancel_order_no_refund">
                                <i class="ft-x"></i>
                            </button>
                        @endif
                        
                    </form>
                </div>
            </div>

            
            @include('portal.main.success-and-error.message')
            <div class="card">
                <div class="card-content collapse show">
                    <div class="card-body card-dashboard">
                        <table class="table table-hover table-xl mb-0">
                            <thead>
                                <tr>
                                    <th class="border-top-0">SKU</th>
                                    <th class="border-top-0">Preview</th>
                                    <th class="border-top-0">Price</th>
                                    <th class="border-top-0">Quantity</th>
                                    <th class="border-top-0">State</th>
                                    <th class="border-top-0">Last Updated</th>
                                    <th class="border-top-0">Action</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @for($i=0; $i<sizeof($order["order_items"]); $i++) 
                                    <tr>
                                        <td>{{ $order["order_items"][$i]["oi_sku"] }}</td>
                                        <td>
                                            <ul class="list-unstyled users-list m-0">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="{{ $order["order_items"][$i]["oi_name"] }}" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius"
                                                    src="{{ url("app/assets/img/products/thumbnails/".$order["order_items"][$i]["sku"]["product"]["images"][0]["pi_path"].".jpg") }}"
                                                    alt="{{ $order["order_items"][$i]["oi_sku"] }}">
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            {{ $order["order_items"][$i]["oi_selling_price"] - $order["order_items"][$i]["oi_discount"] }}
                                        </td>
                                        <td>
                                            x {{ $order["order_items"][$i]["oi_quantity"] }}
                                        </td>
                                        <td>
                                            {!! $order["order_items"][$i]["order_item_state"]["ois_html"] !!}
                                        </td>
                                        <td>
                                            {{ $order["order_items"][$i]["updated_at"] }}
                                        </td>
                                        <td>
                                            <a target="new" href="{{ url("portal/manager/product/".$order["order_items"][$i]["sku"]["product"]["id"]) }}">
                                                <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $order["order_items"][$i]["oi_name"]  }}"  style="margin-top: 3px;" class="btn btn-info btn-sm round">
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
                    <div class="card-footer" style="border-radius: 30px; text-align: right; border-top: none; padding-right:60px;">                
                        <div class="row">
                            <div class="col-md-10" style="text-align: right">
                                <b>
                                    <span style="font-size: 13px;">Sub-Total</span><br>
                                    @if (isset($order["order_scoupon"]) AND $order["order_scoupon"] != NULL AND $order["order_scoupon"] != "NULL")
                                        <span style="font-size: 13px;">Discount from S-Coupon</span><br>
                                    @endif
                                    <span style="font-size: 13px;">Shipping</span><br>
                                    <span style="font-size: 13px;">
                                        Total
                                        @if ($order["order_state"] == 1)
                                            Due
                                        @else
                                            Paid
                                        @endif
                                    </span><br>
                                </b>
                            </div>
                            <div class="col-md-2" style="text-align: right">
                                <b>
                                    <div id="subTotal"style="font-size: 13px;">
                                            {{ round($order["order_subtotal"], 2) }}
                                    </div>
                                    @if (isset($order["order_scoupon"]) AND $order["order_scoupon"] != NULL AND $order["order_scoupon"] != "NULL")
                                        <div id="subTotal"style="font-size: 13px;">
                                                {{ round(0.01 * $order["order_subtotal"], 2) }}
                                        </div>
                                    @endif
                                    <div id="shipping"style="font-size: 13px;">
                                        {{ round($order["order_shipping"], 2) }}
                                    </div>
                                    <div id="total"style="font-size: 13px;">
                                        @if (isset($order["order_scoupon"]) AND $order["order_scoupon"] != NULL AND $order["order_scoupon"] != "NULL")
                                            {{ round((0.99 * $order["order_subtotal"]) + $order["order_shipping"], 2) }}
                                        @else
                                            {{ round(($order["order_subtotal"]) + $order["order_shipping"], 2) }}
                                        @endif
                                    </div>
                                </b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (isset($order["profit_or_loss"]))
                @if ($order["profit_or_loss"] < 0)
                    <h5 class="card-title">Loss on Order 
                        : <b style="color:red"> GH¢ {{$order["profit_or_loss"]}}</b>
                        @if ($order["dp_shipping"] == NULL)
                            ( Shipping charge on company not included. )
                        @endif
                    </h5>
                @else
                    <h5 class="card-title">Profit on Order 
                        : <b style="color:green"> GH¢ {{$order["profit_or_loss"]}}</b>
                        @if ($order["dp_shipping"] == NULL)
                            ( Shipping charge on company not included. )
                        @endif
                    </h5>
                @endif

                {{-- Break down --}}
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-hover table-xl mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-top-0">Description</th>
                                        <th class="border-top-0">Amount</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($order["profit_or_loss_item"]['description']); $i++) 
                                        <tr>
                                            <td>{{ $order["profit_or_loss_item"]["description"][$i] }}</td>
                                            <td>
                                                @if ($order["profit_or_loss_item"]["amount"][$i] < 0)
                                                    <span style="color:red"> GH¢ {{ $order["profit_or_loss_item"]["amount"][$i] }}</span>
                                                @else
                                                    <span style="color:green"> GH¢ {{ $order["profit_or_loss_item"]["amount"][$i] }}</span>
                                                @endif
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
            @endif
        </div>
        <div class="col-md-4">
            <h5 class="card-title">Order Details</h5>
            <div class="card">
                <div class="card-content collapse show">
                    <div class="card-body card-dashboard">
                        <div class="row">
                            <div class="col-md-4">
                                <p style="font-weight:500;">Customer : </p>
                            </div>
                            <div class="col-md-8" >
                                <p>{{ $order["customer"]["first_name"]." ".$order["customer"]["last_name"] }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <p style="font-weight:500;">Phone : </p>
                            </div>
                            <div class="col-md-8" >
                                <p>{{ "0".substr($order["customer"]["phone"], 3) }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <p style="font-weight:500;">Address : </p>
                            </div>
                            <div class="col-md-8" >
                                <p>
                                    {{ $order["address"]["ca_town"]." - ".$order["address"]["ca_address"] }}
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <p style="font-weight:500;">Made At : </p>
                            </div>
                            <div class="col-md-8" >
                                <p>{{ date('g:ia, l jS F Y', strtotime($order["order_date"])) }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <p style="font-weight:500;">State : </p>
                            </div>
                            <div class="col-md-8" >
                                <p>{!! $order["order_state"]["os_user_html"] !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            @if (isset($order["order_scoupon"]) AND $order["order_scoupon"] != NULL AND $order["order_scoupon"] != "NULL")
                <h5 class="card-title">Sales Associate</h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div class="row">
                                <div class="col-md-4">
                                    <p style="font-weight:500;">Name :  </p>
                                </div>
                                <div class="col-md-8" >
                                    <p>{{ $order["coupon"]["sales_associate"]["first_name"]." ".$order["coupon"]["sales_associate"]["last_name"] }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <p style="font-weight:500;">Badge : </p>
                                </div>
                                <div class="col-md-8" >
                                    <p style="font-weight:500;">{{ $order["coupon"]["sales_associate"]["badge_info"]["sab_description"] }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <p style="font-weight:500;">Commision : </p>
                                </div>
                                <div class="col-md-8" >
                                    <p style="font-weight:500;">{{ 100*$order["coupon"]["sales_associate"]["badge_info"]["sab_commission"] }} % on Order</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <p style="font-weight:500;">Coupon : </p>
                                </div>
                                <div class="col-md-8" >
                                    <p style="font-weight:500;">{{ $order["coupon"]["coupon_code"] }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <p style="font-weight:500;">Phone : </p>
                                </div>
                                <div class="col-md-8" >
                                    <p>{{ "0".substr($order["coupon"]["sales_associate"]["phone"], 3) }}</p>
                                </div>
                                </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($order["order_state"]["id"] == 6 AND $order["dp_shipping"] == NULL AND isset($order["allow_shipping_entry"]))
                <h5 class="card-title">Delivery Partner and Charge</h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <form method="POST" action="{{ route("manager.process.order", $order["id"]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="delivery_partner">Partner</label>
                                            <select class="form-control" name='delivery_partner' id="delivery_partner" style='border-radius:7px;' required>
                                                @for ($i = 0; $i < sizeof($order["delivery_partner"]); $i++)
                                                    <option value='{{ $order["delivery_partner"][$i]["id"] }}'>{{ $order["delivery_partner"][$i]["first_name"] }} {{ $order["delivery_partner"][$i]["last_name"] }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6" >
                                        <div class="form-group">
                                            <label for="shipping_amount">Charge</label>
                                            <input id="shipping_amount" name="shipping_amount" value="0" class="form-control round" type="number" step="0.01" min="0" required> 
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions" style="text-align:center; padding: 0px;">
                                    <button type="submit" name="order_action" value="record_shipping" class="btn btn-success">
                                            Record Charge
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection

