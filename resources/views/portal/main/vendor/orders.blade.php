@extends('portal.layouts.vendor.master')

@section('page-title')Orders @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12"  style="margin-top:10px;">
                        <h5 class="card-title">Orders Due for Pick Up</h5>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="pick-up-items">
                                <thead>
                                    <tr>
                                        <th>Order Item ID</th>
                                        <th>Image</th>
                                        <th>SKU</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Ordered At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($orders); $i++) 
                                        <tr>
                                            <td>{{ $orders[$i]->id }}</td>
                                            <td>
                                                <ul class="list-unstyled users-list m-0">
                                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="{{ $orders[$i]->product_name }}" class="avatar avatar-sm pull-up">
                                                        <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius"
                                                        src="{{ url("app/assets/img/products/thumbnails/".$orders[$i]->image["pi_path"].".jpg") }}"
                                                        alt="{{ $orders[$i]->product_name }}">
                                                    </li>
                                                </ul>
                                            </td>
                                            <td>{{ $orders[$i]->oi_sku }}</td>
                                            <td>{{ $orders[$i]->oi_name }}</td>
                                            <td>{{ $orders[$i]->oi_quantity }}</td>
                                            <td>{{ date('g:ia, l jS F Y', strtotime($orders[$i]->updated_at)) }}</td>
                                            <td>
                                                <a href="{{ url('portal/vendor/product/'.$orders[$i]->product_slug) }}" target="_blank">
                                                    <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $orders[$i]->product_name }}"  style="margin-top: 3px;" class="btn btn-info btn-sm round">
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

