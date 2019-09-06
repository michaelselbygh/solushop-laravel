@extends('portal.layouts.sales-associate.master')

@section('page-title')Orders @endsection

@section('content-body')
    <h5 class="card-title">Orders</h5>

    <div class="row">
        <div id="recent-transactions" class="col-md-12">
            @include('portal.main.success-and-error.message')
            <div class="card" style="min-height: 450px">
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        @if ($orders['all_orders'] > 0) 
                            <div class="table-responsive">
                                <table id="orders" class="table table-striped table-bordered zero-configuration">
                                    <thead>
                                        <tr>
                                            <th class="border-top-0">Customer Name</th>
                                            <th class="border-top-0">Customer Phone</th>
                                            <th class="border-top-0">Products Purchased</th>
                                            <th class="border-top-0">Order ID</th>
                                            <th class="border-top-0">Made On</th>
                                            <th class="border-top-0">State</th>
                                            <th class="border-top-0">Created At</th>
                                            <th class="border-top-0">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for ($i = 0; $i < sizeof($orders['all_orders']); $i++) 
                                            <tr>
                                                <td class="text-truncate">
                                                    <span>
                                                        {{ $orders["all_orders"][$i]["customer"]["first_name"]." ".$orders["all_orders"][$i]["customer"]["last_name"] }}
                                                    </span>
                                                </td>
                                                <td class="text-truncate">
                                                    <span>
                                                        {{ "0".substr($orders["all_orders"][$i]["customer"]["phone"], 3) }}
                                                    </span>
                                                </td>
                                                <td class="text-truncate p-1">
                                                    <ul class="list-unstyled users-list m-0">
                                                        @for ($j = 0; $j < sizeof($orders["all_orders"][$i]["order_items"]); $j++) 
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="{{ $orders["all_orders"][$i]["order_items"][$j]["oi_name"] }}" class="avatar avatar-sm pull-up">
                                                                <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius"
                                                                src="{{ url("app/assets/img/products/thumbnails/".$orders["all_orders"][$i]["order_items"][$j]["sku"]["product"]["images"][0]["pi_path"].".jpg") }}"
                                                                alt="{{ $orders["all_orders"][$i]["order_items"][$j]["oi_name"] }}">
                                                            </li>
                                                        @endfor
                                                    </ul>
                                                </td>
                                                <td class="text-truncate">
                                                    {{ $orders["all_orders"][$i]["id"] }}
                                                </td>
                                                <td class="text-truncate">
                                                    {{ date('g:ia, l jS F Y', strtotime($orders["all_orders"][$i]["order_date"])) }}
                                                </td>
                                                <td class="text-truncate">
                                                    {!! $orders["all_orders"][$i]["order_state"]["os_user_html"] !!}
                                                </td>
                                                <td class="text-truncate">
                                                    {{ $orders["all_orders"][$i]["order_date"] }}
                                                </td>
                                                <td class="text-truncate">
                                                    <a target="new" href="{{ url("portal/sales-associate/order/".$orders["all_orders"][$i]["id"]) }}">
                                                        <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View Order"  style="margin-top: 3px;" class="btn btn-info btn-sm round">
                                                            <i class="ft-eye"></i>
                                                        </button>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        @else 
                            <h5 style='margin-top: 15%; margin-bottom: 20%; text-align:center'>No orders.</h5></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function submitOrdersFilterForm(filter)
        {
            document.getElementById('orders_filter').value = filter;
            document.getElementById('orders-filter-form').submit(); 
        } 

        $(document).ready(function(){
            $('#orders').dataTable( {
                "order": [
                    [6, 'desc']
                ]
            } );
        })
    </script>
@endsection