@extends('portal.layouts.manager.master')

@section('page-title')
    Manager Dashboard
@endsection

@section('content-body')
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-12">
            <div class="card pull-up" style="cursor:pointer;" onclick="submitOrdersFilterForm('New')">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="media-body text-left">
                                <h3 class="info">{{ $dashboard['new_orders_count'] }}</h3>
                                <h6>New {{ str_plural('Order', $dashboard['new_orders_count']) }}</h6>
                            </div>
                            <div>
                                <i class="icon-plus info font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                            <div class="progress-bar bg-gradient-x-info" role="progressbar" style="width: {{ 100*$dashboard['new_orders_count']/$dashboard['total_orders_count'] }}%"
                                aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-12">
            <div class="card pull-up" style="cursor:pointer;" onclick="submitOrdersFilterForm('Ongoing')">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="media-body text-left">
                                <h3 class="warning">{{ $dashboard['ongoing_orders_count'] }}</h3>
                                <h6>Ongoing {{ str_plural('Order', $dashboard['ongoing_orders_count']) }}</h6>
                            </div>
                            <div>
                                <i class="icon-loop warning font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                            <div class="progress-bar bg-gradient-x-warning" role="progressbar" style="width: {{ 100*$dashboard['ongoing_orders_count']/$dashboard['total_orders_count'] }}%"
                                aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-12">
            <div class="card pull-up" style="cursor:pointer;" onclick="submitOrdersFilterForm('Completed')">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="media-body text-left">
                                <h3 class="success">{{ $dashboard['completed_orders_count'] }}</h3>
                                <h6>Completed {{ str_plural('Order', $dashboard['completed_orders_count']) }}</h6>
                            </div>
                            <div>
                                <i class="icon-check success font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                            <div class="progress-bar bg-gradient-x-success" role="progressbar" style="width: {{ 100*$dashboard['completed_orders_count']/$dashboard['total_orders_count'] }}%"
                                aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-12">
            <div class="card pull-up" style="cursor:pointer;" onclick="submitOrdersFilterForm('Cancelled')">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="media-body text-left">
                                <h3 class="danger">{{ $dashboard['cancelled_orders_count'] }}</h3>
                                <h6>Cancelled {{ str_plural('Order', $dashboard['cancelled_orders_count']) }}</h6>
                            </div>
                            <div>
                                <i class="icon-close danger font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                            <div class="progress-bar bg-gradient-x-danger" role="progressbar" style="width: {{ 100*$dashboard['cancelled_orders_count']/$dashboard['total_orders_count'] }}%"
                                aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="orders-filter-form" method="POST" action="{{ route("manager.process.orders") }}">
        @csrf
        <input type="hidden" name="orders_filter" id="orders_filter"/>
    </form>


    <div class="row">
        <div id="recent-transactions" class="col-12">
            <h5 class="card-title">New Orders</h5>
            <div class="card" style="min-height: 450px">
                <div class="card-content">
                    @if ($dashboard['new_orders_count'] > 0) 
                            <div class="table-responsive">
                                <table id="recent-orders" class="table table-hover table-xl mb-0">
                                    <thead>
                                        <tr>
                                            <th class="border-top-0">Customer Name</th>
                                            <th class="border-top-0">Customer Phone</th>
                                            <th class="border-top-0">Products Purchased</th>
                                            <th class="border-top-0">Order ID</th>
                                            <th class="border-top-0">Order Date and Time</th>
                                            <th class="border-top-0">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @for ($i = 0; $i < $dashboard['new_orders_count']; $i++) 
                                        <tr>
                                            <td class="text-truncate">
                                                <span>
                                                    {{ $dashboard["new_orders"][$i]["customer"]["first_name"]." ".$dashboard["new_orders"][$i]["customer"]["last_name"] }}
                                                </span>
                                            </td>
                                            <td class="text-truncate">
                                                <span>
                                                    {{ "0".substr($dashboard["new_orders"][$i]["customer"]["phone"], 3) }}
                                                </span>
                                            </td>
                                            <td class="text-truncate p-1">
                                                <ul class="list-unstyled users-list m-0">
                                                    @for ($j = 0; $j < sizeof($dashboard["new_orders"][$i]["order_items"]); $j++) 
                                                        <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="{{ $dashboard["new_orders"][$i]["order_items"][$j]["oi_name"] }}" class="avatar avatar-sm pull-up">
                                                            <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius"
                                                            src="{{ url("app/assets/img/products/thumbnails/".$dashboard["new_orders"][$i]["order_items"][$j]["sku"]["product"]["images"][0]["pi_path"].".jpg") }}"
                                                            alt="{{ $dashboard["new_orders"][$i]["order_items"][$j]["oi_name"] }}">
                                                        </li>
                                                    @endfor
                                                </ul>
                                            </td>
                                            <td class="text-truncate">
                                                {{ $dashboard["new_orders"][$i]["id"] }}
                                            </td>
                                            <td class="text-truncate">
                                                {{ date('g:ia, l jS F Y', strtotime($dashboard["new_orders"][$i]["order_date"])) }}
                                            </td>
                                            <td class="text-truncate">
                                                <a target="new" href="{{ url("portal/manager/order/".$dashboard["new_orders"][$i]["id"]) }}">
                                                    <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View Order"  style="margin-top: 3px; background-color: black !important; border-color: black !important" class="btn btn-success btn-sm round">
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
                        <h5 style='margin-top: 15%; margin-bottom: 20%; text-align:center'>No new orders yet.</h5></div>
                    @endif
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
    </script>
@endsection