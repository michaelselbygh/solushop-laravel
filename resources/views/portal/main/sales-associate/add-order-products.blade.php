@extends('portal.layouts.sales-associate.master')

@section('page-title')Add Order, Select Products @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-7">
                <h5 class="card-title">Add Order - Step 2 (Select {{$order["customer"]["first_name"] }}'s products)</h5>
                @include('portal.main.success-and-error.message')
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="sku">
                                <thead>
                                    <tr>
                                        <th>Product ID | SKU</th>
                                        <th>Product</th>
                                        <th>Preview</th>
                                        <th>Variant</th>
                                        <th>Cost</th>
                                        <th>Select</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i < sizeof($order["products"]); $i++) 
                                        @for ($j = 0; $j < sizeof($order["products"][$i]["skus"]); $j++)
                                            @if ($order["products"][$i]["skus"][$j]["sku_stock_left"] > 0)
                                                <tr>
                                                    <td>{{ $order["products"][$i]["id"]." | ".$order["products"][$i]["skus"][$j]["id"] }}</td>
                                                    <td>{{ $order["products"][$i]["product_name"] }}</td>
                                                    <td>
                                                        <ul class="list-unstyled users-list m-0">
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="{{ $order["products"][$i]["product_name"] }}" class="avatar avatar-sm pull-up">
                                                                <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius"
                                                                src="{{ url("app/assets/img/products/thumbnails/".$order["products"][$i]["images"][0]["pi_path"].".jpg") }}"
                                                                alt="{{ $order["products"][$i]["product_name"] }}">
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td>{{ $order["products"][$i]["skus"][$j]["sku_variant_description"]." ( ".$order["products"][$i]["skus"][$j]["sku_stock_left"]." left)" }}</td>
                                                    <td>{{ round($order["products"][$i]["product_selling_price"] - $order["products"][$i]["product_discount"], 2) }}</td>
                                                    <td>
                                                        <button  
                                                            data-toggle = "tooltip" 
                                                            data-popup = "tooltip-custom" 
                                                            data-original-title = "Add {{ $order["products"][$i]["skus"][$j]["id"] }}" 
                                                            class = "product btn btn-info btn-sm round"
                                                            product-sku     = "{{ $order["products"][$i]["skus"][$j]["id"] }}"
                                                            @if (trim(strtolower($order['products'][$i]['skus'][$j]['sku_variant_description'])) != 'none')
                                                                product-description  =  "{{ $order['products'][$i]['product_name'] }} - {{ $order['products'][$i]['skus'][$j]['sku_variant_description'] }}"
                                                            @else
                                                                product-description  = "{{ $order['products'][$i]['product_name'] }}"
                                                            @endif
                                                            product-cost     = "{{ round($order['products'][$i]['product_selling_price'] - $order['products'][$i]['product_discount'], 2) }}"
                                                            product-shipping     = "{{ $order["products"][$i]["product_dc"] }}"
                                                            product-stock      = "{{ $order["products"][$i]["skus"][$j]["sku_stock_left"] }}">
                                                            <i class="ft-plus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endfor
                                    @endfor
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <h5 class="card-title">Customer Details </h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                                <b>{{ $order["customer"]["first_name"]." ".$order["customer"]["last_name"] }} - {{ "0".substr($order["customer"]["phone"], 3) }}</b><br>
                                <b>{{ $order["address"]["ca_region"]." - ".$order["address"]["ca_town"] }} | {{ $order["address"]["ca_address"] }}</b><br>
                        </div>
                    </div>
                </div>

                <h5 class="card-title">Order Summary </h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div class="card-content" id="confirm-and-proceed">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th style="text-align: right;">Cost (GHS)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="orderItems">
                                        
                                    </tbody>
                                </table>
                                <div id="noItemsPlaceholder">
                                    <h6 style="padding: 100px 0px; text-align: center;">No items selected yet.<br>Complete order by selecting ordered items</h6>
                                </div>
                                
                            </div>
                            <div class="card-footer" style="text-align: center; padding-bottom:0px;">
                                
                                <div class="row">
                                    <div class="col-md-8" style="text-align: right">
                                        <span style="font-size: 12px;">Subtotal in GHS</span><br>
                                        <span style="font-size: 12px;">Shipping in GHS</span><br>
                                        <span style="font-size: 12px;">Total in GHS</span><br>
                                    </div>
                                    <div class="col-md-4" style="text-align: right">
                                        <b>
                                            <div id="subTotal" style="font-size: 12px;">0</div>
                                            <div id="shipping" style="font-size: 12px;">{{ $order["address"]["shipping_fare"]["sf_fare"] }}</div>
                                            <div id="total" style="font-size: 12px;">{{ $order["address"]["shipping_fare"]["sf_fare"] }}</div>
                                        </b>
                                    </div>
                                </div>
                                <hr>
                                <form method="POST" action="{{ route("sales-associate.process.add.order", [$order["customer"]["id"], $order["address"]["id"]]) }}">
                                    @csrf
                                    <input type='hidden' id='orderItemSubTotal' name='orderItemSubTotal' value="0"/>
                                    <input type='hidden' id='orderItemsCount' name='orderItemsCount' value="0"/>
                                    <input type='hidden' id='orderShipping' name='orderShipping' value="0"/>
                                    <input type='hidden' id='orderItemsSKU' name='orderItemsSKU' value="0"/>
                                    <input type='hidden' id='orderItemsQuantity' name='orderItemsQuantity' value="0"/>
                                    <input type='hidden' id='baseShipping' name='baseShipping' value="{{ $order["address"]["shipping_fare"]["sf_fare"] }}"/>
                                    <div class="form-actions center">
                                        <a href=''>
                                            <button type="button" class="btn btn-warning mr-1" id="refreshButton" >
                                                <i class="la la-refresh" style='font-size: 13px;'></i> Refresh Selection
                                            </button>
                                        </a>
                                        
                                        <button type="submit" name='initiateOrder' id="submitButton" onclick="disableMe();" class="btn btn-success  mr-1">
                                            <i class="la la-check" style='font-size: 13px;'></i> Initiate Order
                                        </button>
                                        
                                        <a href="{{ route("sales-associate.show.add.order.step-1") }}">
                                            <button type="button" class="btn btn-danger mr-1">
                                                <i class="la la-close" style='font-size: 13px;'></i> Cancel Order
                                            </button>
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script>
        $(document).ready(function(){
            $('#sku').dataTable( {
                "order": [
                    [1, 'asc'],
                    [3, 'asc']
                ]
            });
        })

        var orderItemsCount = 0;
        var orderItemsSKU = new Array();
        var orderItemsQuantity = new Array();
        var orderItemsCost = new Array();
        var orderItemsShipping = new Array();
        var orderSubTotal = 0;
        var orderTotal = 0;
        var orderShipping = document.getElementById("shipping").innerHTML; 

        
        $(".product").click(function(){
            
            var productSKU = $(this).attr('product-sku');
            var productStock = $(this).attr('product-stock');
            var productDescription = $(this).attr('product-description');
            var productCost = $(this).attr('product-cost');
            var productShipping = $(this).attr('product-shipping');

            console.log(productShipping);

            //scan array for that sku
            if (orderItemsSKU.indexOf(productSKU) != -1) {
                //item exists already, update
                if (orderItemsQuantity[orderItemsSKU.indexOf(productSKU)] < productStock) {
                    orderItemsQuantity[orderItemsSKU.indexOf(productSKU)]++;

                    var quantityID = "quantity"+orderItemsSKU.indexOf(productSKU);
                    var costID = "cost"+orderItemsSKU.indexOf(productSKU);

                    console.log(quantityID);

                    document.getElementById(quantityID).innerHTML = orderItemsQuantity[orderItemsSKU.indexOf(productSKU)];
                    document.getElementById(costID).innerHTML = orderItemsQuantity[orderItemsSKU.indexOf(productSKU)] * productCost;

                }
            }else{
                var updateString = "<tr><td>"+productDescription+"</td><td id='quantity"+orderItemsCount+"'>1</td><td style='text-align: right' id='cost"+orderItemsCount+"'>"+productCost+"</td></tr>";

                document.getElementById("noItemsPlaceholder").style.display = "none";
                
                //populate modal inputs
                $('#orderItems').append(updateString);
                orderItemsSKU.push(productSKU);
                orderItemsQuantity.push(1);
                orderItemsCost.push(productCost);
                orderItemsShipping.push(productShipping);
                
                orderItemsCount++;
                document.getElementById("orderItemsCount").value = orderItemsCount;

            }

            orderSubTotal = 0;
            orderShipping = document.getElementById("baseShipping").value; 
            for (let index = 0; index < orderItemsSKU.length; index++) {
                orderSubTotal =  parseFloat(orderSubTotal) + ( parseFloat(orderItemsQuantity[index] )*  parseFloat(orderItemsCost[index]));
                orderShipping = parseFloat(orderShipping) + ( parseFloat(orderItemsQuantity[index] )*  parseFloat(orderItemsShipping[index]));
            }

            //update subtotal and total
            orderTotal =  parseFloat(orderSubTotal) +  parseFloat(orderShipping);

            //update the texts
            document.getElementById("subTotal").innerHTML = orderSubTotal;
            document.getElementById("total").innerHTML = orderTotal;

            //update the inputs
            document.getElementById("orderItemsSKU").value = orderItemsSKU;
            document.getElementById("orderItemsQuantity").value = orderItemsQuantity;
            document.getElementById("orderItemSubTotal").value = orderSubTotal;
            document.getElementById("orderShipping").value = orderShipping;
            document.getElementById("shipping").innerHTML = orderShipping;
            

            
        });

        function disableMe(){
            document.getElementById("submitButton").disabled = true;
        }
    </script>
@endsection

