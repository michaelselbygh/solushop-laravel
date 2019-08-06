@extends('portal.layouts.manager.master')

@section('page-title')
    Active Deliveries
@endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-6"  style="margin-top:10px;">
                        <h5 class="card-title">Active Deliveries</h5>
                    </div>
                    <div class="col-6" style="text-align: right; margin-bottom:10px;">
                        @if(sizeof($delivery_items) > 0)
                            <a>
                                <button class="btn btn-success">
                                    Download Delivery Guides
                                </button>
                            </a>
                        @else
                            <button class="btn btn-danger" disabled>
                                Delivery Guides Unavailable
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
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($delivery_items); $i++) 
                                        <tr>
                                            <td>{{ $delivery_items[$i]["oi_order_id"] }}</td>
                                            <td><img src="{{ url("app/assets/img/products/thumbnails/".$delivery_items[$i]["sku"]["product"]["images"][0]["pi_path"].".jpg") }}" style="width: 40px; height:auto; border-radius:5px;"/></td>
                                            <td>{{ $delivery_items[$i]["oi_sku"] }}</td>
                                            <td>{{ $delivery_items[$i]["oi_name"] }}</td>
                                            <td>{{ $delivery_items[$i]["oi_quantity"] }}</td>
                                            <td>{{ $delivery_items[$i]["created_at"] }}</td>
                                            <td>{{ $delivery_items[$i]["updated_at"] }}</td>
                                            <td>
                                                <a href="{{ url('portal/manager/product/'.$delivery_items[$i]["sku"]["product"]["id"]) }}">
                                                    <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $delivery_items[$i]["oi_name"] }}"  style="margin-top: 3px; background-color: black !important; border-color: black !important" class="btn btn-success btn-sm round">
                                                        <i class="ft-eye"></i>
                                                    </button>
                                                </a>
                                                @if($delivery_items[$i]["oi_state"] == 3)
                                                    <button onclick="submitItemDeliveredForm('{{ $delivery_items[$i]['id'] }}')" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Mark {{ $delivery_items[$i]["oi_name"] }} as delivered" style="margin-top: 3px; background-color: green !important; border-color: green !important" class="btn btn-success btn-sm round">
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

