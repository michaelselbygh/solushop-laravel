@extends('portal.layouts.manager.master')

@section('page-title')
    Manager Dashboard
@endsection

@section('content-body')
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-12">
            <div class="card pull-up" style="cursor:pointer;" onclick="submitOrdersFilterForm()">
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
            <div class="card pull-up" style="cursor:pointer;" onclick="submitOrdersFilterForm()">
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
            <div class="card pull-up" style="cursor:pointer;" onclick="submitOrdersFilterForm()">
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
            <div class="card pull-up" style="cursor:pointer;" onclick="submitOrdersFilterForm()">
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


    <script>
        function submitOrdersFilterForm(filter)
        {
            document.getElementById('orders_filter').value = filter;
            document.getElementById('orders-filter-form').submit(); 
        } 
    </script>
@endsection