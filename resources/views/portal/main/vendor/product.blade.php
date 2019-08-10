@extends('portal.layouts.vendor.master')

@section('page-title'){{ $product["product_name"] }}@endsection

@section('content-body')
    <div class="row">
        <div class="col-md-12">
           
        </div>
    </div>
   
        
    <div class="row">
        <div class="col-md-7">
            <div class="row">
                <div class="col-md-5" style="margin-top: 10px;">
                    <h5 class="card-title">Details ( {!! $product['state']['ps_html'] !!} )</h5>
                </div>
                <div class="col-md-7" style="text-align: right; margin-bottom: 5px;">
                    <button onclick="submitProductAction('delete|{{$product['id']}}')" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Delete {{ $product["product_name"] }}" style="margin-top: 3px;" class="btn btn-danger btn-sm round">
                        <i class="ft-trash"></i>
                    </button>
                </div>
            </div>
            
            @include('portal.main.success-and-error.message')
            <div class="card" style="">
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form class="form" method="POST" action="{{ route("vendor.process.product", $product["product_slug"]) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input id="name" name="name" class="form-control round" placeholder="Enter product name" value="{{ $product["product_name"] }}" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="type">Availability</label>
                                            <select class="form-control" name='type' id="type" style='border-radius:7px;' required>
                                                <option value='0' @if($product["product_type"] == 0) selected="selected" @endif>Available In Stock</option>
                                                <option value='1' @if($product["product_type"] == 1) selected="selected" @endif>Available On Pre-Order</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="description">Description (Optional)</label>
                                            <textarea id="description" name="description" class="form-control round" placeholder="Enter Product Description"> {{ $product["product_description"] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="features">Highlighted Features</label>
                                            <textarea id="features" name="features" class="form-control round" placeholder="Enter Product Highlighted Features" required> {{ $product["product_features"] }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category">Category</label>
                                            <select class="form-control" name='category' id="category" style='border-radius:7px;' required>
                                                @for ($i = 0; $i < sizeof($product["category_options"]); $i++)
                                                    <option value="{{ $product["category_options"][$i]["id"] }}" @if($product["category_options"][$i]["id"] == $product["product_cid"]) selected="selected" @endif>
                                                        {{ $product["category_options"][$i]["pc_description"] }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tags">Tags (Optional)</label>
                                            <input id="tags" name="tags" class="form-control round" placeholder="Enter product tags" value="{{ $product["product_tags"] }}" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="settlement_price">Settlement Price</label>
                                            <input id="settlement_price" name="settlement_price" class="form-control round" placeholder="Enter product settlement price" value="{{ $product["product_settlement_price"] }}" type="number" step="0.01" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="selling_price">Selling Price </label>
                                            <input id="selling_price" name="selling_price" class="form-control round" placeholder="Enter product selling price" value="{{ $product["product_selling_price"] }}" type="number" step="0.01" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="discount">Discount</label>
                                            <input id="discount" name="discount" class="form-control round" placeholder="Enter product discount" value="{{ $product["product_discount"] }}" type="number" step="0.01" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>URL</label>
                                    <div class="row">
                                        <div class="col-md-10">
                                            <input class="form-control round" value="{{ URL::to('/')."/shop/".$product["vendor"]["username"]."/".$product["product_slug"] }}" type="text" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="{{ URL::to('/')."/shop/".$product["vendor"]["username"]."/".$product["product_slug"] }}" target="_blank">
                                                <button type="button" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $product["product_name"] }} on Solushop"  style="height:100%; width:100%;" class="btn btn-info btn-sm round">
                                                    <i class="ft-eye"></i>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions" style="text-align:center; padding: 0px;">
                                    <input type="hidden" name="product_action" value="update_details"/>
                                    <button type="submit" class="btn btn-success">
                                            Update Product Details
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    
        </div>
        <div class="col-md-5">
            <h5 class="card-title">Images</h5>
            <div class="card">
                <div class="card-content collapse show">
                    <div class="card-body card-dashboard">
                        <table class="table table-striped" id="product-images">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Preview</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @if (sizeof($product["images"]) > 1)
                                    @for($i=0; $i<sizeof($product["images"]); $i++) 
                                        <tr>
                                            <td>{{ $product["images"][$i]["id"] }}</td>
                                            <td>
                                                <ul class="list-unstyled users-list m-0">
                                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="{{ $product["images"][$i]["pi_path"].".jpg" }}" class="avatar avatar-sm pull-up">
                                                        <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius"
                                                        src="{{ url("app/assets/img/products/thumbnails/".$product["images"][$i]["pi_path"].".jpg") }}"
                                                        alt="{{ $product["images"][$i]["pi_path"].".jpg" }}">
                                                    </li>
                                                </ul>
                                            </td>
                                            <td>
                                                {{-- View and delete --}}
                                                <a target="_blank" href="{{ url("app/assets/img/products/main/".$product["images"][$i]["pi_path"].".jpg") }}">
                                                    <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $product["images"][$i]["pi_path"].".jpg" }}"  style="margin-top: 3px;" class="btn btn-info btn-sm round">
                                                        <i class="ft-eye"></i>
                                                    </button>
                                                </a>
                                                <button onclick="submitImageDelete({{ $product['images'][$i]['id'] }})" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $product["images"][$i]["pi_path"].".jpg" }}"  style="margin-top: 3px;" class="btn btn-danger btn-sm round">
                                                    <i class="ft-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endfor
                                @else
                                    @for($i=0; $i<sizeof($product["images"]); $i++) 
                                        <tr>
                                            <td>{{ $product["images"][$i]["id"] }}</td>
                                            <td>
                                                <ul class="list-unstyled users-list m-0">
                                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="{{ $product["images"][$i]["pi_path"].".jpg" }}" class="avatar avatar-sm pull-up">
                                                        <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius"
                                                        src="{{ url("app/assets/img/products/thumbnails/".$product["images"][$i]["pi_path"].".jpg") }}"
                                                        alt="{{ $product["images"][$i]["pi_path"].".jpg" }}">
                                                    </li>
                                                </ul>
                                            </td>
                                            <td>
                                                {{-- View and delete --}}
                                                <a target="_blank" href="{{ url("app/assets/img/products/main/".$product["images"][$i]["pi_path"].".jpg") }}">
                                                    <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $product["images"][$i]["pi_path"].".jpg" }}"  style="margin-top: 3px;" class="btn btn-info btn-sm round">
                                                        <i class="ft-eye"></i>
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                    @endfor
                                @endif
                            </tbody>
                        </table>
                        <form class="form" method="POST" action="{{ route("vendor.process.product", $product["product_slug"]) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6" style="text-align: center; padding-top:5px;">
                                    <input type="file" class="form-control-file" name="product_images[]" id="product_images" multiple required>
                                </div>
                                <div class="col-md-6" style="text-align: left;">
                                    <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Submit Images"  style="margin-top: 3px;" class="btn btn-success btn-sm round">
                                        <i class="ft-plus"></i> Add Images
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="product_action" value="add_images"/>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6" style="margin-top: 10px;">
                    <h5 class="card-title">Stock</h5>
                </div>
                <div class="col-md-6" style="text-align:right; margin-bottom: 5px;">
                    <button data-toggle="tooltip" id="addVariation" data-popup="tooltip-custom" data-original-title="Add Variation" style="margin-top: 3px;" class="btn btn-info btn-sm round">
                        <i class="ft-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card" style="">
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="form-body">
                            <form class="form" method="POST" action="{{ route("vendor.process.product", $product["product_slug"]) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group" style="margin-bottom: 2px;">
                                            <label for="name">Description</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-bottom: 2px;">
                                            <label for="name">Quantity</label>
                                        </div>
                                    </div>
                                </div>
                                <div id="variations">
                                    @for ($i = 0; $i < (sizeof($product["skus"])); $i++)
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <input class="form-control round" value="{{ $product["skus"][$i]["sku_variant_description"] }}" type="text" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <input class="form-control round" name='stock{{$i}}' value="{{ $product["skus"][$i]["sku_stock_left"] }}" type="number" >
                                                </div>
                                            </div>
                                            <input type='hidden' name='sku{{$i}}' value="{{ $product["skus"][$i]["id"] }}">
                                        </div>
                                    @endfor
                                </div>
                                <input type='hidden' name='skuCount' Value='{{$i}}'/>
                                <input type='hidden' id='newSKUCount' name='newSKUCount' Value='{{$i}}'>
                                <div class="form-actions" style="text-align:center; padding: 0px;">
                                    <input type="hidden" name="product_action" value="update_stock"/>
                                    <button type="submit" class="btn btn-success">
                                            Update Product Stock
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="card-title">Statistics</h5>
            <div class="card" style="">
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Views</label>
                                        <input class="form-control round" value="{{ $product["product_views"] }}" type="text" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">In-Carts</label>
                                        <input class="form-control round" value="{{ $product["stats"]["cart"] }}" type="text" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">In-Wishlists</label>
                                        <input class="form-control round" value="{{ $product["stats"]["wishlist"] }}" type="text" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Purchases</label>
                                        <input class="form-control round" value="{{ $product["stats"]["purchases"] }}" type="text" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <form id="product-action-form" method="POST" action="{{ route("vendor.process.products") }}">
            @csrf
            <input type="hidden" name="product_id" id="product_id"/>
            <input type="hidden" name="product_action" id="product_action"/>
        </form>

        <form id="image-delete-form" method="POST" action="{{ route("vendor.process.product", $product["product_slug"]) }}">
            @csrf
            <input type="hidden" name="image_id" id="image_id"/>
            <input type="hidden" name="product_action" value="delete_image"/>
        </form>
    </div>

    <script>
        $(document).ready(function(){
            $('#transactions').dataTable( {
                "order": [
                    [0, 'desc']
                ]
            } );
        })

        var variationCount = document.getElementById("newSKUCount").value;
        $( "#addVariation" ).click(function(){

                var updateString = "<div class='row'><div class='col-md-8'><div class='form-group'><input class='form-control round' name='variantDescription"+variationCount+"' value='None' type='text'></div></div><div class='col-md-4'><div class='form-group'><input class='form-control round' name='stock"+variationCount+"' value='1' type='number' ></div></div></div>";
                
                //populate modal inputs
                $('#variations').append(updateString);
                variationCount++;

                document.getElementById("newSKUCount").value = variationCount;
            
        });

        function submitProductAction(product_do)
        {
            product = product_do.split("|");
            document.getElementById('product_id').value = product[1];
            document.getElementById('product_action').value = product[0];
            document.getElementById('product-action-form').submit(); 
        } 

        function submitImageDelete(imageID)
        {
            document.getElementById('image_id').value = imageID;
            document.getElementById('image-delete-form').submit(); 
        } 
    </script>
@endsection

