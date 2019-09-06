@extends('portal.layouts.manager.master')

@section('page-title')Delivery History @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-12">
                <h5 class="card-title">Delivery History</h5>
                @include('portal.main.success-and-error.message')
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="delivered-items">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Order Item ID</th>
                                        <th>Product SKU</th>
                                        <th>Product Description</th>
                                        <th>Quantity</th>
                                        <th>Recorded On</th>
                                        <th>RTS</th>
                                        <th>Recorded By</th>
                                        <th>Recorder ID</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($delivered_items); $i++) 
                                        <tr>
                                            <td>{{ $delivered_items[$i]["order_item"]["oi_order_id"] }}</td>
                                            <td>{{ $delivered_items[$i]["order_item"]["id"] }}</td>
                                            <td>{{ $delivered_items[$i]["order_item"]["oi_sku"] }}</td>
                                            <td>{{ $delivered_items[$i]["order_item"]["oi_name"] }}</td>
                                            <td>{{ $delivered_items[$i]["order_item"]["oi_quantity"] }}</td>
                                            <td>{{ date('g:ia, l jS F Y', strtotime($delivered_items[$i]["created_at"])) }}</td>
                                            <td>{{ $delivered_items[$i]["created_at"] }}</td>
                                            <td>{{ $delivered_items[$i]["di_marked_by_description"] }}</td>
                                            <td>{{ $delivered_items[$i]["di_marked_by_id"] }}</td>
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


    <script>
    $(document).ready(function(){
        $('#delivered-items').dataTable( {
            "order": [
                [6, 'desc']
            ]
        } );
    })
    </script>
@endsection

