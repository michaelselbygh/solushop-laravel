@extends('portal.layouts.manager.master')

@section('page-title')Active Pick-Ups @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6"  style="margin-top:10px;">
                        <h5 class="card-title">Active Pick-Ups</h5>
                    </div>
                    <div class="col-md-6" style="text-align: right; margin-bottom:10px;">
                        @if(sizeof($pick_up_items) > 0)
                            <form method="POST" action="{{ route('manager.process.active.pick.ups') }}">
                                @csrf
                                <button class="btn btn-success" type="submit">
                                    Download Pick Up Guide
                                </button>
                                <input id="pick_up_action" type="hidden" name="pick_up_action"  value="download_pick_up_guide"/>
                            </form>
                        @else
                            <button class="btn btn-danger" disabled>
                                Pick Up Guide Unavailable
                            </button>
                        @endif
                    </div>
                </div>
                @include('portal.main.success-and-error.message')
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="pick-up-items">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Image</th>
                                        <th>SKU</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Vendor</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th style="min-width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($pick_up_items); $i++) 
                                        <tr>
                                            <td>{{ $pick_up_items[$i]["oi_order_id"] }}</td>
                                            <td>
                                                <ul class="list-unstyled users-list m-0">
                                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="{{ $pick_up_items[$i]["oi_name"] }}" class="avatar avatar-sm pull-up">
                                                        <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius"
                                                        src="{{ url("app/assets/img/products/thumbnails/".$pick_up_items[$i]["sku"]["product"]["images"][0]["pi_path"].".jpg") }}"
                                                        alt="{{ $pick_up_items[$i]["oi_name"] }}">
                                                    </li>
                                                </ul>
                                            </td>
                                            <td>{{ $pick_up_items[$i]["oi_sku"] }}</td>
                                            <td>{{ $pick_up_items[$i]["oi_name"] }}</td>
                                            <td>{{ $pick_up_items[$i]["oi_quantity"] }}</td>
                                            <td>{{ $pick_up_items[$i]["sku"]["product"]["vendor"]["name"] }}</td>
                                            <td>{{ $pick_up_items[$i]["created_at"] }}</td>
                                            <td>{{ $pick_up_items[$i]["updated_at"] }}</td>
                                            <td>
                                                <a href="{{ url('portal/manager/product/'.$pick_up_items[$i]["sku"]["product"]["id"]) }}">
                                                    <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $pick_up_items[$i]["oi_name"] }}"  style="margin-top: 3px;" class="btn btn-info btn-sm round">
                                                        <i class="ft-eye"></i>
                                                    </button>
                                                </a>
                                                <button onclick="submitItemPickedUpForm('{{ $pick_up_items[$i]['id'] }}')" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Mark {{ $pick_up_items[$i]["oi_name"] }} as picked up" style="margin-top: 3px;" class="btn btn-success btn-sm round">
                                                    <i class="ft-check"></i>
                                                </button>
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

    <form id="item-picked-up-form" method="POST" action="{{ route('manager.process.active.pick.ups') }}">
        @csrf
        <input id="picked_up_item" type="hidden" name="picked_up_item_id"  value=""/>
        <input id="pick_up_action" type="hidden" name="pick_up_action"  value="mark_item"/>
    </form>

    <script>
    function submitItemPickedUpForm(orderItemID)
    {
        document.getElementById('picked_up_item').value = orderItemID;
        document.getElementById('item-picked-up-form').submit(); 
    } 

    $(document).ready(function(){
        $('#pick-up-items').dataTable( {
            "order": [
                [6, 'asc'],
                [3, 'asc'],
            ]
        } );
    })

    
    </script>
@endsection

