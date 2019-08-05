@extends('portal.layouts.manager.master')

@section('page-title')
    Order {{ $order["id"] }}
@endsection

@section('content-body')
    <div class="row">
        <div class="col-md-12">
            
        </div>
    </div>
   
        
    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="card-title">Order Summary - <b>{{ $order["id"] }}</b></h5>
                </div>
                <div class="col-md-4" style="text-align: right; margin-bottom:5px;">
                    <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Confirm Order"  style="margin-top: 3px;" class="btn btn-info btn-sm round">
                            <i class="ft-check"></i>
                        </button>
                    <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Confirm Payment Received"  style="margin-top: 3px; background-color: green !important; border-color: green !important" class="btn btn-success btn-sm round">
                        <i class="ft-check"></i>
                    </button>
                    <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Cancel Order with refund to Solushop Wallet "  style="margin-top: 3px; background-color: red !important; border-color: red !important" class="btn btn-success btn-sm round">
                        <i class="ft-x"></i>
                    </button>
                    <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Cancel Order (No Refund)"  style="margin-top: 3px; background-color: black !important; border-color: black !important" class="btn btn-success btn-sm round">
                        <i class="ft-x"></i>
                    </button>
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
                                            {!! $order["order_items"][$i]["order_item_state"]["ois_html"] !!}
                                        </td>
                                        <td>
                                            {{ $order["order_items"][$i]["updated_at"] }}
                                        </td>
                                        <td>
                                            <a target="new" href="{{ url("portal/manager/product/".$order["order_items"][$i]["sku"]["product"]["id"]) }}">
                                                <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $order["order_items"][$i]["oi_name"]  }}"  style="margin-top: 3px; background-color: black !important; border-color: black !important" class="btn btn-success btn-sm round">
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
                                        @if (isset($order["order_scoupon"]) AND $order["order_scoupon"] != NULL AND $order["order_scoupon"] != "NULL")
                                            {{ round(0.99 * $order["order_subtotal"], 2) }}
                                        @else
                                            {{ round($order["order_subtotal"], 2) }}
                                        @endif
                                    </div>
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
                                <p style="font-weight:500;">Made On : </p>
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
        </div>
    </div>

@endsection

