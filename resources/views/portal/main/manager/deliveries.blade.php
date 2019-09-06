@extends('portal.layouts.manager.master')

@section('page-title')Active Deliveries @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6"  style="margin-top:10px;">
                        <h5 class="card-title">Active Deliveries</h5>
                    </div>
                    <div class="col-md-6" style="text-align: right; margin-bottom:10px;">
                        @if(sizeof($delivery_items) > 0)
                        <form method="POST" action="{{ route('manager.process.active.deliveries') }}">
                                @csrf
                                <button class="btn btn-success" type="submit">
                                    Download Delivery Guide
                                </button>
                                <input id="delivery_action" type="hidden" name="delivery_action"  value="download_delivery_guide"/>
                            </form>
                        @else
                            <button class="btn btn-danger" disabled>
                                Delivery Guide Unavailable
                            </button>
                        @endif
                    </div>
                </div>
                @include('portal.main.success-and-error.message')
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="delivery-items">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Image</th>
                                        <th>SKU</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th style="min-width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($delivery_items); $i++) 
                                        <tr>
                                            <td>{{ $delivery_items[$i]["oi_order_id"] }}</td>
                                            <td>
                                                <ul class="list-unstyled users-list m-0">
                                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="{{ $delivery_items[$i]["oi_name"] }}" class="avatar avatar-sm pull-up">
                                                        <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius"
                                                        src="{{ url("app/assets/img/products/thumbnails/".$delivery_items[$i]["sku"]["product"]["images"][0]["pi_path"].".jpg") }}"
                                                        alt="{{ $delivery_items[$i]["oi_name"] }}">
                                                    </li>
                                                </ul>
                                            </td>
                                            <td>{{ $delivery_items[$i]["oi_sku"] }}</td>
                                            <td>{{ $delivery_items[$i]["oi_name"] }}</td>
                                            <td>{{ $delivery_items[$i]["oi_quantity"] }}</td>
                                            <td>{{ $delivery_items[$i]["created_at"] }}</td>
                                            <td>{{ $delivery_items[$i]["updated_at"] }}</td>
                                            <td>
                                                <a href="{{ url('portal/manager/product/'.$delivery_items[$i]["sku"]["product"]["id"]) }}">
                                                    <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $delivery_items[$i]["oi_name"] }}"   class="btn btn-info btn-sm round">
                                                        <i class="ft-eye"></i>
                                                    </button>
                                                </a>
                                                @if($delivery_items[$i]["oi_state"] == 3)
                                                    <button onclick="submitItemDeliveredForm('{{ $delivery_items[$i]['id'] }}')" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Mark {{ $delivery_items[$i]["oi_name"] }} as delivered" class="btn btn-success btn-sm round">
                                                        <i class="ft-check"></i>
                                                    </button>
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
            </div>
        </div>
    </section>
    <form id="item-delivered-form" method="POST" action="{{ route('manager.process.active.deliveries') }}">
        @csrf
        <input type="hidden" name="delivered_item_id" id="delivered_item_id" />
        <input id="delivery_action" type="hidden" name="delivery_action"  value="mark_item"/>
    </form>

    <script>
    $(document).ready(function(){
        $('#delivery-items').dataTable( {
            "order": [
                [0, 'desc']
            ]
        } );
    })

    function submitItemDeliveredForm(orderItemID)
    {
        document.getElementById('delivered_item_id').value = orderItemID;
        document.getElementById('item-delivered-form').submit(); 
    } 
    </script>
@endsection

